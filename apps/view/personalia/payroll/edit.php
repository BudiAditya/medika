<!DOCTYPE HTML>
<?php /** @var $payslip Payroll */
require_once (LIBRARY  . "gen_functions.php");
?>
<html>
<head>
	<title>ERASYS - Ubah Data Gaji Karyawan</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/autoNumeric.js")); ?>"></script>
    <style>
        tr.separated td {
            /* set border style for separated rows */
            border-bottom: 1px solid black;
        }

        table {
            /* make the border continuous (without gaps between columns) */
            border-collapse: collapse;
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function() {
            var elements = ["Gapok","TjJabatan","TjProfesi","BpjsKes","BpjsTk","Thr","FeeTikhus","FeeJasmed","FeeProfit","PotAbsensi","PotPiutang","PotLain","btSubmit"];
            BatchFocusRegister(elements);

            ReCalcThp();
            $(".in").add(".out").change(function() {
                ReCalcThp();
            });
        });

        function ReCalcThp() {
            var totalIn = 0;
            var totalOut = 0;

            $(".in").each(function(idx, ele) {
                var temp = $(ele).autoNumeric({ mDec: 0 });
                totalIn += parseFloat(temp.autoNumericGet({ mDec: 0 }));
            });
            $(".out").each(function(idx, ele) {
                var temp = $(ele).autoNumeric({ mDec: 0 });
                totalOut += parseFloat(temp.autoNumericGet({ mDec: 0 }));
            });

            $("#totalIn").val($.fn.autoNumeric.Format("totalIn", totalIn, { mDec: 0 }));
            $("#totalOut").val($.fn.autoNumeric.Format("totalOut", totalOut, { mDec: 0 }));
            $("#thp").val($.fn.autoNumeric.Format("thp", totalIn - totalOut, { mDec: 0 }));

            $("#frm").submit(function(e) {
                $(".numeric").each(function(idx, ele){
                    this.value  = $(ele).autoNumericGet({mDec: '0'});
                });
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
	<legend><b>Ubah Slip Gaji Karyawan</b></legend>
	<form id="frm" action="<?php print($helper->site_url("personalia/payroll/edit/".$payslip->Id)); ?>" method="post">
        <input type="hidden" id="Id" name="Id" value="<?php print($payslip->Id); ?>"/>
        <input type="hidden" id="Bulan" name="Bulan" value="<?php print($payslip->Bulan); ?>"/>
        <input type="hidden" id="Tahun" name="Tahun" value="<?php print($payslip->Tahun); ?>"/>
        <table cellpadding="2" cellspacing="1">
            <tr>
                <td>Bisnis Unit</td>
                <td><b><?php print(strtoupper($payslip->SbuName)); ?></b></td>
                <td>&nbsp;</td>
                <td>Periode Gaji</td>
                <td><b><?php printf("%s %d",get_bulan($payslip->Bulan),$payslip->Tahun); ?></b></td>
            </tr>
			<tr>
				<td>N I K</td>
                <td><input type="text" class="text2" name="Nik" id="Nik" maxlength="10" size="15" value="<?php print($payslip->Nik); ?>" readonly/></td>
                <td>&nbsp;</td>
                <td>Nama Karyawan</td>
                <td><input type="text" class="text2" name="Nama" id="Nama" maxlength="50" size="30" value="<?php print($payslip->Nama); ?>" readonly /></td>
            </tr>
            <tr>
                <td>Bagian</td>
                <td><input type="text" class="text2" name="Bagian" id="Bagian" maxlength="10" size="15" value="<?php print($payslip->Bagian); ?>" readonly/></td>
                <td>&nbsp;</td>
                <td>Jabatan</td>
                <td><input type="text" class="text2" name="Jabatan" id="Jabatan" maxlength="50" size="30" value="<?php print($payslip->Jabatan); ?>" readonly /></td>
            </tr>
            <tr class="separated">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="2"><b><u>Penerimaan:</u></b></td>
                <td>&nbsp;</td>
                <td colspan="2"><b><u>Potongan:</u></b></td>
            </tr>
            <tr>
                <td>Gaji Pokok</td>
                <td><input type="text" class="right numeric in" name="Gapok" id="Gapok" maxlength="15" size="15" value="<?php print(number_format($payslip->Gapok,0)); ?>" required/></td>
                <td>&nbsp;</td>
                <td>Potongan Absensi</td>
                <td><input type="text" class="right numeric out" name="PotAbsensi" id="PotAbsensi" maxlength="15" size="15" value="<?php print(number_format($payslip->PotAbsensi,0)); ?>" required/></td>
            </tr>
            <tr>
                <td>Tj. Jabatan</td>
                <td><input type="text" class="right numeric in" name="TjJabatan" id="TjJabatan" maxlength="15" size="15" value="<?php print(number_format($payslip->TjJabatan,0)); ?>" required/></td>
                <td>&nbsp;</td>
                <td>Potongan Piutang</td>
                <td><input type="text" class="right numeric out" name="PotPiutang" id="PotPiutang" maxlength="15" size="15" value="<?php print(number_format($payslip->PotPiutang,0)); ?>" required/></td>
            </tr>
            <tr>
                <td>Tj. Profesi</td>
                <td><input type="text" class="right numeric in" name="TjProfesi" id="TjProfesi" maxlength="15" size="15" value="<?php print(number_format($payslip->TjProfesi,0)); ?>" required/></td>
                <td>&nbsp;</td>
                <td>Potongan BPJS</td>
                <td><input type="text" class="right numeric out" name="PotBpjsKes" id="PotBpjsKes" maxlength="15" size="15" value="<?php print(number_format($payslip->PotBpjsKes,0)); ?>"/></td>
            </tr>
            <tr>
                <td>BPJS Kesehatan</td>
                <td><input type="text" class="right numeric in" name="BpjsKes" id="BpjsKes" maxlength="15" size="15" value="<?php print(number_format($payslip->BpjsKes,0)); ?>"/></td>
                <td>&nbsp;</td>
                <td>Potongan Lain2</td>
                <td><input type="text" class="right numeric out" name="PotLain" id="PotLain" maxlength="15" size="15" value="<?php print(number_format($payslip->PotLain,0)); ?>"/></td>
            </tr>
            <tr>
                <td>BPJS T.K.</td>
                <td><input type="text" class="right numeric in" name="BpjsTk" id="BpjsTk" maxlength="15" size="15" value="<?php print(number_format($payslip->BpjsTk,0)); ?>"/></td>
            </tr>
            <tr>
                <td>T H R</td>
                <td><input type="text" class="right numeric in" name="Thr" id="Thr" maxlength="15" size="15" value="<?php print(number_format($payslip->Thr,0)); ?>"/></td>
            </tr>
            <tr>
                <td>Fee Dokter Jaga</td>
                <td><input type="text" class="right numeric in" name="FeeDokterJaga" id="FeeDokterJaga" maxlength="15" size="15" value="<?php print(number_format($payslip->FeeDokterJaga,0)); ?>"/></td>
            </tr>
            <tr>
                <td>Fee JM Khusus</td>
                <td><input type="text" class="right numeric in" name="FeeTikhus" id="FeeTikhus" maxlength="15" size="15" value="<?php print(number_format($payslip->FeeTikhus,0)); ?>"/></td>
            </tr>
            <tr>
                <td>Fee JM Visit Dokter</td>
                <td><input type="text" class="right numeric in" name="FeeJasmed" id="FeeJasmed" maxlength="15" size="15" value="<?php print(number_format($payslip->FeeJasmed,0)); ?>"/></td>
            </tr>
            <tr>
                <td>Fee 10% Profit</td>
                <td><input type="text" class="right numeric in" name="FeeProfit" id="FeeProfit" maxlength="15" size="15" value="<?php print(number_format($payslip->FeeProfit,0)); ?>"/></td>
            </tr>
            <tr class="separated">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>Jumlah Penerimaan</td>
                <td><input type="text" class="bold right numeric" name="totalIn" id="totalIn" maxlength="15" size="11" value="0" readonly/></td>
                <td>&nbsp;</td>
                <td>Jumlah Potongan</td>
                <td><input type="text" class="bold right numeric" name="totalOut" id="totalOut" maxlength="15" size="11" value="0" readonly/></td>
            </tr>
            <tr>
                <td>T H P</td>
                <td><input type="text" class="bold right numeric" name="thp" id="thp" maxlength="15" size="11" value="0" readonly/></td>
            </tr>
			<tr>
                <td colspan="2">&nbsp;</td>
				<td colspan="3">
					<button id="btSubmit" type="submit">Update</button>
					<a href="<?php print($helper->site_url("personalia/payroll/view/".$payslip->Tahun."/".$payslip->Bulan)); ?>" class="button">Data Slip Gaji Karyawan</a>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
</body>
</html>
