<!DOCTYPE HTML>
<html>
<head>
	<title>ERAMEDIKA - Ubah Data Poliklinik</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			var elements = ["KdPoliklinik", "NmPoliklinik"];
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
	<legend><b>Ubah Data Poliklinik</b></legend>
	<form id="frm" action="<?php print($helper->site_url("master.poliklinik/edit/".$poli->Id)); ?>" method="post">
		<table cellpadding="2" cellspacing="1">
			<tr>
				<td>Kode</td>
				<td><input type="text" class="text2" name="KdPoliklinik" id="KdPoliklinik" maxlength="10" size="10" value="<?php print($poli->KdPoliklinik); ?>" /></td>
			</tr>
			<tr>
				<td>Nama Poliklinik</td>
				<td><input type="text" class="text2" name="NmPoliklinik" id="NmPoliklinik" maxlength="50" size="50" value="<?php print($poli->NmPoliklinik); ?>" /></td>
			</tr>
			<tr>
                <td>&nbsp;</td>
				<td>
					<button type="submit">Update</button>
					<a href="<?php print($helper->site_url("master.poliklinik")); ?>" class="button">Daftar Poliklinik</a>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
</body>
</html>
