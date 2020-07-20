<!DOCTYPE HTML>
<html>
<head>
	<title>ERASYS - View Data Karyawan</title>
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
<fieldset>
	<legend><b>View Data Karyawan</b></legend>
    <table cellpadding="2" cellspacing="1">
        <tr>
            <td>
                <table cellpadding="2" cellspacing="1">
                    <tr>
                        <td>N I K</td>
                        <td><input type="text" class="text2" name="Nik" id="Nik" maxlength="10" size="20" value="<?php print($karyawan->Nik); ?>" disabled/></td>
                    </tr>
                    <tr>
                        <td>Nama Lengkap</td>
                        <td colspan="2"><input type="text" class="text2" name="Nama" id="Nama" maxlength="50" size="50" value="<?php print($karyawan->Nama); ?>" disabled /></td>
                        <td>Nama Panggilan</td>
                        <td colspan="2"><input type="text" class="text2" name="NmPanggilan" id="NmPanggilan" maxlength="50" size="20" value="<?php print($karyawan->NmPanggilan); ?>" disabled /></td>
                    </tr>
                    <tr>
                        <td>Bagian</td>
                        <td colspan="2"><select name="DeptId" class="text2" id="DeptId" disabled>
                                <option value=""></option>
                                <?php
                                foreach ($depts as $dept) {
                                    if ($dept->Id == $karyawan->DeptId) {
                                        printf('<option value="%d" selected="selected">%s - %s</option>', $dept->Id, $dept->DeptCd, $dept->DeptName);
                                    } else {
                                        printf('<option value="%d">%s - %s</option>', $dept->Id, $dept->DeptCd, $dept->DeptName);
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td>Jabatan</td>
                        <td colspan="2"><select name="Jabatan" class="text2" id="Jabatan" disabled>
                                <option value=""></option>
                                <option value="STF" <?php ($karyawan->Jabatan == "STF" ? print('selected = "selected"'):'');?>>Staf</option>
                                <option value="SPV" <?php ($karyawan->Jabatan == "SPV" ? print('selected = "selected"'):'');?>>Supervisor</option>
                                <option value="MGR" <?php ($karyawan->Jabatan == "MGR" ? print('selected = "selected"'):'');?>>Manager</option>
                                <option value="DIR" <?php ($karyawan->Jabatan == "DIR" ? print('selected = "selected"'):'');?>>Direktur</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Alamat</td>
                        <td colspan="4"><input type="text" class="text2" name="Alamat" id="Alamat" maxlength="250" size="50" value="<?php print($karyawan->Alamat); ?>" disabled/></td>
                    </tr>
                    <tr>
                        <td>HandPhone</td>
                        <td colspan="4"><input type="tel" class="text2" name="Handphone" id="Handphone" maxlength="50" size="20" value="<?php print($karyawan->Handphone); ?>" disabled/></td>
                    </tr>
                    <tr>
                        <td>Lahir di</td>
                        <td><input type="text" class="text2" name="T4Lahir" id="T4Lahir" maxlength="50" size="20" value="<?php print($karyawan->T4Lahir); ?>" disabled/></td>
                        <td>Tgl.Lahir</td>
                        <td><input type="text" class="text2" name="TglLahir" id="TglLahir" maxlength="10" size="15" value="<?php print($karyawan->FormatTglLahir(JS_DATE)); ?>" disabled/></td>
                        <td>Jns Kelamin</td>
                        <td><select name="Jkelamin" class="text2" id="Jkelamin" disabled style="width: 130px;">
                                <option value=""></option>
                                <option value="L" <?php ($karyawan->Jkelamin == "L" ? print('selected = "selected"'):'');?>>Laki-laki</option>
                                <option value="P" <?php ($karyawan->Jkelamin == "P" ? print('selected = "selected"'):'');?>>Perempuan</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Agama</td>
                        <td><select name="Agama" class="text2" id="Agama" style="width: 125px" disabled>
                                <option value=""></option>
                                <option value="Budha" <?php ($karyawan->Agama == "Budha" ? print('selected = "selected"'):'');?>>Budha</option>
                                <option value="Hindu" <?php ($karyawan->Agama == "Hindu" ? print('selected = "selected"'):'');?>>Hindu</option>
                                <option value="Islam" <?php ($karyawan->Agama == "Islam" ? print('selected = "selected"'):'');?>>Islam</option>
                                <option value="Katolik" <?php ($karyawan->Agama == "Katolik" ? print('selected = "selected"'):'');?>>Katolik</option>
                                <option value="Kristen" <?php ($karyawan->Agama == "Kristen" ? print('selected = "selected"'):'');?>>Kristen</option>
                            </select>
                        </td>
                        <td>Pendidikan</td>
                        <td><select name="Pendidikan" class="text2" id="Pendidikan" style="width: 100px;" disabled>
                                <option value=""></option>
                                <option value="SD" <?php ($karyawan->Pendidikan == "SD" ? print('selected = "selected"'):'');?>>SD</option>
                                <option value="SMP" <?php ($karyawan->Pendidikan == "SMP" ? print('selected = "selected"'):'');?>>SMP</option>
                                <option value="SMA" <?php ($karyawan->Pendidikan == "SMA" ? print('selected = "selected"'):'');?>>SMA</option>
                                <option value="Diploma" <?php ($karyawan->Pendidikan == "Diploma" ? print('selected = "selected"'):'');?>>Diploma</option>
                                <option value="Sarjana" <?php ($karyawan->Pendidikan == "Sarjana" ? print('selected = "selected"'):'');?>>Sarjana</option>
                            </select>
                        </td>
                        <td>Sts. Kawin</td>
                        <td><select name="Status" class="text2" id="Status" style="width: 130px;" disabled>
                                <option value=""></option>
                                <option value="TK" <?php ($karyawan->Status == "TK" ? print('selected = "selected"'):'');?>>TK - Tidak Kawin</option>
                                <option value="K0" <?php ($karyawan->Status == "K0" ? print('selected = "selected"'):'');?>>K/0 - Kawin 0 Anak</option>
                                <option value="K1" <?php ($karyawan->Status == "K1" ? print('selected = "selected"'):'');?>>K/1 - Kawin 1 Anak</option>
                                <option value="K2" <?php ($karyawan->Status == "K2" ? print('selected = "selected"'):'');?>>K/2 - Kawin 2 Anak</option>
                                <option value="K3" <?php ($karyawan->Status == "K3" ? print('selected = "selected"'):'');?>>K/3 - Kawin 3 Anak</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>No. BPJS</td>
                        <td><input type="text" class="text2" name="BpjsNo" id="BpjsNo" maxlength="15" size="20" value="<?php print($karyawan->BpjsNo); ?>" disabled/></td>
                        <td>Tgl. BPJS</td>
                        <td><input type="text" class="text2" name="BpjsDate" id="BpjsDate" maxlength="15" size="15" value="<?php print($karyawan->FormatBpjsDate(JS_DATE)); ?>" disabled/></td>
                    </tr>
                    <tr>
                        <td>Tgl. Mulai Kerja</td>
                        <td><input type="text" class="text2" name="MulaiKerja" id="MulaiKerja" maxlength="20" size="15" value="<?php print($karyawan->FormatMulaiKerja(JS_DATE)); ?>" disabled/></td>
                        <td>Status Kerja</td>
                        <td>
                            <select name="StKerja" class="text2" id="StKerja" disabled>
                                <option value="0"></option>
                                <?php
                                while ($stk = $rsstk->FetchAssoc()) {
                                    if($karyawan->StKerja == $stk["code"]) {
                                        printf("<option value='%d' selected='selected'> %s - %s </option>", $stk["code"], $stk["code"], $stk["short_desc"]);
                                    }else{
                                        printf("<option value='%d'> %s - %s </option>", $stk["code"], $stk["code"], $stk["short_desc"]);
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td>Masih Aktif</td>
                        <td><select name="IsAKtif" class="text2" id="IsAktif" disabled>
                                <option value="1" <?php print($karyawan->IsAktif == 1 ? 'selected="selected"' : ''); ?> > 1 - Aktif </option>
                                <option value="0" <?php print($karyawan->IsAktif == 0 ? 'selected="selected"' : ''); ?> > 0 - Non-Aktif </option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>No. Finger</td>
                        <td><input type="text" class="text2" name="FpNo" id="FpNo" maxlength="15" size="15" value="<?php print($karyawan->FpNo); ?>" disabled/></td>
                        <td>Tgl. Keluar</td>
                        <td><input type="text" class="text2" name="ResignDate" id="ResignDate" maxlength="15" size="15" value="<?php print($karyawan->FormatResignDate(JS_DATE)); ?>" disabled/></td>
                    </tr>
                    <tr>
                        <td>No. KTP</td>
                        <td><input type="text" class="text2" name="NikKtp" id="NikKtp" maxlength="20" size="20" value="<?php print($karyawan->NikKtp); ?>" disabled/></td>
                        <td>No. KK</td>
                        <td><input type="text" class="text2" name="NoKK" id="NoKK" maxlength="20" size="20" value="<?php print($karyawan->NoKK); ?>" disabled/></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td><a href="<?php print($helper->site_url("master.karyawan")); ?>" class="button">Daftar Karyawan</a></td>
                    </tr>
                </table>
            </td>
            <td>
                <table cellpadding="2" cellspacing="1">
                    <tr>
                        <td colspan="2">
                            <?php
                            printf('<img src="%s" width="240" height="250"/>',$helper->site_url($karyawan->Fphoto));
                            ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</fieldset>
</body>
</html>
