<!DOCTYPE HTML>
<html>
<head>
	<title>ERAMEDIKA - Laporan Fee Dokter Jaga</title>
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
            <th colspan="6">LAPORAN DOKTER JAGA</th>
        </tr>
        <tr>
            <th>Jenis Laporan</th>
            <th>Nama Dokter</th>
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
            <td><select name="KdDokter" id="KdDokter">
                    <option value=""> Keseluruhan </option>
                    <?php
                    /** @var $Dokters Dokter[] */
                    foreach ($Dokters as $dokter){
                        if ($KdDokter == $dokter->KdDokter){
                            printf('<option value="%s" selected="selected"> %s - %s </option>',$dokter->KdDokter,$dokter->KdDokter,$dokter->NmDokter);
                        }else{
                            printf('<option value="%s"> %s - %s </option>',$dokter->KdDokter,$dokter->KdDokter,$dokter->NmDokter);
                        }
                    }
                    ?>
                </select>
            </td>
            <td><input type="text" class="text2" maxlength="10" size="10" id="StartDate" name="StartDate" value="<?php printf(date('d-m-Y',$StartDate));?>"/></td>
            <td><input type="text" class="text2" maxlength="10" size="10" id="EndDate" name="EndDate" value="<?php printf(date('d-m-Y',$EndDate));?>"/></td>
            <td><select name="Output" class="text2" id="Output">
                    <option value="0" <?php print($Output == 0 ? 'selected="selected"' : '');?>> 0 - HTML </option>
                    <option value="1" <?php print($Output == 1 ? 'selected="selected"' : '');?>> 1 - Excel </option>
                </select>
            </td>
            <td align="center"><button type="submit" formaction="<?php print($helper->site_url("pelayanan/report/dokterjaga")); ?>"><b>Proses</b></button></td>
        </tr>
    </table>
</form>
<!-- start web report -->
<?php  if ($Reports != null){
    if ($JnsLaporan == 1) {
        print("<h3>Rekapitulasi Fee Dokter Jaga</h3>");
        printf("Dari Tgl. %s - %s", date('d-m-Y', $StartDate), date('d-m-Y', $EndDate)); ?>
        <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
            <tr>
                <th>No.</th>
                <th>Kode</th>
                <th>Nama Dokter</th>
                <th>Hari</th>
                <th>Fee</th>
            </tr>
            <?php
            $nmr = 1;
            $tjumlah = 0;
            $tqty = 0;
            while ($row = $Reports->FetchAssoc()) {
                print("<tr valign='Top'>");
                printf("<td>%s</td>", $nmr);
                printf("<td>%s</td>", $row["kd_dokter"]);
                printf("<td>%s</td>", $row["nm_dokter"]);
                printf("<td align='right'>%s</td>", number_format($row["hari"], 0));
                printf("<td align='right'>%s</td>", number_format($row["fee_dokter"], 0));
                print("</tr>");
                $nmr++;
                $tqty += $row['hari'];
                $tjumlah += $row["fee_dokter"];
            }
            print("<tr>");
            print("<td colspan='4' align='right'>Total..</td>");
            printf("<td align='right'>%s</td>", number_format($tjumlah, 0));
            print("</tr>");
            ?>
        </table>
        <?php
    }else{
        print("<h3>Laporan Fee Dokter Jaga</h3>");
        printf("Dari Tgl. %s - %s",date('d-m-Y',$StartDate),date('d-m-Y',$EndDate));?>
        <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
            <tr>
                <th>No.</th>
                <th>Tanggal</th>
                <th>Kode</th>
                <th>Nama Dokter</th>
                <th>Hari</th>
                <th>Fee</th>
            </tr>
            <?php
            $nmr = 1;
            $tjumlah = 0;
            $tqty = 0;
            while ($row = $Reports->FetchAssoc()) {
                print("<tr valign='Top'>");
                printf("<td>%s</td>",$nmr);
                printf("<td>%s</td>", $row["tanggal"]);
                printf("<td>%s</td>",$row["kd_dokter"]);
                printf("<td>%s</td>",$row["nm_dokter"]);
                printf("<td align='right'>%s</td>",number_format($row["qty"],0));
                printf("<td align='right'>%s</td>",number_format($row["fee_dokter_jaga"],0));
                print("</tr>");
                $nmr++;
                $tjumlah+= $row["fee_dokter_jaga"];
                $tqty+= $row["qty"];
            }
            print("<tr>");
            print("<td colspan='4' align='right'>Total..</td>");
            printf("<td align='right'>%s</td>",number_format($tqty,0));
            printf("<td align='right'>%s</td>",number_format($tjumlah,0));
            print("</tr>");
            ?>
        </table>
<?php
    }
} ?>
</body>
</html>
