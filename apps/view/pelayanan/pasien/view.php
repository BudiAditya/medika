<!DOCTYPE HTML>
<?php /** @var $pasien Pasien */ ?>
<html>
<head>
	<title>ERAMEDIKA - View Data Pasien</title>
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
	<legend><b>View Data Pasien</b></legend>
    <table cellpadding="2" cellspacing="1">
        <tr>
            <td>Jenis Kelamin</td>
            <td><select id="Jkelamin" name="Jkelamin" disabled style="width: 100px">
                    <option value=""></option>
                    <option value="L" <?php print($pasien->Jkelamin == 'L' ? 'selected="selected"' : '');?>>Laki-Laki</option>
                    <option value="P" <?php print($pasien->Jkelamin == 'P' ? 'selected="selected"' : '');?>>Perempuan</option>
                </select>
            </td>
            <td>No Rekam Medik</td>
            <td><input type="text" class="text2" name="NoRm" id="NoRm" maxlength="15" size="15" value="<?php print($pasien->NoRm); ?>" disabled placeholder="Terisi Otomatis" required/></td>
        </tr>
        <tr>
            <td>Nama Pasien</td>
            <td colspan="3"><input type="text" class="text2" name="NmPasien" id="NmPasien" maxlength="50" size="50" value="<?php print($pasien->NmPasien); ?>" disabled/></td>
            <td>No. KTP (NIK)</td>
            <td><input type="text" class="text2" name="NoKtp" id="NoKtp" maxlength="20" size="20" value="<?php print($pasien->NoKtp); ?>" disabled/></td>
            <td>No. BPJS</td>
            <td><input type="text" class="text2" name="NoBpjs" id="NoBpjs" maxlength="20" size="20" value="<?php print($pasien->NoBpjs); ?>" disabled/></td>
        </tr>
        <tr>
            <td>Alamat Rumah</td>
            <td colspan="3"><input type="text" class="text2" name="Alamat" id="Alamat" maxlength="50" size="50" value="<?php print($pasien->Alamat); ?>" disabled/></td>
            <td>Lahir Di</td>
            <td><input type="text" class="text2" name="T4Lahir" id="T4Lahir" maxlength="50" size="20" value="<?php print($pasien->T4Lahir); ?>" disabled/></td>
            <td>Tgl Lahir</td>
            <td><input type="text" class="text2" name="TglLahir" id="TglLahir" maxlength="10" size="20" value="<?php print($pasien->FormatTglLahir(JS_DATE));?>" disabled/></td>
            <td>Usia</td>
            <td><input type="text" class="text2" name="Umur" id="Umur" size="15" value="<?php print($pasien->Umur);?>" disabled/></td>
        </tr>
        <tr>
            <td>Gol. Darah</td>
            <td><input type="text" class="text2" name="GolDarah" id="GolDarah" maxlength="5" size="10" value="<?php print($pasien->GolDarah); ?>" disabled/></td>
            <td>Status Kawin</td>
            <td><select id="StsKawin" name="StsKawin" disabled>
                    <option value="TK" <?php print($pasien->StsKawin == 'TK' ? 'selected="selected"' : '');?>>Belum Kawin</option>
                    <option value="K0" <?php print($pasien->StsKawin == 'K0' ? 'selected="selected"' : '');?>>Kawin/0 Anak</option>
                    <option value="K1" <?php print($pasien->StsKawin == 'K1' ? 'selected="selected"' : '');?>>Kawin/1 Anak</option>
                    <option value="K2" <?php print($pasien->StsKawin == 'K2' ? 'selected="selected"' : '');?>>Kawin/2 Anak</option>
                    <option value="K3" <?php print($pasien->StsKawin == 'K3' ? 'selected="selected"' : '');?>>Kawin/3 Anak</option>
                </select>
            </td>
            <td>No. HP</td>
            <td><input type="text" class="text2" name="NoHp" id="NoHp" maxlength="50" size="20" value="<?php print($pasien->NoHp); ?>" disabled/></td>
            <td>Pekerjaan</td>
            <td><select id="PekerjaanId" name="PekerjaanId" disabled>
                    <option value="0"></option>
                    <?php
                    while ($rsp = $rspekerjaan->FetchAssoc()) {
                        if ($pasien->PekerjaanId == $rsp["id"]) {
                            printf('<option value="%d" selected="selected">%s</option>', $rsp["id"], $rsp["pekerjaan"]);
                        }else{
                            printf('<option value="%d">%s</option>',$rsp["id"],$rsp["pekerjaan"]);
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Tinggi Badan</td>
            <td><input type="text" class="text2" name="TgiBadan" id="TgiBadan" maxlength="5" size="7" value="<?php print($pasien->TgiBadan); ?>" disabled/> CM</td>
            <td>Berat Badan</td>
            <td><input type="text" class="text2" name="BrtBadan" id="BrtBadan" maxlength="5" size="8" value="<?php print($pasien->BrtBadan); ?>" disabled/> KG</td>
            <td>Nama Ibu</td>
            <td><input type="text" class="text2" name="NmIbu" id="NmIbu" maxlength="50" size="20" value="<?php print($pasien->NmIbu); ?>" disabled/></td>
            <td>Jenis Pasien</td>
            <td><select id="JnsPasien" name="JnsPasien" style="width: 125px" disabled>
                    <option value="0"></option>
                    <option value="1" <?php print($pasien->JnsPasien == 1 ? 'selected="selected"' : '');?>>1 - Umum</option>
                    <option value="2" <?php print($pasien->JnsPasien == 2 ? 'selected="selected"' : '');?>>2 - BPJS</option>
                    <option value="3" <?php print($pasien->JnsPasien == 3 ? 'selected="selected"' : '');?>>3 - Asuransi Lain</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Pernah Operasi</td>
            <td colspan="3"><input type="text" class="text2" name="PernahOperasi" id="PernahOperasi" maxlength="50" size="50" value="<?php print($pasien->PernahOperasi); ?>" disabled/></td>
            <td>No. KK</td>
            <td><input type="text" class="text2" name="NoKK" id="NoKK" maxlength="20" size="20" value="<?php print($pasien->NoKK); ?>" disabled/></td>
            <?php
            if ($pasien->StsPasien == 0){
                print("<td colspan='2'><font color='red'><blink>**Pasien Sudah Meninggal**</blink></font></td>");
            }
            ?>
        </tr>
        <tr>
            <td>Riwayat Alergi</td>
            <td colspan="3"><input type="text" class="text2" name="RiwayatAlergi" id="RiwayatAlergi" maxlength="50" size="50" value="<?php print($pasien->RiwayatAlergi); ?>" disabled/></td>
        </tr>
        <tr>
            <td>R.P. Keluarga</td>
            <td colspan="3"><input type="text" class="text2" name="RpKeluarga" id="RpKeluarga" maxlength="50" size="50" value="<?php print($pasien->RpKeluarga); ?>" disabled/></td>
        </tr>
    </table>
    <div align="center">
        <a href="<?php print($helper->site_url("pelayanan.pasien")); ?>" class="button">Daftar Pasien</a>
    </div>
</fieldset>
</body>
</html>
