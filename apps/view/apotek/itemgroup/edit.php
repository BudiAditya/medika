<!DOCTYPE HTML>
<?php
/** @var $itemgroup ItemGroup */
?>

<html>
<head>
	<title>ERAMEDIKA - Update Data Golongan Barang/Obat</title>
	<meta http-equiv="Content-group" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			var elements = ["GroupCode", "GroupName", "GroupDescs", "Update"];
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
	<legend><b>Update Data Golongan Barang/Obat</b></legend>
	<form id="frm" action="<?php print($helper->site_url("apotek.itemgroup/edit/".$itemgroup->Id)); ?>" method="post">
		<table cellpadding="2" cellspacing="1">
			<tr>
				<td>Kode</td>
				<td><input type="text" class="text2" name="GroupCode" id="GroupCode" maxlength="10" size="10" value="<?php print($itemgroup->GroupCode); ?>" required /></td>
			</tr>
			<tr>
				<td>Golongan</td>
				<td><input type="text" class="text2" name="GroupName" id="GroupName" maxlength="50" size="50" value="<?php print($itemgroup->GroupName); ?>" required /></td>
			</tr>
            <tr>
                <td>Keterangan</td>
                <td><input type="text" class="text2" name="GroupDescs" id="GroupDescs" maxlength="50" size="50" value="<?php print($itemgroup->GroupDescs); ?>" /></td>
            </tr>
			<tr>
                <td>&nbsp;</td>
				<td>
					<button id="Update" type="submit">UPDATE DATA</button>
					<a href="<?php print($helper->site_url("apotek.itemgroup")); ?>" class="button">Daftar Golongan Barang</a>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
</body>
</html>
