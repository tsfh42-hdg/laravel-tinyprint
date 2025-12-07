<!--

/*
 * Example of use of code128barcode wirh fpdf
 *   provide a pdf document of barcode stickers
 *
 *  javascript part
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
 *   Thomas Harding, 56 rue de la bourie rouge, 45 000 ORLEANS  FRANCE
 *
 */


 -->
    <script type='text/javascript'>
        i=<?php echo $i ? $i : 0; ?>;
        function add() {
            table = document.getElementById("table1");
            row = document.createElement("tr");
            row.setAttribute("id","line" + i);
            table.appendChild(row);
            cell = document.createElement("td");
            cell.setAttribute("id","td_a" + i);
            row.appendChild(cell);
            inp = document.createElement("input");
            inp.setAttribute("name","input_a" + i);
            inp.setAttribute("id","inp_a" + i);
            inp.setAttribute("type","text");
            inp.setAttribute("onkeypress","memorize_value(event.charCode)");
            inp.setAttribute("onkeyup","delete_non_ascii(this)");
            cell.appendChild(inp);
            cell = document.createElement("td");
            cell.setAttribute("id","td_b" + i);
            row.appendChild(cell);
            inp = document.createElement("input");
            inp.setAttribute("name","input_b" + i);
            inp.setAttribute("id","inp_b" + i);
            inp.setAttribute("type","text");
            inp.setAttribute("size","5");
            inp.setAttribute("value","1");
            cell.appendChild(inp);
            cell = document.createElement("td");
            cell.setAttribute("id","td_c" + i);
            row.appendChild(cell);
            inp = document.createElement("input");
            inp.setAttribute("name","input_c" + i);
            inp.setAttribute("id","inp_c" + i);
            inp.setAttribute("type","text");
            cell.appendChild(inp);
            cell = document.createElement("td");
            cell.setAttribute("id","td_d" + i);
            row.appendChild(cell);
            button = document.createElement("input");
            button.setAttribute("type","button");
            button.setAttribute("onclick","delete_row("+ i +")");
            button.setAttribute("id","bt" + i)
            button.setAttribute("value", "delete");
            row.appendChild(cell);
            cell.appendChild(button);
            i++;
        }
        
        function delete_row(j) {
            table = document.getElementById("table1");
            line = document.getElementById("line" + j);
            cell_a = document.getElementById("td_a" + j);
            cell_b = document.getElementById("td_b" + j);
            cell_c = document.getElementById("td_c" + j);
            cell_d = document.getElementById("td_d" + j);
            line.removeChild(cell_a);
            line.removeChild(cell_b);
            line.removeChild(cell_c);
            line.removeChild(cell_d);
            line.setAttribute("id",undefined);
            //for (k = j+1 ; (line = document.getElementById("line" + k)) != undefined ; k++) {
            for (k = j+1 ; k < i ; k++) {
                line = document.getElementById("line" + k);
                line.setAttribute("id","line" + (k - 1));
                button = document.getElementById("bt" + k);
                button.setAttribute("onclick","delete_row("+ (k - 1) +")");
                button.setAttribute("id","bt" + (k - 1));
                input = document.getElementById("inp_a" + k);
                input.setAttribute("name","input_a" + (k - 1));
                input.setAttribute("id","inp_a" + (k - 1));
                input = document.getElementById("inp_b" + k);
                input.setAttribute("name","input_b" + (k - 1));
                input.setAttribute("id","inp_b" + (k - 1));
                input = document.getElementById("inp_c" + k);
                input.setAttribute("name","input_c" + (k - 1));
                input.setAttribute("id","inp_c" + (k - 1));
                cell = document.getElementById("td_a" + k)
                cell.setAttribute("id","td_a" + (k - 1));
                cell = document.getElementById("td_b" + k)
                cell.setAttribute("id","td_b" + (k - 1));
                cell = document.getElementById("td_c" + k)
                cell.setAttribute("id","td_c" + (k - 1));
                cell = document.getElementById("td_d" + k)
                cell.setAttribute("id","td_d" + (k - 1));
                }
            i --;
        }
        var charcode;
        function memorize_value (character) {
            charcode = character;
        }
        function delete_non_ascii(field) {
            if (charcode > 127)
                field.value = field.value.substr(0,field.value.length - 1);
        }
        function update_values(values) {
            if (values == '')
                return;
            values = values.split(' ');
            page_format = values[0]; 
            margin_top = values[1];
            margin_bottom = values[2];
            margin_left = values[3];
            margin_right = values[4];
            columns = values[5];
            rows = values[6];
            field = document.getElementsByName('margin_top').item(0);
            field.value = margin_top;
            field = document.getElementsByName('margin_bottom').item(0);
            field.value = margin_bottom;
            field = document.getElementsByName('margin_left').item(0);
            field.value = margin_left;
            field = document.getElementsByName('margin_right').item(0);
            field.value = margin_right;
            field = document.getElementsByName('page_type').item(0);
            field.value = page_format;
            field = document.getElementsByName('columns').item(0);
            field.value = columns;
            field = document.getElementsByName('rows').item(0);
            field.value = rows;
        }
    </script>


