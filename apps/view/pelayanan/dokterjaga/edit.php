<!DOCTYPE HTML>
<?php
/** @var $dokterjaga DokterJaga */
/** @var $dokter Dokter[] */
?>
<html>
<head>
    <title>ERAMEDIKA - Ubah Data Dokter Jaga</title>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            var elements = ["Tanggal", "KdDokter","Keterangan","Update"];
            BatchFocusRegister(elements);
            $("#Tanggal").customDatePicker({ showOn: "focus" });
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
    <legend><b>Ubah Data Dokter Jaga</b></legend>
    <form id="frm" action="<?php print($helper->site_url("pelayanan.dokterjaga/edit/".$dokterjaga->Id)); ?>" method="post">
        <table cellpadding="2" cellspacing="1">
            <tr>
                <td>Tanggal</td>
                <td><input type="text" class="text2" name="Tanggal" id="Tanggal" maxlength="20" size="15" value="<?php print($dokterjaga->FormatTanggal(JS_DATE)); ?>" required/></td>
            </tr>
            <tr>
                <td>Dokter Jaga</td>
                <td><select name="KdDokter" id="KdDokter" style="width: 275px" required>
                        <option value=""></option>
                        <?php
                        foreach ($dokter as $dok) {
                            if ($dokterjaga->KdDokter == $dok->KdDokter) {
                                printf("<option value='%s' selected='selected'> %s - %s </option>", $dok->KdDokter, $dok->KdDokter, $dok->NmDokter);
                            } else {
                                printf("<option value='%s'> %s - %s </option>", $dok->KdDokter, $dok->KdDokter, $dok->NmDokter);
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Keterangan</td>
                <td><input type="text" class="text2" name="Keterangan" id="Keterangan" maxlength="50" size="50" value="<?php print($dokterjaga->Keterangan); ?>"/></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <button type="submit" id="Update">Update</button>
                    <a href="<?php print($helper->site_url("pelayanan.dokterjaga")); ?>" class="button">Daftar Dokter Jaga</a>
                </td>
            </tr>
        </table>
    </form>
</fieldset>
</body>
</html>
