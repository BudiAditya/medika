<!DOCTYPE HTML>
<html>
<head>
	<title>ERAMEDIKA - Tambah Data Kelompok Transaksi</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			var elements = ["KdKlpTransaksi", "KlpTransaksi", "Simpan"];
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
	<legend><b>Tambah Data Kelompok Transaksi</b></legend>
	<form id="frm" action="<?php print($helper->site_url("master.klptransaksi/add")); ?>" method="post">
		<table cellpadding="2" cellspacing="1">
			<tr>
				<td>Kode</td>
				<td><input type="text" class="text2" name="KdKlpTransaksi" id="KdKlpTransaksi" maxlength="10" size="10" value="<?php print($klptransaksi->KdKlpTransaksi); ?>" required readonly placeholder="AUTO"/></td>
			</tr>
			<tr>
				<td>Kelompok</td>
				<td><input type="text" class="text2" name="KlpTransaksi" id="KlpTransaksi" maxlength="50" size="50" value="<?php print($klptransaksi->KlpTransaksi); ?>" required/></td>
			</tr>
			<tr>
                <td>&nbsp;</td>
				<td>
					<button type="submit" id="Simpan">Simpan</button>
					<a href="<?php print($helper->site_url("master.klptransaksi")); ?>" class="button">Daftar Kelompok Transaksi</a>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
</body>
</html>
