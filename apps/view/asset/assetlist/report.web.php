<!DOCTYPE HTML>
<html>
<head>
	<title>ERAMEDIKA - Laporan Asset</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
	<script type="text/javascript">
		$(document).ready(function() {

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
            <th colspan="6">LAPORAN ASSET & PENYUSUTAN</th>
        </tr>
        <tr>
            <th>Jenis Asset</th>
            <th>Tahun</th>
            <th>Bulan</th>
            <th>Output</th>
            <th>Action</th>
        </tr>
        <tr>
            <td><select name="KdKlpAsset" id="KdKlpAsset">
                    <option value="0"> A0000 - Keseluruhan </option>
                    <?php
                    /** @var $KlpAsset KlpAsset[] */
                    foreach ($KlpAsset as $klp){
                        if ($klp->KdKlpAsset == $Kasset){
                            printf('<option value="%s" selected="selected"> %s - %s </option>',$klp->KdKlpAsset,$klp->KdKlpAsset,$klp->KlpAsset);
                        }else{
                            printf('<option value="%s"> %s - %s </option>',$klp->KdKlpAsset,$klp->KdKlpAsset,$klp->KlpAsset);
                        }
                    }
                    ?>
                </select>
            </td>
            <td><select name="Tahun" id="Tahun">
                    <?php
                    foreach ($Years as $thn){
                        if ($thn == $Tahun){
                            printf('<option value="%d" selected="selected"> %d </option>',$thn,$thn);
                        }else{
                            printf('<option value="%d"> %d </option>',$thn,$thn);
                        }
                    }
                    ?>
                </select>
            </td>
            <td><select id="Bulan" name="Bulan">
                    <option value="01" <?php print($Bulan == 1 ? 'selected="selected"' : '');?>>1 - Januari</option>
                    <option value="02" <?php print($Bulan == 2 ? 'selected="selected"' : '');?>>2 - Februari</option>
                    <option value="03" <?php print($Bulan == 3 ? 'selected="selected"' : '');?>>3 - Maret</option>
                    <option value="04" <?php print($Bulan == 4 ? 'selected="selected"' : '');?>>4 - April</option>
                    <option value="05" <?php print($Bulan == 5 ? 'selected="selected"' : '');?>>5 - Mei</option>
                    <option value="06" <?php print($Bulan == 6 ? 'selected="selected"' : '');?>>6 - Juni</option>
                    <option value="07" <?php print($Bulan == 7 ? 'selected="selected"' : '');?>>7 - Juli</option>
                    <option value="08" <?php print($Bulan == 8 ? 'selected="selected"' : '');?>>8 - Agustus</option>
                    <option value="09" <?php print($Bulan == 9 ? 'selected="selected"' : '');?>>9 - September</option>
                    <option value="10" <?php print($Bulan == 10 ? 'selected="selected"' : '');?>>10- Oktober</option>
                    <option value="11" <?php print($Bulan == 11 ? 'selected="selected"' : '');?>>11- Nopember</option>
                    <option value="12" <?php print($Bulan == 12 ? 'selected="selected"' : '');?>>12- Desember</option>
                </select>
            </td>
            <td><select name="Output" class="text2" id="Output">
                    <option value="0" <?php print($Output == 0 ? 'selected="selected"' : '');?>> 0 - HTML </option>
                    <option value="1" <?php print($Output == 1 ? 'selected="selected"' : '');?>> 1 - Excel </option>
                </select>
            </td>
            <td align="center"><button type="submit" formaction="<?php print($helper->site_url("asset/assetlist/report")); ?>"><b>Proses</b></button>
                               <a class="button" href="<?php print($helper->site_url("asset/assetlist")); ?>"> Daftar Asset </a>
            </td>
        </tr>
    </table>
</form>
<!-- start web report -->
<?php  if ($Reports != null){
        print("<h3>DAFTAR ASSET</h3>");
        printf("Sampai Periode: %s %s", get_bulan($Bulan),$Tahun); ?>
        <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
            <tr valign="middle">
                <th rowspan="2">No.</th>
                <th rowspan="2">Kelompok Asset</th>
                <th rowspan="2">Kode</th>
                <th rowspan="2">Nama Asset</th>
                <th rowspan="2">Tahun<br>Perolehan</th>
                <th rowspan="2">Masa<br>Manfaat (Thn)</th>
                <th rowspan="2">QTY</th>
                <th rowspan="2">Nilai Buku<br>Awal Tahun</th>
                <th rowspan="2">Sub Total</th>
                <th colspan="3">Nilai Penyusutan</th>
                <th rowspan="2">Nilai Buku<br> S/D Periode ini</th>
            </tr>
            <tr>
                <th>%/Thn</th>
                <th>p/Bulan</th>
                <th>S/D Periode ini</th>
            </tr>
            <?php
            $nmr = 1;
            $njumlah = 0;
            $pbulan = 0;
            $ptotal = 0;
            $nbuku = 0;
            $pdiff = 0;
            $speriod = null;
            $eperiod = new DateTime(left($Periode,4).'-'.right($Periode,2).'-01');
            $kdklp = null;
            $sqty = 0;
            $sjumlah = 0;
            $sptotal = 0;
            $snbuku = 0;
            $tqty = 0;
            $tjumlah = 0;
            $tptotal = 0;
            $tnbuku = 0;
            while ($data = $Reports->FetchAssoc()) {
                if ($nmr == 1){
                    print("<tr valign='Top'>");
                    printf("<td>%s</td>", $nmr);
                    printf("<td class='bold'>%s - %s</td>", $data["kd_klpasset"], $data["klp_asset"]);
                }else{
                    if ($kdklp == $data["kd_klpasset"]) {
                        print("<tr valign='Top'>");
                        printf("<td>%s</td>", $nmr);
                        printf("<td>&nbsp;</td>");
                    }else{
                        print('<tr>');
                        print('<td colspan="6" class="bold" align="right">Sub Total ...</td>');
                        printf("<td class='bold' align='right'>%s</td>", number_format($sqty, 0));
                        print('<td>&nbsp;</td>');
                        printf("<td class='bold' align='right'>%s</td>", number_format($sjumlah, 0));
                        print('<td>&nbsp;</td>');
                        print('<td>&nbsp;</td>');
                        printf("<td class='bold' align='right'>%s</td>", number_format($sptotal, 0));
                        printf("<td class='bold' align='right'>%s</td>", number_format($snbuku, 0));
                        print('</tr>');
                        print('<tr>');
                        print('<td colspan="13">&nbsp;</td>');
                        print('</tr>');
                        $nmr = 1;
                        $sqty = 0;
                        $sjumlah = 0;
                        $sptotal = 0;
                        $snbuku = 0;
                        print("<tr valign='Top'>");
                        printf("<td>%s</td>", $nmr);
                        printf("<td class='bold'>%s - %s</td>", $data["kd_klpasset"], $data["klp_asset"]);
                    }
                }
                $speriod = new DateTime(left($data['last_depr'],4).'-'.right($data['last_depr'],2).'-01');
                $pdiff = $eperiod->diff($speriod);
                $pdiff = (($pdiff->format('%y') * 12) + $pdiff->format('%m'))+1;
                $njumlah = $data["qty"] * $data["nilai_buku"];
                $pbulan = round((($njumlah * $data["depr_year"])/100)/12,0);
                $ptotal = $pdiff * $pbulan;
                $nbuku = $njumlah - $ptotal;
                if ($nbuku < 0){
                    $nbuku = 0;
                }
                printf("<td>%s</td>", $data["kd_asset"]);
                printf("<td>%s</td>", $data["nm_asset"]);
                printf("<td align='center'>%s</td>", $data["thn_perolehan"]);
                printf("<td align='center'>%s</td>", $data["masa_manfaat"]);
                printf("<td align='right'>%s</td>", number_format($data["qty"], 0));
                printf("<td align='right'>%s</td>", number_format($data["nilai_buku"], 0));
                printf("<td align='right'>%s</td>", number_format($njumlah, 0));
                printf("<td align='right'>%s</td>", $data["depr_year"].' %');
                printf("<td align='right'>%s</td>", number_format($pbulan,0));
                printf("<td align='right'>%s</td>", number_format($ptotal,0));
                printf("<td align='right'>%s</td>", number_format($nbuku,0));
                print("</tr>");
                $kdklp = $data["kd_klpasset"];
                $nmr++;
                $sqty += $data["qty"];
                $sjumlah += $data["qty"] * $data["nilai_buku"];
                $sptotal += $ptotal;
                $snbuku += $nbuku;
                $tjumlah += $data["qty"] * $data["nilai_buku"];
                $tqty += $data["qty"];
                $tptotal += $ptotal;
                $tnbuku += $nbuku;
            }
            print('<tr>');
            print('<td colspan="6" class="bold" align="right">Sub Total ...</td>');
            printf("<td class='bold' align='right'>%s</td>", number_format($sqty, 0));
            print('<td>&nbsp;</td>');
            printf("<td class='bold' align='right'>%s</td>", number_format($sjumlah, 0));
            print('<td>&nbsp;</td>');
            print('<td>&nbsp;</td>');
            printf("<td class='bold' align='right'>%s</td>", number_format($sptotal, 0));
            printf("<td class='bold' align='right'>%s</td>", number_format($snbuku, 0));
            print('</tr>');
            print('<tr>');
            print('<td colspan="13">&nbsp;</td>');
            print('</tr>');
            print('<tr>');
            print('<td colspan="6" class="bold" align="right">Grand Total ...</td>');
            printf("<td class='bold' align='right'>%s</td>", number_format($tqty, 0));
            print('<td>&nbsp;</td>');
            printf("<td class='bold' align='right'>%s</td>", number_format($tjumlah, 0));
            print('<td>&nbsp;</td>');
            print('<td>&nbsp;</td>');
            printf("<td class='bold' align='right'>%s</td>", number_format($tptotal, 0));
            printf("<td class='bold' align='right'>%s</td>", number_format($tnbuku, 0));
            print('</tr>');
            ?>
        </table>
<?php
} ?>
</body>
</html>
