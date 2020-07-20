<?php
if ($Output == "1") {
    require_once(LIBRARY . "PHPExcel.php");
    include("ambulance.xls.php");
} elseif ($Output == 2){
    require_once(LIBRARY . "tabular_pdf.php");
    include("ambulance.pdf.php");
} else {
    include("ambulance.web.php");
}