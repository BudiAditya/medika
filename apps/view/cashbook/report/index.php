<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
<head>
	<title>ERASYS - Laporan Transaksi Kas/Bank</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/auto-numeric.js")); ?>"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#StartDate").customDatePicker({ showOn: "focus" });
            $("#EndDate").customDatePicker({ showOn: "focus" });
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
<form id="frm" name="frmReport" method="post">
    <input type="hidden" name="CabangId" id="CabangId" value="<?php print($CabangId);?>"/>
    <input type="hidden" name="TrxTypeCode" id="TrxTypeCode" value="0"/>
    <table cellpadding="2" cellspacing="1" class="tablePadding tableBorder">
        <tr class="center">
            <th colspan="9"><b>Laporan Transaksi Kas/Bank</b></th>
        </tr>
        <tr class="center">
            <th>Mode</th>
            <th>Kas/Bank</th>
            <th>Status</th>
            <th>Dari Tanggal</th>
            <th>Sampai Tanggal</th>
            <th>Output</th>
            <th>Action</th>
        </tr>
        <tr>
            <td>
                <select id="TrxMode" name="TrxMode" required>
                    <option value="0" <?php print($TrxMode == 0 ? 'selected="selected"' : '');?>>0 - Semua</option>
                    <option value="1" <?php print($TrxMode == 1 ? 'selected="selected"' : '');?>>1 - Masuk</option>
                    <option value="2" <?php print($TrxMode == 2 ? 'selected="selected"' : '');?>>2 - Keluar</option>
                </select>
            </td>
            <td>
                <select id="CoaBankId" name="CoaBankId" required>
                    <option value="0">- Semua Kas/Bank -</option>
                    <?php
                    /** @var $CoaBanks Bank[] */
                    foreach ($CoaBanks as $coabank) {
                        if($coabank->AccNo == $CoaBankId){
                            printf('<option value="%s" selected="selected">%s</option>',$coabank->AccNo, $coabank->Name);
                        }else{
                            printf('<option value="%s">%s</option>',$coabank->AccNo, $coabank->Name);
                        }
                    }
                    ?>
                </select>
            </td>
            <td>
                <select id="TrxStatus" name="TrxStatus" required>
                    <option value="-1" <?php print($TrxStatus == -1 ? 'selected="selected"' : '');?>> - Semua Status -</option>
                    <option value="0" <?php print($TrxStatus == 0 ? 'selected="selected"' : '');?>>0 - Draft</option>
                    <option value="1" <?php print($TrxStatus == 1 ? 'selected="selected"' : '');?>>1 - Posted</option>
                </select>
            </td>
            <td><input type="text" class="text2" maxlength="10" size="10" id="StartDate" name="StartDate" value="<?php printf(date('d-m-Y',$StartDate));?>"/></td>
            <td><input type="text" class="text2" maxlength="10" size="10" id="EndDate" name="EndDate" value="<?php printf(date('d-m-Y',$EndDate));?>"/></td>
            <td>
                <select id="Output" name="Output" required>
                    <option value="0" <?php print($Output == 0 ? 'selected="selected"' : '');?>>0 - Web Html</option>
                    <option value="1" <?php print($Output == 1 ? 'selected="selected"' : '');?>>1 - Excel</option>
                </select>
            </td>
            <td><button type="submit" formaction="<?php print($helper->site_url("cashbook/report")); ?>"><b>Proses</b></button></td>
        </tr>
    </table>
</form>
<!-- start web report -->
<?php  if ($Reports != null){ ?>
    <h3>Laporan Transaksi Kas/Bank</h3>
    <?php printf("Dari Tgl. %s - %s",date('d-m-Y',$StartDate),date('d-m-Y',$EndDate));?>
    <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
        <tr>
            <th>No.</th>
            <th>Tanggal</th>
            <th>No. Bukti</th>
            <th>Mode</th>
            <th>Kas / Bank</th>
            <th>Relasi</th>
            <th>Keterangan</th>
            <th>Refferensi</th>
            <?php
            if($TrxMode == 0){
               print('<th>Debet</th>');
               print('<th>Kredit</th>');
               print('<th>Saldo</th>');
            }else{
                print('<th>Jumlah</th>');
            }
            ?>
            <th>Admin</th>
            <th>Status</th>
        </tr>
        <?php
            $nmr = 1;
            $tdebet = 0;
            $tkredit = 0;
            $saldo = 0;
            $url = null;
            while ($row = $Reports->FetchAssoc()) {
                $url = $helper->site_url("cashbook.cbtrx/view/".$row["id"]);
                $debet = 0;
                $kredit = 0;
                if ($TrxMode == 0){
                    if ($row["trx_mode"] == 1){
                        $debet = $row["trx_amount"];
                        $tdebet+= $debet;
                    }else{
                        $kredit = $row["trx_amount"];
                        $tkredit+= $kredit;
                    }
                    $saldo = $saldo + $debet - $kredit;
                }else{
                    $saldo+= $row["trx_amount"];
                }
                print("<tr valign='Top'>");
                printf("<td>%s</td>",$nmr);
                printf("<td>%s</td>",date('d-m-Y',strtotime($row["trx_date"])));
                printf("<td><a href= '%s' target='_blank'>%s</a></td>",$url ,$row["doc_no"]);
                printf("<td>%s</td>",$row["xmode"]);
                printf("<td>%s</td>",$row["bank_name"]);
                printf("<td>%s</td>",$row["contact_name"]);
                printf("<td>%s</td>",$row["trx_descs"]);
                printf("<td>%s</td>",$row["reff_no"]);
                if ($TrxMode == 0){
                    printf("<td align='right'>%s</td>",number_format($debet,0));
                    printf("<td align='right'>%s</td>",number_format($kredit,0));
                    printf("<td align='right'>%s</td>",number_format($saldo,0));
                }else{
                    printf("<td align='right'>%s</td>",number_format($row["trx_amount"],0));
                }
                printf("<td>%s</td>",$row["user_id"]);
                if ($row["trx_status"] == 0){
                    printf("<td>Draft</td>");
                }elseif($row["trx_status"] == 1){
                    printf("<td>Posted</td>");
                }else{
                    printf("<td>Approved</td>");
                }
                print("</tr>");
                $nmr++;
            }
        print("<tr>");
        print("<td colspan='8' align='right'>Total Transaksi</td>");
        if ($TrxMode == 0){
            printf("<td align='right'>%s</td>",number_format($tdebet,0));
            printf("<td align='right'>%s</td>",number_format($tkredit,0));
            printf("<td align='right'>%s</td>",number_format($tdebet-$tkredit,0));
        }else{
            printf("<td align='right'>%s</td>",number_format($saldo,0));
        }
        printf("<td colspan='2'>&nbsp</td>");
        print("</tr>");
        ?>
    </table>
<!-- end web report -->
<?php } ?>
</body>
</html>
