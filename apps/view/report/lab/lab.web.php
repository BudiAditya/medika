<!DOCTYPE HTML>
<html>
<head>
	<title>ERAMEDIKA - Laporan Laboratorium</title>
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
            <th colspan="5">LAPORAN PENDAPATAN LABORATORIUM</th>
        </tr>
        <tr>
            <th>Jenis Laporan</th>
            <th>Dari Tanggal</th>
            <th>s/d Tanggal</th>
            <th>Output</th>
            <th>Action</th>
        </tr>
        <tr>
            <td><select name="JnsLaporan" id="JnsLaporan">
                    <option value="1" <?php print($JnsLaporan == 1 ? 'selected="selected"' : '');?>> 1 - Rekapitulasi </option>
                    <option value="2" <?php print($JnsLaporan == 2 ? 'selected="selected"' : '');?>> 2 - Detail </option>
                </select>
            </td>
            <td><input type="text" class="text2" maxlength="10" size="10" id="StartDate" name="StartDate" value="<?php printf(date('d-m-Y',$StartDate));?>"/></td>
            <td><input type="text" class="text2" maxlength="10" size="10" id="EndDate" name="EndDate" value="<?php printf(date('d-m-Y',$EndDate));?>"/></td>
            <td><select name="Output" class="text2" id="Output">
                    <option value="0" <?php print($Output == 0 ? 'selected="selected"' : '');?>> 0 - HTML </option>
                    <option value="1" <?php print($Output == 1 ? 'selected="selected"' : '');?>> 1 - Excel </option>
                </select>
            </td>
            <td align="center"><button type="submit" formaction="<?php print($helper->site_url("pelayanan.report/lab")); ?>"><b>Proses</b></button></td>
        </tr>
    </table>
</form>
<!-- start web report -->
<?php  if ($reports != null){
    if ($JnsLaporan == 1) {
        print("<h3>Rekapitulasi Pendapatan Laboratorium</h3>");
        printf("Dari Tgl. %s - %s", date('d-m-Y', $StartDate), date('d-m-Y', $EndDate)); ?>
        <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
            <tr>
                <th>No.</th>
                <th>Jenis Pelayanan</th>
                <th>Uraian Jasa/Pelayanan</th>
                <th>Jasa</th>
                <th>QTY</th>
                <th>Jumlah</th>
            </tr>
            <?php
            $nmr = 1;
            $tjumlah = 0;
            $klpjasa = null;
            while ($row = $reports->FetchAssoc()) {
                print("<tr valign='Top'>");
                printf("<td>%s</td>", $nmr);
                if ($klpjasa == $row["klp_jasa"]){
                    print("<td>&nbsp;</td>");
                }else {
                    printf("<td>%s</td>", $row["klp_jasa"]);
                }
                printf("<td>%s</td>", $row["nm_jasa"]);
                printf("<td align='right'>%s</td>", number_format($row["tarif_harga"], 0));
                printf("<td align='right'>%s</td>", number_format($row["qty"], 0));
                printf("<td align='right'>%s</td>", number_format($row["jumlah"], 0));
                print("</tr>");
                $nmr++;
                $tjumlah += $row["jumlah"];
                $klpjasa = $row["klp_jasa"];
            }
            print("<tr>");
            print("<td colspan='5' align='right'>Total..</td>");
            printf("<td align='right'>%s</td>", number_format($tjumlah, 0));
            print("</tr>");
            ?>
        </table>
        <?php
    }else{
        print("<h3>Laporan Pendapatan Laboratorium</h3>");
        printf("Dari Tgl. %s - %s",date('d-m-Y',$StartDate),date('d-m-Y',$EndDate));?>
        <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
            <tr>
                <th>No.</th>
                <th>Tanggal</th>
                <th>Uraian Jasa/Pelayanan</th>
                <th>Jasa</th>
                <th>QTY</th>
                <th>Jumlah</th>
            </tr>
            <?php
            $nmr = 1;
            $tjumlah = 0;
            $tgl = null;
            while ($row = $reports->FetchAssoc()) {
                print("<tr valign='Top'>");
                printf("<td>%s</td>",$nmr);
                if ($tgl == $row["tgl_layanan"]){
                    print("<td>&nbsp;</td>");
                }else {
                    printf("<td>%s</td>", $row["tgl_layanan"]);
                }
                printf("<td>%s</td>",$row["nm_jasa"]);
                printf("<td align='right'>%s</td>",number_format($row["tarif_harga"],0));
                printf("<td align='right'>%s</td>",number_format($row["sum_qty"],0));
                printf("<td align='right'>%s</td>",number_format($row["sum_tarif"],0));
                print("</tr>");
                $nmr++;
                $tjumlah+= $row["sum_tarif"];
                $tgl = $row["tgl_layanan"];
            }
            print("<tr>");
            print("<td colspan='5' align='right'>Total..</td>");
            printf("<td align='right'>%s</td>",number_format($tjumlah,0));
            print("</tr>");
            ?>
        </table>
<?php
    }
} ?>
</body>
</html>
