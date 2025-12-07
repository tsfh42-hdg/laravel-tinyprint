<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/* @(#) $Header: /sources/code128php/code128php/lib/cancel.inc,v 1.1 2006/04/20 18:34:08 harding Exp $

/*
 * Example of use of code128barcode wirh fpdf
 *   provide a pdf document of barcode stickers
 *
 *  "cancel" part
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


    $ipp_error = isset($values->ipp_error) ? $values->ipp_error : "";
    $job = isset($values->job) ? $values->job : false;
    if ($job && class_exists('ExtendedPrintIPP'))
        $cancel_string = "
            <form name='cancel' method='post' style='display: inline'>
                <input type='hidden' name='job' value='$job' />
                <input type='submit' name='cancel_print' value='Cancel' />
            </form>\n";
    else
        $cancel_string = '';
    if ($ipp_error)
    echo <<<FIN
        <div style='font-weight: bold; color: red'>
            Printing-result: $ipp_error<br />
            Job: $job $cancel_string
        </div>
FIN;

?>
