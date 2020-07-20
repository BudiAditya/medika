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
            font-size: 25px;
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
<div id="p" class="easyui-panel" title="<b>View Nota Penjualan</b>" style="width:100%;height:100%;padding:10px;" data-options="footer:'#ft'">
    <table>
        <tr>
            <td style="width: 100%">
                <table cellpadding="0" cellspacing="0" class="tablePadding" align="left" style="font-size: 13px;font-family: tahoma">
                    <tr>
                        <td>Tanggal</td>
                        <td><input class="easyui-textbox bold" id="Tanggal" name="Tanggal" value="<?php print($invoice->Tanggal);?>" style="width:100px;" readonly>
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
                        <td><input type="text" class="easyui-textbox" id="NoRm" name="NoRm" style="width: 150px" value="<?php print($invoice->NoRm);?>" readonly/></td>
                        <td>No. Invoice</td>
                        <td><input type="text" class="f1 easyui-textbox" style="width: 150px" id="TrxNo1" name="TrxNo1" value="<?php print($invoice->TrxNo != null ? $invoice->TrxNo : '-'); ?>" disabled/></td>
                    </tr>
                    <tr>
                        <td>Customer</td>
                        <td><input class="easyui-textbox" id="Kontak" name="Kontak" style="width: 250px" value="<?php print($invoice->Kontak);?>" readonly/>
                        </td>
                        <td>Jenis Pasien</td>
                        <td><select class="easyui-combobox" id="JenisPasien" name="JenisPasien" style="width: 150px" disabled>
                                <option value="UMUM" <?php print($invoice->JenisPasien == 'UMUM' ? 'selected="selected"' : '');?>> UMUM </option>
                                <option value="BPJS" <?php print($invoice->JenisPasien == 'BPJS' ? 'selected="selected"' : '');?>> BPJS </option>
                            </select>
                        </td>
                        <td>Cara Bayar</td>
                        <td><select class="easyui-combobox" id="CaraBayar" name="CaraBayar" style="width: 150px" disabled>
                                <option value="1" <?php print($invoice->CaraBayar == 1 ? 'selected="selected"' : '');?>> TUNAI </option>
                                <option value="2" <?php print($invoice->CaraBayar == 2 ? 'selected="selected"' : '');?>> KREDIT </option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Dokter</td>
                        <td><input class="easyui-textbox" id="NmDokter" name="NmDokter" style="width: 250px" value="<?php print($invoice->NmDokter);?>" readonly/></td>
                        <td>Jenis Obat</td>
                        <td><select class="easyui-combobox" id="JnsBeli" name="JnsBeli" style="width: 150px" disabled>
                                <option value="BEBAS" <?php print($invoice->JnsBeli == 'BEBAS' ? 'selected="selected"' : '');?>> BEBAS </option>
                                <option value="RESEP" <?php print($invoice->JnsBeli == 'RESEP' ? 'selected="selected"' : '');?>> RESEP </option>
                            </select>
                        </td>
                        <td>No. Resep</td>
                        <td><input type="text" class="f1 easyui-textbox" style="width: 150px" id="NoResep" name="NoResep" value="<?php print($invoice->NoResep); ?>" readonly/></td>
                    </tr>
                    <tr>
                        <td>Nama Pasien</td>
                        <td><input class="f1 easyui-textbox" id="NmPasien" name="NmPasien" style="width: 250px" value="<?php print($invoice->NmPasien);?>" readonly/>
                        <td>Umur</td>
                        <td><input type="text" class="f1 easyui-textbox" style="width: 50px" id="UmrPasien" name="UmrPasien" value="<?php print($invoice->UmrPasien); ?>" readonly/> Tahun</td>
                        <td>No. HP</td>
                        <td><input type="text" class="f1 easyui-textbox" style="width: 150px" id="NoHp" name="NoHp" value="<?php print($invoice->NoHp); ?>" readonly/></td>
                    </tr>
                    <tr>
                        <td>Catatan</td>
                        <td colspan="3"><b><input class="f1 easyui-textbox" id="Uraian" name="Uraian" style="width: 500px" value="<?php print($invoice->Uraian != null ? $invoice->Uraian : '-'); ?>" readonly/></b></td>
                        <td colspan="2">
                            <div>
                                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="closeMaster()" style="width:90px;height: 23px">Tutup</a>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
            <td valign="top">
                <table cellpadding="0" cellspacing="0" class="tablePadding" align="right" style="font-size: 18px;font-family: tahoma">
                    <tr>
                        <td align="right" nowrap="nowrap">TOTAL :</td>
                        <td>
                            <input type="text" class="easyui-numberbox numberbox-f validatebox-text" data-options="precision:0,groupSeparator:',',decimalSeparator:'.'"  id="trxAmt" value="<?php print($invoice->TotalTransaksi);?>" style="width: 200px; height: 35px" readonly/>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" nowrap="nowrap">CASH :</td>
                        <td>
                            <input type="text" class="easyui-numberbox numberbox-f validatebox-text" data-options="precision:0,groupSeparator:',',decimalSeparator:'.'"  id="cashAmt" value="<?php print($invoice->JumlahBayar);?>" style="width: 200px; height: 35px"/>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" nowrap="nowrap">KEMBALIAN :</td>
                        <td>
                            <input type="text" class="easyui-numberbox numberbox-f validatebox-text" data-options="precision:0,groupSeparator:',',decimalSeparator:'.'"  id="retAmt" value="<?php print($invoice->JumlahBayar == 0 ? 0 : $invoice->JumlahBayar - $invoice->TotalTransaksi);?>" style="width: 200px; height: 35px" readonly/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="right">
                            <?php
                            if ($invoice->TrxStatus == 1 && $invoice->TotalTransaksi > 0) {
                                print('<div >');
                                print('<a href = "javascript:void(0)" class="easyui-linkbutton" iconCls = "icon-reload" style = "width:150px;height: 30px" id="btBayar" ><b > PROSES BAYAR </b ></a >');
                                print('</div >');
                            }else{
                                print('&nbsp;');
                            }
                            ?>
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
                        print("<td class='center'>&nbsp;</td>");
                        print("</tr>");
                        $total += $detail->SubTotal;
                    }
                    ?>
                    <tr>
                        <td colspan="9" align="right">Sub Total :</td>
                        <td><input type="text" class="right bold" style="width: 150px" id="SubTotal" name="SubTotal" value="<?php print($invoice->SubTotal != null ? number_format($invoice->SubTotal,0) : 0); ?>" readonly/></td>
                        <td>&nbsp</td>
                    </tr>
                    <tr>
                        <td colspan="9" align="right">Diskon (%) :</td>
                        <td><input type="text" class="right bold" style="width: 30px" id="DiskonPersen" name="DiskonPersen" value="<?php print($invoice->DiskonPersen != null ? number_format($invoice->DiskonPersen,1) : 0); ?>"/>
                            <input type="text" class="right bold" style="width: 110px" id="DiskonNilai" name="DiskonNilai" value="<?php print($invoice->DiskonNilai != null ? number_format($invoice->DiskonNilai,0) : 0); ?>" readonly/></td>
                        <?php if ($acl->CheckUserAccess("apotek.invoice", "edit") && $invoice->TrxStatus < 2) { ?>
                            <td class='center'><?php printf('<img src="%s" alt="Edit Data" title="Edit Data" id="bUpdate" style="cursor: pointer;"/>',$bedit);?></td>
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

<script type="text/javascript">
    //script javascript disini
    $( function() {
        //global variables

        //proses pembayaran
        $("#cashAmt").textbox({
            onChange: function (value) {
                var cashAmt = value;
                var trxAmt = $("#trxAmt").textbox("getValue");
                var retAmt = cashAmt - trxAmt;
                $("#retAmt").textbox("setValue",retAmt.toLocaleString('en-IN'));
                if (retAmt >= 0){
                    $("#btBayar").focus();
                }
            }
        });

        $("#btBayar").click(function (e) {
            var invoiceId = "<?php print($invoice->Id);?>";
            var trxAmt = $("#trxAmt").textbox("getValue");
            var cashAmt = $("#cashAmt").textbox("getValue");
            var retAmt = $("#retAmt").textbox("getValue");
            var urx = null;
            var oke = 0;
            if (trxAmt > 0) {
                if (cashAmt == 0) {
                    $("#cashAmt").textbox("setValue", $("#trxAmt").textbox("getValue").toLocaleString('en-IN'));
                    $("#retAmt").textbox("setValue", 0);
                }
                //proses bayar disini
                if (confirm("Proses Pembayaran?")) {
                    var urx = "<?php print($helper->site_url("apotek.invoice/prosesBayar/")); ?>"+invoiceId;
                    $.post(urx, {
                        caraBayar: $("#CaraBayar").combobox("getValue"),
                        //trxNo: $("#TrxNo").val(),
                        //trxAmt: trxAmt,
                        cashAmt: $("#cashAmt").textbox("getValue")
                        //retAmt: $("#retAmt").textbox("getValue")
                    }).done(function(data) {
                        var rst = data.split('|');
                        if (rst[0] == 'OK') {
                            if (confirm ("Print struk ?")) {
                                printStruk();
                            }
                            location.href = "<?php print($helper->site_url("apotek.invoice/add/0")); ?>";
                        }else{
                            alert('Proses Pembayaran gagal!');
                        }
                    });
                }
            }
        });

        $("#bTambah").click(function(){
            if (confirm('Buat Nota baru?')){
                location.href="<?php print($helper->site_url("apotek.invoice/add/0")); ?>";
            }
        });

        $("#bUpdate").click(function(){
            if (confirm('Ubah nota ini?')){
                location.href="<?php print($helper->site_url("apotek.invoice/add/".$invoice->Id)); ?>";
            }
        });

        $("#bHapus").click(function(){
            if (confirm('Anda yakin akan membatalkan nota ini?')){
                location.href="<?php print($helper->site_url("apotek.invoice/void/").$invoice->Id); ?>";
            }
        });

        $("#bCetak").click(function(){
            var tStatus = Number("<?php print($invoice->TrxStatus);?>");
            if (tStatus == 2) {
                if (confirm('Print Struk nota ini?')) {
                    printStruk();
                }
            }else{
                alert("Struk belum boleh di-print!")
            }
        });

       $("#bKembali").click(function(){
            location.href="<?php print($helper->site_url("apotek.invoice")); ?>";
        });

    });

    function printStruk(){
        var urx = "<?php print($helper->site_url("apotek.invoice/printStruk/").$invoice->Id); ?>";
        $.get(urx, function (data, status) {
            //alert("Data: " + data + "\nStatus: " + status);
            if (status == 'success') {
                var dtx = data.split('|');
                if (dtx[0] == 'OK') {
                    //alert(dtx[1]);
                } else {
                    alert('ER1 - '+dtx[1]+'!');
                }
            } else {
                alert('ER2 - Printing fail!');
            }
        });
    }

    function closeMaster() {
        location.href="<?php print($helper->site_url("apotek.invoice")); ?>";
    }
</script>
</body>
</html>
