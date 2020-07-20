<?php
if ($Output == "1") {
    require_once(LIBRARY . "PHPExcel.php");
    include("jmtikus.xls.php");
} elseif ($Output == 2){
    require_once(LIBRARY . "tabular_pdf.php");
    include("jmtikus.pdf.php");
} else {
    include("jmtikus.web.php");
}