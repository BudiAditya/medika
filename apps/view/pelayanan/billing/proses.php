<!DOCTYPE HTML>
<?php
/** @var $pasien Pasien */
/** @var $perawatan Perawatan */
/** @var $poli Poliklinik[] */
/** @var $dokter Dokter[] */
/** @var $petugas Karyawan[] */
/** @var $billing Billing */
?>
<html>
<head>
    <title>ERAMEDIKA - Data Billing Pasien Dirawat</title>
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
    <legend><b>DATA BILLING PASIEN</b></legend>
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
    </table>
    <hr>
    <table cellpadding="2" cellspacing="1">
        <table cellpadding="2" cellspacing="1">
            <tr>
                <td>No. Register</td>
                <td><input type="text" class="bold" name="NoReg1" id="NoReg1" maxlength="15" size="15" value="<?php print($perawatan->NoReg); ?>" readonly placeholder="Auto" /></td>
                <td>Cara Bayar</td>
                <td><select id="CaraBayar" name="CaraBayar" style="width: 130px" disabled>
                        <option value="0"></option>
                        <option value="1" <?php print($perawatan->CaraBayar == 1 ? 'selected="selected"' : '');?>>1 - Umum</option>
                        <option value="2" <?php print($perawatan->CaraBayar == 2 ? 'selected="selected"' : '');?>>2 - BPJS</option>
                        <option value="3" <?php print($perawatan->CaraBayar == 3 ? 'selected="selected"' : '');?>>3 - Asuransi Lain</option>
                    </select>
                </td>
                <td>Jenis Rawat</td>
                <td><select id="JnsRawat" name="JnsRawat" style="width: 130px" disabled>
                        <option value="0"></option>
                        <option value="1" <?php print($perawatan->JnsRawat == 1 ? 'selected="selected"' : '');?>>1 - Jalan (Poliklinik)</option>
                        <option value="2" <?php print($perawatan->JnsRawat == 2 ? 'selected="selected"' : '');?>>2 - I G D</option>
                        <option value="3" <?php print($perawatan->JnsRawat == 3 ? 'selected="selected"' : '');?>>3 - Rawat (Kelas 3)</option>
                        <option value="3" <?php print($perawatan->JnsRawat == 4 ? 'selected="selected"' : '');?>>4 - Rawat (Kelas 2)</option>
                    </select>
                </td>
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
                <td>Status Keluar</td>
                <td><select name="StsKeluar" id="StsKeluar" disabled>
                        <option value="1" <?php print($perawatan->StsKeluar == 1 ? 'selected="selected"' : '') ?>> 1 - Hidup </option>
                        <option value="2" <?php print($perawatan->StsKeluar == 2 ? 'selected="selected"' : '') ?>> 2 - Meninggal </option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Tgl Masuk</td>
                <td><input type="text" class="bold" name="TglMasuk" id="TglMasuk" maxlength="15" size="14" value="<?php print($perawatan->FormatTglMasuk(JS_DATE).' '.$perawatan->JamMasuk); ?>" disabled/></td>
                <td>Tgl Pulang</td>
                <td><input type="text" class="bold" name="TglKeluar" id="TglKeluar" maxlength="15" size="14" value="<?php print($perawatan->FormatTglKeluar(JS_DATE).' '.$perawatan->JamKeluar); ?>" disabled/></td>
                <td>Lama Rawat</td>
                <td><input type="text" class="bold" name="LamaRawat" id="LamaRawat" maxlength="10" size="11" value="<?php print($perawatan->LamaRawat.' hr'); ?>" readonly/></td>
            </tr>
    </table>
    <hr>
    <table>
        <tr>
            <td valign="top">
                <b>DATA JASA/LAYANAN/TINDAKAN:</b>
                <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
                    <tr>
                        <th>No.</th>
                        <th>Tanggal</th>
                        <th>Nama Layanan</th>
                        <th>Jns Rawat</th>
                        <th>QTY</th>
                        <th>Tarif</th>
                        <th>BPJS</th>
                        <th>Non-BPJS</th>
                    </tr>
                    <?php
                    /** @var $layananlist Tindakan[] */
                    $nmr = 1;
                    $totalb = 0;
                    $totals = 0;
                    $totald = 0;
                    foreach ($layananlist as $layanan){
                        print('<tr>');
                        printf('<td align="center">%d</td>',$nmr);
                        printf('<td> %s </td>',date('d-m-Y',$layanan->TglLayanan));
                        printf('<td> %s </td>',$layanan->NmJasa);
                        printf('<td> %s </td>',$layanan->JnsRawatDesc);
                        printf('<td align="right">%s</td>',number_format($layanan->Qty,0));
                        printf('<td align="right">%s</td>',number_format($layanan->TarifHarga,0));
                        if($layanan->IsBpjs == 1){
                            printf('<td align="right">%s</td>', number_format($layanan->Qty * $layanan->TarifHarga, 0));
                            print('<td align="right">0</td>');
                            $totalb+= $layanan->Qty * $layanan->TarifHarga;
                        }else {
                            print('<td align="right">0</td>');
                            printf('<td align="right">%s</td>', number_format($layanan->Qty * $layanan->TarifHarga, 0));
                            $totals+= $layanan->Qty * $layanan->TarifHarga;
                        }
                        print('</tr>');
                        $nmr++;
                    }
                    ?>
                    <tr>
                        <td colspan="6" align="right">Total... </td>
                        <td align="right"><b><?php print(number_format($totalb));?></b></td>
                        <td align="right"><b><?php print(number_format($totals));?></b></td>
                    </tr>
                </table>
            </td>
            <td>&nbsp;&nbsp;&nbsp;</td>
            <td valign="top">
                <?php
                if ($IsDTP == 1){
                    $totald = $totals;
                    $totals = 0;
                }
                ?>
                <b><u>DATA PEMBAYARAN:</u></b>
                <form id="frm" action="<?php print($helper->site_url("pelayanan.billing/proses/".$billing->Id)); ?>" method="post">
                    <input type="hidden" id="NoReg" name="NoReg" value="<?php print($billing->NoReg); ?>"/>
                    <input type="hidden" id="CaraBayar" name="CaraBayar" value="<?php print($perawatan->CaraBayar); ?>"/>
                <table>
                    <tr>
                        <td>Jumlah Biaya/Jasa</td>
                        <td><input type="text" class="bold right" name="NominalTindakan" id="NominalTindakan" maxlength="15" size="11" value="<?php print($totalb + $totals + $totald);?>" required readonly/></td>
                        <td>Tgl Bayar</td>
                        <td><input type="text" class="bold" name="TglBayar" id="TglBayar" maxlength="10" size="10" value="<?php print($billing->FormatTglBayar(JS_DATE)); ?>" required/></td>
                    </tr>
                    <tr>
                        <td>Ditanggung BPJS</td>
                        <td><input type="text" class="bold right" name="DtgBpjs" id="DtgBpjs" maxlength="15" size="11" value="<?php print($totalb);?>"/></td>
                    </tr>
                    <tr>
                        <td>Ditanggung Perusahaan</td>
                        <td><input type="text" class="bold right" name="DtgPerusahaan" id="DtgPerusahaan" maxlength="15" size="11" value="<?php print($totald);?>"/></td>
                    </tr>
                    <tr>
                        <td>Ditanggung Sendiri</td>
                        <td><input type="text" class="bold right" name="DtgSendiri" id="DtgSendiri" maxlength="15" size="11" value="<?php print($totals);?>"/></td>
                    </tr>
                    <tr>
                        <td>Jumlah Dibayar</td>
                        <td><input type="text" class="bold right" name="JumBayar" id="JumBayar" maxlength="15" size="11" value="<?php print($billing->JumBayar == 0 ? $totals : $billing->JumBayar);?>" required/></td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <button id="bt_proses" type="submit"><strong>Proses</strong></button>
                            <a href="<?php print($helper->site_url("pelayanan.billing")); ?>" class="button"><strong>Daftar Billing Pasien</strong></a>
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
        $("#TglBayar").customDatePicker({ showOn: "focus" });

        $("#DtgBpjs").change(function () {
           var vtotal = Number($("#NominalTindakan").val());
           //vtotal = Number(vtotal.replace(',',''));
           $("#JumBayar").val(vtotal - this.value);
        });
    });
</script>
</body>
</html>
