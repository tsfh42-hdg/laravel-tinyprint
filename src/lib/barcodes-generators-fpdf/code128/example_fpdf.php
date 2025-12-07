<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/* @(#) $Header: /sources/code128php/code128php/example_fpdf.php,v 1.5 2008/06/22 14:28:15 harding Exp $

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


require_once('./lib/printing.inc');

echo <<<FIN
<?xml version="1.0" encoding="utf-8"?>

FIN;
    include('./lib/header.inc');
?>
<body>
    <h1>PDF Barcode labelling test</h1>
    <a href='../index#barcode'>Back</a>
    <div id='bandeau'><a href='http://download.savannah.nongnu.org/releases/code128php/'>PHP</a></div>

        <p>prints code128 barcodes on labels sheets.</p>
        <div>This page uses 
            <a href='http://savannah.nongnu.org/projects/code128php'>
                code128barcode.class.php</a><br>
                (class and these examples provided in the tarball).<br />
            By Thomas Harding<br />
            Distribution and modifications alloweds according the terms of
            <a href='http://www.gnu.org/licenses/lgpl.html'>GNU  LGPL</a>.
        
        <div>The class products a zeros and ones suit, suitable for any purpose
           (here: pdf printing of the barcodes).<br />
           usage:<pre>
                $barcode = new code128barcode();
                $code = $barcode-&gt;output('code string');
                ...
                $code = $barcode-&gt;output('another code string');
                $barcode-&gt;->nozerofill = true;
                $code = $barcode-&gt;output('plain width barcode');
                $barcode-&gt;unoptimized = true;
                $code = $barcode-&gt;output('laposte colissimo 123456 barcode');
                </pre>
           a very simple example with GD is 
           <a href='./example_png'>here</a>.
        </div>
           
        <p>Needed fpdf class (Also free software)
            is disponible at 
            <a href='http://fpdf.org'>http://fpdf.org</a>
        </p>
        <p>Optional LGPL PrintIPP class (for direct printing)
            is disponible at
            <a href='http://www.nongnu.org/phpprintipp/'>www.nongnu.org</a>.
        </p>
        <p>Code128 barcode specifications be found
            <a href='http://www.adams1.com/pub/russadam/128code.html'>HERE</a>.
<?php
    include ('./lib/cancel.inc');
?>
        </p>
    <form id="Form1" method='post'>
    <table id='table1' style='text-align: left'>
        <tr>
            <th>
                Predefined sheets:
            </th>
            <td>
                <select name='predefined_sheets' onmouseup='update_values(this.value)'>
<?php
    foreach($predefined_values as $title => $value) {
        if ($value == isset($values->predefined_sheets)
                            ? $values->predefined_sheets
                            : "")
            $selected = "selected='1'";
        else
            $selected="";
        echo <<<FIN
                    <option value='{$value}' $selected>{$title}</option>
                    
FIN;
        }
?>
                </select>
            </td>
<?php
    if (isset($values->printer_choice))
        echo <<<FIN
            <th>
                Printer:
            </th>
            <td>
                {$values->printer_choice}
            </td>
FIN;
    $unoptimized = (!empty($values->unoptimized)) ? 'checked="checked"' : '';
    $nozerofill = (!empty($values->nozerofill)) ? 'checked="checked"' : '';
    echo <<<FIN
            <th>
                Colissimo <input type="checkbox" name="unoptimized" value="1" {$unoptimized} />
            </th>
            <th>
                Plain width <input type="checkbox" name="nozerofill" value="1" {$nozerofill} />
            </th>
FIN;
?>
        </tr>
        <tr>
            <th style='width: 12em;'>Page type</th>
            <th style='width: 12em;'>Start label</th>
            <th style='width: 12em;'>labels / row</th>
            <th style='width: 12em;'>labels / column</th>
        </tr>

<?php

    $A4 = isset($values->page_format) && ($values->page_format == 'A4') 
            ? "selected='1'" : "";
    $legal = isset($values->page_format) && ($values->page_format == 'Legal')
            ? "selected='1'" : "";
    $columns = isset($values->columns) ? $values->columns : 4;
    $rows = isset($values->rows) ? $values->rows : 10;
    $margin_top = isset($values->margin_top) ? $values->margin_top : 5;
    $margin_bottom = isset($values->margin_bottom) ? $values->margin_bottom : 5;
    $margin_left = isset($values->margin_left) ? $values->margin_left : 3;
    $margin_right = isset($values->margin_right) ? $values->margin_right : 3;
    echo <<<FIN
        <tr>
            <td>
                <select name='page_type'>
                    <option value='A4' $A4>A4</option>
                    <option value='Legal' $legal>Legal</option>
                </select>
            <td>
                <input type='text' name='start' size='5' value='1' />
            </td><td>    
                <input type='text' name='columns' size='5' value='$columns' />
            </td><td>    
                <input type='text' name='rows' size='5' value='$rows' />
            </td><td>    
            </td>
        </tr>
        <tr>
            <td colspan='4'>
                Following dimensions are specified in millimeter:
            </td>
        </tr>
        <tr>
            <th>margin top</th>
            <th>margin bottom</th>
            <th>margin left</th>
            <th>margin right</th>
        </tr>
        <tr>
            <td>
                <input type='text' name='margin_top' size='5' value='$margin_top' />
            </td><td>
                <input type='text' name='margin_bottom' size='5' value='$margin_bottom' />
            </td><td>    
                <input type='text' name='margin_left' size='5' value='$margin_left' />
            </td><td>    
                <input type='text' name='margin_right' size='5' value='$margin_right' />
            </td>
        </tr>

FIN;
?>
        <tr>
            <th colspan='4' style='text-align: right'>
                Actions
            </th>
        </tr>
        <tr>
            <td colspan='4' style='text-align: right'>
                <input type="button" name="add_code" title="add code" onclick="JavaScript:add()" value="add code" />
                <input type='submit' name='print' value='print' />
            </td>
        </tr>
        <tr>
            <td colspan='4'>
                Note: Only ASCII characters are allowed in "code"
            </td>
        </tr>
        <tr>
            <th>code</th>
            <th>Qty.</th>
            <th>Optional text</th>
            <th>delete</th>
            <th></th>
        </tr>
<?php

    for ($i = 0;(isset($_SESSION["input_a".$i]))||$i < 1 ; $i++) {
    
        $a = isset($_SESSION["input_a".$i]) && $_SESSION["input_a".$i] 
                ? $_SESSION["input_a".$i] : "";
                
        $b = isset($_SESSION["input_b".$i]) && $_SESSION["input_b".$i] 
                ? $_SESSION["input_b".$i] : 1;
        
        $c = isset($_SESSION["input_c".$i]) && $_SESSION["input_c".$i] 
                ? $_SESSION["input_c".$i] : "";
        echo <<<FIN
        <tr id='line$i'>
            <td id='td_a$i'>
                <input id='inp_a$i'
                       type='text' 
                       name='input_a$i' 
                       value='{$a}'
                       onkeypress='memorize_value(event.charCode)'
                       onkeyup='delete_non_ascii(this)' />
            <!--
                <textarea name='input_a$i'></textarea>
                for \\n testing
            -->
            </td>
            <td id='td_b$i'>
                <input id='inp_b$i' type='text' name='input_b$i' value='$b' size='5' />
            </td>
            <td id='td_c$i'>
                <input id='inp_c$i' type='text' name='input_c$i' value='{$c}' />
            </td>
            <td id='td_d$i'>
                <input type='button' id='bt$i' onclick='JavaScript:delete_row($i)' value='delete' />
            </td>
        </tr>
        
FIN;
        }
?>
    </table>
    </form>
    
<?php
    include("./lib/javascript.inc");
//    @include("../phpmyvisits.php");
?>
</body>
</html>
