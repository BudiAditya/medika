<!DOCTYPE HTML>
<?php /** @var $pasien Pasien */ ?>
<html>
<head>
	<title>ERAMEDIKA - Input Data Pasien Baru</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			var elements = ["NmPasien","NoKtp","NoBpjs","Jkelamin","Alamat","T4Lahir","TglLahir","GolDarah","NoHp","StsKawin","PekerjaanId","TgiBadan","BrtBadan","JnsPasien","PernahOperasi","RiwayatAlergi","RpKeluarga","NoKK","Simpan"];
			BatchFocusRegister(elements);
            $("#TglLahir").customDatePicker({ showOn: "focus" });
			//get autocode
            $("#Jkelamin").change(function () {
                var ins = $("#NmPasien").val();
                var jkl = this.value;
                if (ins != '' && jkl != '') {
                    var url = "<?php print($helper->site_url("pelayanan.pasien/getAutoNoRm/")); ?>" + jkl + '/' + ins;
                    $.get(url, function (data) {
                        $("#NoRm").val(data);
                    });
                }
            });

            $("#NmPasien").change(function () {
                var jkl = $("#Jkelamin").val();
                var ins = this.value;
                if (ins != '' && jkl != '') {
                    var url = "<?php print($helper->site_url("pelayanan.pasien/getAutoNoRm/")); ?>" + jkl + '/' + ins;
                    $.get(url, function (data) {
                        $("#NoRm").val(data);
                    });
                }
            });

            //check umur
            $("#TglLahir").change(function () {
                var url = "<?php print($helper->site_url("pelayanan.pasien/checkUmur/")); ?>" + this.value;
                $.get(url, function (data) {
                    $("#Umur").val(data);
                });
            });
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
	<legend><b>Input Data Pasien Baru</b></legend>
	<form id="frm" action="<?php print($helper->site_url("pelayanan.pasien/add")); ?>" method="post">
		<table cellpadding="2" cellspacing="1">
            <tr>
                <td colspan="12" align="center"><font color="red"><strong><blink>** Ingat! -NAMA PASIEN- dan -JENIS KELAMIN- tidak boleh salah input! (Tidak bisa diubah lagi setelah data di-Simpan) **</blink></strong></font> </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td><select id="Jkelamin" name="Jkelamin" required style="width: 100px">
                        <option value=""></option>
                        <option value="L" <?php print($pasien->Jkelamin == 'L' ? 'selected="selected"' : '');?>>Laki-Laki</option>
                        <option value="P" <?php print($pasien->Jkelamin == 'P' ? 'selected="selected"' : '');?>>Perempuan</option>
                    </select>
                </td>
                <td>No. R M</td>
                <td><input type="text" class="text2" name="NoRm" id="NoRm" maxlength="15" size="15" value="<?php print($pasien->NoRm); ?>" readonly placeholder="Terisi Otomatis" required/></td>
            </tr>
			<tr>
				<td>Nama Pasien</td>
				<td colspan="3"><input type="text" class="text2" name="NmPasien" id="NmPasien" maxlength="50" size="50" value="<?php print($pasien->NmPasien); ?>" required placeholder="Nama Lengkap (sesuai KTP)"/></td>
                <td>No. KTP (NIK)</td>
                <td><input type="text" class="text2" name="NoKtp" id="NoKtp" maxlength="20" size="20" value="<?php print($pasien->NoKtp); ?>" required/></td>
                <td>No. BPJS</td>
                <td><input type="text" class="text2" name="NoBpjs" id="NoBpjs" maxlength="20" size="20" value="<?php print($pasien->NoBpjs); ?>" required/></td>
			</tr>
            <tr>
                <td>Alamat Rumah</td>
                <td colspan="3"><input type="text" class="text2" name="Alamat" id="Alamat" maxlength="50" size="50" value="<?php print($pasien->Alamat); ?>"/></td>
                <td>Lahir Di</td>
                <td><input type="text" class="text2" name="T4Lahir" id="T4Lahir" maxlength="50" size="20" value="<?php print($pasien->T4Lahir); ?>"/></td>
                <td>Tgl Lahir</td>
                <td><input type="text" class="text2" name="TglLahir" id="TglLahir" maxlength="10" size="20" value="<?php print($pasien->FormatTglLahir(JS_DATE));?>" required/></td>
                <td>Usia</td>
                <td><input type="text" class="text2" name="Umur" id="Umur" size="15" value="<?php print($pasien->Umur);?>" readonly/></td>
            </tr>
            <tr>
                <td>Gol. Darah</td>
                <td><input type="text" class="text2" name="GolDarah" id="GolDarah" maxlength="5" size="7" value="<?php print($pasien->GolDarah); ?>" required/></td>
                <td>Status Kawin</td>
                <td><select id="StsKawin" name="StsKawin" required style="width: 115px">
                        <option value="TK" <?php print($pasien->StsKawin == 'TK' ? 'selected="selected"' : '');?>>Belum Kawin</option>
                        <option value="K0" <?php print($pasien->StsKawin == 'K0' ? 'selected="selected"' : '');?>>Kawin/0 Anak</option>
                        <option value="K1" <?php print($pasien->StsKawin == 'K1' ? 'selected="selected"' : '');?>>Kawin/1 Anak</option>
                        <option value="K2" <?php print($pasien->StsKawin == 'K2' ? 'selected="selected"' : '');?>>Kawin/2 Anak</option>
                        <option value="K3" <?php print($pasien->StsKawin == 'K3' ? 'selected="selected"' : '');?>>Kawin/3 Anak</option>
                    </select>
                </td>
                <td>No. HP</td>
                <td><input type="text" class="text2" name="NoHp" id="NoHp" maxlength="50" size="20" value="<?php print($pasien->NoHp); ?>"/></td>
                <td>Pekerjaan</td>
                <td><select id="PekerjaanId" name="PekerjaanId" required style="width: 125px">
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
                <td><input type="text" class="text2" name="TgiBadan" id="TgiBadan" maxlength="5" size="7" value="<?php print($pasien->TgiBadan); ?>"/> CM</td>
                <td>Berat Badan</td>
                <td><input type="text" class="text2" name="BrtBadan" id="BrtBadan" maxlength="5" size="8" value="<?php print($pasien->BrtBadan); ?>"/> KG</td>
                <td>Nama Ibu</td>
                <td><input type="text" class="text2" name="NmIbu" id="NmIbu" maxlength="50" size="20" value="<?php print($pasien->NmIbu); ?>" required/></td>
                <td>Jenis Pasien</td>
                <td><select id="JnsPasien" name="JnsPasien" style="width: 125px">
                        <option value="0"></option>
                        <option value="1" <?php print($pasien->JnsPasien == 1 ? 'selected="selected"' : '');?>>1 - Umum</option>
                        <option value="2" <?php print($pasien->JnsPasien == 2 ? 'selected="selected"' : '');?>>2 - BPJS</option>
                        <option value="3" <?php print($pasien->JnsPasien == 3 ? 'selected="selected"' : '');?>>3 - Asuransi Lain</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Pernah Operasi</td>
                <td colspan="3"><input type="text" class="text2" name="PernahOperasi" id="PernahOperasi" maxlength="50" size="50" value="<?php print($pasien->PernahOperasi); ?>" placeholder="Diisi jika pernah dioperasi"/></td>
                <td>No. KK</td>
                <td><input type="text" class="text2" name="NoKK" id="NoKK" maxlength="20" size="20" value="<?php print($pasien->NoKK); ?>" placeholder="Nomor KK (16 digit)"/></td>
            </tr>
            <tr>
                <td>Riwayat Alergi</td>
                <td colspan="3"><input type="text" class="text2" name="RiwayatAlergi" id="RiwayatAlergi" maxlength="50" size="50" value="<?php print($pasien->RiwayatAlergi); ?>" placeholder="Diisi jika ada riwayat alergi"/></td>
            </tr>
            <tr>
                <td>R.P. Keluarga</td>
                <td colspan="3"><input type="text" class="text2" name="RpKeluarga" id="RpKeluarga" maxlength="50" size="50" value="<?php print($pasien->RpKeluarga); ?>" placeholder="Diisi riwayat penyakit keluarga (jika ada)"/></td>
            </tr>
        </table>
        <div align="center">
            <button id="Simpan" type="submit"><b>Simpan Data</b></button>
            <a href="<?php print($helper->site_url("pelayanan.pasien")); ?>"><b>Daftar Pasien</b></a>
        </div>
	</form>
</fieldset>
</body>
</html>
