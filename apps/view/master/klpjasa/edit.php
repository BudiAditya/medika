<!DOCTYPE HTML>
<?php
/** @var $klpjasa Klpjasa */
/** @var $trxtype TrxType[] */
?>
<html>
<head>
	<title>ERAMEDIKA - Ubah Data Kelompok Jasa/Tindakan</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
	<script type="text/javascript">
		$(document).ready(function() {
            var elements = ["KdKlpJasa", "KlpJasa","TrxTypeCode","PjmKlinik","PjmOperator","PjmPelaksana","Update"];
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
	<legend><b>Ubah Data Kelompok Jasa/Tindakan</b></legend>
	<form id="frm" action="<?php print($helper->site_url("master.klpjasa/edit/".$klpjasa->Id)); ?>" method="post">
		<table cellpadding="2" cellspacing="1">
			<tr>
				<td>Kode</td>
				<td><input type="text" class="text2" name="KdKlpJasa" id="KdKlpJasa" maxlength="10" size="10" value="<?php print($klpjasa->KdKlpJasa); ?>" required/></td>
			</tr>
			<tr>
				<td>Kelompok/Kategori</td>
				<td><input type="text" class="text2" name="KlpJasa" id="KlpJasa" maxlength="50" size="50" value="<?php print($klpjasa->KlpJasa); ?>" required/></td>
			</tr>
            <tr>
                <td>Kode Transaksi</td>
                <td><select name="TrxTypeCode" id="TrxTypeCode">
                        <option value=""></option>
                        <?php
                        foreach ($trxtype as $ttcode){
                            if ($klpjasa->TrxTypeCode == $ttcode->TrxCode){
                                printf("<option value='%s' selected='selected'> %s - %s </option>",$ttcode->TrxCode,$ttcode->TrxCode,$ttcode->TrxDescs);
                            }else{
                                printf("<option value='%s'> %s - %s </option>",$ttcode->TrxCode,$ttcode->TrxCode,$ttcode->TrxDescs);
                            }
                        }
                        ?>
                    </select>

                </td>
            </tr>
            <tr>
                <td>Jasa Medis Klinik</td>
                <td><input type="text" class="text2 right" name="PjmKlinik" id="PjmKlinik" maxlength="3" size="5" value="<?php print($klpjasa->PjmKlinik); ?>" required/> %</td>
            </tr>
            <tr>
                <td>Jasa Medis Operator</td>
                <td><input type="text" class="text2 right" name="PjmOperator" id="PjmOperator" maxlength="3" size="5" value="<?php print($klpjasa->PjmOperator); ?>" required/> %</td>
            </tr>
            <tr>
                <td>Jasa Medis Pelaksana</td>
                <td><input type="text" class="text2 right" name="PjmPelaksana" id="PjmPelaksana" maxlength="3" size="5" value="<?php print($klpjasa->PjmPelaksana); ?>" required/> %</td>
            </tr>
			<tr>
                <td>&nbsp;</td>
				<td>
					<button type="submit" id="Update">Update</button>
					<a href="<?php print($helper->site_url("master.klpjasa")); ?>" class="button">Daftar Kelompok Jasa/Tindakan</a>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
</body>
</html>
