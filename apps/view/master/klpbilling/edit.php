<!DOCTYPE HTML>
<html>
<head>
    <title>ERAMEDIKA - Ubah Data Kelompok Billing</title>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            var elements = ["KdKlpBilling", "KlpBilling","Keterangan","Update"];
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
    <legend><b>Ubah Data Kelompok Billing</b></legend>
    <form id="frm" action="<?php print($helper->site_url("master.klpbilling/edit/".$klpbilling->Id)); ?>" method="post">
        <table cellpadding="2" cellspacing="1">
            <tr>
                <td>Kode</td>
                <td><input type="text" class="text2" name="KdKlpBilling" id="KdKlpBilling" maxlength="10" size="10" value="<?php print($klpbilling->KdKlpBilling); ?>" required/></td>
            </tr>
            <tr>
                <td>Kelompok Billing</td>
                <td><input type="text" class="text2" name="KlpBilling" id="KlpBilling" maxlength="50" size="50" value="<?php print($klpbilling->KlpBilling); ?>" required/></td>
            </tr>
            <tr>
                <td>Keterangan</td>
                <td><input type="text" class="text2" name="Keterangan" id="Keterangan" maxlength="50" size="50" value="<?php print($klpbilling->Keterangan); ?>"/></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <button type="submit" id="Update">Update</button>
                    <a href="<?php print($helper->site_url("master.klpbilling")); ?>" class="button">Daftar Kelompok Billing</a>
                </td>
            </tr>
        </table>
    </form>
</fieldset>
</body>
</html>
