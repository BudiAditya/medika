<?php
if ($Output == "1") {
    require_once(LIBRARY . "PHPExcel.php");
    include("lab.xls.php");
} elseif ($Output == 2){
    require_once(LIBRARY . "tabular_pdf.php");
    include("lab.pdf.php");
} else {
    include("lab.web.php");
}