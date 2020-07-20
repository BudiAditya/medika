<!DOCTYPE HTML>
<?php /** @var $year int */ /** @var $month int */ ?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>ERASYS - User Login</title>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
	<script type="text/javascript">
		$(document).ready(function() {
            //var elements = ["user_id", "user_pwd", "user_captcha", "user_trxmonth", "user_trxyear", "btn_login"];
			var elements = ["user_id","user_pwd","btn_login"];
			BatchFocusRegister(elements);
		});
	</script>

	<style type="text/css"> /* css settings */

	.text1 {
		font-family: Arial, Helvetica, sans-serif;
		font-size: 11px;
		color: #000000;
	}

	.text2 {
		font-family: Arial, Helvetica, sans-serif;
		font-size: 10px;
		color: #0000FF;
	}

	.text4 {
		font-family: Arial, Helvetica, sans-serif;
		font-size: 13px;
		color: #000066;
	}
	</style>
</head>

<body>
<div style="padding:50px;">
	<div align="center">
		<img src="<?php print(base_url('public/images/company/medikajaya.png'));?>" width="300" height="150"
	</div>
	<div align="center">
		<h2 style="color: #0000FF">SYSTEM INFORMASI MEDIS</h2>
        <h1 style="color: #0000FF">KLINIK MEDIKA JAYA MOPUYA</h1>
	</div>
	<hr/>
	<form action="<?php echo site_url("home/login"); ?>" method="post" autocomplete="off">
		<table width="400" border="0" align="center" cellpadding="2" cellspacing="0">
			<tr>
				<td class="text4" width="128">ID Pemakai</td>
				<td width="302"><input type="text" name="user_id" style="width:170px" value="" id="user_id" autocomplete="off" required/></td>
			</tr>
            <tr>
                <td class="text4">Kata Sandi</td>
                <td><input type="password" name="user_pwd" style="width:170px" value="" id="user_pwd" autocomplete="off" required/></td>
            </tr>
			<tr>
				<td>&nbsp;</td>
				<td align="left"><input type="submit" name="btn_login" value="Masuk" id="btn_login" style="width:85px"/>
					<input type="reset" name="btn_reset" value="Batal" id="btn_reset" style="width:85px"/></td>
			</tr>
		</table>
	</form>
	<hr/>
	<div class="text1" align="center">IP Address Anda :
		<b><?php echo "<span class=\"text2\">" . getenv("REMOTE_ADDR") . "</span>"; ?></b></div>
	<div class="text1" align="center">** Untuk alasan keamanan, kami mencatat aktifitas Anda pada system **</div>
	<div class="text1" align="center">Helpdesk & Support : 0431 7242544, 081244138229, 08114319858 email: erasystem.id@gmail.com</div>
	<div class="text2" align="center">Copyright &copy; 2018  CV. Erasystem Infotama</div>
	<?php if (isset($error)) { ?>
	<script type="text/javascript">
		alert("<?php print($error);?>");
	</script>
	<?php } ?>
</div>
</body>
</html>
