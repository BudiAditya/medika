<!DOCTYPE HTML>
<?php /** @var $jasa Jasa */ /** @var $klpjasa Klpjasa[] */ /** @var $klpbilling KlpBilling[] */?>
<html>
<head>
	<title>ERAMEDIKA - Ubah Data Jasa/Tindakan</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
	<script type="text/javascript">
		$(document).ready(function() {
            var elements = ["KdJasa","KdKlpJasa","KdKlpBilling","NmJasa","Satuan","TIgd","TPoli","TPolSp","TPolRd","TPolKb","TPersalinan","TK3","TK2","Update"];
			BatchFocusRegister(elements);
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
<fieldset>
	<legend><b>Ubah Data Jasa/Tindakan</b></legend>
	<form id="frm" action="<?php print($helper->site_url("master.jasa/edit/".$jasa->Id)); ?>" method="post">
		<table cellpadding="2" cellspacing="1">
            <tr>
                <td>Kode</td>
                <td><input type="text" class="text2" name="KdJasa" id="KdJasa" maxlength="10" size="10" value="<?php print($jasa->KdJasa); ?>" required readonly/></td>
                <td colspan="3">&nbsp;&nbsp;&nbsp;</td>
            </tr>
            <tr>
                <td>Kelompok Jasa</td>
                <td><select name="KdKlpJasa" class="text2" id="KdKlpJasa" required>
                        <?php
                        foreach ($klpjasa as $klp) {
                            if ($jasa->KdKlpJasa == $klp->KdKlpJasa) {
                                printf('<option value="%s" selected="selected">%s</option>', $klp->KdKlpJasa, $klp->KlpJasa);
                            } else {
                                printf('<option value="%s">%s</option>', $klp->KdKlpJasa, $klp->KlpJasa);
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Kelompok Billing</td>
                <td><select name="KdKlpBilling" class="text2" id="KdKlpBilling" required>
                        <option value=""></option>
                        <?php
                        foreach ($klpbilling as $klp) {
                            if ($jasa->KdKlpBilling == $klp->KdKlpBilling) {
                                printf('<option value="%s" selected="selected">%s</option>', $klp->KdKlpBilling, $klp->KlpBilling);
                            } else {
                                printf('<option value="%s">%s</option>', $klp->KdKlpBilling, $klp->KlpBilling);
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
			<tr>
				<td>Jasa/Tindakan</td>
				<td colspan="3"><input type="text" class="text2" name="NmJasa" id="NmJasa" maxlength="50" size="50" value="<?php print($jasa->NmJasa); ?>" required/></td>
			</tr>
            <tr>
                <td>Uraian Jasa</td>
                <td colspan="3"><input type="text" class="text2" name="UraianJasa" id="UraianJasa" maxlength="50" size="50" value="<?php print($jasa->UraianJasa); ?>"/></td>
            </tr>
            <tr>
                <td>Satuan</td>
                <td><input type="text" class="text2" name="Satuan" id="Satuan" maxlength="15" size="15" value="<?php print($jasa->Satuan); ?>" required/></td>
                <td>Kena Fee Dokter</td>
                <td><select class="bold" name="IsFeeDokter" id="IsFeeDokter" required>
                        <option value="0" <?php print($jasa->IsFeeDokter == 0 ? 'selected = "selected"' : '');?>> 0 - Tidak </option>
                        <option value="1" <?php print($jasa->IsFeeDokter == 1 ? 'selected = "selected"' : '');?>> 1 - Ya </option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Tarif IGD</td>
                <td><input type="text" class="bold right" name="TIgd" id="TIgd" maxlength="15" size="13" value="<?php print($jasa->TIgd); ?>" required/></td>
                <td>Charge Otomatis</td>
                <td><select class="bold" name="IsAuto" id="IsAuto" required>
                        <option value="0" <?php print($jasa->IsAuto == 0 ? 'selected = "selected"' : '');?>> 0 - Tidak </option>
                        <option value="1" <?php print($jasa->IsAuto == 1 ? 'selected = "selected"' : '');?>> 1 - Ya </option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Tarif Poli Umum</td>
                <td><input type="text" class="bold right" name="TPoli" id="TPoli" maxlength="15" size="13" value="<?php print($jasa->TPoli); ?>" required/></td>
                <td>Ditanggung BPJS</td>
                <td><select class="bold" name="IsBpjs" id="IsBpjs" required>
                        <option value="0" <?php print($jasa->IsBpjs == 0 ? 'selected = "selected"' : '');?>> 0 - Tidak </option>
                        <option value="1" <?php print($jasa->IsBpjs == 1 ? 'selected = "selected"' : '');?>> 1 - Ya </option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Tarif Poli Spesialis</td>
                <td><input type="text" class="bold right" name="TPolSp" id="TPolSp" maxlength="15" size="13" value="<?php print($jasa->TPolSp); ?>" required/></td>
                <td>Maksimal</td>
                <td><input type="text" class="bold right" name="BpjsLimit" id="BpjsLimit" maxlength="2" size="3" value="<?php print($jasa->BpjsLimit); ?>" required/>
                    &nbsp;
                    kali
                    &nbsp;
                    <select class="bold" name="BpjsLimitMode" id="BpjsLimitMode" required>
                        <option value="0" <?php print($jasa->BpjsLimitMode == 0 ? 'selected = "selected"' : '');?>> - - </option>
                        <option value="1" <?php print($jasa->BpjsLimitMode == 1 ? 'selected = "selected"' : '');?>> Per Hari </option>
                        <option value="2" <?php print($jasa->BpjsLimitMode == 2 ? 'selected = "selected"' : '');?>> Per Bulan </option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Tarif Poli Radiologi</td>
                <td><input type="text" class="bold right" name="TPolRd" id="TPolRd" maxlength="15" size="13" value="<?php print($jasa->TPolRd); ?>" required/></td>
            </tr>
            <tr>
                <td>Tarif Poli Kebidanan</td>
                <td><input type="text" class="bold right" name="TPolKb" id="TPolKb" maxlength="15" size="13" value="<?php print($jasa->TPolKb); ?>" required/></td>
            </tr>
            <tr>
                <td>Tarif Persalinan</td>
                <td><input type="text" class="bold right" name="TPersalinan" id="TPersalinan" maxlength="15" size="13" value="<?php print($jasa->TPersalinan); ?>" required/></td>
            </tr>
            <tr>
                <td>Tarif Kelas 3</td>
                <td><input type="text" class="bold right" name="TK3" id="TK3" maxlength="15" size="13" value="<?php print($jasa->TK3); ?>" required/></td>
            </tr>
            <tr>
                <td>Tarif Kelas 2</td>
                <td><input type="text" class="bold right" name="TK2" id="TK2" maxlength="15" size="13" value="<?php print($jasa->TK2); ?>" required/></td>
            </tr>
			<tr>
                <td>&nbsp;</td>
				<td colspan="3">
					<button id="Update" type="submit">Update</button>
					<a href="<?php print($helper->site_url("master.jasa")); ?>" class="button">Daftar Jasa/Tindakan</a>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
</body>
</html>
