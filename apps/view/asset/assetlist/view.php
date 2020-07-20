<!DOCTYPE HTML>
<?php /** @var $asset AssetList */ /** @var $klpasset KlpAsset[] */ ?>
<html>
<head>
    <title>ERAMEDIKA - View Data Asset/Aktiva Tetap</title>
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
    <legend><b>View Data Asset/Aktiva Tetap</b></legend>
    <table cellpadding="2" cellspacing="1">
        <tr>
            <td>Kode Asset</td>
            <td><input type="text" class="text2" name="KdAsset" id="KdAsset" maxlength="15" size="15" value="<?php print($asset->KdAsset); ?>" required readonly placeholder="AUTO"/></td>
        </tr>
        <tr>
            <td>Kelompok Asset</td>
            <td><select name="KdKlpAsset" class="text2" id="KdKlpAsset"style="width: 185px" disabled>
                    <option value=""></option>
                    <?php
                    foreach ($klpasset as $klp) {
                        if ($asset->KdKlpAsset == $klp->KdKlpAsset) {
                            printf('<option value="%s" selected="selected">%s</option>', $klp->KdKlpAsset, $klp->KlpAsset);
                        } else {
                            printf('<option value="%s">%s</option>', $klp->KdKlpAsset, $klp->KlpAsset);
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Nama Asset</td>
            <td colspan="3"><input type="text" class="text2" name="NmAsset" id="NmAsset" maxlength="50" size="50" value="<?php print($asset->NmAsset); ?>" readonly/></td>
        </tr>
        <tr>
            <td>Lokasi Asset</td>
            <td><select name="SbuId" class="text2" id="SbuId"style="width: 185px" disabled>
                    <option value="0"> 0 - Corporate </option>
                    <?php
                    /** @var $sbulist Sbu[]*/
                    foreach ($sbulist as $sbu) {
                        if ($asset->SbuId == $sbu->Id) {
                            printf('<option value="%d" selected="selected"> %s - %s </option>', $sbu->Id, $sbu->Id, $sbu->SbuName);
                        } else {
                            printf('<option value="%d"> %s - %s </option>', $sbu->Id, $sbu->Id, $sbu->SbuName);
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Thn Perolehan</td>
            <td><input type="text" class="text2" name="ThnPerolehan" id="ThnPerolehan" maxlength="4" size="10" value="<?php print($asset->ThnPerolehan); ?>" readonly/></td>
        </tr>
        <tr>
            <td>Nilai Perolehan</td>
            <td><input type="text" class="right numeric" name="NilaiPerolehan" id="NilaiPerolehan" maxlength="50" size="18" value="<?php print(number_format($asset->NilaiPerolehan)); ?>" readonly/></td>
        </tr>
        <tr>
            <td>Nilai Buku</td>
            <td><input type="text" class="right numeric" name="NilaiBuku" id="NilaiBuku" maxlength="50" size="18" value="<?php print(number_format($asset->NilaiBuku)); ?>" readonly/></td>
        </tr>
        <tr>
            <td>QTY</td>
            <td><input type="text" class="right numeric" name="Qty" id="Qty" maxlength="3" size="10" value="<?php print($asset->Qty); ?>" readonly/></td>
        </tr>
        <tr>
            <td>Masa Manfaat</td>
            <td><input type="text" class="right" name="MasaManfaat" id="MasaManfaat" maxlength="3" size="10" value="<?php print($asset->MasaManfaat); ?>" readonly/> Tahun</td>
        </tr>
        <tr>
            <td>Depresiasi</td>
            <td><input type="text" class="right" name="DeprYear" id="DeprYear" maxlength="3" size="10" value="<?php print($asset->DeprYear); ?>" readonly/> % Per Tahun</td>
        </tr>
        <tr>
            <td>Apresiasi</td>
            <td><input type="text" class="right" name="ApreYear" id="ApreYear" maxlength="3" size="10" value="<?php print($asset->ApreYear); ?>" readonly/> % Per Tahun</td>
        </tr>
        <tr>
            <td>Periode Penyusutan Terakhir</td>
            <td><input type="text" class="right" name="LastDepr" id="LastDepr" maxlength="6" size="10" value="<?php print($asset->LastDepr); ?>" readonly/> *Format -> YYYYMM</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td colspan="3">
                <a href="<?php print($helper->site_url("asset.assetlist")); ?>" class="button">Daftar Asset/Aktiva Tetap</a>
            </td>
        </tr>
    </table>
</fieldset>
</body>
</html>
