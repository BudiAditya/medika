<!DOCTYPE HTML>
<?php
/** @var $pasien Pasien */
/** @var $perawatan Perawatan */
/** @var $poli Poliklinik[] */
/** @var $dokter Dokter[] */
/** @var $petugas Karyawan[] */
?>
<html>
<head>
	<title>ERAMEDIKA - Registrasi Kunjungan Pasien</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>

    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery.easyui.min.js")); ?>"></script>
    <!-- easyui themes -->
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-themes/default/easyui.css")); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-themes/icon.css")); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-themes/color.css")); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-demo/demo.css")); ?>"/>
    <style scoped>
        .f1{
            width:200px;
        }
    </style>

    <style type="text/css">
        #fd{
            margin:0;
            padding:5px 10px;
        }
        .ftitle{
            font-size:14px;
            font-weight:bold;
            padding:5px 0;
            margin-bottom:10px;
            border-bottom:1px solid #ccc;
        }
        .fitem{
            margin-bottom:5px;
        }
        .fitem label{
            display:inline-block;
            width:100px;
        }
        .numberbox .textbox-text{
            text-align: right;
            color: blue;
        }
    </style>

    <script type="text/javascript">
        $(document).ready(function() {
            //var elements = ["NmPasien","NoKtp","NoBpjs","Jkelamin","Alamat","T4Lahir","TglLahir","GolDarah","NoHp","StKawin","PekerjaanId","NmPjawab","NoKtpPjawab","NoHpPjawab","AlamatPjawab","HubPjawab","Simpan"];
            //BatchFocusRegister(elements);
            $("#TglMasuk").customDatePicker({ showOn: "focus" });
        });
        var urz = '<?php print($helper->site_url("pelayanan.perawatan/GetJSonListPenyakit"));?>';
        var urx = '<?php print($helper->site_url("pelayanan.perawatan/GetJSonInActivePatient"));?>';
        $(function() {
            $('#NoRm1').combogrid({
                panelWidth: 600,
                url: urx,
                idField: 'no_rm',
                textField: 'no_rm',
                mode: 'remote',
                fitColumns: true,
                columns: [[
                    {field: 'no_rm', title: 'No. RekMed', width: 30},
                    {field: 'nm_pasien', title: 'Nama Pasien', width: 100}
                ]],
                onSelect: function (index, row) {
                    $("#NoRm").val(row.no_rm);
                    $("#NmPasien").val(row.nm_pasien);
                    $("#NoKtp").val(row.no_ktp);
                    $("#NoBpjs").val(row.no_bpjs);
                    $("#Jkelamin").val(row.jkelamin);
                    $("#Alamat").val(row.alamat);
                    $("#T4Lahir").val(row.t4_lahir);
                    $("#TglLahir").val(row.tgl_lahir);
                    $("#Umur").val(row.umur);
                    $("#GolDarah").val(row.gol_darah);
                    $("#StsKawin").val(row.sts_kawin_desc);
                    $("#NoHp").val(row.no_hp);
                    $("#NmPekerjaan").val(row.nm_pekerjaan);
                    $("#TgiBadan").val(row.tgi_badan);
                    $("#BrtBadan").val(row.brt_badan);
                }
            });

            $('#KdUtama').combogrid({
                panelWidth: 600,
                url: urz,
                idField: 'kd_penyakit',
                textField: 'kd_penyakit',
                mode: 'remote',
                fitColumns: true,
                columns: [[
                    {field: 'kd_penyakit', title: 'Kode', width: 50},
                    {field: 'nm_penyakit', title: 'Nama Penyakit', width: 150},
                    {field: 'ciri_ciri', title: 'Ciri Ciri', width: 200}
                ]],
                onSelect: function (index, row) {
                    var dut = $("#DiagnosaUtama").val();
                    if (dut.trim() == ''){
                        $("#DiagnosaUtama").val(row.nm_penyakit);
                    }
                }
            });

            $('#KdKedua').combogrid({
                panelWidth: 600,
                url: urz,
                idField: 'kd_penyakit',
                textField: 'kd_penyakit',
                mode: 'remote',
                fitColumns: true,
                columns: [[
                    {field: 'kd_penyakit', title: 'Kode', width: 30},
                    {field: 'nm_penyakit', title: 'Nama Penyakit', width: 150},
                    {field: 'ciri_ciri', title: 'Ciri Ciri', width: 200}
                ]],
                onSelect: function (index, row) {
                    var dkd = $("#DiagnosaKedua").val();
                    if (dkd.trim() == ''){
                        $("#DiagnosaKedua").val(row.nm_penyakit);
                    }
                }
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
	<legend><b>REGISTRASI KUNJUNGAN PASIEN</b></legend>
    <table cellpadding="2" cellspacing="1">
        <tr>
            <td>Nomor RM</td>
            <td><input class="easyui-combogrid" id="NoRm1" name="NoRm1" style="width: 150px" autofocus/></td>
            <td><a href="<?php print($helper->site_url("pelayanan.pasien/add")); ?>" class="button"><strong>Pasien Baru</strong></a></td>
        </tr>
        <tr>
            <td>Nama Pasien</td>
            <td colspan="3"><input type="text" class="bold" name="NmPasien" id="NmPasien" maxlength="50" size="50" disabled/></td>
            <td>No. KTP</td>
            <td><input type="text" class="bold" name="NoKtp" id="NoKtp" maxlength="20" size="20" disabled/></td>
            <td>No. BPJS</td>
            <td><input type="text" class="bold" name="NoBpjs" id="NoBpjs" maxlength="20" size="20" disabled/></td>
            <td>L/P</td>
            <td><select class="bold" id="Jkelamin" name="Jkelamin" style="width:127px;" disabled>
                    <option value=""></option>
                    <option value="L">Laki-Laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td colspan="3"><input type="text" class="bold" name="Alamat" id="Alamat" maxlength="50" size="50" disabled/></td>
            <td>Lahir Di</td>
            <td><input type="text" class="bold" name="T4Lahir" id="T4Lahir" maxlength="50" size="20" disabled/></td>
            <td>Tgl Lahir</td>
            <td><input type="text" class="bold" name="TglLahir" id="TglLahir" maxlength="10" size="20" disabled/></td>
            <td>Usia</td>
            <td><input type="text" class="bold" name="Umur" id="Umur" size="14" disabled/></td>
        </tr>
        <tr>
            <td>Gol. Darah</td>
            <td><input type="text" class="bold" name="GolDarah" id="GolDarah" maxlength="5" size="10" disabled/></td>
            <td>Sts Perkawinan</td>
            <td><input type="text" class="bold" name="StsKawinDesc" id="StsKawinDesc" maxlength="10" size="12" disabled/></td>
            <td>No. HP</td>
            <td><input type="text" class="bold" name="NoHp" id="NoHp" maxlength="50" size="20" disabled/></td>
            <td>Pekerjaan</td>
            <td><input type="text" class="bold" name="NmPekerjaan" id="NmPekerjaan" maxlength="50" size="20" disabled/></td>
        </tr>
    </table>
    <hr>
    <form id="frm" action="<?php print($helper->site_url("pelayanan.perawatan/addirect")); ?>" method="post">
        <input type="hidden" name="NoRm" id="NoRm"/>
    <table cellpadding="2" cellspacing="1">
        <tr>
            <td>No. Register</td>
            <td><input type="text" class="bold" name="NoReg" id="NoReg" maxlength="15" size="15" value="<?php print($perawatan->NoReg); ?>" readonly placeholder="Auto" /></td>
            <td>Tinggi Badan</td>
            <td><input type="text" class="text2 right" name="TgiBadan" id="TgiBadan" maxlength="5" size="7" value="0"/> CM</td>
            <td>Berat Badan</td>
            <td><input type="text" class="text2 right" name="BrtBadan" id="BrtBadan" maxlength="5" size="8" value="0"/> KG</td>
            <td rowspan="8" valign="top">
                <table cellpadding="2" cellspacing="1">
                    <tr>
                        <td colspan="2"><strong><u>Data Penjamin Pasien Rawat Inap:</u></strong> </td>
                    </tr>
                    <tr>
                        <td>Nama Penjamin</td>
                        <td><input type="text" name="NmPjawab" id="NmPjawab" maxlength="50" size="30" value="<?php print($perawatan->NmPjawab); ?>"/></td>
                        <td>No KTP</td>
                        <td><input type="text" name="NoKtpPjawab" id="NoKtpPjawab" maxlength="50" size="20" value="<?php print($perawatan->NoKtpPjawab); ?>"/></td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td colspan="3"><input type="text" name="AlamatPjawab" id="AlamatPjawab" maxlength="150" size="67" value="<?php print($perawatan->AlamatPjawab); ?>"/></td>
                    </tr>
                    <tr>
                        <td>Hubungan</td>
                        <td><select name="HubPjawab" id="HubPjawab">
                                <option value="0"></option>
                                <option value="1" <?php print($perawatan->HubPjawab == 1 ? "selected = 'selected'" : "");?>> 1 - Orang Tua </option>
                                <option value="2" <?php print($perawatan->HubPjawab == 2 ? "selected = 'selected'" : "");?>> 2 - Anak </option>
                                <option value="3" <?php print($perawatan->HubPjawab == 3 ? "selected = 'selected'" : "");?>> 3 - Keluarga </option>
                                <option value="4" <?php print($perawatan->HubPjawab == 4 ? "selected = 'selected'" : "");?>> 4 - Lain-lain </option>
                            </select>
                        </td>
                        <td>No HP</td>
                        <td><input type="text" name="NoHpPjawab" id="NoHpPjawab" maxlength="50" size="20" value="<?php print($perawatan->NoHpPjawab); ?>"/></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>Cara Bayar</td>
            <td><select id="CaraBayar" name="CaraBayar" style="width: 130px">
                    <option value="0"></option>
                    <option value="1" <?php print($perawatan->CaraBayar == 1 ? 'selected="selected"' : '');?>>1 - Umum</option>
                    <option value="2" <?php print($perawatan->CaraBayar == 2 ? 'selected="selected"' : '');?>>2 - BPJS</option>
                    <option value="3" <?php print($perawatan->CaraBayar == 3 ? 'selected="selected"' : '');?>>3 - Asuransi Lain</option>
                </select>
            </td>
            <td>Jenis Rujukan</td>
            <td><select id="JnsRujukan" name="JnsRujukan" style="width: 130px">
                    <option value="0"></option>
                    <option value="1" <?php print($perawatan->JnsRujukan == 1 ? 'selected="selected"' : '');?>>1 - Datang Sendiri</option>
                    <option value="2" <?php print($perawatan->JnsRujukan == 2 ? 'selected="selected"' : '');?>>2 - Rujukan Dokter</option>
                    <option value="3" <?php print($perawatan->JnsRujukan == 3 ? 'selected="selected"' : '');?>>3 - Rujukan R.S. Lain</option>
                </select>
            </td>
            <td>Asal Rujukan</td>
            <td><input type="text" name="AsalRujukan" id="AsalRujukan" maxlength="150" size="20" value="<?php print($perawatan->AsalRujukan); ?>"/></td>
        </tr>
        <tr>
            <td>Jenis Rawat</td>
            <td><select id="JnsRawat" name="JnsRawat" style="width: 130px">
                    <option value="0"></option>
                    <option value="1" <?php print($perawatan->JnsRawat == 1 ? 'selected="selected"' : '');?>>1 - Jalan (Poliklinik)</option>
                    <option value="7" <?php print($perawatan->JnsRawat == 7 ? 'selected="selected"' : '');?>>7 - Jalan (Poli Spesialis)</option>
                    <option value="8" <?php print($perawatan->JnsRawat == 8 ? 'selected="selected"' : '');?>>8 - Jalan (Poli Radiologi)</option>
                    <option value="9" <?php print($perawatan->JnsRawat == 9 ? 'selected="selected"' : '');?>>9 - Jalan (Poli Kebidanan)</option>
                    <option value="10" <?php print($perawatan->JnsRawat == 10 ? 'selected="selected"' : '');?>>10- Persalinan</option>
                    <option value="2" <?php print($perawatan->JnsRawat == 2 ? 'selected="selected"' : '');?>>2 - I G D</option>
                    <option value="3" <?php print($perawatan->JnsRawat == 3 ? 'selected="selected"' : '');?>>3 - Rawat (Kelas 3)</option>
                    <option value="4" <?php print($perawatan->JnsRawat == 4 ? 'selected="selected"' : '');?>>4 - Rawat (Kelas 2)</option>
                    <option value="5" <?php print($perawatan->JnsRawat == 5 ? 'selected="selected"' : '');?>>5 - Rawat (Kelas 1)</option>
                    <option value="6" <?php print($perawatan->JnsRawat == 6 ? 'selected="selected"' : '');?>>6 - Rawat (VIP)</option>
                </select>
            </td>
            <td>Tgl Masuk</td>
            <td><input type="text" class="bold" name="TglMasuk" id="TglMasuk" maxlength="15" size="14" value="<?php print($perawatan->FormatTglMasuk(JS_DATE)); ?>"/></td>
            <td>Jam Masuk</td>
            <td><input type="text" class="bold" name="JamMasuk" id="JamMasuk" maxlength="5" size="5" value="<?php print($perawatan->JamMasuk); ?>"/></td>
        </tr>
        <tr>
            <td>Poliklinik</td>
            <td><select id="KdPoliklinik" name="KdPoliklinik" style="width: 130px">
                    <option value=""></option>
                    <?php
                    foreach ($poli as $poliklinik){
                        if ($perawatan->KdPoliklinik == $poliklinik->KdPoliklinik){
                            printf('<option value = "%s" selected="selected"> %s - %s </option>',$poliklinik->KdPoliklinik,$poliklinik->KdPoliklinik,$poliklinik->NmPoliklinik);
                        }else {
                            printf('<option value = "%s"> %s - %s </option>', $poliklinik->KdPoliklinik, $poliklinik->KdPoliklinik, $poliklinik->NmPoliklinik);
                        }
                    }
                    ?>
                </select>
            </td>
            <td>Kamar Rawat</td>
            <td><select id="KdKamar" name="KdKamar" style="width: 130px">
                    <option value="0"></option>
                    <?php
                    /** @var $kamar Kamar[] */
                    foreach ($kamar as $kamarlist){
                        if ($kamarlist->KdKamar == $perawatan->KdKamar){
                            printf('<option value = "%s" selected="selected"> %s - %s </option>',$kamarlist->KdKamar,$kamarlist->KdKamar,$kamarlist->NmKamar);
                        }else {
                            printf('<option value = "%s"> %s - %s </option>',$kamarlist->KdKamar,$kamarlist->KdKamar,$kamarlist->NmKamar);
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Petugas</td>
            <td><select id="KdPetugas" name="KdPetugas" style="width: 130px">
                    <option value=""></option>
                    <?php
                    foreach ($petugas as $karyawan){
                        if ($perawatan->KdPetugas == $karyawan->Nik){
                            printf('<option value = "%s" selected="selected"> %s </option>',$karyawan->Nik,$karyawan->Nama);
                        }else {
                            printf('<option value = "%s"> %s </option>', $karyawan->Nik,$karyawan->Nama);
                        }
                    }
                    ?>
                </select>
            </td>
            <td>Dokter</td>
            <td colspan="2"><select id="KdDokter" name="KdDokter" style="width: 200px">
                    <option value=""></option>
                    <?php
                    foreach ($dokter as $dtr){
                        if ($perawatan->KdDokter == $dtr->KdDokter){
                            printf('<option value = "%s" selected="selected"> %s </option>',$dtr->KdDokter,$dtr->NmDokter);
                        }else {
                            printf('<option value = "%s"> %s </option>', $dtr->KdDokter,$dtr->NmDokter);
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Keluhan</td>
            <td colspan="3"><input type="text" name="Keluhan" id="Keluhan" maxlength="150" size="65" value="<?php print($perawatan->Keluhan); ?>"/></td>
        </tr>
        <tr>
            <td>Diagnosa Utama</td>
            <td colspan="3"><input type="text" name="DiagnosaUtama" id="DiagnosaUtama" maxlength="150" size="65" value="<?php print($perawatan->DiagnosaUtama); ?>"/></td>
            <td colspan="2">Code ICD 10<sub>1</sub>
            <input class="easyui-combogrid" id="KdUtama" name="KdUtama" style="width: 200px" value="<?php print($perawatan->KdUtama); ?>" autofocus/></td>
        </tr>
        <tr>
            <td>Diagnosa Kedua</td>
            <td colspan="3"><input type="text" name="DiagnosaKedua" id="DiagnosaKedua" maxlength="150" size="65" value="<?php print($perawatan->DiagnosaKedua); ?>"/></td>
            <td colspan="2">Code ICD 10<sub>2</sub>
            <input class="easyui-combogrid" id="KdKedua" name="KdKedua" style="width: 200px" value="<?php print($perawatan->KdKedua); ?>" autofocus/></td>
        </tr>
        <tr>
            <td>Status</td>
            <td><select id="RegStatus" name="RegStatus" style="width: 130px">
                    <option value="1" <?php print($perawatan->RegStatus == 1 ? 'selected="selected"' : '');?>>1 - Masih Rawat</option>
                    <option value="2" <?php print($perawatan->RegStatus == 2 ? 'selected="selected"' : '');?>>2 - Sdh Pulang</option>
                </select>
            </td>
            <td>Tgl Keluar</td>
            <td><input type="text" name="TglKeluar" id="TglKeluar" maxlength="15" size="15" value="<?php print($perawatan->FormatTglKeluar(JS_DATE)); ?>" disabled/></td>
            <td>Jam Keluar</td>
            <td><input type="text" name="JamKeluar" id="JamKeluar" maxlength="5" size="5" value="<?php print($perawatan->JamKeluar); ?>" disabled/></td>
        </tr>
    </table>
    <hr>
    <div align="center">
        <button id="Simpan" type="submit"><b>Simpan Data</b></button>
        <a href="<?php print($helper->site_url("pelayanan.pasien")); ?>" class="button"><strong>Batal</strong></a>
        <a href="<?php print($helper->site_url("pelayanan.perawatan")); ?>" class="button"><strong>Daftar Pasien Dirawat</strong></a>
    </div>
    </form>
</fieldset>
</body>
</html>
