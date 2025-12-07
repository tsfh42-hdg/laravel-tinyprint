<?php
// src/TinyPrint.php

namespace LaravelTinyPrint;

use TinyPrint as BaseTinyPrint;
use TinyPrintException;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class TinyPrint
{
    protected BaseTinyPrint $printer;

    public function __construct(array $config = [])
    {
        $protocol = $config['protocol'] ?? config('tinyp.default', 'ipp');
        $host = $config['host'] ?? config("tinyp.printers.{$protocol}.host");
        $port = $config['port'] ?? config("tinyp.printers.{$protocol}.port");
        $printer = $config['printer'] ?? config("tinyp.printers.{$protocol}.printer");
        $gzip = $config['gzip'] ?? config("tinyp.printers.{$protocol}.gzip", false);

        $this->printer = new BaseTinyPrint($protocol, $host, $port, $printer, $gzip);

        // Auth si configurée
        $username = $config['username'] ?? config("tinyp.printers.{$protocol}.username");
        $password = $config['password'] ?? config("tinyp.printers.{$protocol}.password");
        if ($username && $password) {
            $auth = $config['auth'] ?? config("tinyp.printers.{$protocol}.auth", 'digest-sha256');
            $client = new \HTTPClass($host, $port ?? 631, true, $username, $password, $auth);
            $this->printer->setHttpClient($client);
        }
    }

    public function print(string $pathOrView, array $data = [], bool $cut = false): array
    {
        try {
            $pdfPath = $this->resolvePath($pathOrView, $data);
            $result = $this->printer->print($pdfPath);

            if ($cut && $this->printer->protocol === BaseTinyPrint::RAW) {
                $this->printer->raw(hex2bin('1D5600'))->print();
            }

            if ($pdfPath !== $pathOrView) {
                @unlink($pdfPath);
            }

            Log::info("TinyPrint: Succès via {$result['protocol']} sur " . basename($pdfPath));
            return $result;
        } catch (TinyPrintException $e) {
            return $this->fallback($pathOrView, $data, $cut, $e);
        }
    }

    public function raw(string $host, int $port = 9100): self
    {
        $this->printer = new BaseTinyPrint(BaseTinyPrint::RAW, $host, $port);
        return $this;
    }

    public function text(string $txt): self { $this->printer->text($txt); return $this; }
    public function bold(bool $on = true): self { $this->printer->bold($on); return $this; }
    public function cut(): self { $this->printer->cut(); return $this; }
    public function send(): array { return $this->printer->print(); }

    public static function ticket(string $host, string $text, int $port = 9100): self
    {
        return (new static())->raw($host, $port)->text($text);
    }

    protected function resolvePath(string $pathOrView, array $data): string
    {
        if (view()->exists($pathOrView) || str_ends_with($pathOrView, '.blade.php')) {
            $pdfPath = tempnam(sys_get_temp_dir(), 'tinyp_') . '.pdf';
            Pdf::loadView($pathOrView, $data)->save($pdfPath);
            return $pdfPath;
        }
        return $pathOrView;
    }

    protected function fallback(string $pathOrView, array $data, bool $cut, TinyPrintException $e): array
    {
        Log::warning("TinyPrint fallback: {$e->getMessage()}");
        $order = config('tinyp.fallback_order', ['ipp', 'raw', 'lpr', 'usb']);
        $current = $this->printer->protocol;

        foreach ($order as $proto) {
            if ($proto === $current) continue;
            try {
                $fallback = new static(['protocol' => $proto]);
                return $fallback->print($pathOrView, $data, $cut);
            } catch (TinyPrintException) {
                continue;
            }
        }

        throw $e;
    }
}
?>
