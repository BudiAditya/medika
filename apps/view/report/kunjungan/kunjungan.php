<?php
if ($Output == "1") {
    require_once(LIBRARY . "PHPExcel.php");
    include("kunjungan.xls.php");
} elseif ($Output == 2){
    require_once(LIBRARY . "tabular_pdf.php");
    include("kunjungan.pdf.php");
} else {
    include("kunjungan.web.php");
}