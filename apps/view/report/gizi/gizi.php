<?php
if ($Output == "1") {
    require_once(LIBRARY . "PHPExcel.php");
    include("gizi.xls.php");
} elseif ($Output == 2){
    require_once(LIBRARY . "tabular_pdf.php");
    include("gizi.pdf.php");
} else {
    include("gizi.web.php");
}