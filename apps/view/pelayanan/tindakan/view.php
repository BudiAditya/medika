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
    <title>ERAMEDIKA - Data Registrasi Pasien Dirawat</title>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            //var elements = ["NmPasien","NoKtp","NoBpjs","Jkelamin","Alamat","T4Lahir","TglLahir","GolDarah","NoHp","StKawin","PekerjaanId","NmPjawab","NoKtpPjawab","NoHpPjawab","AlamatPjawab","HubPjawab","Simpan"];
            //BatchFocusRegister(elements);
            $("#TglMasuk").customDatePicker({ showOn: "focus" });
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
    <legend><b>DATA PASIEN DIRAWAT</b></legend>
    <table cellpadding="2" cellspacing="1">
        <tr>
            <td>Nomor RM</td>
            <td><input type="text" class="bold" name="NoRm1" id="NoRm1" maxlength="15" size="15" value="<?php print($pasien->NoRm); ?>" readonly /></td>
        </tr>
        <tr>
            <td>Nama Pasien</td>
            <td colspan="3"><input type="text" class="bold" name="NmPasien" id="NmPasien" maxlength="50" size="50" value="<?php print($pasien->NmPasien); ?>" readonly/></td>
            <td>No. KTP</td>
            <td><input type="text" class="bold" name="NoKtp" id="NoKtp" maxlength="20" size="20" value="<?php print($pasien->NoKtp); ?>" readonly/></td>
            <td>No. BPJS</td>
            <td><input type="text" class="bold" name="NoBpjs" id="NoBpjs" maxlength="20" size="20" value="<?php print($pasien->NoBpjs); ?>" readonly/></td>
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
            <td colspan="3"><input type="text" class="bold" name="Alamat" id="Alamat" maxlength="50" size="50" value="<?php print($pasien->Alamat); ?>" readonly/></td>
            <td>Lahir Di</td>
            <td><input type="text" class="bold" name="T4Lahir" id="T4Lahir" maxlength="50" size="20" value="<?php print($pasien->T4Lahir); ?>" readonly/></td>
            <td>Tgl Lahir</td>
            <td><input type="text" class="bold" name="TglLahir" id="TglLahir" maxlength="10" size="20" value="<?php print($pasien->FormatTglLahir(JS_DATE));?>" readonly/></td>
            <td>Usia</td>
            <td><input type="text" class="bold" name="Umur" id="Umur" size="14" value="<?php print($pasien->Umur);?>" readonly/></td>
        </tr>
        <tr>
            <td>Gol. Darah</td>
            <td><input type="text" class="bold" name="GolDarah" id="GolDarah" maxlength="5" size="10" value="<?php print($pasien->GolDarah); ?>" readonly/></td>
            <td>Sts.Perkawinan</td>
            <td><input type="text" class="bold" name="StsKawinDesc" id="StsKawinDesc" maxlength="50" size="17" value="<?php print($pasien->StsKawinDesc); ?>" readonly/></td>
            <td>No. HP</td>
            <td><input type="text" class="bold" name="NoHp" id="NoHp" maxlength="50" size="20" value="<?php print($pasien->NoHp); ?>" readonly/></td>
            <td>Pekerjaan</td>
            <td><input type="text" class="bold" name="NmPekerjaan" id="NmPekerjaan" maxlength="50" size="20" value="<?php print($pasien->Pekerjaan); ?>" readonly/></td>
        </tr>
    </table>
    <hr>
    <table cellpadding="2" cellspacing="1">
        <table cellpadding="2" cellspacing="1">
            <tr>
                <td>No. Register</td>
                <td><input type="text" class="bold" name="NoReg" id="NoReg" maxlength="15" size="15" value="<?php print($perawatan->NoReg); ?>" readonly placeholder="Auto" /></td>
                <td>Tinggi Badan</td>
                <td><input type="text" class="text2 right" name="TgiBadan" id="TgiBadan" maxlength="5" size="7" value="<?php print($pasien->TgiBadan); ?>" disabled/> CM</td>
                <td>Berat Badan</td>
                <td><input type="text" class="text2 right" name="BrtBadan" id="BrtBadan" maxlength="5" size="8" value="<?php print($pasien->BrtBadan); ?>" disabled/> KG</td>
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
                        <option value="2" <?php print($perawatan->JnsRawat == 2 ? 'selected="selected"' : '');?>>2 - I G D</option>
                        <option value="3" <?php print($perawatan->JnsRawat == 3 ? 'selected="selected"' : '');?>>3 - Rawat (Kelas 3)</option>
                        <option value="3" <?php print($perawatan->JnsRawat == 4 ? 'selected="selected"' : '');?>>4 - Rawat (Kelas 2)</option>
                    </select>
                </td>
                <td>Tgl Masuk</td>
                <td><input type="text" class="bold" name="TglMasuk" id="TglMasuk" maxlength="15" size="14" value="<?php print($perawatan->FormatTglMasuk(JS_DATE)); ?>" disabled/></td>
                <td>Jam Masuk</td>
                <td><input type="text" class="bold" name="JamMasuk" id="JamMasuk" maxlength="5" size="5" value="<?php print($perawatan->JamMasuk); ?>" disabled/></td>
            </tr>
            <tr>
                <td>Poliklinik</td>
                <td><select id="PoliklinikId" name="PoliklinikId" style="width: 130px" disabled>
                        <option value="0"></option>
                        <?php
                        foreach ($poli as $poliklinik){
                            if ($perawatan->PoliklinikId == $poliklinik->Id){
                                printf('<option value = "%d" selected="selected"> %s - %s </option>',$poliklinik->Id,$poliklinik->KdPoliklinik,$poliklinik->NmPoliklinik);
                            }else {
                                printf('<option value = "%d"> %s - %s </option>', $poliklinik->Id, $poliklinik->KdPoliklinik, $poliklinik->NmPoliklinik);
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
                <td><select id="PetugasId" name="PetugasId" style="width: 130px" disabled>
                        <option value="0"></option>
                        <?php
                        foreach ($petugas as $karyawan){
                            if ($perawatan->PetugasId == $karyawan->Id){
                                printf('<option value = "%d" selected="selected"> %s </option>',$karyawan->Id,$karyawan->Nama);
                            }else {
                                printf('<option value = "%d"> %s </option>', $karyawan->Id,$karyawan->Nama);
                            }
                        }
                        ?>
                    </select>
                </td>
                <td>Dokter</td>
                <td colspan="3"><select id="DokterId" name="DokterId" style="width: 200px" disabled>
                        <option value="0"></option>
                        <?php
                        foreach ($dokter as $dtr){
                            if ($perawatan->DokterId == $dtr->Id){
                                printf('<option value = "%d" selected="selected"> %s </option>',$dtr->Id,$dtr->NmDokter);
                            }else {
                                printf('<option value = "%d"> %s </option>', $dtr->Id,$dtr->NmDokter);
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Keluhan</td>
                <td colspan="5"><input type="text" name="Keluhan" id="Keluhan" maxlength="150" size="65" value="<?php print($perawatan->Keluhan); ?>" disabled/></td>
            </tr>
            <tr>
                <td>Diagnosa Utama</td>
                <td colspan="5"><input type="text" name="DiagnosaUtama" id="DiagnosaUtama" maxlength="150" size="65" value="<?php print($perawatan->DiagnosaUtama); ?>" disabled/></td>
            </tr>
            <tr>
                <td>Diagnosa Kedua</td>
                <td colspan="5"><input type="text" name="DiagnosaKedua" id="DiagnosaKedua" maxlength="150" size="65" value="<?php print($perawatan->DiagnosaKedua); ?>" disabled/></td>
            </tr>
            <tr>
                <td>Status</td>
                <td><select id="RegStatus" name="RegStatus" style="width: 130px" disabled>
                        <option value="1" <?php print($perawatan->RegStatus == 1 ? 'selected="selected"' : '');?>>1 - Masih Rawat</option>
                        <option value="2" <?php print($perawatan->RegStatus == 2 ? 'selected="selected"' : '');?>>2 - Sdh Pulang</option>
                    </select>
                </td>
                <td>Tgl Keluar</td>
                <td><input type="text" name="TglKeluar" id="TglKeluar" maxlength="15" size="15" value="<?php print($perawatan->FormatTglKeluar(JS_DATE)); ?>" disabled/></td>
                <td>Jam Keluar</td>
                <td><input type="text" name="JamKeluar" id="JamKeluar" maxlength="5" size="5" value="<?php print($perawatan->JamKeluar); ?>" disabled/></td>
                <td colspan="3">
                    <a href="<?php print($helper->site_url("pelayanan.perawatan")); ?>" class="button"><strong>Daftar Pasien Dirawat</strong></a>
                </td>
            </tr>
        </table>
        <hr>
        <b>DATA LAYANAN/TINDAKAN YANG SUDAH DIBERIKAN:</b>
        <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
            <tr>
                <th>No.</th>
                <th>Tanggal</th>
                <th>Pelaksana</th>
                <th>Operator/Dokter</th>
                <th>Kode</th>
                <th>Nama Layanan</th>
                <th>Jenis Rawat</th>
                <th>Keterangan</th>
                <th>QTY</th>
                <th>Tarif/Harga</th>
                <th>Jumlah</th>
            </tr>
            <?php
            /** @var $layananlist Tindakan[] */
            $nmr = 1;
            $total = 0;
            foreach ($layananlist as $layanan){
                print('<tr>');
                printf('<td align="center">%d</td>',$nmr);
                printf('<td> %s </td>',date('d-m-Y',$layanan->TglLayanan));
                printf('<td> %s </td>',$layanan->KdPetugas);
                printf('<td> %s </td>',$layanan->KdDokter);
                printf('<td> %s </td>',$layanan->KdJasa);
                printf('<td> %s </td>',$layanan->NmJasa);
                printf('<td> %s </td>',$layanan->JnsRawatDesc);
                printf('<td>%s</td>',$layanan->Keterangan);
                printf('<td align="right">%s</td>',number_format($layanan->Qty,0));
                printf('<td align="right">%s</td>',number_format($layanan->TarifHarga,0));
                printf('<td align="right">%s</td>',number_format($layanan->Qty * $layanan->TarifHarga,0));
                print('</tr>');
                $total+= $layanan->Qty * $layanan->TarifHarga;
                $nmr++;
            }
            ?>
            <tr>
                <td colspan="8" align="right">Total... </td>
                <td align="right"><b><?php print(number_format($total));?></b></td>
                <td>&nbsp;</td>
            </tr>
        </table>
</fieldset>
</body>
</html>
