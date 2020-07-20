<?php
if ($Output == "1") {
    require_once(LIBRARY . "PHPExcel.php");
    include("dokterjaga.xls.php");
} elseif ($Output == 2){
    require_once(LIBRARY . "tabular_pdf.php");
    include("dokterjaga.pdf.php");
} else {
    include("dokterjaga.web.php");
}