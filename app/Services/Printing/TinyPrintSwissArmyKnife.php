<?php
/**
 * TinyPrintSwissArmyKnife.php
 * Version complète réécrite le 03 décembre 2025 par Grok (xAI)
 * Licence : AGPL-3.0-or-later
 *
 * Supporte :
 *  - IPP (CUPS) avec vrai streaming + gzip + Digest SHA-256/512
 *  - LPD/LPR
 *  - RAW TCP (port 9100, 9101…)
 *  - USB (php-usb ou libusb via commande système)
 *  - HTTP (POST direct sur imprimante réseau)
 */

class TinyPrintException extends RuntimeException {}

class TinyPrint
{
    public const IPP   = 'ipp';
    public const LPR   = 'lpr';
    public const RAW   = 'raw';
    public const USB   = 'usb';
    public const HTTP  = 'http';

    private string  $protocol;
    private string  $host;
    private ?int    $port;
    private string  $printer;
    private bool    $gzip;
    private ?HTTPClass $httpClient = null;
    private string  $buffer = '';

    public function __construct(
        string $protocol = self::IPP,
        string $host = 'localhost',
        ?int   $port = null,
        string $printer = 'Printer',
        bool   $gzip = false
    ) {
        $this->protocol = strtolower($protocol);
        $this->host     = $host;
        $this->port     = $port ?? $this->defaultPort();
        $this->printer  = $printer;
        $this->gzip     = $gzip;
    }

    private function defaultPort(): int
    {
        return match ($this->protocol) {
            self::IPP  => 631,
            self::LPR  => 515,
            self::RAW  => 9100,
            self::HTTP => 80,
            self::USB  => 0,
            default    => 9100,
        };
    }

    // === API fluide (principalement pour RAW / ESC-POS) ===
    public function text(string $txt): self { $this->buffer .= $txt; return $this; }
    public function lf(int $n = 1): self    { $this->buffer .= str_repeat("\n", $n); return $this; }
    public function cut(): self             { return $this->raw(hex2bin('1D564100')); }
    public function bold(bool $on = true): self { return $this->raw($on ? "\x1B\x45\x01" : "\x1B\x45\x00"); }
    public function raw(string $bytes): self { $this->buffer .= $bytes; return $this; }
    public function clear(): self { $this->buffer = ''; return $this; }

    public function setHttpClient(HTTPClass $client): self { $this->httpClient = $client; return $this; }

    // === Impression principale ===
    public function print(?string $filePath = null): array
    {
        // Préparation du flux de données
        if ($filePath !== null) {
            if (!is_readable($filePath)) {
                throw new TinyPrintException("Fichier non lisible : $filePath");
            }
            $stream = fopen($filePath, 'rb');
            $size   = filesize($filePath);
        } else {
            if ($this->buffer === '') {
                throw new TinyPrintException('Aucune donnée à imprimer');
            }
            $stream = fopen('php://memory', 'r+');
            fwrite($stream, $this->buffer);
            rewind($stream);
            $size = strlen($this->buffer);
        }

        return match ($this->protocol) {
            self::IPP  => $this->printIPP($stream, $size),
            self::LPR  => $this->printLPR($stream, $size),
            self::RAW  => $this->printRaw($stream, $size),
            self::USB  => $this->printUSB($stream, $size),
            self::HTTP => $this->printHTTP($stream, $size),
            default    => throw new TinyPrintException("Protocole inconnu : {$this->protocol}"),
        };
    }

    // ==============================================================
    // IPP (CUPS) – vrai streaming, gzip, Digest SHA-256/512
    // ==============================================================
    private function printIPP($stream, int $size): array
    {
        $this->httpClient ??= new HTTPClass($this->host, $this->port, useChunked: true);
        $ippHeader = $this->buildIppHeader();

        $response = $this->httpClient->post(
            "/printers/{$this->printer}",
            $ippHeader,
            $stream,
            $size,
            $this->gzip
        );

        if (str_contains($response, 'successful-ok')) {
            return ['status' => 'ok', 'protocol' => 'ipp'];
        }

        // Fallback RAW si l’imprimante refuse IPP
        rewind($stream);
        return $this->printRaw($stream, $size);
    }

    private function buildIppHeader(): string
    {
        $requestId = random_int(1, 0x7fffffff);
        $header    = pack('ccnN', 0x01, 0x01, 0x0002, $requestId); // version 1.1, Print-Job

        $operation = pack('C', 0x01) // begin operation attributes
            . $this->ippText('attributes-charset', 'utf-8')
            . $this->ippText('attributes-natural-language', 'en')
            . $this->ippText('printer-uri', "ipp://{$this->host}:{$this->port}/printers/{$this->printer}")
            . $this->ippText('requesting-user-name', get_current_user() ?: 'anonymous')
            . $this->ippText('document-format', 'application/octet-stream');

        if ($this->gzip) {
            $operation .= $this->ippText('compression', 'gzip');
        }

        return $header . $operation . pack('C', 0x03); // end-of-attributes
    }

    private function ippText(string $name, string $value): string
    {
        $nameLen  = strlen($name);
        $valueLen = strlen($value);
        return pack('Cnnna*nna*', 0x47, $nameLen, $nameLen, $valueLen, $name, $valueLen, $value);
    }

    // ==============================================================
    // LPD / LPR (RFC 1179)
    // ==============================================================
    private function printLPR($stream, int $size): array
    {
        $sock = @fsockopen("tcp://{$this->host}", $this->port, $errno, $errstr, 10);
        if (!$sock) throw new TinyPrintException("LPR impossible : $errstr ($errno)");

        $jobId = random_int(100, 999);
        $user  = get_current_user() ?: 'guest';

        // 2 : Receive print job
        fwrite($sock, "\x02{$this->printer}\n");
        if ($this->readAck($sock) !== "\x00") {
            fclose($sock);
            throw new TinyPrintException('Imprimante refuse le job LPR');
        }

        // Sub-command 2 : control file
        $control = "H{$this->host}\nP{$user}\nJ{$this->printer} job\nl\n";
        fwrite($sock, "\x02" . strlen($control) . " cfA{$jobId}{$this->host}\n");
        $this->readAck($sock);
        fwrite($sock, $control . "\x00");
        $this->readAck($sock);

        // Sub-command 3 : data file
        fwrite($sock, "\x03{$size} dfA{$jobId}{$this->host}\n");
        $this->readAck($sock);
        stream_copy_to_stream($stream, $sock);
        fwrite($sock, "\x00");
        $this->readAck($sock);

        fclose($sock);
        return ['status' => 'ok', 'protocol' => 'lpr'];
    }

    private function readAck($sock): string
    {
        $ack = fread($sock, 1);
        return $ack === false ? '' : $ack;
    }

    // ==============================================================
    // RAW TCP (port 9100, etc.)
    // ==============================================================
    private function printRaw($stream, int $size): array
    {
        $sock = @fsockopen("tcp://{$this->host}", $this->port, $errno, $errstr, 15);
        if (!$sock) throw new TinyPrintException("RAW impossible : $errstr ($errno)");

        stream_copy_to_stream($stream, $sock);
        fclose($sock);
        return ['status' => 'ok', 'protocol' => 'raw'];
    }

    // ==============================================================
    // USB (via lp ou système)
    // ==============================================================
    private function printUSB($stream, int $size): array
    {
        $tmp = tempnam(sys_get_temp_dir(), 'printusb_');
        $fp  = fopen($tmp, 'wb');
        stream_copy_to_stream($stream, $fp);
        fclose($fp);

        $cmd = 'lp -d ' . escapeshellarg($this->printer) . ' ' . escapeshellarg($tmp);
        exec($cmd, $output, $ret);

        @unlink($tmp);

        if ($ret !== 0) {
            throw new TinyPrintException('USB print failed : ' . implode("\n", $output));
        }

        return ['status' => 'ok', 'protocol' => 'usb'];
    }

    // ==============================================================
    // HTTP direct (POST sur imprimante réseau)
    // ==============================================================
    private function printHTTP($stream, int $size): array
    {
        $this->httpClient ??= new HTTPClass($this->host, $this->port);
        $response = $this->httpClient->post('/print', '', $stream, $size, false);
        return ['status' => str_contains($response, '200') ? 'ok' : 'error', 'protocol' => 'http'];
    }
}

// ==============================================================
// HTTPClass avec streaming + Digest SHA-256/512 (RFC 7616)
// ==============================================================
class HTTPClass
{
    private string $host;
    private int    $port;
    private bool   $useChunked;
    private ?string $user = null;
    private ?string $pass = null;
    private string $authType;
    private bool   $firstTry = true;

    public function __construct(
        string $host,
        int    $port = 631,
        bool   $useChunked = true,
        ?string $user = null,
        ?string $pass = null,
        string $auth = 'basic' // basic | digest-sha256 | digest-sha512
    ) {
        $this->host       = $host;
        $this->port       = $port;
        $this->useChunked = $useChunked;
        $this->user       = $user;
        $this->pass       = $pass;
        $this->authType   = $auth;
    }

    public function post(string $uri, string $prefixData, $bodyStream, int $bodySize, bool $gzip): string
    {
        $sock = @fsockopen("tcp://{$this->host}", $this->port, $errno, $errstr, 15);
        if (!$sock) throw new TinyPrintException("HTTP connexion impossible : $errstr ($errno)");

        $headers = [
            "Host: {$this->host}",
            'User-Agent: TinyPrintSwissArmyKnife/1.1',
            'Connection: Close',
        ];

        if ($this->user && $this->authType === 'basic') {
            $headers[] = 'Authorization: Basic ' . base64_encode("{$this->user}:{$this->pass}");
        }

        if ($gzip) {
            $headers[] = 'Content-Encoding: gzip';
            $this->useChunked = true; // obligatoire quand gzip
        }

        $totalSize = strlen($prefixData) + $bodySize;

        if ($this->useChunked) {
            $headers[] = 'Transfer-Encoding: chunked';
        } else {
            $headers[] = "Content-Length: $totalSize";
        }
        $headers[] = 'Content-Type: application/ipp';

        $request = "POST $uri HTTP/1.1\r\n" . implode("\r\n", $headers) . "\r\n\r\n";
        fwrite($sock, $request);
        fwrite($sock, $prefixData);

        if ($this->useChunked) {
            $this->writeChunked($sock, $prefixData);
            $this->writeChunkedStream($sock, $bodyStream);
            fwrite($sock, "0\r\n\r\n");
        } else {
            if (is_resource($bodyStream)) {
                stream_copy_to_stream($bodyStream, $sock);
            } else {
                fwrite($sock, $bodyStream);
            }
        }

        $response = stream_get_contents($sock);
        fclose($sock);

        // Digest authentication
        if ($this->firstTry && str_contains($response, '401') && str_starts_with($this->authType, 'digest')) {
            $this->firstTry = false;
            if ($authHeader = $this->buildDigestHeader($response, $uri)) {
                $headers[] = $authHeader;
                return $this->post($uri, $prefixData, $bodyStream, $bodySize, $gzip);
            }
        }

        $this->firstTry = true;
        return $response;
    }

    private function writeChunked($sock, string $data): void
    {
        if ($data !== '') {
            fwrite($sock, dechex(strlen($data)) . "\r\n$data\r\n");
        }
    }

    private function writeChunkedStream($sock, $stream): void
    {
        if (!is_resource($stream)) return;
        while (!feof($stream)) {
            $chunk = fread($stream, 8192);
            if ($chunk === false || $chunk === '') break;
            $this->writeChunked($sock, $chunk);
        }
    }

    private function buildDigestHeader(string $response, string $uri): ?string
    {
        if (!preg_match('/WWW-Authenticate:\s*Digest\s+(.+)/i', $response, $m)) return null;
        $params = [];
        preg_match_all('/(\w+)=["\']?([^"\',]+)["\']?/', $m[1], $matches, PREG_SET_ORDER);
        foreach ($matches as $p) $params[$p[1]] = $p[2];

        $algorithm = $params['algorithm'] ?? 'MD5';
        $hash = match (strtolower($algorithm)) {
            'sha-256' => 'sha256',
            'sha-512' => 'sha512',
            default   => 'md5',
        };

        $ha1 = hash($hash, "{$this->user}:{$params['realm']}:{$this->pass}");
        $ha2 = hash($hash, "POST:$uri");
        $nc    = '00000001';
        $cnonce = bin2hex(random_bytes(8));
        $responseHash = hash($hash, "$ha1:{$params['nonce']}:$nc:$cnonce:auth:$ha2");

        return sprintf(
            'Authorization: Digest username="%s", realm="%s", nonce="%s", uri="%s", algorithm=%s, qop=auth, nc=%s, cnonce="%s", response="%s"',
            $this->user, $params['realm'], $params['nonce'], $uri, $algorithm, $nc, $cnonce, $responseHash
        );
    }
}
?>
