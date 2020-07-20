<!DOCTYPE HTML>
<?php
require_once (LIBRARY  . "gen_functions.php");
?>
<html>
<head>
	<title>ERAMEDIKA - Daftar Gaji Karyawan</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
    <script>
        function fEdit(id) {
            var url = '<?php print($helper->site_url("personalia/payroll/edit/")); ?>'+id;
            if (confirm("Edit data slip gaji ini?")) {
                window.location.href = url;
            }
        }

        function fHapus(id) {
            var url = '<?php print($helper->site_url("personalia/payroll/delete/".$Tahun."/".$Bulan."/")); ?>'+id;
            if (confirm("Hapus data slip gaji ini?")) {
                window.location.href = url;
            }
        }
    </script>
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
            <th colspan="5">DAFTAR GAJI KARYAWAN</th>
        </tr>
        <tr>
            <th>Bisnis Unit</th>
            <th>Tahun</th>
            <th>Bulan</th>
            <th>Output</th>
            <th>Action</th>
        </tr>
        <tr>
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
            <td><select name="Bulan" class="text2" id="Bulan">
                    <option value="1" <?php print($Bulan == 1 ? 'selected="selected"' : '');?>> 1 - Januari </option>
                    <option value="2" <?php print($Bulan == 2 ? 'selected="selected"' : '');?>> 2 - Februari </option>
                    <option value="3" <?php print($Bulan == 3 ? 'selected="selected"' : '');?>> 3 - Maret </option>
                    <option value="4" <?php print($Bulan == 4 ? 'selected="selected"' : '');?>> 4 - April </option>
                    <option value="5" <?php print($Bulan == 5 ? 'selected="selected"' : '');?>> 5 - M e i </option>
                    <option value="6" <?php print($Bulan == 6 ? 'selected="selected"' : '');?>> 6 - Juni </option>
                    <option value="7" <?php print($Bulan == 7 ? 'selected="selected"' : '');?>> 7 - Juli </option>
                    <option value="8" <?php print($Bulan == 8 ? 'selected="selected"' : '');?>> 8 - Agustus </option>
                    <option value="9" <?php print($Bulan == 9 ? 'selected="selected"' : '');?>> 9 - September </option>
                    <option value="10" <?php print($Bulan == 10 ? 'selected="selected"' : '');?>> 10 - Oktober </option>
                    <option value="11" <?php print($Bulan == 11 ? 'selected="selected"' : '');?>> 11 - Nopember </option>
                    <option value="12" <?php print($Bulan == 12 ? 'selected="selected"' : '');?>> 12 - Desember </option>
                </select>
            </td>
            <td><select name="Output" id="Output">
                    <option value="0" <?php print($Output == 0 ? 'selected="selected"' : '');?>> 0 - HTML </option>
                    <option value="1" <?php print($Output == 1 ? 'selected="selected"' : '');?>> 1 - Excel </option>
                </select>
            </td>
            <td align="center"><button type="submit" formaction="<?php print($helper->site_url("personalia/payroll/view")); ?>"><b>Tampilkan</b></button></td>
        </tr>
    </table>
</form>
<br>
<!-- start web report -->
<?php  if ($dtpayroll != null){
        printf("<h2>DAFTAR GAJI KARYAWAN %s</h2>",strtoupper($SbuName));
        printf("<h3>Bulan: %s %d</h3>",get_bulan($Bulan),$Tahun); ?>
        <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
            <tr nowrap>
                <th>No.</th>
                <th>N I K</th>
                <th nowrap>Nama Karyawan</th>
                <th>Bagian</th>
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
                <th>Status</th>
                <th>Action</th>
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
                printf("<td>%s</td>", $payroll->Nik);
                printf("<td nowrap>%s</td>", $payroll->Nama);
                printf("<td>%s</td>", $payroll->Bagian);
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
                printf("<td>%s</td>", $payroll->TrxStatus == 0 ? 'Asli' : 'Edited');
                //printf("<td><a class='button' href='%s'>Edit</a><a class='button' href='%s'>Hapus</a></td>",$helper->site_url("personalia/payroll/edit/".$payroll->Id),$helper->site_url("personalia/payroll/delete/".$payroll->Id));
                printf("<td nowrap><button id='btEdit' onclick='fEdit(%d)'>Edit</button><button id='btHapus' onclick='fHapus(%d)'>Hapus</button></td>",$payroll->Id,$payroll->Id);
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
            print("<td colspan='2'>&nbsp;</td>");
            print("</tr>");
            ?>
        </table>
    <?php
} ?>
</body>
</html>
