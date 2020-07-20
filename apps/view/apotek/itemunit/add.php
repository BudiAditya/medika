<!DOCTYPE HTML>
<?php
/** @var $itemunit ItemUnit */
?>

<html>
<head>
	<title>ERAMEDIKA - Tambah Data Satuan Barang/Obat</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			var elements = ["UnitCode", "UnitName", "UnitValue"];
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
	<legend><b>Tambah Data Satuan Barang/Obat</b></legend>
	<form id="frm" action="<?php print($helper->site_url("apotek.itemunit/add")); ?>" method="post">
		<table cellpadding="2" cellspacing="1">
			<tr>
				<td>Kode</td>
				<td><input type="text" class="text2" name="UnitCode" id="UnitCode" maxlength="10" size="10" value="<?php print($itemunit->UnitCode); ?>" required /></td>
			</tr>
			<tr>
				<td>Satuan Barang</td>
				<td><input type="text" class="text2" name="UnitName" id="UnitName" maxlength="50" size="20" value="<?php print($itemunit->UnitName); ?>" required /></td>
			</tr>
            <tr>
                <td>Isi</td>
                <td><input type="text" class="text2" name="UnitValue" id="UnitValue" maxlength="10" size="20" value="<?php print($itemunit->UnitValue); ?>" /></td>
            </tr>
			<tr>
                <td>&nbsp;</td>
				<td>
					<button type="submit">SIMPAN DATA</button>
					<a href="<?php print($helper->site_url("apotek.itemunit")); ?>" class="button">Daftar Satuan Barang</a>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
</body>
</html>
