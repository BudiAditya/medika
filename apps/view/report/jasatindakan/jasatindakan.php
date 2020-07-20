<?php
if ($Output == "1") {
    require_once(LIBRARY . "PHPExcel.php");
    include("jasatindakan.xls.php");
} elseif ($Output == 2){
    require_once(LIBRARY . "tabular_pdf.php");
    include("jasatindakan.pdf.php");
} else {
    include("jasatindakan.web.php");
}