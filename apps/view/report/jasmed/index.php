<!DOCTYPE HTML>
<html>
<head>
	<title>ERAMEDIKA - Laporan Fee Jasa Medis</title>
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
            <th colspan="5">LAPORAN FEE JASA MEDIS</th>
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
            <td align="center">
                <button type="submit" formaction="<?php print($helper->site_url("report/jasmed")); ?>"><b>Preview</b></button>
                <input type="button" class="button bold" onclick="printDiv('divReport')" value="Print" />
            </td>
        </tr>
    </table>
</form>
<!-- start web report -->
<div id="divReport">
<?php  if ($reports != null){
    if ($JnsLaporan == 1) {
        print("<h3>Rekapitulasi Fee Jasa Medis</h3>");
        printf("Dari Tgl. %s - %s", date('d-m-Y', $StartDate), date('d-m-Y', $EndDate)); ?>
        <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
            <tr>
                <th>No.</th>
                <th>Jenis Pasien</th>
                <th>Uraian Jasa/Pelayanan</th>
                <th>Nama Dokter</th>
                <th>Qty</th>
                <th>Jasa Visit</th>
            </tr>
            <?php
            $nmr = 1;
            $tjumlah = 0;
            while ($row = $reports->FetchAssoc()) {
                print("<tr valign='Top'>");
                printf("<td>%s</td>", $nmr);
                printf("<td>%s</td>", $row["cara_bayar_desc"]);
                printf("<td>%s</td>", $row["nm_jasa"]);
                printf("<td>%s</td>", $row["nm_dokter"]);
                printf("<td align='right'>%s</td>", number_format($row["jum_visit"], 0));
                printf("<td align='right'>%s</td>", number_format($row["jumlah"], 0));
                print("</tr>");
                $nmr++;
                $tjumlah += $row["jumlah"];
            }
            print("<tr>");
            print("<td colspan='5' align='right'>Total..</td>");
            printf("<td align='right'>%s</td>", number_format($tjumlah, 0));
            print("</tr>");
            ?>
        </table>
        <?php
    }else{
        print("<h3>Laporan Fee Jasa Medis</h3>");
        printf("Dari Tgl. %s - %s",date('d-m-Y',$StartDate),date('d-m-Y',$EndDate));?>
        <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
            <tr>
                <th>No.</th>
                <th>Tanggal</th>
                <th>No. Reg</th>
                <th>Jenis Rawat</th>
                <th>Jenis Pasien</th>
                <th>Jasa/Tindakan/Layanan</th>
                <th>Nama Dokter</th>
                <th>QTY</th>
                <th>Jasa Visit</th>
            </tr>
            <?php
            $nmr = 1;
            $tjumlah = 0;
            $tqty = 0;
            $tgl = null;
            $nrg = null;
            while ($row = $reports->FetchAssoc()) {
                print("<tr valign='Top'>");
                printf("<td>%s</td>",$nmr);
                if ($tgl == $row["tgl_layanan"]){
                    print("<td>&nbsp;</td>");
                }else {
                    printf("<td>%s</td>", $row["tgl_layanan"]);
                }
                if ($nrg == $row["no_reg"]){
                    print("<td>&nbsp;</td>");
                }else {
                    printf("<td>%s</td>", $row["no_reg"]);
                }
                printf("<td>%s</td>",$row["jns_rawat_desc"]);
                printf("<td>%s</td>",$row["cara_bayar_desc"]);
                printf("<td>%s</td>",$row["nm_jasa"]);
                printf("<td>%s</td>",$row["nm_dokter"]);
                printf("<td align='right'>%s</td>",number_format($row["qty"],0));
                printf("<td align='right'>%s</td>",number_format($row["jasa_visit"],0));
                print("</tr>");
                $nmr++;
                $tjumlah+= $row["jasa_visit"];
                $tqty+= $row["qty"];
                $tgl = $row["tgl_layanan"];
                $nrg = $row["no_reg"];
            }
            print("<tr>");
            print("<td colspan='7' align='right'>Total..</td>");
            printf("<td align='right'>%s</td>",number_format($tqty,0));
            printf("<td align='right'>%s</td>",number_format($tjumlah,0));
            print("</tr>");
            ?>
        </table>
<?php
    }
} ?>
</div>
<script type="text/javascript">
    function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;
    }
</script>
</body>
</html>
