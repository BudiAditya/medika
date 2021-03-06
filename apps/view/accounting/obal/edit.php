<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
<head>
	<title>Erasys - Edit Opening Balance</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>

	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/auto-numeric.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $("#OpDate").customDatePicker({ showOn: "focus" });
			$("#Debit").autoNumeric({ vMax: "99999999999999.99" });
			$("#Credit").autoNumeric({ vMax: "99999999999999.99" });
        });

    </script>
</head>

<body>

<?php include(VIEW . "main/menu.php"); ?>
<?php if (isset($error)) { ?>
<div class="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
<div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php } ?>
<br />

<fieldset>
	<legend><span class="bold">Entry Saldo Awal Akuntansi</span></legend>

	<form action="<?php print($helper->site_url("accounting.obal/edit/" . $openingBalance->Id)) ?>" method="post">
		<table cellpadding="0" cellspacing="0" class="tablePadding">
			<tr>
				<td class="right"><label for="AccountNo">Kode Akun : </label></td>
				<td><select id="AccountNo" name="AccountNo">
					<option value="">-- PILIH AKUN --</option>
					<?php
					$prevParentId = null;
					foreach ($accounts as $account) {
/*
						if ($prevParentId != $account->ParentId) {
							$prevParentId = $account->ParentId;
							$parent = $parentAccounts[$prevParentId];
							printf('<optgroup label="%s - %s"></optgroup>', $parent->AccNo, $parent->AccName);
						}
*/
						if ($account->Kode == $openingBalance->AccountNo) {
							printf('<option value="%s" selected="selected">%s - %s</option>', $account->Kode, $account->Kode, $account->Perkiraan);
						} else {
							printf('<option value="%s">%s - %s</option>', $account->Kode, $account->Kode, $account->Perkiraan);
						}
					}
					?>
				</select></td>
			</tr>
            <tr>
                <td class="right"><label for="OpDate">Tanggal Saldo : </label></td>
                <td><input type="text" id="OpDate" name="OpDate" value="<?php print($openingBalance->FormatDate(JS_DATE)); ?>"/></td>
            </tr>
            <tr>
                <td class="right"><label for="Debit">Jumlah Debet :</label></td>
                <td><input type="text" id="Debit" name="Debit" value="<?php print($openingBalance->DebitAmount); ?>" style="text-align: right;"/></td>
            </tr>
            <tr>
                <td class="right"><label for="Credit">Jumlah Kredit :</label></td>
                <td><input type="text" id="Credit" name="Credit" value="<?php print($openingBalance->CreditAmount); ?>" style="text-align: right"/></td>
            </tr>
			<tr>
				<td>&nbsp;</td>
				<td><button type="submit">Update</button>
                    <a href="<?php print($helper->site_url("accounting.obal")); ?>" class="button">Daftar Saldo Awal</a>
                </td>
			</tr>
		</table>
	</form>
</fieldset>

</body>
</html>
