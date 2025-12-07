<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/* @(#) $Header: /sources/code128php/code128php/lib/printing.inc,v 1.4 2007/11/10 11:31:28 harding Exp $

/*
 * Example of use of code128barcode wirh fpdf
 *   provide a pdf document of barcode stickers
 *
 * Copyright(C) 2006 Thomas Harding
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are
 * met:
 * 
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of Thomas Harding nor the names of its
 *       contributors may be used to endorse or promote products derived from
 *       this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
 * IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE. 
 *
 *   mailto:thomas.harding@laposte.net
 *   Thomas Harding, 56 rue de la bourie rouge, 45 000 ORLEANS -- FRANCE
 *
 */

//
// SETTINGS
//
//error_reporting(E_ERROR);
error_reporting(E_ALL);
define('DIRECT_PRINTING',true); // using PrintIPP
//define('DIRECT_PRINTING',false); // force PDF output
define('IPP_HOST','localhost');
define('IPP_PORT',631);
define('IPP_PRINTER_URI','/printers/epson');
define('IPP_USER_NAME','barcode');
define('IPP_USER_PASSWD','barcode');

$predefined_values = array(
                    "" => "",
                    // "name" => "page-format margin-top margin-bottom margin-left margin-right columns rows",
                    "foobar 2006" => "A4 8 8 4 4 5 12",
                    "foobar 2007" => "Legal 10 10 5 5 5 11",
                );
//$values->printer_list = array("PDF" => "PDF","ipp://geekette:631/printers/epson" => "epson");
//
// END OF SETTINGS
//




// loads PrintIPP if needed
if (DIRECT_PRINTING && file_exists('./PrintIPP.php'))
    require_once('./PrintIPP.php');
if (DIRECT_PRINTING && file_exists('./CupsPrintIPP.php')) {
    require_once('./ExtendedPrintIPP.php');
    require_once('./CupsPrintIPP.php');
    }

if (isset($_REQUEST['cancel_print']) && class_exists('ExtendedPrintIPP')) {
    $job = isset($_REQUEST['job']) ? $_REQUEST['job'] : false;
    if ($job) {
        $ipp = new ExtendedPrintIPP();
        
        if (IPP_USER_NAME)
            $ipp->setUserName(IPP_USER_NAME);
        else
            $ipp->setUserName();
        
        if (IPP_USER_NAME && IPP_USER_PASSWD)
            $ipp->setAuthentification (IPP_USER_NAME,IPP_USER_PASSWD);

        $ipp->cancelJob($job);
        }
}
if (class_exists('CupsPrintIPP') && !isset($values->printer_list)) {

    $ipp = new CupsPrintIPP();
    $ipp->setHost(IPP_HOST);
    $ipp->setPort(IPP_PORT);
        
    if (IPP_USER_NAME)
        $ipp->setUserName(IPP_USER_NAME);
    else
        $ipp->setUserName();
        
    if (IPP_USER_NAME && IPP_USER_PASSWD)
        $ipp->setAuthentification (IPP_USER_NAME,IPP_USER_PASSWD);
    
    $values->printer_list = array('PDF' => 'PDF');
    $ipp->getPrinters();
    $printer_names = array("epson");
    foreach ($ipp->available_printers AS $printer) {
        $ipp->setPrinterURI($printer);
        $ipp->getPrinterAttributes();
        $printer_name = $ipp->printer_attributes->printer_name->_value0;
    
        $values->printer_list[$printer] = $printer_name;
        }
    
}

if (isset($values->printer_list)) {

    $values->printer_choice = "\n\n
                <select name='printer'>\n";
    foreach($values->printer_list as $printer => $printer_name) {
        if (isset($_REQUEST['printer']) && $_REQUEST['printer'] == $printer) {
            $selected = "selected='1'";
            $values->printer_uri = $printer;
        } else
            $selected = "";
    $values->printer_choice .= "
                    <option value='$printer' $selected>$printer_name</option>";
    
    }
    $values->printer_choice .= "
                </select>\n\n";
}

if (isset($_REQUEST['print'])) {
    
    require_once('./fpdf153/fpdf.php');
    // ###
    require_once('./code128barcode.class.php');
    //require_once('./code128.php');
    
       
    Print_barcodes($values);    

}

function Print_barcodes(&$values) {
        
    session_start();

    $values->predefined_sheets = isset($_REQUEST['predefined_sheets'])
                            ? $_REQUEST['predefined_sheets']
                            : "";

    $values->page_format = $_SESSION['page_type'] = isset($_REQUEST['page_type']) 
                        && $_REQUEST['page_type'] === 'Legal'
                        ? 'Legal'
                        : 'A4';
    
    $values->start = isset($_REQUEST['start']) && intval($_REQUEST['start']) 
                ? intval($_REQUEST['start'])
                : 1;
                
    $values->columns = $_SESSION['columns'] = isset($_REQUEST['columns']) 
                && intval($_REQUEST['columns'])
                ? intval($_REQUEST['columns'])
                : 4;
    
    $values->rows = $_SESSION['rows'] = isset($_REQUEST['rows']) 
                && intval($_REQUEST['rows'])
                ? intval($_REQUEST['rows'])
                : 10;
                
    $values->margin_top = $_SESSION['margin_top'] = 
                    isset($_REQUEST['margin_top']) 
                    && intval($_REQUEST['margin_top'])
                    ? intval($_REQUEST['margin_top'])
                    : 0;
                
    $values->margin_bottom = $_SESSION['margin_bottom'] = 
                    isset($_REQUEST['margin_bottom']) 
                    && intval($_REQUEST['margin_bottom'])
                    ? intval($_REQUEST['margin_bottom'])
                    : 0;

                
    $values->margin_left = $_SESSION['margin_left'] = 
                    isset($_REQUEST['margin_left']) 
                    && intval($_REQUEST['margin_left'])
                    ? intval($_REQUEST['margin_left'])
                    : 0;

                
    $values->margin_right = $_SESSION['margin_right'] =
                    isset($_REQUEST['margin_right']) 
                    && intval($_REQUEST['margin_right'])
                    ? intval($_REQUEST['margin_right'])
                    : 0;
    
    $values->unoptimized = (!empty($_REQUEST['unoptimized'])) ? true : false;
    $values->nozerofill = (!empty($_REQUEST['nozerofill'])) ? true : false;

    // delete old barcodes
    foreach($_SESSION as $input => $value)
        if(preg_match('#^input_(a|b)[0-9]{1,}$#',$input))
            unset($_SESSION[$input]);
        
    foreach($_REQUEST as $input => $value) 
        if(preg_match('#^input_a[0-9]{1,}$#',$input)) {
        
            $key = str_replace('input_a','',$input);
            $codes[] = stripslashes($value);
            
            $quantities[] = $quantity = isset($_REQUEST['input_b'.$key]) &&
                                intval($_REQUEST['input_b'.$key])
                                ? intval($_REQUEST['input_b'.$key])
                                : 1;
            $texts[] = $text = isset($_REQUEST['input_c'.$key])
                                ? stripslashes($_REQUEST['input_c'.$key])
                                : "";
                                
            $_SESSION[$input] = $value;
            $_SESSION['input_b'.$key] = $quantity;
            $_SESSION['input_c'.$key] = $text;
            }
    // ###
    $barcode = new code128barcode();
    $pdf = new FPDF('P','mm',$values->page_format);
    $pdf->SetMargins(0,0,0,0);
    $pdf->setAutoPageBreak(false);
    $pdf->addPage();
    
    switch ($values->page_format) {
        
        case 'A4':
            $pagewidth = 220;
            $pageheight = 297;
            break;
        case 'Legal':
            $pagewidth = 8.5 * 25.4;
            $pageheight = 14 * 25.4;
            break;
        }
        
    $limit = count($codes);
    $x = $values->margin_left;
    $y = $values->margin_top;
    $width = ($pagewidth 
                - $values->margin_left 
                - $values->margin_right)
                / $values->columns;
    $height = ($pageheight
                - $values->margin_top
                - $values->margin_bottom)
                / $values->rows;
    $pdf->SetFont('Courier','',8);
    
    for ($i = 1 ; $i < $values->start ; $i++) {
    
        $x += $width;
        if ($x + $width > $pagewidth) {
            $x = $values->margin_left;
            $y += $height;
            }
        if ($y + $height > $pageheight) {
            if ((($i + 1) < $limit) && (($j + 1) < $quantities[$i]))
            //$pdf->AddPage();
            $y = $values->margin_top;
            }
        }
    
    $pdf->setXY($x + 3,$y + 3);
   
    if ($values->unoptimized) $barcode->unoptimized = true;
    if ($values->nozerofill) $barcode->nozerofill = true;

    for ($i = 0 ; $i < $limit ; $i++) {
        if (empty($codes[$i]))
            continue;
            

        $code = $barcode->output($codes[$i]);    
        
        $extent = $width / strlen($code);

        if ($extent < 0.19050)
            $pdf->MultiCell($width,5,sprintf(_("Error: ")
                ._("%s: Please choose larger labels or shorter code"),
                $codes[$i]));
        else
        for ($j = 0 ; $j < $quantities[$i] ; $j++) {
            
            $pdf->SetLineWidth($extent);     
            if ($barcode->error)
                $pdf->MultiCell($width,5,_("Error: ").$barcode->error);
            else
                for ($k=0; $k < strlen($code) ; $k++) {
                    
                    if ($code[$k] == 1)
                        $pdf->SetDrawColor(0,0,0);
                    else
                        $pdf->SetDrawColor(255,255,255);

                    $pdf->Line(($x+($k*$extent)),($y + 3),($x+($k*$extent)),($y + $height - 8));
                    }
            $pdf->setXY($x+3,$y + $height - 7);
            $pdf->MultiCell($width - 6,4,$texts[$i] ? $texts[$i] : $codes[$i]);
            //$pdf->Text($x + 3,$y + $height - 3,$texts[$i] ? $texts[$i] : $codes[$i]);
            //$pdf->Text($x + $width,3,$barcode->checksum);
            
            $x += $width;
            if ($x + $width > $pagewidth) {
                $x = $values->margin_left;
                $y += $height;
                }
            if ($y + $height > $pageheight) {
                if ((($i + 1) < $limit) && (($j + 1) < $quantities[$i]))
                $pdf->AddPage();
                $y = $values->margin_top;
                }
            $pdf->setXY($x + 3,$y + 3);
            }
        }
        
    $printer = isset($values->printer_uri) 
                            ? $values->printer_uri
                            : IPP_PRINTER_URI;
                            
    $pdfstring = $pdf->output("barcodes".date('U')."pdf",'S');

    if (class_exists('PrintIPP') && DIRECT_PRINTING && $printer != 'PDF')
        Print_pdf($pdfstring,$values);
    else {
        $pdf->output("barcodes".date('U').".pdf","I");
        exit();    
        }

}
function Print_pdf($pdf,&$values) {

    if (!class_exists('PrintIPP')) 
        trigger_error('Please Add PrintIPP.php file to allow printing',
                        E_USER_ERROR);
    $ipp = new PrintIPP();
    $ipp->setHost(IPP_HOST);
    $ipp->setPort(IPP_PORT);
    $ipp->setPrinterURI(isset($values->printer_uri) 
                            ? $values->printer_uri
                            : IPP_PRINTER_URI);
    $ipp->setData($pdf);
    $ipp->setMimeMediaType('application/pdf');
    $ipp->setDocumentName('barcodes '.date('U'));
    if (IPP_USER_NAME)
        $ipp->setUserName(IPP_USER_NAME);
    else
        $ipp->setUserName();
    if (IPP_USER_NAME && IPP_USER_PASSWD)
        $ipp->setAuthentification (IPP_USER_NAME,IPP_USER_PASSWD);
    $values->ipp_error = $ipp->printJob();
    $values->job = $ipp->last_job;
}
?>
