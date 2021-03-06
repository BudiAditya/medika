<!DOCTYPE HTML>
<?php
/** @var $layanan Layanan */
/** @var $klpjasa Klpjasa[] */
/** @var $jasa Jasa[] */
/** @var $dokterlist Dokter[] */
/** @var $petugaslist Karyawan[] */
?>
<html>
<head>
	<title>ERAMEDIKA - View Data Layanan & Jasa</title>
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
	<legend><b>View Data Layanan & Jasa</b></legend>
    <table cellpadding="2" cellspacing="1">
        <tr>
            <td>Tanggal</td>
            <td><input type="text" class="bold" name="TglLayanan" id="TglLayanan" maxlength="15" size="10" value="<?php print($layanan->FormatTglLayanan(JS_DATE)); ?>" readonly/></td>
            <td colspan="2" align="center">Jam Layanan</td>
            <td align="right"><input type="text" class="bold" name="JamLayanan" id="JamLayanan" maxlength="5" size="5" value="<?php print($layanan->JamLayanan); ?>" readonly/></td>
        </tr>
        <tr>
            <td>No. Register</td>
            <td colspan="4"><select id="NoReg" name="NoReg" style="width: 300px" disabled>
                    <option value="">-- Pilih Pasien --</option>
                    <?php
                    while ($row = $pasienlist->FetchAssoc()) {
                        if ($row["no_reg"] == $layanan->NoReg) {
                            printf('<option value="%s" selected="selected"> %s - %s </option>', $row["no_reg"], $row["no_reg"], $row["nm_pasien"]);
                        } else {
                            printf('<option value="%s"> %s - %s </option>', $row["no_reg"], $row["no_reg"], $row["nm_pasien"]);
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Nama Dokter</td>
            <td colspan="4"><select id="KdDokter" name="KdDokter" style="width: 300px" disabled>
                    <option value="">-- Pilih Nama Dokter --</option>
                    <?php
                    foreach ($dokterlist as $dokter) {
                        if ($dokter->KdDokter == $layanan->KdDokter) {
                            printf('<option value="%s" selected="selected"> %s - %s </option>', $dokter->KdDokter, $dokter->KdDokter, $dokter->NmDokter);
                        } else {
                            printf('<option value="%s">%s - %s </option>', $dokter->KdDokter, $dokter->KdDokter, $dokter->NmDokter);
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Nama Petugas</td>
            <td colspan="4"><select id="KdPetugas" name="KdPetugas" style="width: 300px" disabled>
                    <option value="">-- Pilih Nama Petugas --</option>
                    <?php
                    foreach ($petugaslist as $petugas) {
                        if ($petugas->Nik == $layanan->KdPetugas) {
                            printf('<option value="%s" selected="selected"> %s - %s </option>', $petugas->Nik, $petugas->Nik, $petugas->Nama);
                        } else {
                            printf('<option value="%s">%s - %s </option>', $petugas->Nik, $petugas->Nik, $petugas->Nama);
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Nama Layanan</td>
            <td colspan="4"><select id="KdJasa1" name="KdJasa1" style="width: 300px" disabled>
                    <option value="">-- Pilih Nama Layanan --</option>
                    <?php
                    foreach ($jasa as $nmjasa) {
                        $dtx = $nmjasa->KdJasa.'|'.$nmjasa->NmJasa.'|'.$nmjasa->KdKlpJasa.'|'.$nmjasa->Tarif.'|'.$nmjasa->Satuan;
                        if ($nmjasa->KdJasa == $layanan->KdJasa) {
                            printf('<option value="%s" selected="selected"> %s - %s </option>', $dtx, $nmjasa->KdJasa, $nmjasa->NmJasa);
                        } else {
                            printf('<option value="%s">%s - %s </option>', $dtx, $nmjasa->KdJasa, $nmjasa->NmJasa);
                        }
                    }
                    ?>
                </select>
                <input type="hidden" name="KdKlpJasa" id="KdKlpJasa" value="<?php print($layanan->KdKlpJasa);?>">
                <input type="hidden" name="KdJasa" id="KdJasa" value="<?php print($layanan->KdJasa);?>">
                <input type="hidden" name="NmJasa" id="NmJasa" value="<?php print($layanan->NmJasa);?>">
            </td>
        </tr>
        <tr>
            <td>Tarif / Harga</td>
            <td><input type="text" class="right bold" name="TarifHarga" id="TarifHarga" maxlength="10" size="8" value="<?php print($layanan->TarifHarga);?>" required readonly/></td>
            <td>QTY</td>
            <td align="right"><input type="text" class="right bold" name="Qty" id="Qty" maxlength="10" size="3" value="<?php print($layanan->Qty);?>" readonly/>
                <input type="text" name="Satuan" id="Satuan" size="5" value="" disabled>
            </td>
            <td>Sub Total</td>
            <td><input type="text" class="right bold" name="SubTotal" id="SubTotal" maxlength="15" size="10" value="<?php print($layanan->TarifHarga * $layanan->Qty);?>" readonly/></td>
        </tr>
        <tr>
            <td>Keterangan</td>
            <td colspan="4"><input type="text" name="Keterangan" id="Keterangan" maxlength="150" size="55" value="<?php print($layanan->Keterangan);?>" readonly/></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td colspan="4" align="center">
                <?php
                if ($layanan->TrxStatus == 0){ ?>
                <a href="<?php print($helper->site_url("outpatient.rjlayanan/edit/".$layanan->Id)); ?>" class="button">Ubah Data</a>
                <?php } ?>
                <a href="<?php print($helper->site_url("outpatient.rjlayanan")); ?>" class="button">Daftar Layanan</a>
            </td>
        </tr>
    </table>
</fieldset>
</body>
</html>
