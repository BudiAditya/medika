<!DOCTYPE HTML>
<?php /** @var $bhp Bhp */ ?>
<html>
<head>
	<title>ERAMEDIKA - Ubah Data Bahan Habis Pakai</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			var elements = ["KdBhp","NmBhp","Satuan","HargaDasar","HargaJual","StockAwal","StockQty","Update"];
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
	<legend><b>Ubah Data Bahan Habis Pakai</b></legend>
	<form id="frm" action="<?php print($helper->site_url("master.bhp/edit/".$bhp->Id)); ?>" method="post">
		<table cellpadding="2" cellspacing="1">
            <tr>
                <td>Kode</td>
                <td><input type="text" class="text2" name="KdBhp" id="KdBhp" maxlength="15" size="15" value="<?php print($bhp->KdBhp); ?>" required/></td>
            </tr>
            <tr>
				<td>Nama Bahan</td>
				<td colspan="3"><input type="text" class="text2" name="NmBhp" id="NmBhp" maxlength="50" size="50" value="<?php print($bhp->NmBhp); ?>" required/></td>
			</tr>
            <tr>
                <td>Satuan</td>
                <td><input type="text" class="text2" name="Satuan" id="Satuan" maxlength="15" size="15" value="<?php print($bhp->Satuan); ?>" required/></td>
            </tr>
            <tr>
                <td>Harga Dasar</td>
                <td><input type="text" class="right" name="HargaDasar" id="HargaDasar" maxlength="15" size="15" value="<?php print($bhp->HargaDasar); ?>" required/></td>
                <td>Harga Jual</td>
                <td><input type="text" class="right" name="HargaJual" id="HargaJual" maxlength="15" size="15" value="<?php print($bhp->HargaJual); ?>" required/></td>
            </tr>
            <tr>
                <td>Stok Awal</td>
                <td><input type="text" class="right" name="StockAwal" id="StockAwal" maxlength="15" size="15" value="<?php print($bhp->StockAwal); ?>" required/></td>
                <td>Stok Qty</td>
                <td><input type="text" class="right" name="StockQty" id="StockQty" maxlength="15" size="15" value="<?php print($bhp->StockQty); ?>" required/></td>
            </tr>
			<tr>
                <td>&nbsp;</td>
				<td colspan="3">
					<button id="Update" type="submit">Update</button>
					<a href="<?php print($helper->site_url("master.bhp")); ?>" class="button">Daftar Bahan Habis Pakai</a>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
</body>
</html>
