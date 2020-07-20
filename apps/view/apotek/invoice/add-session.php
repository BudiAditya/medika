<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
<?php
/** @var $invoice Invoice */
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
//get session value
$persistence = PersistenceManager::GetInstance();
$invDate = $persistence->LoadState("invDate");
$relasiId = $persistence->LoadState("relasiId");
$nmPasien = $persistence->LoadState("nmPasien");
?>
<br />
<div id="p" class="easyui-panel" title="<b>Entry Nota Penjualan</b>" style="width:100%;height:100%;padding:10px;" data-options="footer:'#ft'">
    <table>
        <tr>
            <td style="width: 100%">
                <form id="fmaster" method="post">
                <table cellpadding="0" cellspacing="0" class="tablePadding" align="left" style="font-size: 13px;font-family: tahoma">
                    <tr>
                        <td>Tanggal</td>
                        <td><input class="easyui-datebox bold" id="Tanggal" name="Tanggal" value="<?php print($invDate);?>" data-options="formatter:myformatter,parser:myparser" style="width:100px;">
                            &nbsp;&nbsp;<b>
                            <?php
                            if($invoice->TrxStatus == 1) {
                                print('- PENDING -');
                            }elseif ($invoice->TrxStatus == 2){
                                print('- POSTED -');
                            }elseif ($invoice->TrxStatus == 3) {
                                print('- VOID -');
                            }else{
                                print('- DRAFT -');
                            }
                            ?>
                            </b>
                        </td>
                        <td>No. MedRek</td>
                        <td><input type="text" class="easyui-textbox" id="NoRm" name="NoRm" style="width: 150px" value="<?php print($invoice->NoRm);?>"/></td>
                        <td>No. Invoice</td>
                        <td><input type="text" class="f1 easyui-textbox" style="width: 150px" id="TrxNo1" name="TrxNo1" value="<?php print($invoice->TrxNo != null ? $invoice->TrxNo : '-'); ?>" disabled/></td>
                    </tr>
                    <tr>
                        <td>Customer</td>
                        <td><input class="easyui-combogrid" id="RelasiId" name="RelasiId" style="width: 250px" value="<?php print($relasiId);?>" required autofocus/>
                            <input type="hidden" id="Kontak" name="Kontak" value="<?php print($invoice->Kontak);?>"/>
                            <input type="hidden" id="InvoiceId" name="InvoiceId" value="<?php print($invoice->Id);?>"/>
                            <input type="hidden" id="TrxNo" name="TrxNo" value="<?php print($invoice->TrxNo);?>"/>
                            <input type="hidden" id="TrxStatus" name="TrxStatus" value="<?php print($invoice->TrxStatus);?>"/>
                        </td>
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
                        <td>Nama Pasien</td>
                        <td><input class="f1 easyui-textbox" id="NmPasien" name="NmPasien" style="width: 250px" value="<?php print($nmPasien);?>"/>
                        <td>Umur</td>
                        <td><input type="text" class="f1 easyui-textbox" style="width: 50px" id="UmrPasien" name="UmrPasien" value="<?php print($invoice->UmrPasien); ?>"/> Tahun</td>
                        <td>No. HP</td>
                        <td><input type="text" class="f1 easyui-textbox" style="width: 150px" id="NoHp" name="NoHp" value="<?php print($invoice->NoHp); ?>"/></td>
                    </tr>
                    <tr>
                        <td>Catatan</td>
                        <td colspan="3"><b><input class="f1 easyui-textbox" id="Uraian" name="Uraian" style="width: 500px" value="<?php print($invoice->Uraian != null ? $invoice->Uraian : '-'); ?>" required/></b></td>
                        <td colspan="2">
                            <div>
                                <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveMaster()" style="width:90px;height: 23px">Simpan</a>
                                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="closeMaster()" style="width:90px;height: 23px">Tutup</a>
                            </div>
                        </td>
                    </tr>
                </table>
                </form>
            </td>
            <td valign="top">
                <table cellpadding="0" cellspacing="0" class="tablePadding" align="right">
                    <tr>
                        <td>
                            <input type="text" class="easyui-numberbox numberbox-f validatebox-text" data-options="precision:0,groupSeparator:'.',decimalSeparator:','"  id="grandTotal" value="<?php print($invoice->TotalTransaksi);?>" style="width: 350px; height: 60px" readonly/>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2"><b>Perincian Barang:</b></td>
        </tr>
        <tr>
            <td colspan="2">
                <table cellpadding="0" cellspacing="0" class="tablePadding tableBorder" align="left" style="font-size: 12px;font-family: tahoma">
                    <tr>
                        <th>No.</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>QTY</th>
                        <th>Satuan</th>
                        <th>Harga</th>
                        <th>Disc(%)</th>
                        <th>Diskon</th>
                        <th>Gratis</th>
                        <th>Jumlah</th>
                        <th>Action</th>
                    </tr>
                    <?php
                    $counter = 0;
                    $total = 0;
                    $dta = null;
                    $dtx = null;
                    foreach($invoice->Details as $idx => $detail) {
                        $counter++;
                        print("<tr class='bold'>");
                        printf('<td class="right">%s.</td>', $counter);
                        printf('<td>%s</td>', $detail->KodeBarang);
                        printf('<td>%s</td>', $detail->NamaBarang);
                        printf('<td class="right">%s</td>', number_format($detail->QtyKeluar,0));
                        printf('<td>%s</td>', $detail->Satuan);
                        printf('<td class="right">%s</td>', number_format($detail->Harga,0));
                        printf('<td class="right">%s</td>', number_format($detail->DiskonPersen,0));
                        printf('<td class="right">%s</td>', number_format($detail->DiskonNilai,0));
                        if($detail->IsFree == 0){
                            print("<td class='center'><input type='checkbox' disabled></td>");
                        }else{
                            print("<td class='center'><input type='checkbox' checked='checked' disabled></td>");
                        }
                        printf('<td class="right">%s</td>', number_format($detail->SubTotal,0));
                        print("<td class='center'>");
                        $dtx = addslashes($detail->Id.'|'.$detail->KodeBarang.'|'.str_replace('"',' in',$detail->NamaBarang).'|'.$detail->QtyKeluar.'|'.$detail->Satuan.'|'.$detail->Harga.'|'.$detail->DiskonPersen.'|'.$detail->DiskonNilai.'|'.$detail->DiskonNilai.'|'.$detail->IsFree.'|'.$detail->SubTotal.'|'.$detail->HppNilai);
                        printf('&nbsp<img src="%s" alt="Edit barang" title="Edit barang" style="cursor: pointer" onclick="return feditdetail(%s,%s);"/>',$bedit,"'".$dtx."'",$invoice->RelasiId);
                        printf('&nbsp<img src="%s" alt="Hapus barang" title="Hapus barang" style="cursor: pointer" onclick="return fdeldetail(%s);"/>',$bclose,"'".$dtx."'");
                        print("</td>");
                        print("</tr>");
                        $total += $detail->SubTotal;
                    }
                    ?>
                    <tr>
                        <td colspan="9" align="right">Sub Total :</td>
                        <td><input type="text" class="right bold" style="width: 150px" id="SubTotal" name="SubTotal" value="<?php print($invoice->SubTotal != null ? number_format($invoice->SubTotal,0) : 0); ?>" readonly/></td>
                        <?php if ($acl->CheckUserAccess("apotek.invoice", "add")) { ?>
                            <td class='center'><?php printf('<img src="%s" alt="Tambah Barang" title="Tambah Barang Detail" id="bAdDetail" style="cursor: pointer;"/>',$badd);?></td>
                        <?php }else{ ?>
                            <td>&nbsp</td>
                        <?php } ?>
                    </tr>
                    <tr>
                        <td colspan="9" align="right">Diskon (%) :</td>
                        <td><input type="text" class="right bold" style="width: 30px" id="DiskonPersen" name="DiskonPersen" value="<?php print($invoice->DiskonPersen != null ? number_format($invoice->DiskonPersen,1) : 0); ?>"/>
                            <input type="text" class="right bold" style="width: 110px" id="DiskonNilai" name="DiskonNilai" value="<?php print($invoice->DiskonNilai != null ? number_format($invoice->DiskonNilai,0) : 0); ?>" readonly/></td>
                        <?php if ($acl->CheckUserAccess("apotek.invoice", "add")) { ?>
                            <td class='center'><?php printf('<img src="%s" alt="Simpan Data" title="Simpan data master" id="bUpdate" style="cursor: pointer;"/>',$bsubmit);?></td>
                        <?php }else{ ?>
                            <td>&nbsp</td>
                        <?php } ?>
                    </tr>
                    <tr>
                        <td colspan="9" align="right">D P P :</td>
                        <td><input type="text" class="right bold" style="width: 150px" id="DppAmount" name="DppAmount" value="<?php print(number_format($invoice->SubTotal - $invoice->DiskonNilai,0)); ?>" readonly/></td>
                        <?php if ($acl->CheckUserAccess("apotek.invoice", "add")) { ?>
                            <td class='center'><?php printf('<img src="%s" alt="Invoice Baru" title="Buat invoice baru" id="bTambah" style="cursor: pointer;"/>',$baddnew);?></td>
                        <?php }else{ ?>
                            <td>&nbsp</td>
                        <?php } ?>
                    </tr>
                    <tr>
                        <td colspan="9" align="right">Pajak (%) :</td>
                        <td><input type="text" class="right bold" style="width: 30px" id="PajakPersen" name="PajakPersen" value="<?php print($invoice->PajakPersen != null ? $invoice->PajakPersen : 0); ?>"/>
                            <input type="text" class="right bold" style="width: 110px" id="PajakNilai" name="PajakNilai" value="<?php print($invoice->PajakNilai != null ? number_format($invoice->PajakNilai,0) : 0); ?>"/></td>
                        <?php if ($acl->CheckUserAccess("apotek.invoice", "delete")) { ?>
                            <td class='center'><?php printf('<img src="%s" alt="Hapus Invoice" title="Proses hapus invoice" id="bHapus" style="cursor: pointer;"/>',$bdelete);?></td>
                        <?php }else{ ?>
                            <td>&nbsp</td>
                        <?php } ?>
                    </tr>
                    <tr>
                        <td colspan="9" align="right">Biaya Lain :</td>
                        <td><input type="text" class="right bold" style="width: 150px" id="BiayaNilai" name="BiayaNilai" value="<?php print($invoice->BiayaNilai != null ? number_format($invoice->BiayaNilai,0) : 0); ?>"/></td>
                        <?php if ($acl->CheckUserAccess("apotek.invoice", "print")) { ?>
                            <td class='center'><?php printf('<img src="%s" id="bCetak" alt="Cetak Invoice" title="Proses cetak invoice" style="cursor: pointer;"/>',$bcetak);?></td>
                        <?php }else{ ?>
                            <td>&nbsp</td>
                        <?php } ?>
                    </tr>
                    <tr>
                        <td colspan="9" align="right">Grand Total :</td>
                        <td><input type="text" class="right bold" style="width: 150px;" id="TotalTransaksi" name="TotalTransaksi" value="<?php print($invoice->TotalTransaksi != null ? number_format($invoice->TotalTransaksi,0) : 0); ?>" readonly/></td>
                        <td class='center'><?php printf('<img src="%s" id="bKembali" alt="Daftar Invoice" title="Kembali ke daftar invoice" style="cursor: pointer;"/>',$bkembali);?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
<div id="ft" style="padding:5px; text-align: center; font-family: verdana; font-size: 9px" >
    Copyright &copy; 2016 - 2018  PT. Rekasystem Technology
</div>

<!-- Form Add/Edit Invoice Detail -->
<div id="dlg" class="easyui-dialog" style="width:1150px;height:200px;padding:5px 5px"
     closed="true" buttons="#dlg-buttons">
    <form id="fm" method="post" novalidate>
        <table cellpadding="0" cellspacing="0" class="tablePadding tableBorder" style="font-size: 13px;font-family: tahoma">
            <tr>
                <td class="right">Cari Data Obat:</td>
                <td colspan="8"><input class="easyui-combogrid" id="aItemSearch" name="aItemSearch" style="width:600px"/></td>
            </tr>
            <tr>
                <th>Kode**</th>
                <th>Nama Barang</th>
                <th>Qty</th>
                <th>Satuan</th>
                <th>Harga</th>
                <th>Discount (Per Item)</th>
                <th>Gratis</th>
                <th>Jumlah</th>
            </tr>
            <tr>
                <td>
                    <input type="text" class="bold" id="aItemCode" name="aItemCode" size="10" value="" required/>
                    <input type="hidden" id="aId" name="aId" value="0"/>
                    <input type="hidden" id="aQtyStock" name="aQtyStock" value="0"/>
                    <input type="hidden" id="aItemHpp" name="aItemHpp" value="0"/>
                    <input type="hidden" id="aMode" name="aMode" value="0"/>
                </td>
                <td>
                    <input type="text" class="bold" id="aItemName" name="aItemName" size="45" value="" readonly/>
                </td>
                <td>
                    <input class="right bold" type="text" id="aQty" name="aQty" size="5" value="0"/>
                </td>
                <td>
                    <input type="text" class="bold" id="aSatuan" name="aSatuan" size="5" value="" disabled/>
                </td>
                <td>
                    <input class="right bold" type="text" id="aHarga" name="aHarga" size="10" value="0"/>
                </td>
                <td>
                    <input class="right bold" type="text" id="aDiskonPersen" name="aDiskonPersen" size="2" maxlength="2" value="0"/>% =
                    <input class="right bold" type="text" id="aDiskonNilai" name="aDiskonNilai" size="10" value="0"/>
                </td>
                <td>
                    <input class="right" type="checkbox" id="aIsFree" name="aIsFree" value="0"/>
                </td>
                <td>
                    <input class="right bold" type="text" id="aSubTotal" name="aSubTotal" style="width:100px" value="0" readonly/>
                </td>
            </tr>
        </table>
    </form>
    <span style="color: red" class="blink"><b>**Ketik Kode Barang atau Scan BarCode agar lebih cepat**</b></span>
    <br>
</div>
<div id="dlg-buttons">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveDetail()" style="width:90px">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg').dialog('close')" style="width:90px">Batal</a>
</div>

<script type="text/javascript">
    //script javascript disini
    //overide tab agar bisa move next field on enter
    (function($){
        $.extend($.fn.textbox.defaults.inputEvents, {
            keydown: function(e){
                if (e.keyCode == 13){
                    var t = $(e.data.target);
                    t.textbox('setValue', t.textbox('getText'));

                    var all = $('input:visible').not(':disabled').toArray().sort(function(a,b){
                        var t1 = parseInt($(a).attr('tabindex')||1);
                        var t2 = parseInt($(b).attr('tabindex')||1);
                        return t1==t2?0:(t1>t2?1:-1);
                    });
                    var index = $.inArray(this, all);
                    var nextIndex = (index+1) % all.length;
                    $(all[nextIndex]).focus();
                }
            }
        })

    })(jQuery);


    $( function() {
        //isi detail  barang
        $("#bAdDetail").click(function (e) {
            //deteksi data master sudah diisi belum?
            var rli = $("#RelasiId").combogrid("getValue");
            if (rli > 0) {
                $('#aMode').val(0);
                $('#aItemCode').val('');
                $('#aItemName').val('');
                $('#aSatuan').val('');
                $('#aHarga').val(0);
                $('#aQty').val(0);
                $('#aQtyStock').val(0);
                $('#aDiskonPersen').val('0');
                $('#aDiskonNilai').val(0);
                $('#aSubTotal').val(0);
                $('#aIsFree').val(0);
                $('#aItemHpp').val(0);
                newItem();
            }else{
                alert("Data Master belum lengkap!");
            }
        });

        //data barang grid
        $('#aItemSearch').combogrid({
            panelWidth:600,
            url: "<?php print($helper->site_url('apotek/invoice/getStockJson'));?>",
            idField:'id',
            textField:'item_code',
            mode:'remote',
            fitColumns:true,
            columns:[[
                {field:'item_code',title:'Kode',width:50},
                {field:'item_name',title:'Nama Barang',width:200},
                {field:'qty_stock',title:'Stock',width:40,align:'right'},
                {field:'item_unit',title:'Satuan',width:40},
                {field:'harga_jual',title:'Harga',width:70,align:'right'}
            ]],
            onSelect: function(index,row){
                var bid = row.id;
                console.log(bid);
                var bkode = row.item_code;
                console.log(bkode);
                var bnama = row.item_name;
                console.log(bnama);
                var satuan = row.item_unit;
                console.log(satuan);
                var bqstock = row.qty_stock;
                console.log(bqstock);
                var bharga = row.harga_jual;
                console.log(bharga);
                var bhpp = row.cogs_value;
                console.log(bhpp);
                $('#aItemCode').val(bkode);
                $('#aItemName').val(bnama);
                $('#aSatuan').val(satuan);
                $('#aQtyStock').val(bqstock);
                $('#aHarga').val(bharga);
                $('#aDiskonPersen').val(0);
                $('#aDiskonNilai').val(0);
                $('#aItemHpp').val(bhpp);
                $('#aQty').val(1);
                hitDetail();
            }
        });

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
                var nmr = row.nm_relasi;
                $("#Kontak").val(nmr);
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
        $("#NoRm").textbox({
            onChange: function(value) {
                //$data = "OK|".$pasien->NmPasien."|".$pasien->Usia."|".$pasien->NoHp;
                var norm = value;
                var url = "<?php print($helper->site_url("apotek.invoice/getPlainPasien/"));?>" + norm;
                if (norm != '') {
                    $.get(url, function (data, status) {
                        //alert("Data: " + data + "\nStatus: " + status);
                        if (status == 'success') {
                            var dtx = data.split('|');
                            if (dtx[0] == 'OK') {
                                $('#NmPasien').textbox('setValue',dtx[1]);
                                $('#UmrPasien').textbox('setValue',dtx[2]);
                                $('#NoHp').textbox('setValue',dtx[3]);
                            } else {
                                alert('ER1 - Data Medrek No: ['+norm+'] tidak ditemukan!');
                                $('#NoRm').textbox("setValue","");
                            }
                        } else {
                            alert('ER2 - Data Medrek No: ['+norm+'] tidak ditemukan!');
                            $('#NoRm').textbox("setValue","");
                        }
                    });
                }
            }
        });

        $("#aItemCode").change(function(e){
            //$data = "OK|".$stock->ItemName."|".$stock->ItemStockQty."|".$stock->ItemUnit."|".$stock->SalePrice1."|".$stock->PurchasePrice."|".$stock->CogsValue;
            var itc = $("#aItemCode").val();
            var url = "<?php print($helper->site_url('apotek.invoice/getStockPlain/'));?>"+itc;
            if (itc != ''){
                $.get(url, function(data, status){
                    //alert("Data: " + data + "\nStatus: " + status);
                    if (status == 'success'){
                        var dtx = data.split('|');
                        if (dtx[0] == 'OK'){
                            $('#aItemName').val(dtx[1]);
                            $('#aSatuan').val(dtx[3]);
                            $('#aQtyStock').val(dtx[2]);
                            $('#aHarga').val(dtx[4]);
                            $('#aDiskonPersen').val(0);
                            $('#aDiskonNilai').val(0);
                            $('#aItemHpp').val(dtx[6]);
                            $('#aQty').val(1);
                            hitDetail();
                        }else{
                            $('#aItemCode').val('');
                            $('#aItemName').val('');
                            $('#aSatuan').val('');
                            $('#aHarga').val(0);
                            $('#aQty').val(0);
                            $('#aQtyStock').val(0);
                            $('#aDiskonPersen').val('0');
                            $('#aDiskonNilai').val(0);
                            $('#aSubTotal').val(0);
                            $('#aIsFree').val(0);
                            $('#aItemHpp').val(0);
                            alert('Data Barang ini tidak ditemukan!');
                        }
                    }else{
                        $('#aItemCode').val('');
                        $('#aItemName').val('');
                        $('#aSatuan').val('');
                        $('#aHarga').val(0);
                        $('#aQty').val(0);
                        $('#aQtyStock').val(0);
                        $('#aDiskonPersen').val('0');
                        $('#aDiskonNilai').val(0);
                        $('#aSubTotal').val(0);
                        $('#aIsFree').val(0);
                        $('#aItemHpp').val(0);
                        alert('Data Barang ini tidak ditemukan!');
                    }
                });
            }
        });

        $("#aHarga").change(function(e){
            hitDetail();
        });

        $("#aQty").change(function(e){
            hitDetail();
        });

        $("#aDiskonPersen").change(function(e){
            hitDetail();
        });

        $("#aDiskonNilai").change(function(e){
            hitDetail();
        });

        $('#aIsFree').change(function () {
            if (this.checked){
                $('#aIsFree').val(1);
            }else{
                $('#aIsFree').val(0);
            }
            hitDetail();
        });

        $("#bUpdate").click(function(){
            saveMaster();
        });

        $("#bTambah").click(function(){
            if (confirm('Buat Nota baru?')){
                location.href="<?php print($helper->site_url("apotek.invoice/add/0")); ?>";
            }
        });

        $("#bHapus").click(function(){
            if (confirm('Anda yakin akan membatalkan nota ini?')){
                location.href="<?php print($helper->site_url("apotek.invoice/void/").$invoice->Id); ?>";
            }
        });

        $("#bKembali").click(function(){
            location.href="<?php print($helper->site_url("apotek.invoice")); ?>";
        });

    });

    function newItem(){
        $('#dlg').dialog('open').dialog('setTitle','Tambah Detail Barang yang dijual');
        $('#fm').form('clear');
    }

    function saveMaster () {
        //validasi master
        var custId = $("#RelasiId").combogrid('getValue');
        var invoiceId = $("#InvoiceId").val();
        if (custId > 0){
            if (confirm('Update data invoice ini?')) {
                var url = "<?php print($helper->site_url("apotek.invoice/proses_master/")); ?>"+invoiceId;
                //proses simpan dan update master
                $.post(url, {
                    Tanggal: $("#Tanggal").val(),
                    TrxNo: $("#TrxNo").val(),
                    RelasiId: $("#RelasiId").val(),
                    CustomerId: custId,
                    CustLevel: $("#CustLevel").val(),
                    SalesId: salesId,
                    PaymentType: $("#PaymentType").val(),
                    CreditTerms: $("#CreditTerms").val(),
                    BaseAmount: $("#BaseAmount").val(),
                    Disc1Pct: $("#Disc1Pct").val(),
                    Disc1Amount: $("#Disc1Amount").val(),
                    TaxPct: $("#TaxPct").val(),
                    TaxAmount: $("#TaxAmount").val(),
                    OtherCosts: $("#OtherCosts").val(),
                    OtherCostsAmount: $("#OtherCostsAmount").val(),
                    ExSoNo: $("#ExSoNo").val(),
                    InvoiceType: ivcType
                }).done(function(data) {
                    var rst = data.split('|');
                    if (rst[0] == 'OK') {
                        location.href = "<?php print($helper->site_url("apotek.invoice/add/")); ?>" + invoiceId;
                    }else{
                        alert('Data Invoice gagal diupdate!');
                    }
                });
            }
        }else{
            alert('Data Update tidak valid!');
        }
    }

    function closeMaster() {
        location.href="<?php print($helper->site_url("apotek.invoice")); ?>";
    }

    function hitDetail(){
        var isFree = Number($("#aIsFree").val());
        var subTotal = 0;
        var discAmount = Number($("#aDiskonNilai").val().replace(/,/g,""));
        var totalDetail = 0;
        var discPct = Number($("#aDiskonPersen").val().replace(/,/g,""));
        if (isFree == 0){
            subTotal = (Number($("#aQty").val().replace(/,/g,"")) * Number($("#aHarga").val().replace(/,/g,"")));
            if (discPct > 0){
                discAmount = Math.round((discPct/100) * Number($("#aHarga").val().replace(/,/g,"")));
            }
            totalDetail = subTotal - (discAmount * Number($("#aQty").val().replace(/,/g,"")));
        }
        $('#aDiskonNilai').val(discAmount);
        $('#aSubTotal').val(totalDetail);
    }

    function hitMaster(){
        var bam = Number($("#SubTotal").val().replace(/,/g,""));
        var dpc = Number($("#DiskonPersen").val().replace(/,/g,""));
        var tpc = Number($("#PajakPersen").val().replace(/,/g,""));
        var oca = Number($("#BiayaNilai").val().replace(/,/g,""));
        var dam = 0;
        var tam = 0;
        var dpp = 0;
        if (bam > 0 && dpc > 0 ){
            dam = Math.round(bam * (dpc/100),0);
            $("#DiskonNilai").val(dam);
        }else{
            $("#DiskonNilai").val(0);
        }
        dpp = bam - dam;
        $("#DppAmount").val(dpp);
        if (dpp > 0 && tpc > 0 ){
            tam = Math.round(dpp * (tpc/100),0);
            $("#PajakNilai").val(tam);
        }else{
            $("#PajakNilai").val(0);
        }
        $("#TotalTransaksi").val(dpp+tam+oca);
    }

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
