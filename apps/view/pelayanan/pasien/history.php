<!DOCTYPE HTML>
<?php /** @var $pasien Pasien */ ?>
<html>
<head>
	<title>ERAMEDIKA - History Kunjungan Pasien</title>
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
	<legend><b>History Kunjungan Pasien</b></legend>
    <table cellpadding="2" cellspacing="1">
        <tr>
            <td>Nama</td>
            <td colspan="3"><input type="text" class="text2" name="NmPasien" id="NmPasien" maxlength="50" size="50" value="<?php print($pasien->NmPasien); ?>" readonly/></td>
            <td>No. KTP</td>
            <td><input type="text" class="text2" name="NoKtp" id="NoKtp" maxlength="20" size="20" value="<?php print($pasien->NoKtp); ?>" readonly/></td>
            <td>No. BPJS</td>
            <td><input type="text" class="text2" name="NoBpjs" id="NoBpjs" maxlength="20" size="20" value="<?php print($pasien->NoBpjs); ?>" readonly/></td>
            <td>L/P</td>
            <td><select id="Jkelamin" name="Jkelamin" disabled>
                    <option value=""></option>
                    <option value="L" <?php print($pasien->Jkelamin == 'L' ? 'selected="selected"' : '');?>>Laki-Laki</option>
                    <option value="P" <?php print($pasien->Jkelamin == 'P' ? 'selected="selected"' : '');?>>Perempuan</option>
                </select>
            </td>
            <td>No. RM</td>
            <td><input type="text" class="text2" name="NoRm" id="NoRm" maxlength="15" size="15" value="<?php print($pasien->NoRm); ?>" readonly /></td>
        </tr>
        <tr>
            <td>Alamat </td>
            <td colspan="3"><input type="text" class="text2" name="Alamat" id="Alamat" maxlength="50" size="50" value="<?php print($pasien->Alamat); ?>" readonly/></td>
            <td>Lahir Di</td>
            <td><input type="text" class="text2" name="T4Lahir" id="T4Lahir" maxlength="50" size="20" value="<?php print($pasien->T4Lahir); ?>" readonly/></td>
            <td>Tgl Lahir</td>
            <td><input type="text" class="text2" name="TglLahir" id="TglLahir" maxlength="10" size="20" value="<?php print($pasien->FormatTglLahir(JS_DATE));?>" readonly/></td>
            <td>Usia</td>
            <td><input type="text" class="text2" name="Umur" id="Umur" size="15" value="<?php print($pasien->Umur);?>" readonly/></td>
        </tr>
    </table>
    <br>
    <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
        <thead>
        <tr>
            <th>No.</th>
            <th>Tgl Masuk</th>
            <th>No. Registrasi</th>
            <th>Cara Bayar</th>
            <th>Jenis Rawat</th>
            <th>Kamar Rawat</th>
            <th>Dokter</th>
            <th>Lama Rawat</th>
            <th>Tgl Pulang</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if ($rsdata != null){
            $nmr = 1;
            $url = null;
            while ($row = $rsdata->FetchAssoc()){
                $url = $helper->site_url("pelayanan.perawatan/view/".$row["id"]);
                print('<tr>');
                printf('<td align="center">%d</td>',$nmr);
                printf('<td>%s</td>',$row['tgl_masuk']);
                printf("<td><a href= '%s' target='_blank'>%s</a></td>",$url ,$row["no_reg"]);
                printf('<td>%s</td>',$row['cara_bayar'] == 1 ? 'Umum' : 'BPJS');
                printf('<td>%s</td>',$row['jns_rawat'] == 1 ? 'Rawat Jalan' : 'Rawat Inap');
                printf('<td>%s</td>',$row['kmr_rawat']);
                printf('<td>%s</td>',$row['nm_dokter']);
                printf('<td align="right">%s hari</td>',$row['lama_rawat']);
                printf('<td>%s</td>',$row['tgl_keluar']);
                print('</tr>');
                $nmr++;
            }
        }
        ?>
        </tbody>
    </table>
</fieldset>
</body>
</html>
