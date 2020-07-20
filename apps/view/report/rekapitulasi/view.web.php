<?php require_once(LIBRARY . "gen_functions.php");?>
<!DOCTYPE HTML>
<html>
<head>
	<title>ERAMEDIKA - Rekap Bulanan</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
	<script type="text/javascript">
		$(document).ready(function() {
            //$("#StartDate").customDatePicker({ showOn: "focus" });
           // $("#EndDate").customDatePicker({ showOn: "focus" });
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
            <th colspan="8">REKAPITULASI BULANAN</th>
        </tr>
        <tr>
            <th>Jenis Laporan</th>
            <th>Jenis Pasien</th>
            <th>Unit Rawat</th>
            <th>Jenis Jasa/Layanan</th>
            <th>Bulan</th>
            <th>Tahun</th>
            <th>Output</th>
            <th>Action</th>
        </tr>
        <tr>
            <td><select name="JnsLaporan" id="JnsLaporan">
                    <option value="1" <?php print($JnsLaporan == 1 ? 'selected="selected"' : '');?>> 1 - Detail </option>
                    <option value="2" <?php print($JnsLaporan == 2 ? 'selected="selected"' : '');?>> 2 - Rekapitulasi </option>
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
                    <option value="5" <?php print($JnsRawat == 5 ? 'selected="selected"' : '');?>> 5 - Rawat (Kelas 1) </option>
                </select>
            </td>
            <td><select id="KdJnsJasa" name="KdJnsJasa">
                    <option value=""> Semua Layanan </option>
                    <?php
                    /** @var $JnsJasa Klpjasa[] */
                    foreach ($JnsJasa as $jasa){
                        if ($jasa->KdKlpJasa == $KdJnsJasa) {
                            printf('<option value="%s" selected="selected">%s</option>', $jasa->KdKlpJasa, $jasa->KlpJasa);
                        }else{
                            printf('<option value="%s">%s</option>',$jasa->KdKlpJasa,$jasa->KlpJasa);
                        }
                    }
                    ?>
                </select>
            </td>
            <td><select id="Bulan" name="Bulan">
                    <option value="0" <?php print($Bulan == 0 ? 'selected="selected"' : '');?>></option>
                    <option value="1" <?php print($Bulan == 1 ? 'selected="selected"' : '');?>> 1 - Januari </option>
                    <option value="2" <?php print($Bulan == 2 ? 'selected="selected"' : '');?>> 2 - Februari </option>
                    <option value="3" <?php print($Bulan == 3 ? 'selected="selected"' : '');?>> 3 - Maret </option>
                    <option value="4" <?php print($Bulan == 4 ? 'selected="selected"' : '');?>> 4 - April </option>
                    <option value="5" <?php print($Bulan == 5 ? 'selected="selected"' : '');?>> 5 - Mei </option>
                    <option value="6" <?php print($Bulan == 6 ? 'selected="selected"' : '');?>> 6 - Juni </option>
                    <option value="7" <?php print($Bulan == 7 ? 'selected="selected"' : '');?>> 7 - Juli </option>
                    <option value="8" <?php print($Bulan == 8 ? 'selected="selected"' : '');?>> 8 - Agustus </option>
                    <option value="9" <?php print($Bulan == 9 ? 'selected="selected"' : '');?>> 9 - September </option>
                    <option value="10" <?php print($Bulan == 10 ? 'selected="selected"' : '');?>> 10 - Oktober </option>
                    <option value="11" <?php print($Bulan == 11 ? 'selected="selected"' : '');?>> 11 - Nopember </option>
                    <option value="12" <?php print($Bulan == 12 ? 'selected="selected"' : '');?>> 12 - Desember </option>
                </select>
            </td>
            <td><select id="Tahun" name="Tahun">
                    <option value="2018" <?php print($Tahun == 2018 ? 'selected="selected"' : '');?>> 2018 </option>
                    <option value="2019" <?php print($Tahun == 2019 ? 'selected="selected"' : '');?>> 2019 </option>
                    <option value="2020" <?php print($Tahun == 2020 ? 'selected="selected"' : '');?>> 2020 </option>
                    <option value="2021" <?php print($Tahun == 2021 ? 'selected="selected"' : '');?>> 2021 </option>
                    <option value="2022" <?php print($Tahun == 2022 ? 'selected="selected"' : '');?>> 2022 </option>
                    <option value="2023" <?php print($Tahun == 2023 ? 'selected="selected"' : '');?>> 2023 </option>
                </select>
            </td>
            <td><select name="Output" class="text2" id="Output">
                    <option value="0" <?php print($Output == 0 ? 'selected="selected"' : '');?>> 0 - HTML </option>
                    <option value="1" <?php print($Output == 1 ? 'selected="selected"' : '');?>> 1 - Excel </option>
                </select>
            </td>
            <td align="center"><button type="submit" formaction="<?php print($helper->site_url("report/rekapitulasi/view")); ?>"><b>Proses</b></button></td>
        </tr>
    </table>
</form>
<!-- start web report -->
<?php  if ($Reports != null){
    $unit = null;
    switch($JnsRawat){
        case 1:
            $unit = 'Poliklinik';
            break;
        case 2:
            $unit = 'I G D';
            break;
        case 3:
            $unit = 'Ranap Kls III';
            break;
        case 4:
            $unit = 'Ranap Kls II';
            break;
        case 5:
            $unit = 'Ranap Kls I';
            break;
        default:
            $unit = "Keseluruhan";
    }

    if ($JnsLaporan == 1) {
        printf("<h2>REKAP PER BULAN DETAIL (%s)</h2>",strtoupper($unit));
        printf("<h3>BULAN: %s %d </h3>", strtoupper(get_bulan($Bulan)), $Tahun); ?>
        <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
            <tr>
                <th>No.</th>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Pelayanan</th>
                <th>Uraian</th>
                <th>Dokter/Petugas</th>
                <th>Unit Rawat</th>
                <th>Cara Bayar</th>
                <th>Jasa</th>
                <th>QTY</th>
                <th>Jumlah</th>
            </tr>
            <?php
            $nmr = 1;
            $jpasien = null;
            $skeluar = null;
            $tgl = null;
            $total = 0;
            while ($row = $Reports->FetchAssoc()) {
                print("<tr valign='Top'>");
                printf("<td>%s</td>", $nmr);
                if ($tgl == $row["tgl_layanan"]){
                    print('<td>&nbsp;</td>');
                }else {
                    printf("<td>%s</td>", $row["tgl_layanan"]);
                }
                printf("<td>%s</td>", $row["klp_jasa"]);
                printf("<td>%s</td>", $row["nm_jasa"]);
                printf("<td>%s</td>", $row["uraian_jasa"]);
                if ($row["is_feedokter"] == 1) {
                    printf("<td>%s</td>", $row["nm_dokter"]);
                }else{
                    print('<td>&nbsp;</td>');
                }
                printf("<td>%s</td>", $row["jns_rawat_desc"]);
                if ($row["cara_bayar"] == 1){
                    print('<td>Umum</td>');
                }else{
                    print('<td>BPJS</td>');
                }
                printf("<td align='right'>%s</td>", number_format($row["tarif_harga"]));
                printf("<td align='right'>%s</td>", $row["qty"]);
                printf("<td align='right'>%s</td>", number_format($row["tarif_harga"] * $row["qty"]));
                print("</tr>");
                $nmr++;
                $tgl = $row["tgl_layanan"];
                $total+= $row["tarif_harga"] * $row["qty"];
            }
            print('<tr>');
            print('<td align="right" colspan="10">T o t a l . .</td>');
            printf('<td align="right"><b>%s</b></td>',number_format($total));
            print('</tr>');
            ?>
        </table>
        <?php
    }elseif ($JnsLaporan == 2){
        printf("<h2>REKAPITULASI PER BULAN (%s)</h2>",strtoupper($unit));
        printf("<h3>BULAN: %s %d </h3>", strtoupper(get_bulan($Bulan)), $Tahun); ?>
        <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
            <tr>
                <th>No.</th>
                <th>Jenis Pelayanan</th>
                <th>Uraian</th>
                <th>Jasa</th>
                <th>QTY</th>
                <th>Jumlah</th>
            </tr>
            <?php
            $nmr = 0;
            $total = 0;
            $kjasa = null;
            $njasa = null;
            $ifdok = false;
            while ($row = $Reports->FetchAssoc())
            {
                if ($kjasa != $row["kd_klpjasa"]){
                    $nmr++;
                    print("<tr valign='Top'>");
                    printf("<td>%s</td>",$nmr);
                    printf("<td>%s</td>",$row["klp_jasa"]);
                    print("<td>&nbsp;</td>");
                    print("<td>&nbsp;</td>");
                    print("<td>&nbsp;</td>");
                    print("<td>&nbsp;</td>");
                    print('</tr>');
                }
                print("<tr valign='Top'>");
                print("<td>&nbsp;</td>");
                if ($njasa != $row["nm_jasa"]) {
                    printf("<td>%s</td>", $row["nm_jasa"]);
                }else{
                    print("<td>&nbsp;</td>");
                }
                if ($row["is_feedokter"] == 1) {
                    printf("<td>%s</td>", $row["nm_dokter"]);
                } else {
                    printf("<td>%s</td>", $row["uraian_jasa"]);
                }
                printf("<td align='right'>%s</td>", number_format($row["tarif_harga"], 0));
                printf("<td align='right'>%s</td>", number_format($row["sum_qty"], 0));
                printf("<td align='right'>%s</td>", number_format($row["tarif_harga"] * $row["sum_qty"], 0));
                print("</tr>");
                $total += $row["tarif_harga"] * $row["sum_qty"];
                $kjasa = $row["kd_klpjasa"];
                $njasa = $row["nm_jasa"];
            }
            print("<tr>");
            print("<td colspan='5' align='right'>Total..</td>");
            printf("<td align='right'>%s</td>",number_format($total,0));
            print("</tr>");
            ?>
        </table>
<?php
    }
} ?>
</body>
</html>
