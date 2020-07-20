<!DOCTYPE HTML>
<?php
/** @var $itemtype ItemType */
?>

<html>
<head>
	<title>ERAMEDIKA - Tambah Data Jenis Barang/Obat</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			var elements = ["TypeCode", "TypeName", "TypeDescs"];
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
	<legend><b>Tambah Data Jenis Barang/Obat</b></legend>
	<form id="frm" action="<?php print($helper->site_url("apotek.itemtype/add")); ?>" method="post">
		<table cellpadding="2" cellspacing="1">
			<tr>
				<td>Kode</td>
				<td><input type="text" class="text2" name="TypeCode" id="TypeCode" maxlength="10" size="10" value="<?php print($itemtype->TypeCode); ?>" required /></td>
			</tr>
			<tr>
				<td>Jenis Barang</td>
				<td><input type="text" class="text2" name="TypeName" id="TypeName" maxlength="50" size="50" value="<?php print($itemtype->TypeName); ?>" required /></td>
			</tr>
            <tr>
                <td>Keterangan</td>
                <td><input type="text" class="text2" name="TypeDescs" id="TypeDescs" maxlength="50" size="50" value="<?php print($itemtype->TypeDescs); ?>" /></td>
            </tr>
			<tr>
                <td>&nbsp;</td>
				<td>
					<button type="submit">SIMPAN DATA</button>
					<a href="<?php print($helper->site_url("apotek.itemtype")); ?>" class="button">Daftar Jenis Barang</a>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
</body>
</html>
