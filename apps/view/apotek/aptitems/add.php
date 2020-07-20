<!DOCTYPE HTML>
<?php
/** @var $items AptItems */
/** @var $relasi Relasi[] */
/** @var $itemtype ItemType[] */
/** @var $itemgroup ItemGroup[] */
/** @var $itemunit ItemUnit[] */
?>

<html>
<head>
	<title>ERAMEDIKA - Tambah Data Barang/Obat</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			//var elements = ["TypeCode", "TypeName", "TypeDescs"];
			//BatchFocusRegister(elements);
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
	<legend><b>Tambah Data Barang/Obat</b></legend>
	<form id="frm" action="<?php print($helper->site_url("apotek.aptitems/add")); ?>" method="post">
        <input type="hidden" name="ItemCode" id="ItemCode" value="<?php print($items->ItemCode); ?>"/>
        <table cellpadding="2" cellspacing="1">
            <tr>
                <td valign="top">
                    <h4><u>DETAIL BARANG</u></h4>
                    <table cellpadding="2" cellspacing="1">
                        <tr class="bold">
                            <td>Kode Barang</td>
                            <td><input type="text" class="text2 bold" name="ItemCode1" id="ItemCode1" maxlength="20" size="10" value="<?php print($items->ItemCode); ?>" disabled /></td>
                            <td>Bar Code</td>
                            <td><input type="text" class="text2 bold" name="ItemBarCode" id="ItemBarCode" maxlength="30" size="20" value="<?php print($items->ItemBarCode); ?>" /></td>
                        </tr>
                        <tr class="bold">
                            <td>Nama Barang</td>
                            <td colspan="3"><input type="text" class="text2 bold" name="ItemName" id="ItemName" maxlength="100" size="44" value="<?php print($items->ItemName); ?>" required /></td>
                        </tr>
                        <tr class="bold">
                            <td>Keterangan</td>
                            <td colspan="3"><input type="text" class="text2 bold" name="ItemDescs" id="ItemDescs" maxlength="150" size="44" value="<?php print($items->ItemDescs); ?>" /></td>
                        </tr>
                        <tr class="bold">
                            <td>Jenis Barang</td>
                            <td colspan="2">
                                <select name="ItemTypeCode" id="ItemTypeCode" style="width: 160px" required>
                                    <option value="">-- Pilih Jenis Barang --</option>
                                    <?php
                                    foreach ($itemtype as $type){
                                        if($items->ItemTypeCode == $type->TypeCode){
                                            printf("<option value='%s' selected='selected'> %s </option>",$type->TypeCode,$type->TypeCode);
                                        }else{
                                            printf("<option value='%s'> %s </option>",$type->TypeCode,$type->TypeCode);
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>Satuan
                                &nbsp;
                                <select name="ItemUnit" id="ItemUnit" style="width: 120px" required>
                                    <option value="">-- Satuan Barang --</option>
                                    <?php
                                    foreach ($itemunit as $unit){
                                        if($items->ItemUnit == $unit->UnitCode){
                                            printf("<option value='%s' selected='selected'> %s </option>",$unit->UnitCode,$unit->UnitCode);
                                        }else{
                                            printf("<option value='%s'> %s </option>",$unit->UnitCode,$unit->UnitCode);
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr class="bold">
                            <td>Golongan</td>
                            <td colspan="2">
                                <select name="ItemGroupCode" id="ItemGroupCode" style="width: 160px" required>
                                    <option value="">-- Pilih Golongan --</option>
                                    <?php
                                    foreach ($itemgroup as $group){
                                        if($items->ItemGroupCode == $group->GroupCode){
                                            printf("<option value='%s' selected='selected'> %s </option>",$group->GroupCode,$group->GroupCode);
                                        }else{
                                            printf("<option value='%s'> %s </option>",$group->GroupCode,$group->GroupCode);
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr class="bold">
                            <td>Supplier</td>
                            <td colspan="3">
                                <select name="SupplierCode" id="SupplierCode" style="width:335px">
                                    <option value="">-- Pilih Supplier --</option>
                                    <?php
                                    foreach ($relasi as $supplier){
                                        if($items->SupplierCode == $supplier->KdRelasi){
                                            printf("<option value='%s' selected='selected'> %s - %s </option>",$supplier->KdRelasi,$supplier->KdRelasi,$supplier->NmRelasi);
                                        }else{
                                            printf("<option value='%s'> %s - %s </option>",$supplier->KdRelasi,$supplier->KdRelasi,$supplier->NmRelasi);
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr class="bold">
                            <td>Jenis Obat</td>
                            <td><select name="IsBpjs" id="IsBpjs" style="width:100px">
                                    <option value="0" <?php print($items->IsBpjs == 0 ? 'selected="selected"' : '');?>>0 - Umum</option>
                                    <option value="1" <?php print($items->IsBpjs == 1 ? 'selected="selected"' : '');?>>1 - BPJS</option>
                                </select>

                            </td>
                        </tr>
                    </table>
                </td>
                <td>&nbsp;&nbsp;&nbsp;</td>
                <td valign="top">
                    <h4><u>DETAIL HARGA & STOK</u></h4>
                    <table cellpadding="2" cellspacing="1">
                        <tr class="bold">
                            <td>Harga Beli</td>
                            <td><input type="text" class="text2 bold right" name="PurchasePrice" id="PurchasePrice" maxlength="15" size="12" value="<?php print($items->PurchasePrice); ?>"/></td>
                            <td>HPP</td>
                            <td><input type="text" class="text2 bold right" name="CogsValue" id="CogsValue" maxlength="15" size="12" value="<?php print($items->CogsValue); ?>"/></td>
                        </tr>
                        <tr class="bold">
                            <td>Margin (%)</td>
                            <td><input type="text" class="text2 bold right" name="SpMarkup1" id="SpMarkup1" maxlength="15" size="12" value="<?php print($items->SpMarkup1); ?>"/></td>
                            <td>Harga Jual</td>
                            <td><input type="text" class="text2 bold right" name="SalePrice1" id="SalePrice1" maxlength="15" size="12" value="<?php print($items->SalePrice1); ?>"/></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr class="bold">
                            <td>Posisi Stok </td>
                            <td><input type="text" class="text2 bold right" name="ItemStockQty" id="ItemStockQty" maxlength="15" size="12" value="<?php print($items->ItemStockQty); ?>"/></td>
                            <td>Minimum Stok</td>
                            <td><input type="text" class="text2 bold right" name="MinStockQty" id="MinStockQty" maxlength="15" size="12" value="<?php print($items->MinStockQty); ?>"/></td>
                        </tr>
                        <tr class="bold">
                            <td>Boleh Minus</td>
                            <td><select name="AllowOutOfStock" id="AllowOutOfStock" style="width:100px">
                                    <option value="0" <?php print($items->AllowOutOfStock == 0 ? 'selected="selected"' : '');?>>0 - Tidak</option>
                                    <option value="1" <?php print($items->AllowOutOfStock == 1 ? 'selected="selected"' : '');?>>1 - Boleh</option>
                                </select>

                            </td>
                        </tr>
                        <tr class="bold">
                            <td>Barang Aktif</td>
                            <td><select name="IsAktif" id="IsAktif" style="width:100px">
                                    <option value="0" <?php print($items->IsAktif == 0 ? 'selected="selected"' : '');?>>0 - Tidak</option>
                                    <option value="1" <?php print($items->IsAktif == 1 ? 'selected="selected"' : '');?>>1 - Aktif</option>
                                </select>

                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr class="bold">
                            <td>Sudah Obsolet</td>
                            <td><select name="IsObsolete" id="IsObsolete" style="width:100px">
                                    <option value="0" <?php print($items->IsObsolete == 0 ? 'selected="selected"' : '');?>>0 - Tidak</option>
                                    <option value="1" <?php print($items->IsObsolete == 1 ? 'selected="selected"' : '');?>>1 - Iya</option>
                                </select>

                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="center">
                    <button type="submit"><b>SIMPAN DATA</b></button>
                    <a href="<?php print($helper->site_url("apotek.aptitems")); ?>" class="button">Daftar Barang/Obat</a>
                </td>
            </tr>
        </table>
	</form>
</fieldset>
</body>
</html>
