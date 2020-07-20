<!DOCTYPE HTML>
<?php /** @var $pembelian Pembelian */ /** @var $klpasset KlpAsset[] */ ?>
<html>
<head>
	<title>ERAMEDIKA - Entry Data Pembelian Asset</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/autoNumeric.js")); ?>"></script>
	<script type="text/javascript">
		$(document).ready(function() {
            var elements = ["SbuId","JnsPembelian","BankId","TglPembelian","KdRelasi","KdKlpAsset","NoReff","NmAsset","KdAsset","Qty","Harga","Jumlah","MasaManfaat","DeprYear","ApreYear","Simpan"];
			BatchFocusRegister(elements);
            $("#TglPembelian").customDatePicker({ showOn: "focus" });
            //$("#Harga").autoNumeric({ vMax: "99999999999999" });
            //$("#Jumlah").autoNumeric({ vMax: "99999999999999" });
            //$("#Qty").autoNumeric({ vMax: "99999999999999" });

            var urz = '<?php print($helper->site_url("asset.assetlist/AutoKode"));?>';
            $("#NmAsset").change(function (e) {
                $.get(urz, function(data, status){
                    //alert("Data: " + data + "\nStatus: " + status);
                    $("#KdAsset").val(data);
                });
            });

            $("#Qty").change(function() {
                hitungJumlah($("#Qty").val(),$("#Harga").val());
            });

            $("#Harga").change(function() {
                hitungJumlah($("#Qty").val(),$("#Harga").val());
            });

            $("#MasaManfaat").change(function() {
                hitungDeprYear($("#MasaManfaat").val());
            });

            $(".numeric").change(function() {
                numFormat();
            });

            $("#frm").submit(function(e) {
                $(".numeric").each(function(idx, ele){
                    this.value  = $(ele).autoNumericGet({mDec: '0'});
                });
            });
		});

		function hitungJumlah(qty,harga) {
            qty = Number(qty.replace(",",""));
            harga = Number(harga.replace(",",""));
            $("#Jumlah").val(qty * harga);
        }

        function hitungDeprYear(mm) {
            mm = Number(mm.replace(",",""));
            $("#DeprYear").val(100 / mm);
        }

        function numFormat() {
            $(".numeric").each(function(idx, ele) {
                var temp = $(ele).autoNumeric({ mDec: 0 });
            });
        }
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
	<legend><b>Entry Data Pembelian Asset</b></legend>
	<form id="frm" action="<?php print($helper->site_url("asset.pembelian/add")); ?>" method="post">
		<table cellpadding="2" cellspacing="1">
            <tr>
                <td>Lokasi Asset</td>
                <td><select name="SbuId" class="text2" id="SbuId" style="width: 100px;" required>
                        <?php
                        /** @var $sbulist Sbu[]*/
                        foreach ($sbulist as $sbu) {
                            if ($pembelian->SbuId == $sbu->Id) {
                                printf('<option value="%d" selected="selected"> %s - %s </option>', $sbu->Id, $sbu->Id, $sbu->SbuName);
                            } else {
                                printf('<option value="%d"> %s - %s </option>', $sbu->Id, $sbu->Id, $sbu->SbuName);
                            }
                        }
                        ?>
                    </select>
                </td>
                <td>Cara Pembelian</td>
                <td><select name="JnsPembelian" id="JnsPembelian" required>
                        <option value="1"> 1 - Tunai </option>
                        <option value="2"> 2 - Kredit </option>
                    </select>
                </td>
                <td>Via Kas/Bank
                    &nbsp;&nbsp;
                    <select name="BankId" id="BankId">
                        <option value=""></option>
                        <?php
                        /** @var $banks Bank[] */
                        foreach ($banks as $kasbank){
                            if ($pembelian->BankId == $kasbank->AccNo) {
                                printf('<option value="%s" selected="selected"> %s </option>', $kasbank->AccNo, $kasbank->Name);
                            }else{
                                printf('<option value="%s"> %s </option>',$kasbank->AccNo,$kasbank->Name);
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Tgl Pembelian</td>
                <td><input type="text" class="text2" name="TglPembelian" id="TglPembelian" maxlength="10" size="15" value="<?php print($pembelian->FormatTglPembelian(JS_DATE)); ?>" required/></td>
                <td>No. Bukti</td>
                <td colspan="2"><input type="text" class="text2" name="NoBukti" id="NoBukti" maxlength="15" size="15" value="<?php print($pembelian->NoBukti); ?>" readonly placeholder="AUTO"/></td>
            </tr>
            <tr>
                <td>Nama Supplier</td>
                <td colspan="2"><select name="KdRelasi" class="text2" id="KdRelasi"style="width: 225px" required>
                        <option value=""></option>
                        <?php
                        /** @var $relasilist Relasi[] */
                        foreach ($relasilist as $relasi) {
                            if ($pembelian->KdRelasi == $relasi->KdRelasi) {
                                printf('<option value="%s" selected="selected">%s</option>', $relasi->KdRelasi, $relasi->NmRelasi);
                            } else {
                                printf('<option value="%s">%s</option>', $relasi->KdRelasi, $relasi->NmRelasi);
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Kelompok Asset</td>
                <td colspan="2"><select name="KdKlpAsset" class="text2" id="KdKlpAsset"style="width: 225px" required>
                        <option value=""></option>
                        <?php
                        foreach ($klpasset as $klp) {
                            if ($pembelian->KdKlpAsset == $klp->KdKlpAsset) {
                                printf('<option value="%s" selected="selected">%s</option>', $klp->KdKlpAsset, $klp->KlpAsset);
                            } else {
                                printf('<option value="%s">%s</option>', $klp->KdKlpAsset, $klp->KlpAsset);
                            }
                        }
                        ?>
                    </select>
                </td>
                <td>No. Refferensi</td>
                <td><input type="text" class="text2" name="NoReff" id="NoReff" maxlength="100" size="25" value="<?php print($pembelian->NoReff); ?>"/></td>
            </tr>
			<tr>
				<td>Nama Asset</td>
				<td colspan="2"><input type="text" class="text2" name="NmAsset" id="NmAsset" maxlength="150" size="40" value="<?php print($pembelian->NmAsset); ?>" required/></td>
                <td>Kode Asset</td>
                <td><input type="text" class="text2" name="KdAsset" id="KdAsset" maxlength="15" size="15" value="<?php print($pembelian->KdAsset); ?>" required readonly placeholder="AUTO"/></td>
			</tr>
            <tr>
                <td>QTY</td>
                <td colspan="3">
                    <input type="text" class="right numeric" name="Qty" id="Qty" maxlength="3" size="3" value="<?php print($pembelian->Qty); ?>" required/>
                    &nbsp;&nbsp;Harga
                    <input type="text" class="right numeric" name="Harga" id="Harga" maxlength="50" size="12" value="<?php print($pembelian->Harga); ?>" required/>
                    &nbsp;&nbsp;Jumlah
                    <input type="text" class="right numeric" name="Jumlah" id="Jumlah" maxlength="50" size="15" value="<?php print($pembelian->Jumlah); ?>" required readonly/>
                </td>
            </tr>
            <tr>
                <td>Masa Manfaat</td>
                <td><input type="text" class="right" name="MasaManfaat" id="MasaManfaat" maxlength="3" size="10" value="<?php print($pembelian->MasaManfaat); ?>" required/> Tahun</td>
            </tr>
            <tr>
                <td>Depresiasi</td>
                <td><input type="text" class="right" name="DeprYear" id="DeprYear" maxlength="3" size="10" value="<?php print($pembelian->DeprYear); ?>"/> % Per Tahun</td>
            </tr>
            <tr>
                <td>Apresiasi</td>
                <td><input type="text" class="right" name="ApreYear" id="ApreYear" maxlength="3" size="10" value="<?php print($pembelian->ApreYear); ?>"/> % Per Tahun</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
				<td colspan="3">
					<button id="Simpan" type="submit">Simpan</button>
					<a href="<?php print($helper->site_url("asset.pembelian")); ?>" class="button">Daftar Pembelian Asset</a>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
</body>
</html>
