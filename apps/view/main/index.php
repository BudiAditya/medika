<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml">
<?php /** @var $notifications NotificationGroup[] */ ?>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>ERAMEDIKA - System Informasi Medis</title>

	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>" />
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>" />

	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
</head>
<body>
<?php include(VIEW . "main/menu.php"); ?>

<?php if (isset($error)) { ?>
<div class="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
<div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php } ?>
<br>
<div align="center">
    <table class="list" align="center" width="100%" border="0">
        <thead>
        <td class='left subTitle' colspan=2>SELAMAT DATANG!</td>
        <td class='right' colspan=4><a href="<?php print($helper->site_url('main/aclview/0')); ?>">Klik disini untuk mengetahui <strong>Hak Akses Anda</strong></a></td>
        </thead>
        <tr height="30px">
            <td colspan="6">&nbsp;</td>
        </tr>
        <tr height="120px">
            <td>&nbsp;</td>
            <td width="150" align="center"><a href="pelayanan.pasien"><img src="<?php print(base_url('public/images/pics/pasien.png'));?>" width="80px" height="80px"><br /><b>PASIEN</b></a></td>
            <td width="150" align="center"><a href="master.dokter"><img src="<?php print(base_url('public/images/pics/doctors.jpg'));?>" width="80px" height="80px"><br /><b>DOKTER</b></a></td>
            <td width="150" align="center"><a href="master.karyawan"><img src="<?php print(base_url('public/images/pics/perawat.jpg'));?>" width="80px" height="80px"><br /><b>PETUGAS</b></a></td>
            <td width="150" align="center"><a href="master.poliklinik"><img src="<?php print(base_url('public/images/pics/poliklinik.png'));?>" width="80px" height="80px"><br /><b>POLIKLINIK</b></a></td>
            <td>&nbsp;</td>
        </tr>
        <tr height="30px">
            <td colspan="6">&nbsp;</td>
        </tr>
    </table>
</div>
</body>
</html>
