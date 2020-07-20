<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Eraditya Inc
 * Date: 16/01/15
 * Time: 7:42
 * To change this template use File | Settings | File Templates.
 */
$phpExcel = new PHPExcel();
$headers = array(
    'Content-Type: application/vnd.ms-excel'
, 'Content-Disposition: attachment;filename="print-cashbook-transaction.xls"'
, 'Cache-Control: max-age=0'
);
$writer = new PHPExcel_Writer_Excel5($phpExcel);
// Excel MetaData
$phpExcel->getProperties()->setCreator("Erasystem Infotama Inc (c) Budi Aditya")->setTitle("Print Laporan")->setCompany("Erasystem Infotama Inc");
$sheet = $phpExcel->getActiveSheet();
$sheet->setTitle("Rekapitulasi Transaksi Kas");
//helper for styling
$center = array("alignment" => array("horizontal" => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
$right = array("alignment" => array("horizontal" => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT));
$allBorders = array("borders" => array("allborders" => array("style" => PHPExcel_Style_Border::BORDER_THIN)));
$idrFormat = array("numberformat" => array("code" => '_([$-421]* #,##0_);_([$-421]* (#,##0);_([$-421]* "-"??_);_(@_)'));
// OK mari kita bikin ini cuma bisa di read-only
//$password = "" . time();
//$sheet->getProtection()->setSheet(true);
//$sheet->getProtection()->setPassword($password);

// FORCE Custom Margin for continous form
/*
$sheet->getPageMargins()->setTop(0)
    ->setRight(0.2)
    ->setBottom(0)
    ->setLeft(0.2)
    ->setHeader(0)
    ->setFooter(0);
*/
$row = 1;
$sheet->setCellValue("A$row",$company_name);
// Hmm Reset Pointer
$sheet->getStyle("A1");
$sheet->setShowGridlines(false);
$row++;
if ($JnsRpt == 1) {
    //Laporan Detail
    if ($PosKas == 0) {
        $sheet->setCellValue("A$row", "LAPORAN TRANSAKSI KAS");
    } elseif ($PosKas == 1) {
        $sheet->setCellValue("A$row", "LAPORAN PENERIMAAN KAS");
    } else {
        $sheet->setCellValue("A$row", "LAPORAN PENGELUARAN KAS");
    }
    $row++;
    $sheet->setCellValue("A$row", "Dari Tgl. " . date('d-m-Y', $StartDate) . " - " . date('d-m-Y', $EndDate));
    $row++;
    $sheet->setCellValue("A$row", "No.");
    $sheet->setCellValue("B$row", "Tanggal");
    $sheet->setCellValue("C$row", "No. Bukti");
    $sheet->setCellValue("D$row", "Jenis Transaksi");
    $sheet->setCellValue("E$row", "Uraian Transaksi");
    if ($PosKas == 0) {
        $sheet->setCellValue("F$row", "Debet");
        $sheet->setCellValue("G$row", "Kredit");
        $sheet->setCellValue("H$row", "Saldo");
        $sheet->setCellValue("I$row", "Beban/Sumber");
        $sheet->setCellValue("J$row", "Status");
        $sheet->getStyle("A$row:J$row")->applyFromArray(array_merge($center, $allBorders));
    } else {
        $sheet->setCellValue("F$row", "Jumlah");
        $sheet->setCellValue("G$row", "Beban/Sumber");
        $sheet->setCellValue("H$row", "Status");
        $sheet->getStyle("A$row:H$row")->applyFromArray(array_merge($center, $allBorders));
    }
    $nmr = 0;
    $str = $row;
    if ($reports != null) {
        $debet = 0;
        $kredit = 0;
        $saldo = $Sawal;
        if ($PosKas == 0) {
            $row++;
            $nmr++;
            $sheet->setCellValue("A$row", $nmr);
            $sheet->getStyle("A$row")->applyFromArray($center);
            $sheet->setCellValue("E$row", "Saldo Kas lalu..");
            $sheet->setCellValue("H$row", $saldo);
            $sheet->getStyle("A$row:J$row")->applyFromArray(array_merge($allBorders));
        }
        while ($rpt = $reports->FetchAssoc()) {
            $row++;
            $nmr++;
            $debet = $rpt["debet"];
            $kredit = $rpt["kredit"];
            if ($PosKas == 0) {
                $saldo = $saldo + $debet - $kredit;
            } else {
                $saldo += $rpt["jumlah"];
            }
            $sheet->setCellValue("A$row", $nmr);
            $sheet->getStyle("A$row")->applyFromArray($center);
            $sheet->setCellValue("B$row", date('d-m-Y', strtotime($rpt["tgl_transaksi"])));
            $sheet->setCellValue("C$row", $rpt["no_bukti"]);
            $sheet->setCellValue("D$row", $rpt["jns_transaksi"]);
            $sheet->setCellValue("E$row", $rpt["keterangan"]);
            if ($PosKas == 0) {
                $sheet->setCellValue("F$row", $debet);
                $sheet->setCellValue("G$row", $kredit);
                $sheet->setCellValue("H$row", $saldo);
                $sheet->setCellValue("I$row", $rpt["beban_bagian"]);
                if ($rpt["sts_transaksi"] == 1) {
                    $sheet->setCellValue("J$row", "Approved");
                } else {
                    $sheet->setCellValue("J$row", "Draft");
                }
                $sheet->getStyle("A$row:J$row")->applyFromArray(array_merge($allBorders));
            } else {
                $sheet->setCellValue("F$row", $rpt["jumlah"]);
                $sheet->setCellValue("G$row", $rpt["beban_bagian"]);
                if ($rpt["sts_transaksi"] == 1) {
                    $sheet->setCellValue("H$row", "Approved");
                } else {
                    $sheet->setCellValue("H$row", "Draft");
                }
                $sheet->getStyle("A$row:H$row")->applyFromArray(array_merge($allBorders));
            }
        }
        $edr = $row;
        $row++;
        $sheet->setCellValue("A$row", "Total...");
        $sheet->mergeCells("A$row:E$row");
        $sheet->getStyle("A$row")->applyFromArray($center);
        if ($PosKas == 0) {
            $sheet->setCellValue("F$row", "=SUM(F$str:F$edr)");
            $sheet->setCellValue("G$row", "=SUM(G$str:G$edr)");
            $sheet->setCellValue("H$row", $saldo);
            $sheet->mergeCells("I$row:J$row");
            $sheet->getStyle("F$str:H$row")->applyFromArray($idrFormat);
            $sheet->getStyle("A$row:J$row")->applyFromArray(array_merge($allBorders));
        } else {
            $sheet->setCellValue("F$row", "=SUM(F$str:F$edr)");
            $sheet->mergeCells("G$row:H$row");
            $sheet->getStyle("F$str:F$row")->applyFromArray($idrFormat);
            $sheet->getStyle("A$row:H$row")->applyFromArray(array_merge($allBorders));
        }
        $row++;
    }
}else{
    //Rekapitulasi
    if ($PosKas == 0) {
        $sheet->setCellValue("A$row", "REKAPITULASI TRANSAKSI KAS");
    } elseif ($PosKas == 1) {
        $sheet->setCellValue("A$row", "REKAPITULASI PENERIMAAN KAS");
    } else {
        $sheet->setCellValue("A$row", "REKAPITULASI PENGELUARAN KAS");
    }
    $row++;
    $sheet->setCellValue("A$row", "Dari Tgl. " . date('d-m-Y', $StartDate) . " - " . date('d-m-Y', $EndDate));
    $row++;
    $sheet->setCellValue("A$row", "No.");
    $sheet->setCellValue("B$row", "Jenis Transaksi");
    if ($PosKas == 0) {
        $sheet->setCellValue("C$row", "Debet");
        $sheet->setCellValue("D$row", "Kredit");
        $sheet->setCellValue("E$row", "Saldo");
        $sheet->getStyle("A$row:E$row")->applyFromArray(array_merge($center, $allBorders));
    } else {
        $sheet->setCellValue("C$row", "Jumlah");
        $sheet->getStyle("A$row:C$row")->applyFromArray(array_merge($center, $allBorders));
    }
    $nmr = 0;
    $str = $row;
    if ($reports != null) {
        $debet = 0;
        $kredit = 0;
        $saldo = $Sawal;
        if ($PosKas == 0) {
            $row++;
            $nmr++;
            $sheet->setCellValue("A$row", $nmr);
            $sheet->getStyle("A$row")->applyFromArray($center);
            $sheet->setCellValue("B$row", "Saldo Kas lalu..");
            $sheet->setCellValue("E$row", $saldo);
            $sheet->getStyle("A$row:E$row")->applyFromArray(array_merge($allBorders));
        }
        while ($rpt = $reports->FetchAssoc()) {
            $row++;
            $nmr++;
            $debet = $rpt["sum_debet"];
            $kredit = $rpt["sum_kredit"];
            if ($PosKas == 0) {
                $saldo = $saldo + $debet - $kredit;
            } else {
                $saldo += $rpt["sum_jumlah"];
            }
            $sheet->setCellValue("A$row", $nmr);
            $sheet->getStyle("A$row")->applyFromArray($center);
            $sheet->setCellValue("B$row", $rpt["jns_transaksi"]);
            if ($PosKas == 0) {
                $sheet->setCellValue("C$row", $debet);
                $sheet->setCellValue("D$row", $kredit);
                $sheet->setCellValue("E$row", $saldo);
                $sheet->getStyle("A$row:E$row")->applyFromArray(array_merge($allBorders));
            } else {
                $sheet->setCellValue("C$row", $rpt["sum_jumlah"]);
                $sheet->getStyle("A$row:C$row")->applyFromArray(array_merge($allBorders));
            }
        }
        $edr = $row;
        $row++;
        $sheet->setCellValue("A$row", "Total...");
        $sheet->mergeCells("A$row:B$row");
        $sheet->getStyle("A$row")->applyFromArray($center);
        if ($PosKas == 0) {
            $sheet->setCellValue("C$row", "=SUM(C$str:C$edr)");
            $sheet->setCellValue("D$row", "=SUM(D$str:D$edr)");
            $sheet->setCellValue("E$row", $saldo);
            $sheet->getStyle("C$str:E$row")->applyFromArray($idrFormat);
            $sheet->getStyle("A$row:E$row")->applyFromArray(array_merge($allBorders));
        } else {
            $sheet->setCellValue("C$row", "=SUM(C$str:C$edr)");
            $sheet->getStyle("C$str:C$row")->applyFromArray($idrFormat);
            $sheet->getStyle("A$row:C$row")->applyFromArray(array_merge($allBorders));
        }
        $row++;
    }
}

// Flush to client
foreach ($headers as $header) {
    header($header);
}
// Hack agar client menutup loading dialog box... (Ada JS yang checking cookie ini pada common.js)
$writer->save("php://output");

// Garbage Collector
$phpExcel->disconnectWorksheets();
unset($phpExcel);
ob_flush();
