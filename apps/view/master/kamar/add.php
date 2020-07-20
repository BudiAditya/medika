<!DOCTYPE HTML>
<html>
<head>
	<title>ERAMEDIKA - Tambah Data Kamar</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			var elements = ["KdKamar","NmKamar","KdKelas","Tarif","Status"];
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
	<legend><b>Tambah Data Kamar</b></legend>
	<form id="frm" action="<?php print($helper->site_url("master.kamar/add")); ?>" method="post">
		<table cellpadding="2" cellspacing="1">
			<tr>
				<td>Kode</td>
				<td><input type="text" class="text2" name="KdKamar" id="KdKamar" maxlength="10" size="10" value="<?php print($kamar->KdKamar); ?>" style="width: 110px" required/></td>
			</tr>
			<tr>
				<td>Nama Kamar</td>
				<td><input type="text" class="text2" name="NmKamar" id="NmKamar" maxlength="50" size="30" value="<?php print($kamar->NmKamar); ?>" required/></td>
			</tr>
            <tr>
                <td>Kelas</td>
                <td><select id="KdKelas" name="KdKelas" style="width: 110px" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php
                        foreach ($kelas as $ikelas) {
                            if ($kamar->KdKelas == $ikelas->KdKelas) {
                                printf('<option value="%s" selected="selected">%s</option>', $ikelas->KdKelas, $ikelas->KdKelas);
                            } else {
                                printf('<option value="%s">%s</option>',$ikelas->KdKelas, $ikelas->KdKelas);
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Tarif/Hari</td>
                <td><input type="text" class="right" name="Tarif" id="Tarif" maxlength="10" size="10" value="<?php print($kamar->Tarif == null ? 0 : $kamar->Tarif); ?>" style="width: 110px" required/></td>
            </tr>
            <tr>
                <td>Status</td>
                <td><select id="Status" name="Status" style="width: 110px" required>
                        <option value="">-- Pilih Status --</option>
                        <option value="0" <?php print($kamar->Status == 0 ? 'selected="selected"' : ''); ?>>0 - Siap</option>
                        <option value="1" <?php print($kamar->Status == 1 ? 'selected="selected"' : ''); ?>>1 - Terisi</option>
                        <option value="2" <?php print($kamar->Status == 2 ? 'selected="selected"' : ''); ?>>2 - Tdk Siap</option>
                    </select>
                </td>
            </tr>
			<tr>
                <td>&nbsp;</td>
				<td>
					<button type="submit">Simpan</button>
					<a href="<?php print($helper->site_url("master.kamar")); ?>" class="button">Daftar Kamar</a>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
</body>
</html>
