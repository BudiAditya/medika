<!DOCTYPE HTML>
<?php 
/** @var $akun CoaDetail[] */
/** @var $klpasset KlpAsset */    
?>
<html>
<head>
	<title>ERAMEDIKA - Tambah Data Kelompok Asset</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			var elements = ["KdKlpAsset", "KlpAsset","Keterangan","AssetAccNo","ApreAccNo","DeprAccNo","CostAccNo","RevnAccNo","Simpan"];
			BatchFocusRegister(elements);
            var urz = '<?php print($helper->site_url("asset.klpasset/AutoKode"));?>';
            $("#KlpAsset").change(function (e) {
                var kde = $("#KdKlpAsset").val();
                if (kde == '') {
                    $.get(urz, function (data, status) {
                        //alert("Data: " + data + "\nStatus: " + status);
                        $("#KdKlpAsset").val(data);
                    });
                }
            });
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
	<legend><b>Tambah Data Kelompok Asset</b></legend>
	<form id="frm" action="<?php print($helper->site_url("asset.klpasset/add")); ?>" method="post">
		<table cellpadding="2" cellspacing="1">
			<tr>
				<td>Kode</td>
				<td><input type="text" class="text2" name="KdKlpAsset" id="KdKlpAsset" maxlength="10" size="10" value="<?php print($klpasset->KdKlpAsset); ?>" required placeholder="AUTO"/></td>
			</tr>
			<tr>
				<td>Kelompok Asset</td>
				<td><input type="text" class="text2" name="KlpAsset" id="KlpAsset" maxlength="50" size="30" value="<?php print($klpasset->KlpAsset); ?>" required/></td>
			</tr>
            <tr>
                <td>Keterangan</td>
                <td><input type="text" class="text2" name="Keterangan" id="Keterangan" maxlength="250" size="60" value="<?php print($klpasset->Keterangan); ?>"/></td>
            </tr>
            <tr>
                <td>Akun Asset</td>
                <td><select name="AssetAccNo" id="AssetAccNo">
                        <option value=""></option>
                        <?php
                        foreach ($akun as $akuns){
                            if ($klpasset->AssetAccNo == $akuns->Kode){
                                printf('<option value = "%s" selected = "selected"> %s - %s </option>',$akuns->Kode,$akuns->Kode,$akuns->Perkiraan);
                            }else{
                                printf('<option value = "%s"> %s - %s </option>',$akuns->Kode,$akuns->Kode,$akuns->Perkiraan);
                            }
                        }
                        ?>
                    </select>

                </td>
            </tr>
            <tr>
                <td>Akun Apresiasi</td>
                <td><select name="ApreAccNo" id="ApreAccNo">
                        <option value=""></option>
                        <?php
                        foreach ($akun as $akuns){
                            if ($klpasset->ApreAccNo == $akuns->Kode){
                                printf('<option value = "%s" selected = "selected"> %s - %s </option>',$akuns->Kode,$akuns->Kode,$akuns->Perkiraan);
                            }else{
                                printf('<option value = "%s"> %s - %s </option>',$akuns->Kode,$akuns->Kode,$akuns->Perkiraan);
                            }
                        }
                        ?>
                    </select>

                </td>
            </tr>
            <tr>
                <td>Akun Depresiasi</td>
                <td><select name="DeprAccNo" id="DeprAccNo">
                        <option value=""></option>
                        <?php
                        foreach ($akun as $akuns){
                            if ($klpasset->DeprAccNo == $akuns->Kode){
                                printf('<option value = "%s" selected = "selected"> %s - %s </option>',$akuns->Kode,$akuns->Kode,$akuns->Perkiraan);
                            }else{
                                printf('<option value = "%s"> %s - %s </option>',$akuns->Kode,$akuns->Kode,$akuns->Perkiraan);
                            }
                        }
                        ?>
                    </select>

                </td>
            </tr>
            <tr>
                <td>Akun Biaya</td>
                <td><select name="CostAccNo" id="CostAccNo">
                        <option value=""></option>
                        <?php
                        foreach ($akun as $akuns){
                            if ($klpasset->CostAccNo == $akuns->Kode){
                                printf('<option value = "%s" selected = "selected"> %s - %s </option>',$akuns->Kode,$akuns->Kode,$akuns->Perkiraan);
                            }else{
                                printf('<option value = "%s"> %s - %s </option>',$akuns->Kode,$akuns->Kode,$akuns->Perkiraan);
                            }
                        }
                        ?>
                    </select>

                </td>
            </tr>
            <tr>
                <td>Akun Pendapatan</td>
                <td><select name="RevnAccNo" id="RevnAccNo">
                        <option value=""></option>
                        <?php
                        foreach ($akun as $akuns){
                            if ($klpasset->RevnAccNo == $akuns->Kode){
                                printf('<option value = "%s" selected = "selected"> %s - %s </option>',$akuns->Kode,$akuns->Kode,$akuns->Perkiraan);
                            }else{
                                printf('<option value = "%s"> %s - %s </option>',$akuns->Kode,$akuns->Kode,$akuns->Perkiraan);
                            }
                        }
                        ?>
                    </select>

                </td>
            </tr>
			<tr>
                <td>&nbsp;</td>
				<td>
					<button type="submit" id="Simpan">Simpan</button>
					<a href="<?php print($helper->site_url("asset.klpasset")); ?>" class="button">Daftar Kelompok Asset</a>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
</body>
</html>
