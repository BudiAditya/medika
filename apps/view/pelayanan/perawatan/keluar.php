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
    <title>ERAMEDIKA - Proses Pasien Pulang</title>
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
</head>
<body>
<?php include(VIEW . "main/menu.php"); ?>
<?php if (isset($error)) { ?>
    <div class="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
    <div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php } ?>

<br/>
<fieldset>
    <legend><b>PROSES PASIEN PULANG</b></legend>
    <table cellpadding="2" cellspacing="1">
        <tr>
            <td>Nomor RM</td>
            <td><input type="text" class="bold" name="NoRm1" id="NoRm1" maxlength="15" size="15" value="<?php print($pasien->NoRm); ?>" disabled /></td>
        </tr>
        <tr>
            <td>Nama Pasien</td>
            <td colspan="3"><input type="text" class="bold" name="NmPasien" id="NmPasien" maxlength="50" size="50" value="<?php print($pasien->NmPasien); ?>" disabled/></td>
            <td>No. KTP</td>
            <td><input type="text" class="bold" name="NoKtp" id="NoKtp" maxlength="20" size="20" value="<?php print($pasien->NoKtp); ?>" disabled/></td>
            <td>No. BPJS</td>
            <td><input type="text" class="bold" name="NoBpjs" id="NoBpjs" maxlength="20" size="20" value="<?php print($pasien->NoBpjs); ?>" disabled/></td>
            <td>L/P</td>
            <td><select class="bold" id="Jkelamin" name="Jkelamin" style="width:127px;" disabled>
                    <option value=""></option>
                    <option value="L" <?php print($pasien->Jkelamin == 'L' ? 'selected="selected"' : '');?>>Laki-Laki</option>
                    <option value="P" <?php print($pasien->Jkelamin == 'P' ? 'selected="selected"' : '');?>>Perempuan</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td colspan="3"><input type="text" class="bold" name="Alamat" id="Alamat" maxlength="50" size="50" value="<?php print($pasien->Alamat); ?>" disabled/></td>
            <td>Lahir Di</td>
            <td><input type="text" class="bold" name="T4Lahir" id="T4Lahir" maxlength="50" size="20" value="<?php print($pasien->T4Lahir); ?>" disabled/></td>
            <td>Tgl Lahir</td>
            <td><input type="text" class="bold" name="TglLahir" id="TglLahir" maxlength="10" size="20" value="<?php print($pasien->FormatTglLahir(JS_DATE));?>" disabled/></td>
            <td>Usia</td>
            <td><input type="text" class="bold" name="Umur" id="Umur" size="14" value="<?php print($pasien->Umur);?>" disabled/></td>
        </tr>
        <tr>
            <td>Gol. Darah</td>
            <td><input type="text" class="bold" name="GolDarah" id="GolDarah" maxlength="5" size="10" value="<?php print($pasien->GolDarah); ?>" disabled/></td>
            <td>Sts Perkawinan</td>
            <td><input type="text" class="bold" name="StsKawinDesc" id="StsKawinDesc" maxlength="10" size="12" value="<?php print($pasien->StsKawinDesc); ?>" disabled/></td>
            <td>No. HP</td>
            <td><input type="text" class="bold" name="NoHp" id="NoHp" maxlength="50" size="20" value="<?php print($pasien->NoHp); ?>" disabled/></td>
            <td>Pekerjaan</td>
            <td><input type="text" class="bold" name="NmPekerjaan" id="NmPekerjaan" maxlength="50" size="20" value="<?php print($pasien->Pekerjaan); ?>" disabled/></td>
        </tr>
    </table>
    <hr>
    <table cellpadding="2" cellspacing="1">
        <table cellpadding="2" cellspacing="1">
            <tr>
                <td>No. Register</td>
                <td><input type="text" class="bold" name="NoReg1" id="NoReg1" maxlength="15" size="15" value="<?php print($perawatan->NoReg); ?>" readonly placeholder="Auto" /></td>
                <td>Tinggi Badan</td>
                <td><input type="text" class="text2 right" name="TgiBadan" id="TgiBadan" maxlength="5" size="7" value="<?php print($pasien->TgiBadan); ?>" disabled/> CM</td>
                <td>Berat Badan</td>
                <td><input type="text" class="text2 right" name="BrtBadan" id="BrtBadan" maxlength="5" size="8" value="<?php print($pasien->BrtBadan); ?>" disabled/> KG</td>
                <td rowspan="8" valign="top">
                    <table cellpadding="2" cellspacing="1">
                        <tr>
                            <td colspan="2"><strong><u>Data Penjamin Pasien Rawat Inap:</u></strong> </td>
                        </tr>
                        <tr>
                            <td>Nama Penjamin</td>
                            <td><input type="text" name="NmPjawab" id="NmPjawab" maxlength="50" size="30" value="<?php print($perawatan->NmPjawab); ?>" disabled/></td>
                            <td>No KTP</td>
                            <td><input type="text" name="NoKtpPjawab" id="NoKtpPjawab" maxlength="50" size="20" value="<?php print($perawatan->NoKtpPjawab); ?>" disabled/></td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
                            <td colspan="3"><input type="text" name="AlamatPjawab" id="AlamatPjawab" maxlength="150" size="67" value="<?php print($perawatan->AlamatPjawab); ?>" disabled/></td>
                        </tr>
                        <tr>
                            <td>Hubungan</td>
                            <td><select name="HubPjawab" id="HubPjawab" disabled>
                                    <option value="0"></option>
                                    <option value="1" <?php print($perawatan->HubPjawab == 1 ? "selected = 'selected'" : "");?>> 1 - Orang Tua </option>
                                    <option value="2" <?php print($perawatan->HubPjawab == 2 ? "selected = 'selected'" : "");?>> 2 - Anak </option>
                                    <option value="3" <?php print($perawatan->HubPjawab == 3 ? "selected = 'selected'" : "");?>> 3 - Keluarga </option>
                                    <option value="4" <?php print($perawatan->HubPjawab == 4 ? "selected = 'selected'" : "");?>> 4 - Lain-lain </option>
                                </select>
                            </td>
                            <td>No HP</td>
                            <td><input type="text" name="NoHpPjawab" id="NoHpPjawab" maxlength="50" size="20" value="<?php print($perawatan->NoHpPjawab); ?>" disabled/></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>Cara Bayar</td>
                <td><select id="CaraBayar" name="CaraBayar" style="width: 130px" disabled>
                        <option value="0"></option>
                        <option value="1" <?php print($perawatan->CaraBayar == 1 ? 'selected="selected"' : '');?>>1 - Umum</option>
                        <option value="2" <?php print($perawatan->CaraBayar == 2 ? 'selected="selected"' : '');?>>2 - BPJS</option>
                        <option value="3" <?php print($perawatan->CaraBayar == 3 ? 'selected="selected"' : '');?>>3 - Asuransi Lain</option>
                    </select>
                </td>
                <td>Jenis Rujukan</td>
                <td><select id="JnsRujukan" name="JnsRujukan" style="width: 130px" disabled>
                        <option value="0"></option>
                        <option value="1" <?php print($perawatan->JnsRujukan == 1 ? 'selected="selected"' : '');?>>1 - Datang Sendiri</option>
                        <option value="2" <?php print($perawatan->JnsRujukan == 2 ? 'selected="selected"' : '');?>>2 - Rujukan Dokter</option>
                        <option value="3" <?php print($perawatan->JnsRujukan == 3 ? 'selected="selected"' : '');?>>3 - Rujukan R.S. Lain</option>
                    </select>
                </td>
                <td>Asal Rujukan</td>
                <td><input type="text" name="AsalRujukan" id="AsalRujukan" maxlength="150" size="30" value="<?php print($perawatan->AsalRujukan); ?>" disabled/></td>
            </tr>
            <tr>
                <td>Jenis Rawat</td>
                <td><select id="JnsRawat" name="JnsRawat" style="width: 130px" disabled>
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
                <td><input type="text" class="bold" name="TglMasuk" id="TglMasuk" maxlength="15" size="14" value="<?php print($perawatan->FormatTglMasuk(JS_DATE)); ?>" disabled/></td>
                <td>Jam Masuk</td>
                <td><input type="text" class="bold" name="JamMasuk" id="JamMasuk" maxlength="5" size="5" value="<?php print($perawatan->JamMasuk); ?>" disabled/></td>
            </tr>
            <tr>
                <td>Poliklinik</td>
                <td><select id="KdPoliklinik" name="KdPoliklinik" style="width: 130px" disabled>
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
                <td><select id="KdKamar" name="KdKamar" style="width: 130px" disabled>
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
                <td><select id="KdPetugas" name="KdPetugas" style="width: 130px" disabled>
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
                <td colspan="2"><select id="KdDokter" name="KdDokter" style="width: 200px" disabled>
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
                <td colspan="3"><input type="text" name="Keluhan" id="Keluhan" maxlength="150" size="65" value="<?php print($perawatan->Keluhan); ?>" disabled/></td>
                <td colspan="2">Status
                    <select id="RegStatus" name="RegStatus" disabled>
                        <option value="1" <?php print($perawatan->RegStatus == 1 ? 'selected="selected"' : '');?>>1 - Masih Rawat</option>
                        <option value="2" <?php print($perawatan->RegStatus == 2 ? 'selected="selected"' : '');?>>2 - Sdh Pulang</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Diagnosa Utama</td>
                <td colspan="3"><input type="text" name="DiagnosaUtama" id="DiagnosaUtama" maxlength="150" size="65" value="<?php print($perawatan->DiagnosaUtama); ?>" disabled/></td>
                <td colspan="2">Code ICD 10<sub>1</sub>
                    <input class="easyui-combogrid" id="KdUtama" name="KdUtama" style="width: 200px" value="<?php print($perawatan->KdUtama); ?>" disabled/></td>
            </tr>
            <tr>
                <td>Diagnosa Kedua</td>
                <td colspan="3"><input type="text" name="DiagnosaKedua" id="DiagnosaKedua" maxlength="150" size="65" value="<?php print($perawatan->DiagnosaKedua); ?>" disabled/></td>
                <td colspan="2">Code ICD 10<sub>2</sub>
                    <input class="easyui-combogrid" id="KdKedua" name="KdKedua" style="width: 200px" value="<?php print($perawatan->KdKedua); ?>" disabled/></td>
            </tr>
    </table>
    <hr>
    <table>
        <tr>
            <td valign="top">
                <b>DATA JASA/LAYANAN/TINDAKAN:</b>
                <table cellpadding="1" cellspacing="1" border="1">
                    <tr>
                        <th>No.</th>
                        <th>Tanggal</th>
                        <th>Nama Layanan</th>
                        <th>QTY</th>
                        <th>Tarif</th>
                        <th>BPJS</th>
                        <th>Byr Sendiri</th>
                        <th>Keterangan</th>
                    </tr>
                    <?php
                    /** @var $layananlist Tindakan[] */
                    $nmr = 1;
                    $totals = 0;
                    $totalb = 0;
                    foreach ($layananlist as $layanan){
                        print('<tr>');
                        printf('<td align="center">%d</td>',$nmr);
                        printf('<td> %s </td>',date('d-m-Y',$layanan->TglLayanan));
                        printf('<td> %s </td>',$layanan->NmJasa);
                        printf('<td align="right">%s</td>',number_format($layanan->Qty,0));
                        printf('<td align="right">%s</td>',number_format($layanan->TarifHarga,0));
                        if($layanan->IsBpjs == 1){
                            printf('<td align="right">%s</td>',number_format($layanan->Qty * $layanan->TarifHarga,0));
                            print('<td align="right">0</td>');
                            $totalb+= $layanan->Qty * $layanan->TarifHarga;
                        }else{
                            print('<td align="right">0</td>');
                            printf('<td align="right">%s</td>',number_format($layanan->Qty * $layanan->TarifHarga,0));
                            $totals+= $layanan->Qty * $layanan->TarifHarga;
                        }
                        printf('<td>%s</td>',$layanan->Keterangan);
                        print('</tr>');
                        $nmr++;
                    }
                    ?>
                    <tr>
                        <td colspan="5" align="right">Total... </td>
                        <td align="right"><b><?php print(number_format($totalb));?></b></td>
                        <td align="right"><b><?php print(number_format($totals));?></b></td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
            </td>
            <td>&nbsp;&nbsp;&nbsp;</td>
            <td valign="top">
                <b>DATA PASIEN KELUAR:</b>
                <form id="frm" action="<?php print($helper->site_url("pelayanan.perawatan/keluar/".$perawatan->Id)); ?>" method="post">
                    <input type="hidden" id="NoReg" name="NoReg" value="<?php print($perawatan->NoReg); ?>"/>
                    <input type="hidden" id="RegId" name="RegId" value="<?php print($perawatan->Id); ?>"/>
                    <input type="hidden" id="NoRm" name="NoRm" value="<?php print($perawatan->NoRm); ?>"/>
                    <input type="hidden" id="DtgSendiri" name="DtgSendiri" value="<?php print($totals); ?>"/>
                    <input type="hidden" id="DtgBpjs" name="DtgBpjs" value="<?php print($totalb); ?>"/>
                <table>
                    <tr>
                        <td>Tgl Keluar</td>
                        <td><input type="text" name="TglKeluar" id="TglKeluar" maxlength="12" size="12" value="<?php print($perawatan->TglKeluar); ?>" required/></td>
                        <td>Jam Keluar</td>
                        <td><input type="text" name="JamKeluar" id="JamKeluar" maxlength="5" size="5" value="<?php print($perawatan->JamKeluar); ?>" required/></td>
                        <td>Status</td>
                        <td><select name="StsKeluar" id="StsKeluar">
                                <option value="1"> 1 - Hidup </option>
                                <option value="2"> 2 - Dirujuk </option>
                                <option value="3"> 3 - Meninggal </option>
                                <option value="4"> 4 - Piutang </option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <button id="bt_proses" type="submit"><strong>Proses</strong></button>
                            <a href="<?php print($helper->site_url("pelayanan.perawatan")); ?>" class="button"><strong>Daftar Pasien Dirawat</strong></a>
                        </td>
                    </tr>
                </table>
                </form>
            </td>
        </tr>
    </table>
</fieldset>
<script type="text/javascript">
    $(document).ready(function() {
        $("#TglKeluar").customDatePicker({ showOn: "focus" });
    });
</script>
</body>
</html>
