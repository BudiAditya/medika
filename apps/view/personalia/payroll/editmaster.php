<!DOCTYPE HTML>
<?php /** @var $gaji Gaji */ /** @var $dokters Dokter[] */ /** @var $karyawan Karyawan */ ?>
<html>
<head>
	<title>ERASYS - Ubah Data Gaji Karyawan</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/autoNumeric.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
	<script type="text/javascript">
		$(document).ready(function() {
            var elements = ["Gapok","TjJabatan","TjProfesi","BpjsKes","BpjsTk","Thr","KdDokter","IsFeeProfit","IsFeeTikhus","IsFeeJasmed","btSubmit"];
			BatchFocusRegister(elements);

            $(".numeric").change(function() {
                numFormat();
            });

            $("#frm").submit(function(e) {
                $(".numeric").each(function(idx, ele){
                    this.value  = $(ele).autoNumericGet({mDec: '0'});
                });
            });
		});

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
	<legend><b>Ubah Master Gaji Karyawan</b></legend>
	<form id="frm" action="<?php print($helper->site_url("personalia/payroll/editmaster/".$sbi.'/'.$kid)); ?>" method="post">
        <input type="hidden" id="Id" name="Id" value="<?php print($gaji->Id); ?>"/>
        <input type="hidden" id="fMode" name="fMode" value="<?php print($fmode); ?>"/>
        <table cellpadding="2" cellspacing="1">
            <tr>
                <td>Bisnis Unit</td>
                <td colspan="3"><select name="SbuId" class="text2" id="SbuId" disabled>
                        <?php
                        /** @var $sbus Sbu[] */
                        foreach ($sbus as $sbu) {
                            if ($sbu->Id == $sbi) {
                                printf('<option value="%d" selected="selected"> %s </option>', $sbu->Id, $sbu->SbuName);
                            } else {
                                printf('<option value="%d"> %s </option>', $sbu->Id, $sbu->SbuName);
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
			<tr>
				<td>N I K</td>
                <td colspan="3"><input type="text" class="text2" name="Nik" id="Nik" maxlength="10" size="15" value="<?php print($karyawan->Nik); ?>" disabled/></td>
			</tr>
            <tr>
                <td>Nama</td>
                <td colspan="3"><input type="text" class="text2" name="Nama" id="Nama" maxlength="50" size="43" value="<?php print($karyawan->Nama); ?>" disabled /></td>
                <td>Panggilan</td>
                <td><input type="text" class="text2" name="NmPanggilan" id="NmPanggilan" maxlength="20" size="15" value="<?php print($karyawan->NmPanggilan); ?>" disabled /></td>
            </tr>
			<tr>
                <td>Bagian</td>
                <td colspan="3"><select name="DeptId" class="text2" id="DeptId" disabled>
                        <option value=""></option>
                        <?php
                        foreach ($depts as $dept) {
                            if ($dept->Id == $karyawan->DeptId) {
                                printf('<option value="%d" selected="selected">%s - %s</option>', $dept->Id, $dept->DeptCd, $dept->DeptName);
                            } else {
                                printf('<option value="%d">%s - %s</option>', $dept->Id, $dept->DeptCd, $dept->DeptName);
                            }
                        }
                        ?>
                    </select>
                </td>
                <td>Jabatan</td>
                <td><select name="Jabatan" class="text2" id="Jabatan" disabled>
                        <option value=""></option>
                        <option value="STF" <?php ($karyawan->Jabatan == "STF" ? print('selected = "selected"'):'');?>>Staf</option>
                        <option value="SPV" <?php ($karyawan->Jabatan == "SPV" ? print('selected = "selected"'):'');?>>Supervisor</option>
                        <option value="MGR" <?php ($karyawan->Jabatan == "MGR" ? print('selected = "selected"'):'');?>>Manager</option>
                        <option value="DIR" <?php ($karyawan->Jabatan == "DIR" ? print('selected = "selected"'):'');?>>Direktur</option>
                    </select>
                </td>
			</tr>
            <tr>
                <td>Gaji Pokok</td>
                <td><input type="text" class="numeric right" name="Gapok" id="Gapok" maxlength="15" size="15" value="<?php print(number_format($gaji->Gapok)); ?>" required/></td>
                <td colspan="2">Kode Dokter <sup><font color="blue">*Jika bertugas sebagai dokter</font></sup></td>
                <td colspan="2"><select name="KdDokter" class="text2" id="KdDokter">
                        <option value=""></option>
                        <?php
                        foreach ($dokters as $dokter) {
                            if ($dokter->KdDokter == $gaji->KdDokter) {
                                printf('<option value="%s" selected="selected">%s - %s</option>', $dokter->KdDokter, $dokter->KdDokter, $dokter->NmDokter);
                            } else {
                                printf('<option value="%s">%s - %s</option>', $dokter->KdDokter, $dokter->KdDokter, $dokter->NmDokter);
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Tj. Jabatan</td>
                <td><input type="text" class="numeric right" name="TjJabatan" id="TjJabatan" maxlength="15" size="15" value="<?php print(number_format($gaji->TjJabatan)); ?>" required/></td>
                <td colspan="2">Mendapat 10% Profit Sharing?</td>
                <td colspan="2"><select name="IsFeeProfit" class="text2" id="IsFeeProfit" required>
                        <option value="0" <?php ($gaji->IsFeeProfit == "0" ? print('selected = "selected"'):'');?>> 0 - Tidak </option>
                        <option value="1" <?php ($gaji->IsFeeProfit == "1" ? print('selected = "selected"'):'');?>> 1 - Iya </option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Tj. Profesi</td>
                <td><input type="text" class="numeric right" name="TjProfesi" id="TjProfesi" maxlength="15" size="15" value="<?php print(number_format($gaji->TjProfesi)); ?>" required/></td>
                <td colspan="2">Mendapat Sharing Tindakan Khusus?</td>
                <td colspan="2"><select name="IsFeeTikhus" class="text2" id="IsFeeTikhus" required>
                        <option value="0" <?php ($gaji->IsFeeTikhus == "0" ? print('selected = "selected"'):'');?>> 0 - Tidak </option>
                        <option value="1" <?php ($gaji->IsFeeTikhus == "1" ? print('selected = "selected"'):'');?>> 1 - Iya </option>
                    </select>
                    <sub><font color="red">* Khusus Perawat/Tenaga Medis</font></sub>
                </td>
            </tr>
            <tr>
                <td>BPJS Kesehatan</td>
                <td><input type="text" class="numeric right" name="BpjsKes" id="BpjsKes" maxlength="15" size="15" value="<?php print(number_format($gaji->BpjsKes)); ?>"/></td>
                <td colspan="2">Mendapat Sharing Jasa Medis?</td>
                <td colspan="2"><select name="IsFeeJasmed" class="text2" id="IsFeeJasmed" required>
                        <option value="0" <?php ($gaji->IsFeeJasmed == "0" ? print('selected = "selected"'):'');?>> 0 - Tidak </option>
                        <option value="1" <?php ($gaji->IsFeeJasmed == "1" ? print('selected = "selected"'):'');?>> 1 - Iya </option>
                    </select>
                    <sub><font color="red">**Khusus Dokter</font></sub>
                </td>
            </tr>
            <tr>
                <td>BPJS T.K.</td>
                <td><input type="text" class="numeric right" name="BpjsTk" id="BpjsTk" maxlength="15" size="15" value="<?php print(number_format($gaji->BpjsTk)); ?>"/></td>
            </tr>
            <tr>
                <td>T H R</td>
                <td><input type="text" class="numeric right" name="Thr" id="Thr" maxlength="15" size="15" value="<?php print(number_format($gaji->Thr)); ?>"/></td>
            </tr>
			<tr>
                <td>&nbsp;</td>
				<td colspan="3">
                    <?php if ($fmode == 1) {
                                print('<button id="btSubmit" type="submit">Simpan</button>');
                            }else{
                                print('<button id="btSubmit" type="submit">Update</button>');
                            }
                            ?>
					<a href="<?php print($helper->site_url("personalia/payroll")); ?>" class="button">Data Gaji Karyawan</a>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
</body>
</html>
