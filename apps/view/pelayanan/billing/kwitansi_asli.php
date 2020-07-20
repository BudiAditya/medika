<!DOCTYPE HTML>
<?php
/** @var $pasien Pasien */
/** @var $perawatan Perawatan */
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
                <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder" width="700">
                    <tr>
                        <td>No.</td>
                        <td>Jasa/Layanan/Tindakan</td>
                        <td>Kelas</td>
                        <td>Jumlah</td>
                        <td>Keterangan</td>
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
                        <td colspan="3" align="right">Total... </td>
                        <td align="right"><b><?php print(number_format($total));?></b></td>
                        <td>&nbsp;</td>
                    </tr>
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
    </table>
</body>
</html>
