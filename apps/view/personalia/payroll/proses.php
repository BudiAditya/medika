<!DOCTYPE HTML>
<?php
require_once (LIBRARY  . "gen_functions.php");
?>
<html>
<head>
	<title>ERAMEDIKA - Proses Hitung Gaji Karyawan</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
</head>
<body>
<?php include(VIEW . "main/menu.php"); ?>
<?php if (isset($error)) { ?>
<div class="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
<div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php } ?>
<br/>
<form id="frm" name="frmReport" method="post">
    <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
        <tr>
            <th colspan="4">PROSES HITUNG GAJI KARYAWAN</th>
        </tr>
        <tr>
            <th>Bisnis Unit</th>
            <th>Tahun</th>
            <th>Bulan</th>
            <th>Action</th>
        </tr>
        <tr>
            <td><select name="SbuId" id="SbuId">
                    <?php
                    /** @var $SbuList Sbu[] */
                    foreach ($SbuList as $sbu) {
                        if ($sbu->IsPayroll == 1) {
                            if ($sbu->Id == $SbuId) {
                                printf('<option value="%d" selected="selected"> %s - %s </option>', $sbu->Id, $sbu->Id, $sbu->SbuName);
                            } else {
                                printf('<option value="%d"> %s - %s </option>', $sbu->Id, $sbu->Id, $sbu->SbuName);
                            }
                        }
                    }
                    ?>
                </select>
            </td>
            <td><select name="Tahun" id="Tahun">
                    <option value="2018" <?php print($Tahun == 2018 ? 'selected="selected"' : '');?>> 2018 </option>
                    <option value="2019" <?php print($Tahun == 2019 ? 'selected="selected"' : '');?>> 2019 </option>
                    <option value="2020" <?php print($Tahun == 2020 ? 'selected="selected"' : '');?>> 2020 </option>
                    <option value="2021" <?php print($Tahun == 2021 ? 'selected="selected"' : '');?>> 2021 </option>
                </select>
            </td>
            <td><select name="Bulan" class="text2" id="Bulan">
                    <option value="1" <?php print($Bulan == 1 ? 'selected="selected"' : '');?>> 1 - Januari </option>
                    <option value="2" <?php print($Bulan == 2 ? 'selected="selected"' : '');?>> 2 - Februari </option>
                    <option value="3" <?php print($Bulan == 3 ? 'selected="selected"' : '');?>> 3 - Maret </option>
                    <option value="4" <?php print($Bulan == 4 ? 'selected="selected"' : '');?>> 4 - April </option>
                    <option value="5" <?php print($Bulan == 5 ? 'selected="selected"' : '');?>> 5 - M e i </option>
                    <option value="6" <?php print($Bulan == 6 ? 'selected="selected"' : '');?>> 6 - Juni </option>
                    <option value="7" <?php print($Bulan == 7 ? 'selected="selected"' : '');?>> 7 - Juli </option>
                    <option value="8" <?php print($Bulan == 8 ? 'selected="selected"' : '');?>> 8 - Agustus </option>
                    <option value="9" <?php print($Bulan == 9 ? 'selected="selected"' : '');?>> 9 - September </option>
                    <option value="10" <?php print($Bulan == 10 ? 'selected="selected"' : '');?>> 10 - Oktober </option>
                    <option value="11" <?php print($Bulan == 11 ? 'selected="selected"' : '');?>> 11 - Nopember </option>
                    <option value="12" <?php print($Bulan == 12 ? 'selected="selected"' : '');?>> 12 - Desember </option>
                </select>
            </td>
            <td align="center"><button type="submit" formaction="<?php print($helper->site_url("personalia/payroll/proses")); ?>"><b>Proses</b></button></td>
        </tr>
    </table>
</form>
</body>
</html>
