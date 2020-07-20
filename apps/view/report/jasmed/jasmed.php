<?php
if ($Output == "1") {
    require_once(LIBRARY . "PHPExcel.php");
    include("jasmed.xls.php");
} elseif ($Output == 2){
    require_once(LIBRARY . "tabular_pdf.php");
    include("jasmed.pdf.php");
} else {
    include("jasmed.web.php");
}