<!DOCTYPE HTML>
<?php
/** @var $pasien Pasien */
/** @var $perawatan Perawatan */
/** @var $poli Poliklinik[] */
/** @var $dokter Dokter[] */
/** @var $petugas Karyawan[] */
/** @var $kamar Kamar[] */
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
if ($perawatan->CaraBayar == 1) {
    $jpasien = 'Umum';
}elseif ($perawatan->CaraBayar == 2){
    $jpasien = 'BPJS';
}else{
    $jpasien = 'Asuransi';
}
?>
<html>
<head>
    <title>ERAMEDIKA - Data Layanan Pasien Dirawat</title>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
    <!-- jquery -->
    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/auto-numeric.js")); ?>"></script>

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
    <div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php }?>
<br />
<div id="p" class="easyui-panel" title="Data Layanan & Tindakan - Pasien <?php print($jpasien);?>" style="width:100%;height:600px;padding:5px;" data-options="footer:'#ft'">
    <table cellpadding="0" cellspacing="0" class="tablePadding" align="left" style="font-size: 13px;font-family: tahoma">
        <tr>
            <td>No Register</td>
            <td><input class="easyui-combogrid" id="NoReg" name="NoReg" style="width: 150px" value="<?php print($perawatan->NoReg); ?>" autofocus/></td>
            <td>Nama Pasien</td>
            <td><input type="text" class="easyui-textbox" id="NmPasien" name="NmPasien" size="30" value="<?php print($pasien->NmPasien); ?>" readonly/></td>
            <td>Kamar Rawat</td>
            <td><input type="text" class="easyui-textbox" id="KmrRawat" name="KmrRawat" size="15" value="<?php print($perawatan->KmrRawat); ?>" readonly/></td>
            <td>Tgl Masuk</td>
            <td><input type="text" class="easyui-textbox" id="TglMasuk" name="TglMasuk" size="20" value="<?php print(date('Y-m-d',$perawatan->TglMasuk).' '.$perawatan->JamMasuk); ?>" readonly/></td>
        </tr>
        <tr>
            &nbsp;
        </tr>
        <tr>
            <td colspan="12">
                <table id="dglayanan" class="easyui-datagrid" title="Data Layanan & Tindakan"
                       data-options="singleSelect:true,
                       width:'1100',
                       fitColumns:'true',
                       rownumbers:'true',
                       showFooter:'true',
                       collapsible:true,
                       url:'<?php print($helper->site_url("pelayanan.tindakan/GetJSonTindakanList/".$perawatan->NoReg));?>',
                       method:'get',
                       toolbar:'#dgtoolbar'">
                    <thead>
                    <tr>
                        <th data-options="field:'tgl_layanan'">Tanggal</th>
                        <th data-options="field:'jam_layanan'">Jam</th>
                        <th data-options="field:'kd_jasa'">Kode</th>
                        <th data-options="field:'nm_jasa'">Nama Jasa/Layanan</th>
                        <th data-options="field:'uraian_jasa'">Uraian Jasa</th>
                        <th data-options="field:'jns_rawat_desc'">Jns Rawat</th>
                        <th data-options="field:'qty',align:'right'">QTY</th>
                        <th data-options="field:'tarif_harga',align:'right'", formatter = "nformat">Tarif</th>
                        <th data-options="field:'jumlah',align:'right'", formatter = "nformat">Jumlah</th>
                        <th data-options="field:'kd_dokter'">Dokter</th>
                        <th data-options="field:'kd_petugas'">Petugas</th>
                        <th data-options="field:'ditanggung'">Ditanggung</th>
                    </tr>
                    </thead>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="10"><a href="<?php print($helper->site_url("pelayanan.perawatan")); ?>" class="button"><strong>Daftar Pasien Dirawat</strong></a></td>
        </tr>
    </table>
</div>
<div id="ft" style="padding:5px; text-align: center; font-family: verdana; font-size: 9px" >
    Copyright &copy; 2018  CV. Erasystem Infotama
</div>
<div id="dlform" class="easyui-dialog" style="width:710px;height:350px;padding:10px 10px"
     closed="true" buttons="#dlbuttons">
    <form id="frm" method="post" novalidate>
        <div class="fitem">
            <label for="TglLayanan">Tgl Layanan</label>
            <input id="TglLayanan" name="TglLayanan" type="text" size="13" class="easyui-datebox" required>
            <label for="JamLayanan">Jam</label>
            <input id="JamLayanan" name="JamLayanan" type="text" size="5" class="easyui-textbox">
            <label for="NoReg1">No REG</label>
            <input id="NoReg1" name="NoReg1" type="text" size="16" class="easyui-textbox" readonly>
            <input type="hidden" id="JnsRawat" name="JnsRawat" class="easyui-textbox"/>
            <input type="hidden" id="IsBpjs" name="IsBpjs" value="0" class="easyui-textbox"/>
        </div>
        <div class="fitem">
            <label for="KdDokter">Dokter</label>
            <input class="easyui-combobox" name="aKdDokter[]" id="KdDokter" style="width:515px;height: 30px" data-options="
                    url:'<?php print($helper->site_url("pelayanan.tindakan/GetJSonDokterList"));?>',
                    method:'post',
                    valueField:'kd_dokter',
                    textField:'nm_dokter',
                    multiple:true,
                    value:['<?php print($perawatan->KdDokter);?>'],
                    multiline:true,
                    panelHeight:'auto',
                    labelPosition: 'top'
                    ">
        </div>
        <div class="fitem">
            <label for="KdPetugas">Petugas</label>
            <input class="easyui-combobox" name="aKdPetugas[]" id="KdPetugas" style="width:515px;height: 30px" data-options="
                    url:'<?php print($helper->site_url("pelayanan.tindakan/GetJSonPetugasList"));?>',
                    method:'post',
                    valueField:'kd_petugas',
                    textField:'nm_petugas',
                    multiple:true,
                    value:['<?php print($perawatan->KdPetugas);?>'],
                    multiline:true,
                    panelHeight:'auto',
                    labelPosition: 'top'
                    ">
        </div>
        <div class="fitem">
            <label for="NmJasa">Jasa/Layanan</label>
            <input class="easyui-combogrid" id="NmJasa" name="NmJasa" style="width: 320px" required/>
            <label for="KdJasa">Kode Jasa</label>
            <input id="KdJasa" name="KdJasa" type="text" size="9" class="easyui-textbox" required>
        </div>
        <div class="fitem">
            <label for="UraianJasa">Uraian Jasa</label>
            <input id="UraianJasa" name="UraianJasa" type="text" style="width: 515px" class="easyui-textbox">
        </div>
        <div class="fitem numberbox">
            <label for="Tarif">Tarif/Harga</label>
            <input id="Tarif" name="Tarif" type="text" class="easyui-textbox" size="10" value="0" required readonly>
            <label for="Qty">QTY</label>
            <input id="Qty" name="Qty" type="text" class="easyui-textbox" size="3" value="1" required>
            <input id="Satuan" name="Satuan" type="text" class="easyui-textbox" size="5" readonly>
            <label for="Jumlah">Jumlah</label>
            <input id="Jumlah" name="Jumlah" type="text" class="easyui-textbox" size="11" value="0" required readonly>
        </div>
        <div class="fitem">
            <label for="Ditanggung">Ditanggung</label>
            <input id="Ditanggung" name="Ditanggung" type="text" size="10" class="easyui-textbox">
        </div>
        <div class="fitem">
            <label for="Keterangan">Catatan</label>
            <input id="Keterangan" name="Keterangan" type="text" style="width: 515px" class="easyui-textbox">
        </div>
    </form>
</div>
<div id="dlbuttons">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveLayanan()" style="width:90px">Save</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlform').dialog('close')" style="width:90px">Cancel</a>
</div>
<div id="dgtoolbar">
    <?php if ($allow_add && $perawatan->RegStatus == 1){ ?>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="tbhLayanan()" style="width:90px">Tambah</a>
    <?php }
    if ($allow_edit && $perawatan->RegStatus == 1){ ?>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="editLayanan()" style="width:90px">Ubah</a>
    <?php }
    if ($allow_delete && $perawatan->RegStatus == 1){ ?>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="hpsLayanan()" style="width:90px">Hapus</a>
    <?php } ?>
</div>
<script type="text/javascript">
    var idx = '<?php print($perawatan->Id);?>';
    var nrg = '<?php print($perawatan->NoReg);?>';
    var jnr = '<?php print($perawatan->JnsRawat);?>';
    var cby = '<?php print($perawatan->CaraBayar);?>';
    var urx = '<?php print($helper->site_url("pelayanan.tindakan/GetJSonActivePatient"));?>';
    var urz = '<?php print($helper->site_url("pelayanan.tindakan/GetJSonJasaList/"));?>'+jnr;
    var urf;

    function nformat (value, row){
        if (value > 0) {
            var nf = new Intl.NumberFormat();
            return nf.format(value);
        }
    }

    $( function() {
        $('#NoReg').combogrid({
            panelWidth: 600,
            url: urx,
            idField: 'no_reg',
            textField: 'no_reg',
            mode: 'remote',
            fitColumns: true,
            columns: [[
                {field: 'no_reg', title: 'No. Registrasi', width: 30},
                {field: 'nm_pasien', title: 'Nama Pasien', width: 80},
                {field: 'kmr_rawat', title: 'Poli/Kamar', width: 30},
                {field: 'tgl_masuk', title: 'Tgl Masuk', width: 40}
            ]],
            onSelect: function (index, row) {
                idx = row.id;
                jnr = row.jns_rawat;
                nrg = row.no_reg;
                var nmp = row.nm_pasien;
                console.log(nmp);
                $('#NmPasien').textbox('setValue',nmp);
                $('#NoReg1').textbox('setValue',nrg);
                $('#JnsRawat').textbox('setValue',jnr);
                var kmr = row.kmr_rawat;
                console.log(kmr);
                $('#KmrRawat').textbox('setValue',kmr);
                var tms = row.tgl_masuk;
                console.log(tms);
                $('#TglMasuk').textbox('setValue',tms);
                urz = '<?php print($helper->site_url("pelayanan.tindakan/GetJSonTindakanList/"));?>'+nrg;
                $('#dglayanan').datagrid('load',urz);
                urz = "<?php print($helper->site_url("pelayanan.tindakan/GetJSonJasaList/"));?>"+jnr;
                var dg = $('#NmJasa').combogrid('grid');
                dg.datagrid('load', urz);
            }
        });

        $('#NmJasa').combogrid({
            panelWidth: 850,
            url: urz,
            idField: 'nm_jasa',
            textField: 'nm_jasa',
            mode: 'remote',
            fitColumns: true,
            columns: [[
                {field: 'nm_jasa', title: 'Jasa/Layanan', width: 100},
                {field: 'uraian_jasa', title: 'Uraian Jasa', width: 100},
                {field: 'kd_jasa', title: 'Kode Jasa', width: 50},
                {field: 'satuan', title: 'Satuan', width: 40},
                {field: 'tarif', title: 'Tarif', width: 40, align: 'right'},
                {field: 'ket_bpjs', title: 'Ditanggung', width: 50},
            ]],
            onSelect: function (index, row) {
                var kjs = row.kd_jasa;
                $('#JnsRawat').textbox('setValue',jnr);
                //console.log(kjs);
                $('#KdJasa').textbox('setValue',kjs);
                var ujs = row.uraian_jasa;
                $('#UraianJasa').textbox('setValue',ujs);
                var tjs = row.tarif;
                $('#Tarif').textbox('setValue',tjs);
                var sjs = row.satuan;
                $('#Satuan').textbox('setValue',sjs);
                var qty = 1;
                $('#Qty').textbox('setValue',qty);
                var jum = tjs * qty;
                $('#Jumlah').textbox('setValue',jum);
                var isb = row.is_bpjs;
                if (isb == 1 && cby == 2) {
                    $('#Ditanggung').textbox('setValue', 'BPJS');
                    $('#IsBpjs').textbox('setValue', 1);
                }else{
                    $('#Ditanggung').textbox('setValue','');
                    $('#IsBpjs').textbox('setValue', 0);
                }
            }
        });

        $('#Qty').textbox({
            onChange: function(value){
                //console.log('The value has been changed to ' + value);
                var tjs = $('#Tarif').val();
                var jum = tjs * value;
                $('#Jumlah').textbox('setValue',jum);
            }
        });
    });

    function tbhLayanan() {
        $('#dlform').dialog('open').dialog('setTitle','Tambah Data Layanan');
        $('#frm').form('clear');
        var DNow = '<?php print(date('d-m-Y'));?>';
        var TNow = '<?php print(date('h:i'));?>';
        $('#TglLayanan').textbox('setValue',DNow);
        $('#JamLayanan').textbox('setValue',TNow);
        $('#NoReg1').textbox('setValue',nrg);
        urf = '<?php print($helper->site_url("pelayanan.tindakan/addnew"));?>';
    }

    function editLayanan() {
        var row = $('#dglayanan').datagrid('getSelected');
        if (row){
            $.messager.confirm('Confirm','Ubah Data Layanan ini?',function(r){
                if (r) {
                    $('#dlform').dialog('open').dialog('setTitle', 'Ubah Data Layanan');
                    $('#frm').form('clear');
                    $('#TglLayanan').textbox('setValue', row.tgl_layanan);
                    $('#JamLayanan').textbox('setValue', row.jam_layanan);
                    $('#NoReg1').textbox('setValue', row.no_reg);
                    if (row.kd_petugas != null) {
                        $('#KdPetugas').combobox('setValue', row.kd_petugas);
                    }
                    if (row.kd_dokter != null) {
                        $('#KdDokter').combobox('setValue', row.kd_dokter);
                    }
                    $('#KdJasa').textbox('setValue', row.kd_jasa);
                    $('#NmJasa').textbox('setValue', row.nm_jasa);
                    $('#UraianJasa').textbox('setValue', row.uraian_jasa);
                    $('#Keterangan').textbox('setValue', row.keterangan);
                    $('#Tarif').textbox('setValue', row.tarif_harga);
                    $('#Qty').textbox('setValue', row.qty);
                    $('#Jumlah').textbox('setValue', row.tarif_harga * row.qty);
                    if (row.is_bpjs == 1 && cby == 2) {
                        $('#Ditanggung').textbox('setValue', 'BPJS');
                        $('#IsBpjs').textbox('setValue', 1);
                    }else{
                        $('#Ditanggung').textbox('setValue','');
                        $('#IsBpjs').textbox('setValue', 0);
                    }
                    urf = '<?php print($helper->site_url("pelayanan.tindakan/update/"));?>' + row.id;
                }
            });
        }
    }

    function saveLayanan(){
        $.messager.confirm('Confirm','Apakah Data yang diinput sudah benar?',function(r) {
            if (r) {
                $('#frm').form('submit', {
                    url: urf,
                    onSubmit: function () {
                        return $(this).form('validate');
                    },
                    success: function (result) {
                        var result = eval('(' + result + ')');
                        if (result.errorMsg) {
                            $.messager.show({
                                title: 'Error',
                                msg: result.errorMsg
                            });
                        } else {
                            $('#dlform').dialog('close');		// close the dialog
                            $('#dglayanan').datagrid('reload');	// reload the user data
                        }
                    }
                });
            }
        });
    }

    function hpsLayanan(){
        var row = $('#dglayanan').datagrid('getSelected');
        if (row){
            $.messager.confirm('Confirm','Hapus Data Layanan ini?',function(r){
                if (r){
                    $.post('<?php print($helper->site_url("pelayanan.tindakan/delete"));?>',{id:row.id},function(result){
                        if (result.success){
                            $('#dglayanan').datagrid('reload');    // reload the user data
                        } else {
                            $.messager.show({    // show error message
                                title: 'Error',
                                msg: result.errorMsg
                            });
                        }
                    },'json');
                }
            });
        }
    }

    //datebox custom formatting
    $.extend($.fn.calendar.methods, {
        moveTo: function(jq, date){
            return jq.each(function(){
                if (!date){
                    var now = new Date();
                    $(this).calendar({
                        year: now.getFullYear(),
                        month: now.getMonth()+1,
                        current: date
                    });
                    return;
                }
                var opts = $(this).calendar('options');
                if (opts.validator.call(this, date)){
                    var oldValue = opts.current;
                    $(this).calendar({
                        year: date.getFullYear(),
                        month: date.getMonth()+1,
                        current: date
                    });
                    if (!oldValue || oldValue.getTime() != date.getTime()){
                        opts.onChange.call(this, opts.current, oldValue);
                    }
                }
            });
        }
    });
    $.extend($.fn.datebox.defaults, {
        formatter: function(date){
            if (!date){return ' ';}
            var y = date.getFullYear();
            var m = date.getMonth()+1;
            var d = date.getDate();
            return (d<10?('0'+d):d)+'-'+(m<10?('0'+m):m)+'-'+y;
        },
        parser: function(s){
            if (!s) return null;
            var ss = s.split('-');
            var d = parseInt(ss[0],10);
            var m = parseInt(ss[1],10);
            var y = parseInt(ss[2],10);
            if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
                return new Date(y,m-1,d);
            } else {
                return null;
            }
        }
    });
</script>
</body>
</html>
