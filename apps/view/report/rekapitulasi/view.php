<?php
if ($Output == "1") {
    require_once(LIBRARY . "PHPExcel.php");
    include("view.xls.php");
} elseif ($Output == 2){
    require_once(LIBRARY . "tabular_pdf.php");
    include("view.pdf.php");
} else {
    include("view.web.php");
}