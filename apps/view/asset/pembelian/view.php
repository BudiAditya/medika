<!DOCTYPE HTML>
<?php /** @var $pembelian Pembelian */ /** @var $klpasset KlpAsset[] */ ?>
<html>
<head>
	<title>ERAMEDIKA - View Data Pembelian Asset</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/autoNumeric.js")); ?>"></script>
</head>
<body>
<?php include(VIEW . "main/menu.php"); ?>
<?php if (isset($error)) { ?>
<div class="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
<div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php } ?>

<br/>
<fieldset>
	<legend><b>View Data Pembelian Asset</b></legend>
    <table cellpadding="2" cellspacing="1">
        <tr>
            <td>Lokasi Asset</td>
            <td><select name="SbuId" class="text2" id="SbuId" style="width: 100px;" disabled>
                    <option value="0"> 0 - Corporate </option>
                    <?php
                    /** @var $sbulist Sbu[]*/
                    foreach ($sbulist as $sbu) {
                        if ($pembelian->SbuId == $sbu->Id) {
                            printf('<option value="%d" selected="selected"> %s - %s </option>', $sbu->Id, $sbu->Id, $sbu->SbuName);
                        } else {
                            printf('<option value="%d"> %s - %s </option>', $sbu->Id, $sbu->Id, $sbu->SbuName);
                        }
                    }
                    ?>
                </select>
            </td>
            <td>Cara Pembelian</td>
            <td><select name="JnsPembelian" id="JnsPembelian" disabled>
                    <option value="1"> 1 - Tunai </option>
                    <option value="2"> 2 - Kredit </option>
                </select>
            </td>
            <td>Via Kas/Bank
                &nbsp;&nbsp;
                <select name="BankId" id="BankId" disabled>
                    <option value=""></option>
                    <?php
                    /** @var $banks Bank[] */
                    foreach ($banks as $kasbank){
                        if ($pembelian->BankId == $kasbank->AccNo) {
                            printf('<option value="%s" selected="selected"> %s </option>', $kasbank->AccNo, $kasbank->Name);
                        }else{
                            printf('<option value="%s"> %s </option>',$kasbank->AccNo,$kasbank->Name);
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Tgl Pembelian</td>
            <td><input type="text" class="text2" name="TglPembelian" id="TglPembelian" maxlength="10" size="15" value="<?php print($pembelian->FormatTglPembelian(JS_DATE)); ?>" disabled/></td>
            <td>No. Bukti</td>
            <td colspan="2"><input type="text" class="text2" name="NoBukti" id="NoBukti" maxlength="15" size="15" value="<?php print($pembelian->NoBukti); ?>" disabled placeholder="AUTO"/></td>
        </tr>
        <tr>
            <td>Nama Supplier</td>
            <td colspan="2"><select name="KdRelasi" class="text2" id="KdRelasi"style="width: 225px" disabled>
                    <option value=""></option>
                    <?php
                    /** @var $relasilist Relasi[] */
                    foreach ($relasilist as $relasi) {
                        if ($pembelian->KdRelasi == $relasi->KdRelasi) {
                            printf('<option value="%s" selected="selected">%s</option>', $relasi->KdRelasi, $relasi->NmRelasi);
                        } else {
                            printf('<option value="%s">%s</option>', $relasi->KdRelasi, $relasi->NmRelasi);
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Kelompok Asset</td>
            <td colspan="2"><select name="KdKlpAsset" class="text2" id="KdKlpAsset"style="width: 225px" disabled>
                    <option value=""></option>
                    <?php
                    foreach ($klpasset as $klp) {
                        if ($pembelian->KdKlpAsset == $klp->KdKlpAsset) {
                            printf('<option value="%s" selected="selected">%s</option>', $klp->KdKlpAsset, $klp->KlpAsset);
                        } else {
                            printf('<option value="%s">%s</option>', $klp->KdKlpAsset, $klp->KlpAsset);
                        }
                    }
                    ?>
                </select>
            </td>
            <td>No. Refferensi</td>
            <td><input type="text" class="text2" name="NoReff" id="NoReff" maxlength="100" size="25" value="<?php print($pembelian->NoReff); ?>" disabled/></td>
        </tr>
        <tr>
            <td>Nama Asset</td>
            <td colspan="2"><input type="text" class="text2" name="NmAsset" id="NmAsset" maxlength="150" size="40" value="<?php print($pembelian->NmAsset); ?>" disabled/></td>
            <td>Kode Asset</td>
            <td><input type="text" class="text2" name="KdAsset" id="KdAsset" maxlength="15" size="15" value="<?php print($pembelian->KdAsset); ?>" disabled placeholder="AUTO"/></td>
        </tr>
        <tr>
            <td>QTY</td>
            <td colspan="3">
                <input type="text" class="right numeric" name="Qty" id="Qty" maxlength="3" size="3" value="<?php print($pembelian->Qty); ?>" disabled/>
                &nbsp;&nbsp;Harga
                <input type="text" class="right numeric" name="Harga" id="Harga" maxlength="50" size="12" value="<?php print($pembelian->Harga); ?>" disabled/>
                &nbsp;&nbsp;Jumlah
                <input type="text" class="right numeric" name="Jumlah" id="Jumlah" maxlength="50" size="15" value="<?php print($pembelian->Jumlah); ?>" disabled/>
            </td>
        </tr>
        <tr>
            <td>Masa Manfaat</td>
            <td><input type="text" class="right" name="MasaManfaat" id="MasaManfaat" maxlength="3" size="10" value="<?php print($pembelian->MasaManfaat); ?>" disabled/> Tahun</td>
        </tr>
        <tr>
            <td>Depresiasi</td>
            <td><input type="text" class="right" name="DeprYear" id="DeprYear" maxlength="3" size="10" value="<?php print($pembelian->DeprYear); ?>" disabled/> % Per Tahun</td>
        </tr>
        <tr>
            <td>Apresiasi</td>
            <td><input type="text" class="right" name="ApreYear" id="ApreYear" maxlength="3" size="10" value="<?php print($pembelian->ApreYear); ?>" disabled/> % Per Tahun</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td colspan="3">
                <a href="<?php print($helper->site_url("asset.pembelian")); ?>" class="button">Daftar Pembelian Asset</a>
            </td>
        </tr>
    </table>
</fieldset>
</body>
</html>
