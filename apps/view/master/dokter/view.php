<!DOCTYPE HTML>
<?php /** @var $dokter Dokter */  ?>
<html>
<head>
	<title>ERASYS - View Data Dokter</title>
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
<fieldset>
	<legend><b>View Data Dokter</b></legend>
    <table cellpadding="2" cellspacing="1">
        <tr>
            <td>Kode</td>
            <td><input type="text" class="text2" name="KdDokter" id="KdDokter" maxlength="10" size="20" value="<?php print($dokter->KdDokter); ?>" disabled /></td>
            <td>Nama Dokter</td>
            <td colspan="2"><input type="text" class="text2" name="NmDokter" id="NmDokter" maxlength="50" size="30" value="<?php print($dokter->NmDokter); ?>" disabled /></td>
            <td>&nbsp;</td>
            <td rowspan="7"class="center">
                <?php
                printf('<img src="%s" width="200" height="200"/>',$helper->site_url($dokter->Fphoto));
                ?>
            </td>
        </tr>
        <tr>
            <td>Spesialisasi</td>
            <td><input type="text" class="text2" name="Spesialisasi" id="Spesialisasi" maxlength="50" size="20" value="<?php print($dokter->Spesialisasi); ?>" disabled /></td>
            <td>Alumni</td>
            <td colspan="2"><input type="text" class="text2" name="Alumni" id="Alumni" maxlength="50" size="30" value="<?php print($dokter->Alumni); ?>" disabled /></td>
        </tr>
        <tr>
            <td>No. SIP</td>
            <td><input type="text" class="text2" name="NoSip" id="NoSip" maxlength="50" size="20" value="<?php print($dokter->NoSip); ?>" disabled/></td>
            <td>Mulai Praktek</td>
            <td><input type="text" class="text2" name="MulaiPraktek" id="MulaiPraktek" maxlength="10" size="15" value="<?php print($dokter->FormatMulaiPraktek(JS_DATE)); ?>" disabled/></td>
        </tr>
        <tr>
            <td>Lahir di</td>
            <td><input type="text" class="text2" name="T4Lahir" id="T4Lahir" maxlength="50" size="20" value="<?php print($dokter->T4Lahir); ?>" disabled/></td>
            <td>Tgl.Lahir</td>
            <td><input type="text" class="text2" name="TglLahir" id="TglLahir" maxlength="10" size="15" value="<?php print($dokter->FormatTglLahir(JS_DATE)); ?>" disabled/></td>
            <td>Gender</td>
            <td><select name="Jkelamin" class="text2" id="Jkelamin" disabled>
                    <option value=""></option>
                    <option value="L" <?php ($dokter->Jkelamin == "L" ? print('selected = "selected"'):'');?>>Laki-laki</option>
                    <option value="P" <?php ($dokter->Jkelamin == "P" ? print('selected = "selected"'):'');?>>Perempuan</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td colspan="3"><input type="text" class="text2" name="Alamat" id="Alamat" maxlength="250" size="57" value="<?php print($dokter->Alamat); ?>" disabled/></td>
            <td>Handphone</td>
            <td><input type="tel" class="text2" name="Handphone" id="Handphone" maxlength="50" size="20" value="<?php print($dokter->Handphone); ?>" disabled/></td>
        </tr>
        <tr>
            <td>Status</td>
            <td>
                <select name="DokStatus" class="text2" id="DokStatus" style="width: 125px;" disabled>
                    <option value="1" <?php ($dokter->DokStatus == 1 ? print('selected = "selected"'):'');?>>1 - Aktif</option>
                    <option value="0" <?php ($dokter->DokStatus == 0 ? print('selected = "selected"'):'');?>>0 - Cuti</option>
                </select>
            </td>
            <td>Hari Praktek</td>
            <td colspan="2"><input type="text" class="text2" name="HariPraktek" id="HariPraktek" maxlength="50" size="30" value="<?php print($dokter->HariPraktek); ?>" disabled /></td>
        </tr>
        <tr>
            <td>Jasa Visit Poli</td>
            <td><input type="text" class="text2 right" name="JvPoli" id="JvPoli" maxlength="10" size="15" value="<?php print($dokter->JvPoli); ?>" disabled/></td>
        </tr>
        <tr>
            <td>Jasa Visit Inap Umum</td>
            <td><input type="text" class="text2 right" name="JvInapum" id="JvInapum" maxlength="10" size="15" value="<?php print($dokter->JvInapum); ?>" disabled/></td>
        </tr>
        <tr>
            <td>Jasa Visit Pasien Bpjs</td>
            <td><input type="text" class="text2 right" name="JvBpjs" id="JvBpjs" maxlength="10" size="15" value="<?php print($dokter->JvBpjs); ?>" disabled/></td>
        </tr>
        <tr>
            <td>Jasa Konsul Via Telpon</td>
            <td><input type="text" class="text2 right" name="JkVitel" id="JkVitel" maxlength="10" size="15" value="<?php print($dokter->JkVitel); ?>" disabled/></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td colspan="2">
                <a href="<?php print($helper->site_url("master.dokter")); ?>" class="button">Daftar Dokter</a>
            </td>
        </tr>
    </table>
</fieldset>
</body>
</html>
