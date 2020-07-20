<!DOCTYPE HTML>
<?php /** @var $jnstransaksi JnsTransaksi */ /** @var $klptransaksi Klptransaksi[] */ ?>
<html>
<head>
    <title>ERAMEDIKA - Ubah Data Jenis Transaksi</title>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            var elements = ["KdJnsTransaksi","KdKlpTransaksi","JnsTransaksi","PosisiKas","Update"];
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
    <legend><b>Ubah Data Jenis Transaksi</b></legend>
    <form id="frm" action="<?php print($helper->site_url("master.jnstransaksi/edit/".$jnstransaksi->Id)); ?>" method="post">
        <table cellpadding="2" cellspacing="1">
            <tr>
                <td>Kode</td>
                <td><input type="text" class="text2" name="KdJnsTransaksi" id="KdJnsTransaksi" maxlength="10" size="10" value="<?php print($jnstransaksi->KdJnsTransaksi); ?>" required readonly/></td>
            </tr>
            <tr>
                <td>Kelompok Transaksi</td>
                <td><select name="KdKlpTransaksi" class="text2" id="KdKlpTransaksi" required>
                        <option value=""></option>
                        <?php
                        foreach ($klptransaksi as $klp) {
                            if ($jnstransaksi->KdKlpTransaksi == $klp->KdKlpTransaksi) {
                                printf('<option value="%s" selected="selected">%s</option>', $klp->KdKlpTransaksi, $klp->KlpTransaksi);
                            } else {
                                printf('<option value="%s">%s</option>', $klp->KdKlpTransaksi, $klp->KlpTransaksi);
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Jenis Transaksi</td>
                <td colspan="3"><input type="text" class="text2" name="JnsTransaksi" id="JnsTransaksi" maxlength="50" size="50" value="<?php print($jnstransaksi->JnsTransaksi); ?>" required/></td>
            </tr>
            <tr>
                <td>Posisi Kas</td>
                <td><select class="text2" name="PosisiKas" id="PosisiKas" required>
                        <option value=""></option>
                        <option value="1" <?php print($jnstransaksi->PosisiKas == 1 ? 'selected = "selected"' : '') ?> > 1 - Masuk </option>
                        <option value="2" <?php print($jnstransaksi->PosisiKas == 2 ? 'selected = "selected"' : '') ?> > 2 - Keluar </option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td colspan="3">
                    <button id="Update" type="submit">Update</button>
                    <a href="<?php print($helper->site_url("master.jnstransaksi")); ?>" class="button">Daftar Jenis Transaksi</a>
                </td>
            </tr>
        </table>
    </form>
</fieldset>
</body>
</html>
