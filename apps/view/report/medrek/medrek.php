<?php
if ($Output == "1") {
    require_once(LIBRARY . "PHPExcel.php");
    include("medrek.xls.php");
} elseif ($Output == 2){
    require_once(LIBRARY . "tabular_pdf.php");
    include("medrek.pdf.php");
} else {
    include("medrek.web.php");
}