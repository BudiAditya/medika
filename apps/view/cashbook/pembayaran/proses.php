<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
<?php
/** @var $pembayaran Hutang */ ?>
<head>
	<title>ERASYS - Proses Pembayaran Hutang Supplier</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/auto-numeric.js")); ?>"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            var elements = ["TglTerbayar","JumTerbayar","BankId","Update"];
            BatchFocusRegister(elements);
            $("#TglTerbayar").customDatePicker({ showOn: "focus" });
            // autoNumeric
            $(".num").autoNumeric({mDec: '0'});
            $("#frm").submit(function(e) {
                $(".num").each(function(idx, ele){
                    this.value  = $(ele).autoNumericGet({mDec: '0'});
                });
            });
        });
    </script>
</head>
<body>
<?php include(VIEW . "main/menu.php"); ?>
<?php if (isset($error)) { ?>
<div class="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
<div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php } ?>

<br />
<fieldset>
	<legend><b>Proses Pembayaran Hutang Supplier</b></legend>
	<form id="frm" action="<?php print($helper->site_url("cashbook.pembayaran/proses/".$pembayaran->Id)); ?>" method="post">
        <table cellpadding="2" cellspacing="2">
            <tr>
                <td>Tanggal</td>
                <td><input type="text" class="text2" maxlength="10" size="20" id="TglHutang" name="TglHutang1" value="<?php print($pembayaran->FormatTglHutang(JS_DATE)); ?>" disabled/></td>
                <td>No. Bukti</td>
                <td><input type="text" class="text2" maxlength="20" size="20" id="NoBukti1" name="NoBukti1" value="<?php print($pembayaran->NoBukti == null ? 'Auto Number' : $pembayaran->NoBukti); ?>" disabled/></td>
            </tr>
            <tr>
                <td>Keterangan</td>
                <td colspan="3"><input type="text" class="text2" maxlength="250" size="63" id="Keterangan1" name="Keterangan1" value="<?php print($pembayaran->Keterangan == null ? '-' : $pembayaran->Keterangan);?>" disabled/></td>
            </tr>
            <tr>
                <td>Kode Supplier</td>
                <td><input type="text" class="text2" maxlength="20" size="20" id="KdRelasi1" name="KdRelasi1" value="<?php print($pembayaran->KdRelasi == null ? '-' : $pembayaran->KdRelasi);?>" disabled/></td>
                <td>No. Refferensi</td>
                <td><input type="text" class="text2" maxlength="20" size="20" id="NoReff1" name="NoReff1" value="<?php print($pembayaran->NoReff == null ? '-' : $pembayaran->NoReff);?>" disabled/></td>
            </tr>
            <tr>
                <td>Jumlah Hutang</td>
                <td><input type="text" class="num" id="JumHutang" name="JumHutang" size="20" maxlength="20" value="<?php print(number_format($pembayaran->JumHutang,0)); ?>" style="text-align: right" readonly/></td>
            </tr>
            <tr>
                <td><b><u>Data Pembayaran :</u></b></td>
            </tr>
            <tr>
                <td>Tanggal Dibayar</td>
                <td><input type="text" class="text2" maxlength="10" size="20" id="TglTerbayar" name="TglTerbayar" value="<?php print($pembayaran->FormatTglTerbayar(JS_DATE)); ?>" required/></td>
                <td>No. Bukti</td>
                <td><input type="text" class="text2" maxlength="20" size="20" id="NoBuktiBayar" name="NoBuktiBayar" value="<?php print($pembayaran->NoBuktiBayar == null ? 'AUTO' : $pembayaran->NoBuktiBayar); ?>" disabled/></td>
            </tr>
            <tr>
                <td>Jumlah Dibayar</td>
                <td><input type="text" class="num" id="JumTerbayar" name="JumTerbayar" size="20" maxlength="20" value="<?php print($pembayaran->JumTerbayar == null ? 0 : $pembayaran->JumTerbayar); ?>" style="text-align: right" required/></td>
                <td>Via Kas/Bank</td>
                <td><select name="BankId" id="BankId" style="width: 128px">
                        <option value=""></option>
                        <?php
                        /** @var $banks Bank[] */
                        foreach ($banks as $kasbank){
                            if ($pembayaran->BankId == $kasbank->AccNo){
                                printf('<option value="%s" selected="selected"> %s </option>',$kasbank->AccNo,$kasbank->Name);
                            }else {
                                printf('<option value="%s"> %s </option>', $kasbank->AccNo, $kasbank->Name);
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
			<tr>
                <td>&nbsp;</td>
				<td colspan="3">
					<button type="submit" id="Update">Proses</button>
					<a href="<?php print($helper->site_url("cashbook.pembayaran")); ?>" class="button">Daftar Hutang</a>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
</body>
</html>
