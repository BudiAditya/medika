<!DOCTYPE HTML>
<?php /** @var $investor Investor */ ?>
<html>
<head>
	<title>ERASYS - Tambah Data Investor</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			var elements = ["NikKtp", "NoKK","Nama","Alamat","Simpan"];
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
	<legend><b>Tambah Data Investor</b></legend>
	<form id="frm" action="<?php print($helper->site_url("master.investor/add")); ?>" method="post">
		<table cellpadding="2" cellspacing="1">
			<tr>
				<td>Nomor KTP</td>
				<td><input type="text" class="text2" name="NikKtp" id="NikKtp" maxlength="20" size="20" value="<?php print($investor->NikKtp); ?>" required placeholder="No KTP (16 digit)"/></td>
			</tr>
            <tr>
                <td>Nomor KK</td>
                <td><input type="text" class="text2" name="NoKK" id="NoKK" maxlength="20" size="20" value="<?php print($investor->NoKK); ?>" required placeholder="No KK (16 digit)"/></td>
            </tr>
			<tr>
				<td>Nama Investor</td>
				<td><input type="text" class="text2" name="Nama" id="Nama" maxlength="50" size="50" value="<?php print($investor->Nama); ?>" required/></td>
			</tr>
            <tr>
                <td>Alamat</td>
                <td><input type="text" class="text2" name="Alamat" id="Alamat" maxlength="150" size="50" value="<?php print($investor->Alamat); ?>" required/></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
			<tr>
                <td>&nbsp;</td>
				<td>
                    <button id="Simpan" type="submit"><b>SIMPAN DATA</b></button>
					<a href="<?php print($helper->site_url("master.investor")); ?>" class="button">Daftar Investor</a>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
</body>
</html>
