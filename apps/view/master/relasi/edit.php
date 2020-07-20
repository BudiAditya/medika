<!DOCTYPE HTML>
<?php /** @var $relasi Relasi */ ?>
<html>
<head>
	<title>ERAMEDIKA - Ubah Data Relasi</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			var elements = ["NmRelasi","Alamat","Kabkota","KdPos","Npwp","Cperson","CpJabatan","TelNo","LmKredit","Status","Update"];
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
	<legend><b>Ubah Data Relasi</b></legend>
	<form id="frm" action="<?php print($helper->site_url("master.relasi/edit/".$relasi->Id)); ?>" method="post">
        <input type="hidden" id="JnsRelasi" name="JnsRelasi" value="<?php print($relasi->JnsRelasi);?>">
		<table cellpadding="2" cellspacing="1">
			<tr>
                <td>Kategori</td>
                <td><select id="JnsRelasi1" name="JnsRelasi1" disabled>
                        <option value="0" <?php print($relasi->JnsRelasi == 0 ? 'selected = "selected"' : '');?>>- Pilih Kategori -</option>
                        <option value="1" <?php print($relasi->JnsRelasi == 1 ? 'selected = "selected"' : '');?>>1 - Supplier</option>
                        <option value="2" <?php print($relasi->JnsRelasi == 2 ? 'selected = "selected"' : '');?>>2 - Customer</option>
                    </select>
                </td>
				<td>Kode</td>
				<td><input type="text" class="text2" name="KdRelasi" id="KdRelasi" maxlength="10" size="10" value="<?php print($relasi->KdRelasi); ?>" readonly/></td>
			</tr>
			<tr>
				<td>Nama Relasi</td>
				<td colspan="3"><input type="text" class="text2" name="NmRelasi" id="NmRelasi" maxlength="50" size="50" value="<?php print($relasi->NmRelasi); ?>" required/></td>
			</tr>
            <tr>
                <td>Alamat</td>
                <td colspan="3"><input type="text" class="text2" name="Alamat" id="Alamat" maxlength="50" size="50" value="<?php print($relasi->Alamat); ?>" required/></td>
                <td>Kab/Kota</td>
                <td><input type="text" class="text2" name="Kabkota" id="Kabkota" maxlength="50" size="20" value="<?php print($relasi->Kabkota); ?>" required/></td>
                <td>Kode Pos</td>
                <td><input type="text" class="text2" name="KdPos" id="KdPos" maxlength="10" size="20" value="<?php print($relasi->KdPos); ?>" /></td>
                <td>NPWP</td>
                <td><input type="text" class="text2" name="Npwp" id="Npwp" maxlength="20" size="20" value="<?php print($relasi->Npwp); ?>" /></td>
            </tr>
            <tr>
                <td>Atas Nama</td>
                <td colspan="3"><input type="text" class="text2" name="Cperson" id="Cperson" maxlength="50" size="50" value="<?php print($relasi->Cperson); ?>" /></td>
                <td>Jabatan</td>
                <td><input type="text" class="text2" name="CpJabatan" id="CpJabatan" maxlength="50" size="20" value="<?php print($relasi->CpJabatan); ?>" /></td>
                <td>No. HP</td>
                <td><input type="text" class="text2" name="TelNo" id="TelNo" maxlength="10" size="20" value="<?php print($relasi->TelNo); ?>" /></td>
            </tr>
            <tr>
                <td>Lama Kredit</td>
                <td><input type="text" class="right" name="LmKredit" id="LmKredit" maxlength="5" size="5" value="<?php print($relasi->LmKredit); ?>" />&nbsp;hari</td>
                <td>Status</td>
                <td><select id="Status" name="Status" required>
                        <option value="0" <?php print($relasi->Status == 0 ? 'selected = "selected"' : '');?>>Non-Aktif</option>
                        <option value="1" <?php print($relasi->Status == 1 ? 'selected = "selected"' : '');?>>Aktif</option>
                    </select>
                </td>
            </tr>
			<tr>
                <td>&nbsp;</td>
				<td colspan="3">
					<button id="Update" type="submit">Update</button>
					<a href="<?php print($helper->site_url("master.relasi")); ?>" class="button">Daftar Relasi</a>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
</body>
</html>
