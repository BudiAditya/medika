<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
<?php
/** @var $penerimaan Piutang */ ?>
<head>
	<title>ERASYS - Entry Penerimaan Piutang Pasien</title>
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
	<legend><b>Proses Entry Penerimaan Piutang Pasien</b></legend>
	<form id="frm" action="<?php print($helper->site_url("cashbook.penerimaan/proses/".$penerimaan->Id)); ?>" method="post">
        <table cellpadding="2" cellspacing="2">
            <tr>
                <td>Tanggal</td>
                <td><input type="text" class="text2" maxlength="10" size="20" id="TglPiutang" name="TglPiutang1" value="<?php print($penerimaan->FormatTglPiutang(JS_DATE)); ?>" disabled/></td>
                <td>No. Bukti</td>
                <td><input type="text" class="text2" maxlength="20" size="20" id="NoBukti1" name="NoBukti1" value="<?php print($penerimaan->NoBukti == null ? 'Auto Number' : $penerimaan->NoBukti); ?>" disabled/></td>
            </tr>
            <tr>
                <td>Keterangan</td>
                <td colspan="3"><input type="text" class="text2" maxlength="250" size="63" id="Keterangan1" name="Keterangan1" value="<?php print($penerimaan->Keterangan == null ? '-' : $penerimaan->Keterangan);?>" disabled/></td>
            </tr>
            <tr>
                <td>No. Medrek</td>
                <td><input type="text" class="text2" maxlength="20" size="20" id="NoRmPasien1" name="NoRmPasien1" value="<?php print($penerimaan->NoRmPasien == null ? '-' : $penerimaan->NoRmPasien);?>" disabled/></td>
                <td>No. Refferensi</td>
                <td><input type="text" class="text2" maxlength="20" size="20" id="NoReg1" name="NoReg1" value="<?php print($penerimaan->NoReg == null ? '-' : $penerimaan->NoReg);?>" disabled/></td>
            </tr>
            <tr>
                <td>Jumlah Klaim</td>
                <td><input type="text" class="num" id="JumPiutang1" name="JumPiutang1" size="20" maxlength="20" value="<?php print(number_format($penerimaan->JumPiutang,0)); ?>" style="text-align: right" disabled/></td>
            </tr>
            <tr>
                <td colspan="2"><b><u>Data Penerimaan :</u></b></td>
            </tr>
            <tr>
                <td>Tanggal Dibayar</td>
                <td><input type="text" class="text2" maxlength="10" size="20" id="TglTerbayar" name="TglTerbayar" value="<?php print($penerimaan->FormatTglTerbayar(JS_DATE)); ?>" required/></td>
                <td>No. Bukti</td>
                <td><input type="text" class="text2" maxlength="20" size="20" id="NoBuktiBayar" name="NoBuktiBayar" value="<?php print($penerimaan->NoBuktiBayar == null ? 'AUTO' : $penerimaan->NoBuktiBayar); ?>" disabled/></td>
            </tr>
            <tr>
                <td>Jumlah Dibayar</td>
                <td><input type="text" class="num" id="JumTerbayar" name="JumTerbayar" size="20" maxlength="20" value="<?php print($penerimaan->JumTerbayar == null ? 0 : $penerimaan->JumTerbayar); ?>" style="text-align: right" required/></td>
                <td>Via Kas/Bank</td>
                <td><select name="BankId" id="BankId" style="width: 128px">
                        <?php
                        /** @var $banks Bank[] */
                        foreach ($banks as $kasbank){
                            printf('<option value="%s"> %s </option>',$kasbank->AccNo,$kasbank->Name);
                        }
                        ?>
                    </select>
                </td>
            </tr>
			<tr>
                <td>&nbsp;</td>
				<td colspan="3">
					<button type="submit" id="Update">Proses</button>
					<a href="<?php print($helper->site_url("cashbook.penerimaan")); ?>" class="button">Daftar Klaim</a>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
</body>
</html>
