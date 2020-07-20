<!DOCTYPE HTML>
<html>
<head>
	<title>ERAMEDIKA - Laporan Kunjungan Pasien</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
	<script type="text/javascript">
		$(document).ready(function() {
            $("#StartDate").customDatePicker({ showOn: "focus" });
            $("#EndDate").customDatePicker({ showOn: "focus" });
		});
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
            <th colspan="11">LAPORAN KUNJUNGAN PASIEN</th>
        </tr>
        <tr>
            <th>Jenis Laporan</th>
            <th>Jenis Pasien</th>
            <th>Jenis Rawat</th>
            <th>Poliklinik</th>
            <th>Kamar Rawat</th>
            <th>Dokter</th>
            <th>Status</th>
            <th>Dari Tanggal</th>
            <th>s/d Tanggal</th>
            <th>Output</th>
            <th>Action</th>
        </tr>
        <tr>
            <td><select name="JnsLaporan" id="JnsLaporan">
                    <option value="1" <?php print($JnsLaporan == 1 ? 'selected="selected"' : '');?>> 1 - Detail </option>
                    <option value="2" <?php print($JnsLaporan == 2 ? 'selected="selected"' : '');?>> 2 - Rekapitulasi </option>
                    <option value="3" <?php print($JnsLaporan == 3 ? 'selected="selected"' : '');?>> 3 - Laporan QI-9 (BPJS)</option>
                </select>
            </td>
            <td><select name="JnsPasien" id="JnsPasien">
                    <option value="0" <?php print($JnsPasien == 2 ? 'selected="selected"' : '');?>> 0 - Semua </option>
                    <option value="1" <?php print($JnsPasien == 1 ? 'selected="selected"' : '');?>> 1 - Umum </option>
                    <option value="2" <?php print($JnsPasien == 2 ? 'selected="selected"' : '');?>> 2 - BPJS </option>
                    <option value="3" <?php print($JnsPasien == 3 ? 'selected="selected"' : '');?>> 3 - Asuransi/Lain </option>
                </select>
            </td>
            <td><select id="JnsRawat" name="JnsRawat">
                    <option value="0" <?php print($JnsRawat == 0 ? 'selected="selected"' : '');?>> 0 - Semua </option>
                    <option value="1" <?php print($JnsRawat == 1 ? 'selected="selected"' : '');?>> 1 - Poliklinik </option>
                    <option value="2" <?php print($JnsRawat == 2 ? 'selected="selected"' : '');?>> 2 - I G D </option>
                    <option value="3" <?php print($JnsRawat == 3 ? 'selected="selected"' : '');?>> 3 - Rawat (Kelas 3) </option>
                    <option value="4" <?php print($JnsRawat == 4 ? 'selected="selected"' : '');?>> 4 - Rawat (Kelas 2) </option>
                </select>
            </td>
            <td><select id="KdPoli" name="KdPoli">
                    <option value="" <?php print($KdPoli == Null ? 'selected="selected"' : '');?>> - Semua Poli - </option>
                    <?php
                    /** @var $poli Poliklinik[] */
                    foreach ($poli as $polis){
                        if ($KdPoli == $polis->KdPoliklinik){
                            printf("<option value='%s' selected='selected'> %s - %s </option>",$polis->KdPoliklinik,$polis->KdPoliklinik,$polis->NmPoliklinik);
                        }else{
                            printf("<option value='%s'> %s - %s </option>",$polis->KdPoliklinik,$polis->KdPoliklinik,$polis->NmPoliklinik);
                        }
                    }
                    ?>
                </select>
            </td>
            <td><select id="KdKamar" name="KdKamar">
                    <option value="" <?php print($KdKamar == Null ? 'selected="selected"' : '');?>> - Semua Kamar - </option>
                    <?php
                    /** @var $kamar Kamar[] */
                    foreach ($kamar as $kamars){
                        if ($KdKamar == $kamars->KdKamar){
                            printf("<option value='%s' selected='selected'> %s - %s </option>",$kamars->KdKamar,$kamars->KdKamar,$kamars->NmKamar);
                        }else{
                            printf("<option value='%s'> %s - %s </option>",$kamars->KdKamar,$kamars->KdKamar,$kamars->NmKamar);
                        }
                    }
                    ?>
                </select>
            </td>
            <td><select id="StsKeluar" name="StsKeluar">
                    <option value="-1" <?php print($StsKeluar == -1 ? 'selected="selected"' : '');?>> Keseluruhan </option>
                    <option value="0" <?php print($StsKeluar == 0 ? 'selected="selected"' : '');?>> Masih Dirawat </option>
                    <option value="1" <?php print($StsKeluar == 1 ? 'selected="selected"' : '');?>> Sudah Pulang </option>
                </select>
            </td>
            <td><input type="text" class="text2" maxlength="10" size="10" id="StartDate" name="StartDate" value="<?php printf(date('d-m-Y',$StartDate));?>"/></td>
            <td><input type="text" class="text2" maxlength="10" size="10" id="EndDate" name="EndDate" value="<?php printf(date('d-m-Y',$EndDate));?>"/></td>
            <td><select name="Output" class="text2" id="Output">
                    <option value="0" <?php print($Output == 0 ? 'selected="selected"' : '');?>> 0 - HTML </option>
                    <option value="1" <?php print($Output == 1 ? 'selected="selected"' : '');?>> 1 - Excel </option>
                </select>
            </td>
            <td align="center"><button type="submit" formaction="<?php print($helper->site_url("pelayanan/report/kunjungan")); ?>"><b>Proses</b></button></td>
        </tr>
    </table>
</form>
<!-- start web report -->
<?php  if ($Reports != null){
    if ($JnsLaporan == 1) {
        print("<h3>LAPORAN KUNJUNGAN PASIEN</h3>");
        printf("Dari Tgl. %s - %s", date('d-m-Y', $StartDate), date('d-m-Y', $EndDate)); ?>
        <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
            <tr>
                <th>No.</th>
                <th>No. RM</th>
                <th>No. Registrasi</th>
                <th>Nama Pasien</th>
                <th>L/P</th>
                <th>Umur</th>
                <th>Jns Pasien</th>
                <th>Kamar Rawat</th>
                <th>Tgl Masuk</th>
                <th>Tgl Keluar</th>
                <th>Lama Rawat</th>
                <th>Status</th>
            </tr>
            <?php
            $nmr = 1;
            $jpasien = null;
            $skeluar = null;
            while ($row = $Reports->FetchAssoc()) {
                if ($row["cara_bayar"] == 1){
                    $jpasien = 'Umum';
                }elseif ($row["cara_bayar"] == 2){
                    $jpasien = 'BPJS';
                }else{
                    $jpasien = 'Lain';
                }
                if ($row["sts_keluar"] == 1){
                    $skeluar = 'Pulang';
                }elseif ($row["sts_keluar"] == 2) {
                    $skeluar = 'Dirujuk';
                }elseif ($row["sts_keluar"] == 3) {
                    $skeluar = 'Meninggal';
                }elseif ($row["sts_keluar"] == 4) {
                    $skeluar = 'Piutang';
                }else{
                    $skeluar = 'Masih Dirawat';
                }
                print("<tr valign='Top'>");
                printf("<td>%s</td>", $nmr);
                printf("<td>%s</td>", $row["no_rm"]);
                printf("<td>%s</td>", $row["no_reg"]);
                printf("<td>%s</td>", $row["nm_pasien"]);
                printf("<td>%s</td>", $row["jkelamin"]);
                printf("<td>%s</td>", $row["umur_tahun_pasien"]);
                printf("<td>%s</td>", $jpasien);
                printf("<td>%s</td>", $row["kmr_rawat"]);
                printf("<td>%s</td>", $row["tgl_masuk"]);
                printf("<td>%s</td>", $row["tgl_keluar"]);
                printf("<td align='right'>%s</td>", $row["lama_rawat"].' hari');
                printf("<td>%s</td>", $skeluar);
                print("</tr>");
                $nmr++;
            }
            ?>
        </table>
        <?php
    }elseif ($JnsLaporan == 2){
        print("<h3>REKAPITULASI KUNJUNGAN PASIEN</h3>");
        printf("Dari Tgl. %s - %s",date('d-m-Y',$StartDate),date('d-m-Y',$EndDate));?>
        <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
            <tr>
                <th>No.</th>
                <th>Jns Pasien</th>
                <th>Kamar Rawat</th>
                <th>L/P</th>
                <th>Jumlah</th>
            </tr>
            <?php
            $nmr = 1;
            $tqty = 0;
            $jpasien = null;
            $skeluar = null;
            while ($row = $Reports->FetchAssoc()) {
                if ($row["cara_bayar"] == 1){
                    $jpasien = 'Umum';
                }elseif ($row["cara_bayar"] == 2){
                    $jpasien = 'BPJS';
                }else{
                    $jpasien = 'Lain';
                }
                print("<tr valign='Top'>");
                printf("<td>%s</td>",$nmr);
                printf("<td>%s</td>", $jpasien);
                printf("<td>%s</td>",$row["kmr_rawat"]);
                printf("<td>%s</td>",$row["jkelamin"]);
                printf("<td align='right'>%s</td>",number_format($row["qty"],0));
                print("</tr>");
                $nmr++;
                $tqty+= $row["qty"];
            }
            print("<tr>");
            print("<td colspan='4' align='right'>Total..</td>");
            printf("<td align='right'>%s</td>",number_format($tqty,0));
            print("</tr>");
            ?>
        </table>
<?php
    }
} ?>
</body>
</html>
