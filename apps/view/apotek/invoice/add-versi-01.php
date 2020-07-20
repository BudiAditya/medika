<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
<?php
/** @var $invoice Invoice */ /** @var $sales Karyawan[] */
$counter = 0;
?>
<head>
    <title>ERAMEDIKA | Point Of Sales (POS)</title>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/auto-numeric.js")); ?>"></script>

    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery.easyui.min.js")); ?>"></script>

    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>

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
            font-size: 50px;
            font-weight:bold;
        }
    </style>
</head>
<body>
<?php include(VIEW . "main/menu.php"); ?>
<?php if (isset($error)) { ?>
    <div class="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
    <div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php }
$badd = base_url('public/images/button/').'add.png';
$bsave = base_url('public/images/button/').'accept.png';
$bcancel = base_url('public/images/button/').'cancel.png';
$bview = base_url('public/images/button/').'view.png';
$bedit = base_url('public/images/button/').'edit.png';
$bdelete = base_url('public/images/button/').'delete.png';
$bclose = base_url('public/images/button/').'close.png';
$bsearch = base_url('public/images/button/').'search.png';
$bkembali = base_url('public/images/button/').'back.png';
$bcetak = base_url('public/images/button/').'printer.png';
$bsubmit = base_url('public/images/button/').'ok.png';
$baddnew = base_url('public/images/button/').'create_new.png';
$bpdf = base_url('public/images/button/').'pdf.png';
?>
<br />
<div id="p" class="easyui-panel" title="<b>Entry Nota Penjualan</b>" style="width:100%;height:100%;padding:10px;" data-options="footer:'#ft'">
    <table>
        <tr>
            <td style="width: 100%">
                <table cellpadding="0" cellspacing="0" class="tablePadding" align="left" style="font-size: 13px;font-family: tahoma">
                    <tr>
                        <td>Tanggal</td>
                        <td><input class="easyui-datebox bold" id="Tanggal" name="Tanggal" value="<?php print($invoice->Tanggal);?>" data-options="formatter:myformatter,parser:myparser" style="width:100px;"></td>
                        <td>No. Invoice</td>
                        <td><input type="text" class="f1 easyui-textbox" style="width: 150px" id="TrxNo" name="TrxNo" value="<?php print($invoice->TrxNo != null ? $invoice->TrxNo : '-'); ?>" readonly/></td>
                    </tr>
                    <tr>
                        <td>Customer</td>
                        <td><input class="easyui-combogrid" id="RelasiId" name="RelasiId" style="width: 250px" value="<?php print($invoice->RelasiId);?>" required autofocus/></td>
                        <td>Jenis Pasien</td>
                        <td><select class="easyui-combobox" id="JenisPasien" name="JenisPasien" style="width: 150px" required>
                                <option value="UMUM" <?php print($invoice->JenisPasien == 'UMUM' ? 'selected="selected"' : '');?>> UMUM </option>
                                <option value="BPJS" <?php print($invoice->JenisPasien == 'BPJS' ? 'selected="selected"' : '');?>> BPJS </option>
                            </select>
                        </td>
                        <td>Cara Bayar</td>
                        <td><select class="easyui-combobox" id="CaraBayar" name="CaraBayar" style="width: 150px" required>
                                <option value="1" <?php print($invoice->CaraBayar == 1 ? 'selected="selected"' : '');?>> TUNAI </option>
                                <option value="2" <?php print($invoice->CaraBayar == 2 ? 'selected="selected"' : '');?>> KREDIT </option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Dokter</td>
                        <td><input class="easyui-combogrid" id="KdDokter" name="KdDokter" style="width: 250px" value="<?php print($invoice->KdDokter);?>"/></td>
                        <td>Jenis Obat</td>
                        <td><select class="easyui-combobox" id="JnsBeli" name="JnsBeli" style="width: 150px" required>
                                <option value="BEBAS" <?php print($invoice->JnsBeli == 'BEBAS' ? 'selected="selected"' : '');?>> BEBAS </option>
                                <option value="RESEP" <?php print($invoice->JnsBeli == 'RESEP' ? 'selected="selected"' : '');?>> RESEP </option>
                            </select>
                        </td>
                        <td>No. Resep</td>
                        <td><input type="text" class="f1 easyui-textbox" style="width: 150px" id="NoResep" name="NoResep" value="<?php print($invoice->NoResep); ?>"/></td>
                    </tr>
                    <tr>
                        <td>No. MedRek</td>
                        <td><input class="easyui-combogrid" id="NoRm" name="NoRm" style="width: 250px" value="<?php print($invoice->NoRm);?>"/></td>
                    </tr>
                    <tr>
                        <td>Nama Pasien</td>
                        <td><input class="f1 easyui-textbox" id="NmPasien" name="NmPasien" style="width: 250px" value="<?php print($invoice->NmPasien);?>"/>
                        <td>Umur</td>
                        <td><input type="text" class="f1 easyui-textbox" style="width: 50px" id="UmrPasien" name="UmrPasien" value="<?php print($invoice->UmrPasien); ?>"/> Tahun</td>
                        <td>No. HP</td>
                        <td><input type="text" class="f1 easyui-textbox" style="width: 150px" id="NoHp" name="NoHp" value="<?php print($invoice->NoHp); ?>"/></td>
                    </tr>
                    <tr>
                        <td>Catatan</td>
                        <td colspan="3"><b><input class="f1 easyui-textbox" id="Uraian" name="Uraian" style="width: 500px" value="<?php print($invoice->Uraian != null ? $invoice->Uraian : '-'); ?>" required/></b></td>

                    </tr>
                </table>
            </td>
            <td valign="top">
                <table cellpadding="0" cellspacing="0" class="tablePadding" align="right">
                    <tr>
                        <td>
                            <input type="text" class="easyui-numberbox numberbox-f validatebox-text" data-options="precision:0,groupSeparator:'.',decimalSeparator:','"  id="grandTotal" value="0" style="width: 350px; height: 60px" readonly/>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</div>
<div id="ft" style="padding:5px; text-align: center; font-family: verdana; font-size: 9px" >
    Copyright &copy; 2016 - 2018  PT. Rekasystem Technology
</div>
<script type="text/javascript">
    //script javascript disini


    //relasi combogrid fill
    $('#RelasiId').combogrid({
        panelWidth:600,
        url: "<?php print($helper->site_url('master.relasi/getJsonRelasi/2'));?>",
        idField:'id',
        textField:'nm_relasi',
        mode:'remote',
        fitColumns:true,
        columns:[[
            {field:'nm_relasi',title:'Nama Customer',width:100},
            {field:'kd_relasi',title:'Kode',width:40},
            {field:'alamat',title:'Alamat',width:100},
            {field:'kabkota',title:'Kota',width:60}
        ]],
        onSelect: function(index,row){
            var cid = row.id;
            console.log(cid);
        }
    });

    //dokter combogrid fill
    $('#KdDokter').combogrid({
        panelWidth:500,
        url: "<?php print($helper->site_url('master.dokter/getJsonDokter'));?>",
        idField:'kd_dokter',
        textField:'nm_dokter',
        mode:'remote',
        fitColumns:true,
        columns:[[
            {field:'nm_dokter',title:'Nama Dokter',width:100},
            {field:'kd_dokter',title:'Kode',width:40},
            {field:'spesialisasi',title:'Spesialis',width:100}
        ]],
        onSelect: function(index,row){
            var cid = row.id;
            console.log(cid);
        }
    });

    //medrek pasien combogrid fill
    $('#NoRm').combogrid({
        panelWidth:900,
        url: "<?php print($helper->site_url('pelayanan.pasien/getJsonPasien'));?>",
        idField:'no_rm',
        textField:'no_rm',
        mode:'remote',
        fitColumns:true,
        columns:[[
            {field:'no_rm',title:'No. Medrek',width:70},
            {field:'nm_pasien',title:'Nama Pasien',width:150},
            {field:'alamat',title:'Alamat',width:150},
            {field:'usia',title:'Umur',width:40},
            {field:'no_hp',title:'No. HP',width:80},
            {field:'no_ktp',title:'NIK',width:100},
            {field:'no_bpjs',title:'BPJS',width:100}
        ]],
        onSelect: function(index,row){
            var cid = row.id;
            console.log(cid);
            alert(row.nm_pasien);
            $("#NmPasien").val(row.nm_pasien);
            $("#UmrPasien").val(row.usia);
            $("#NoHp").val(row.no_hp);
        }
    });


    //format tanggal datepicker
    function myformatter(date){
        var y = date.getFullYear();
        var m = date.getMonth()+1;
        var d = date.getDate();
        return y+'-'+(m<10?('0'+m):m)+'-'+(d<10?('0'+d):d);
    }
    //parser tanggal sesuai keinginan
    function myparser(s){
        if (!s) return new Date();
        var ss = (s.split('-'));
        var y = parseInt(ss[0],10);
        var m = parseInt(ss[1],10);
        var d = parseInt(ss[2],10);
        if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
            return new Date(y,m-1,d);
        } else {
            return new Date();
        }
    }
</script>
</body>
</html>
