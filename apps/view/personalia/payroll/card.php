<!DOCTYPE HTML>
<?php
require_once (LIBRARY  . "gen_functions.php");
?>
<html>
<head>
	<title>ERAMEDIKA - Kartu Gaji Karyawan</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
</head>
<body>
<?php include(VIEW . "main/menu.php"); ?>
<?php if (isset($error)) { ?>
<div class="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
<div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php } ?>
<br/>
<form id="frm" name="frmReport" method="post">
    <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
        <tr>
            <th colspan="5">KARTU GAJI KARYAWAN</th>
        </tr>
        <tr>
            <th>Nama Karyawan</th>
            <th>Bisnis Unit</th>
            <th>Tahun</th>
            <th>Output</th>
            <th>Action</th>
        </tr>
        <tr>
            <td><select name="Nik" id="Nik" required>
                    <option value=""></option>
                    <?php
                    /** @var $lstKaryawan Karyawan[] */
                    foreach ($lstKaryawan as $karyawan){
                        if ($Nik == $karyawan->Nik){
                            printf('<option value="%s" selected="selected"> %s - %s </option>',$karyawan->Nik,$karyawan->Nik,$karyawan->Nama);
                        }else {
                            printf('<option value="%s"> %s - %s </option>',$karyawan->Nik,$karyawan->Nik,$karyawan->Nama);
                        }
                    }
                    ?>
                </select>
            </td>
            <td><select name="SbuId" id="SbuId">
                    <?php
                    /** @var $SbuList Sbu[] */
                    foreach ($SbuList as $sbu) {
                        if ($sbu->IsPayroll == 1) {
                            if ($sbu->Id == $SbuId) {
                                printf('<option value="%d" selected="selected"> %s - %s </option>', $sbu->Id, $sbu->Id, $sbu->SbuName);
                            } else {
                                printf('<option value="%d"> %s - %s </option>', $sbu->Id, $sbu->Id, $sbu->SbuName);
                            }
                        }
                    }
                    ?>
                </select>
            </td>
            <td><select name="Tahun" id="Tahun">
                    <option value="2018" <?php print($Tahun == 2018 ? 'selected="selected"' : '');?>> 2018 </option>
                    <option value="2019" <?php print($Tahun == 2019 ? 'selected="selected"' : '');?>> 2019 </option>
                    <option value="2020" <?php print($Tahun == 2020 ? 'selected="selected"' : '');?>> 2020 </option>
                    <option value="2021" <?php print($Tahun == 2021 ? 'selected="selected"' : '');?>> 2021 </option>
                </select>
            </td>
            <td><select name="Output" id="Output">
                    <option value="0" <?php print($Output == 0 ? 'selected="selected"' : '');?>> 0 - HTML </option>
                    <option value="1" <?php print($Output == 1 ? 'selected="selected"' : '');?>> 1 - Excel </option>
                </select>
            </td>
            <td align="center"><button type="submit" formaction="<?php print($helper->site_url("personalia/payroll/card")); ?>"><b>Tampilkan</b></button></td>
        </tr>
    </table>
</form>
<br>
<!-- start web report -->
<?php  if ($dtpayroll != null){
        printf("<h2>KARTU GAJI KARYAWAN TAHUN %s</h2>",$Tahun);
        printf("<h3>%s</h3>",$NmKaryawan); ?>
        <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
            <tr nowrap>
                <th>No.</th>
                <th>Unit</th>
                <th>Bulan</th>
                <th>Tahun</th>
                <th>Gapok</th>
                <th nowrap>Tj. Jabatan</th>
                <th nowrap>Tj. Profesi</th>
                <th nowrap>BPJS Kes</th>
                <th nowrap>BPJS TK</th>
                <th>THR</th>
                <th nowrap>JM Khusus</th>
                <th nowrap>JM Visit</th>
                <th nowrap>Fee Dokter Jaga</th>
                <th nowrap>Fee Profit</th>
                <th nowrap>Total Gaji</th>
                <th nowrap>Pot Absensi</th>
                <th nowrap>Pot Piutang</th>
                <th nowrap>Pot BPJS</th>
                <th nowrap>Pot Lain</th>
                <th nowrap>Tot Potongan</th>
                <th>T H P</th>
            </tr>
            <?php
            $nmr = 1;
            $tGapok = 0;
            $tTujab = 0;
            $tTupro = 0;
            $tBkes = 0;
            $tBtk = 0;
            $tThr = 0;
            $tFTk = 0;
            $tFJm = 0;
            $tFPf = 0;
            $tFDj = 0;
            $tThp = 0;
            $tTotGaji = 0;
            $tPotAbsensi = 0;
            $tPotPiutang = 0;
            $tPotBpjsKes = 0;
            $tPotLain = 0;
            $tTotPot = 0;
            /** @var $dtpayroll Payroll[]*/
            foreach ($dtpayroll as $payroll) {
                print("<tr valign='Top' nowrap>");
                printf("<td>%s</td>", $nmr);
                printf("<td>%s</td>", $payroll->SbuName);
                printf("<td nowrap>%s</td>", get_bulan($payroll->Bulan));
                printf("<td>%s</td>", $payroll->Tahun);
                printf("<td align='right'>%s</td>", number_format($payroll->Gapok, 0));
                printf("<td align='right'>%s</td>", number_format($payroll->TjJabatan, 0));
                printf("<td align='right'>%s</td>", number_format($payroll->TjProfesi, 0));
                printf("<td align='right'>%s</td>", number_format($payroll->BpjsKes, 0));
                printf("<td align='right'>%s</td>", number_format($payroll->BpjsTk, 0));
                printf("<td align='right'>%s</td>", number_format($payroll->Thr, 0));
                printf("<td align='right'>%s</td>", number_format($payroll->FeeTikhus, 0));
                printf("<td align='right'>%s</td>", number_format($payroll->FeeJasmed, 0));
                printf("<td align='right'>%s</td>", number_format($payroll->FeeDokterJaga, 0));
                printf("<td align='right'>%s</td>", number_format($payroll->FeeProfit, 0));
                printf("<td class='bold' align='right'>%s</td>", number_format($payroll->TotGaji, 0));
                printf("<td align='right'>%s</td>", number_format($payroll->PotAbsensi, 0));
                printf("<td align='right'>%s</td>", number_format($payroll->PotPiutang, 0));
                printf("<td align='right'>%s</td>", number_format($payroll->PotBpjsKes, 0));
                printf("<td align='right'>%s</td>", number_format($payroll->PotLain, 0));
                printf("<td class='bold' align='right'>%s</td>", number_format($payroll->TotPotongan, 0));
                printf("<td class='bold' align='right'>%s</td>", number_format($payroll->Thp, 0));
                print("</tr>");
                $nmr++;
                $tGapok += $payroll->Gapok;
                $tTujab += $payroll->TjJabatan;
                $tTupro += $payroll->TjProfesi;
                $tBkes += $payroll->BpjsKes;
                $tBtk += $payroll->BpjsTk;
                $tThr += $payroll->Thr;
                $tFTk += $payroll->FeeTikhus;
                $tFJm += $payroll->FeeJasmed;
                $tFPf += $payroll->FeeProfit;
                $tThp += $payroll->Thp;
                $tFDj += $payroll->FeeDokterJaga;
                $tTotGaji += $payroll->TotGaji;
                $tPotAbsensi += $payroll->PotAbsensi;
                $tPotPiutang += $payroll->PotPiutang;
                $tPotBpjsKes += $payroll->PotBpjsKes;
                $tPotLain += $payroll->PotLain;
                $tTotPot += $payroll->TotPotongan;
            }
            print("<tr>");
            print("<td class='bold' colspan='4' align='right'>Total..</td>");
            printf("<td class='bold' align='right'>%s</td>", number_format($tGapok, 0));
            printf("<td class='bold' align='right'>%s</td>", number_format($tTujab, 0));
            printf("<td class='bold' align='right'>%s</td>", number_format($tTupro, 0));
            printf("<td class='bold' align='right'>%s</td>", number_format($tBkes, 0));
            printf("<td class='bold' align='right'>%s</td>", number_format($tBtk, 0));
            printf("<td class='bold' align='right'>%s</td>", number_format($tThr, 0));
            printf("<td class='bold' align='right'>%s</td>", number_format($tFTk, 0));
            printf("<td class='bold' align='right'>%s</td>", number_format($tFJm, 0));
            printf("<td class='bold' align='right'>%s</td>", number_format($tFDj, 0));
            printf("<td class='bold' align='right'>%s</td>", number_format($tFPf, 0));
            printf("<td class='bold' align='right'>%s</td>", number_format($tTotGaji, 0));
            printf("<td class='bold' align='right'>%s</td>", number_format($tPotAbsensi, 0));
            printf("<td class='bold' align='right'>%s</td>", number_format($tPotPiutang, 0));
            printf("<td class='bold' align='right'>%s</td>", number_format($tPotBpjsKes, 0));
            printf("<td class='bold' align='right'>%s</td>", number_format($tPotLain, 0));
            printf("<td class='bold' align='right'>%s</td>", number_format($tTotPot, 0));
            printf("<td class='bold' align='right'>%s</td>", number_format($tThp, 0));
            print("</tr>");
            ?>
        </table>
    <?php
} ?>
</body>
</html>
