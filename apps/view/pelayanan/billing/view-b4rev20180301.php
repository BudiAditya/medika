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
    <title>ERAMEDIKA - Billing Pasien Pulang</title>
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
<div id="printKwitansi">
    <table cellpadding="1" cellspacing="1" width="700">
        <tr>
            <td colspan="2"><img src="<?php print(base_url('public/images/company/medikajaya.png'));?>" width="100" height="75"</td>
            <td colspan="4" valign="top">
                <b><font size="3">KLINIK MEDIKA JAYA MOPUYA</font></b>
                <br>
                Jl. Arah Mopugad, Mopuya Selatan II, Kec. Dumoga Utara, Kab. Bolaang Mongondow
                <br>
                SULAWESI UTARA, I N D O N E S I A
                <br>
                Telp. 0853 9420 1999      Email: klinikmedikajaya@gmail.com
            </td>
        </tr>
        <tr class="bold center">
            <th colspan="6">K W I T A N S I</th>
        </tr>
        <tr>
            <td width="15%">No. Registrasi</td>
            <td width="2%">:</td>
            <td class="bold" width="40%"><?php print($perawatan->NoReg); ?></td>
            <td width="16%">Cara Bayar</td>
            <td width="2%">:</td>
            <td class="bold" width="25%"><?php print($perawatan->CaraBayar == 2 ? 'BPJS' : 'UMUM'); ?></td>
        </tr>
        <tr>
            <td width="15%">Nama Pasien</td>
            <td width="2%">:</td>
            <td class="bold" width="40%"><?php print($pasien->NmPasien); ?></td>
            <td width="16%">Kamar Rawat</td>
            <td width="2%">:</td>
            <td class="bold" width="25%"><?php print($perawatan->KmrRawat); ?></td>
        </tr>
        <tr>
            <td width="15%">Gender</td>
            <td width="2%">:</td>
            <td class="bold" width="40%"><?php print($pasien->Jkelamin == 'L' ? 'Laki' : 'Perempuan');?></td>
            <td width="16%">Tgl Masuk</td>
            <td width="2%">:</td>
            <td class="bold" width="25%"><?php print($perawatan->FormatTglMasuk(JS_DATE)); ?></td>
        </tr>
        <tr>
            <td width="15%">Alamat</td>
            <td width="2%">:</td>
            <td class="bold" width="40%"><?php print($pasien->Alamat); ?></td>
            <td width="16%">Tgl Pulang</td>
            <td width="2%">:</td>
            <td class="bold" width="25%"><?php print($perawatan->FormatTglKeluar(JS_DATE)); ?></td>
        </tr>
        <tr>
            <td width="15%">Usia</td>
            <td width="2%">:</td>
            <td class="bold" width="40%"><?php print($pasien->Umur);?></td>
            <td width="16%">Lama Rawat</td>
            <td width="2%">:</td>
            <td class="bold" width="25%"><?php print($perawatan->LamaRawat.' hari'); ?></td>
        </tr>
    </table>
    <br>
    <table>
        <tr>
            <td valign="top">
                <table cellpadding="1" cellspacing="1" class="tableBorder" width="700">
                    <tr>
                        <td>No.</td>
                        <td align="center">Jasa/Layanan/Tindakan</td>
                        <td align="center">Kelas Rawat</td>
                        <td align="center">Jumlah</td>
                        <td align="center">Keterangan</td>
                    </tr>
                    <?php
                    /** @var $layananlist Layanan[] */
                    $nmr = 1;
                    $total = 0;
                    while ($row = $rslayanan->FetchAssoc()) {
                        print('<tr>');
                        printf('<td align="center">%d</td>',$nmr);
                        printf('<td> %s </td>',$row["klp_billing"]);
                        printf('<td> %s </td>',$row["jns_rawat_desc"]);
                        printf('<td align="right">%s</td>',number_format($row["jumlah"],0));
                        print('<td>&nbsp;</td>');
                        print('</tr>');
                        $total+= $row["jumlah"];
                        $nmr++;
                    }
                    ?>
                    <tr>
                        <td colspan="3" align="right"><?php print($billing->NominalBpjs > 0 ? 'Sub Total ...' : 'Total ...');?></td>
                        <td align="right"><?php print(number_format($total));?></td>
                        <td>&nbsp;</td>
                    </tr>
                    <?php if($billing->NominalBpjs > 0){ ?>
                        <tr>
                            <td colspan="3" align="right">Potongan BPJS ...</td>
                            <td align="right"><?php print(number_format($billing->NominalBpjs));?></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="3" align="right">Sisa Dibayar ...</td>
                            <td align="right"><?php print($total - $billing->NominalBpjs > 0 ? number_format($total - $billing->NominalBpjs) : 0);?></td>
                            <td>&nbsp;</td>
                        </tr>
                    <?php } ?>
                </table>
            </td>
        </tr>
    </table>
    <table cellpadding="1" cellspacing="1" width="700">
        <tr>
            <td width="60%"><u>Keterangan :</u></td>
            <td width="40%">&nbsp;</td>
        </tr>
        <tr>
            <td width="60%">&nbsp;</td>
            <td width="40%">Mopuya Selatan, <?php print($billing->FormatTglBayar(HUMAN_DATE));?></td>
        </tr>
        <tr>
            <td width="60%">&nbsp;</td>
            <td width="40%">Staf Administrasi,</td>
        </tr>
        <tr>
            <td width="60%">&nbsp;</td>
            <td width="40%">&nbsp;</td>
        </tr>
        <tr>
            <td width="60%">&nbsp;</td>
            <td width="40%">&nbsp;</td>
        </tr>
        <tr>
            <td width="60%">&nbsp;</td>
            <td width="40%">&nbsp;</td>
        </tr>
        <tr>
            <td width="60%">&nbsp;</td>
            <td width="40%">_________________________</td>
        </tr>
        <tr>
            <td width="60%"><sub><i>User: <?php print($realName.' -- '.date('Y-m-d H:i:s'));?></i></sub></td>
            <td width="40%">&nbsp;</td>
        </tr>
    </table>
</div>
<br>
<?php if($billing->StsBilling == 1){ ?>
<input type="button" class="button" onclick="printDiv('printKwitansi')" value="Print Kwitansi" />
<?php } ?>
<a href="<?php print($helper->site_url("pelayanan.billing")); ?>">Daftar Billing Pasien</a>
<script type="text/javascript">
    function printDiv(divName) {
        if (confirm('Print Kwitansi ini?')) {
            var printContents = document.getElementById(divName).innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;

            window.print();

            document.body.innerHTML = originalContents;
        }
    }
</script>
</body>
</html>
