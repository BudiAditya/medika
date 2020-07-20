<!DOCTYPE HTML>
<html>
<head>
	<title>ERASYS - Edit Data Hak Akses</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-themes/default/easyui.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-themes/icon.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-themes/color.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-demo/demo.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.easyui.min.js")); ?>"></script>
</head>

<body>
<?php include(VIEW . "main/menu.php"); ?>
<br/>
<?php if (isset($error)) { ?>
<div class="ui-state-error subTitle center"><?php print($error); ?></div>
<?php } ?>
<?php if (isset($info)) { ?>
<div class="ui-state-highlight subTitle center"><?php print($info); ?></div>
<?php } ?>

<fieldset>
	<legend><b>Hak Akses untuk User ID: <?php print($userdata->UserId);?></b></legend>
    <form name="frm" id="frm" method="post">
		<span align="left">Copy Hak Akses dari User:
        <select id="copyFrom" name="copyFrom">
            <option value="">--</option>
            <?php
			while ($row = $userlist->FetchAssoc()) {
			    if ($row['user_uid'] != $userdata->UserUid) {
                    printf("<option value='%s'>%s</option>", $row['user_uid'] . '|' . $row['cabang_id'], $row['user_id']);
                }
			}
            ?>
        </select>
            <button type="submit" formaction="<?php printf($helper->site_url('master.useracl/copy/%s'), $userdata->UserUid); ?>"><b>Proses Copy</b></button>
            <button type="submit" class="bold" formaction="<?php printf($helper->site_url('master.useracl/add/%s'), $userdata->UserUid); ?>">Simpan Data</button>&nbsp;
            <a href="<?php print($helper->site_url("master.useradmin")); ?>" class="button">Daftar User System</a>
        </span>
        <br>
        <br>
		<div class="easyui-accordion" style="width:800px;">
			<?php
			$m1 = "";
			$nmr = 0;
			$akses = null;
			foreach ($resources as $menu) {
				if ($m1 != $menu->MenuName) {
					if ($m1 != "") {
						print('</table>');
						print('</div>');
					}

					$m1 = $menu->MenuName;
					//printf('<h3><a href="#">%s</a></h3>', $menu->MenuName);
					printf('<div title="%s" style="overflow:auto;padding:10px;">',strtoupper($menu->MenuName));
					print('<table border="1" cellpadding="0" cellspacing="0" class="smallTablePadding">');
					//print('<tr class="text2"><th>No.</th><th>Resource Name</th><th>Add</th><th>Edit</th><th>Delete</th><th>View</th><th>Print</th><th>Approve</th><th>Verify</th><th>Post</th><th>All</th></tr>');
					print('<tr class="text2"><th>No.</th><th>Nama Modul</th><th>Lihat</th><th>Tambah</th><th>Ubah</th><th>Hapus</th><th>Cetak</th><th>Approve</th><th>Semua</th></tr>');
				}

				if (isset($hak[$menu->ResourceId])) {
					$akses = $hak[$menu->ResourceId];
				} else {
					$akses = null;
				}

				print('<tr>');
				printf('<td align="center" class="text2">%d</td>', $menu->ResourceSeq);
				printf('<td class="text2">%s</td>', $menu->ResourceName);
                printf('<td align="center"><input type="checkbox" name="hakakses[]" value="%s|4" id="c4%d" %s /></td>', $menu->ResourceId, $nmr, ($akses != null && strpos($akses->Rights, "4") !== false) ? 'checked="checked"' : '');
				printf('<td align="center"><input type="checkbox" name="hakakses[]" value="%s|1" id="c1%d" %s /></td>', $menu->ResourceId, $nmr, ($akses != null && strpos($akses->Rights, "1") !== false) ? 'checked="checked"' : '');
				printf('<td align="center"><input type="checkbox" name="hakakses[]" value="%s|2" id="c2%d" %s /></td>', $menu->ResourceId, $nmr, ($akses != null && strpos($akses->Rights, "2") !== false) ? 'checked="checked"' : '');
				printf('<td align="center"><input type="checkbox" name="hakakses[]" value="%s|3" id="c3%d" %s /></td>', $menu->ResourceId, $nmr, ($akses != null && strpos($akses->Rights, "3") !== false) ? 'checked="checked"' : '');
				printf('<td align="center"><input type="checkbox" name="hakakses[]" value="%s|5" id="c5%d" %s /></td>', $menu->ResourceId, $nmr, ($akses != null && strpos($akses->Rights, "5") !== false) ? 'checked="checked"' : '');
				printf('<td align="center"><input type="checkbox" name="hakakses[]" value="%s|6" id="c6%d" %s /></td>', $menu->ResourceId, $nmr, ($akses != null && strpos($akses->Rights, "6") !== false) ? 'checked="checked"' : '');
				//printf('<td align="center"><input type="checkbox" name="hakakses[]" value="%s|7" %s /></td>', $menu->ResourceId, ($akses != null && strpos($akses->Rights, "7") !== false) ? 'checked="checked"' : '');
				//printf('<td align="center"><input type="checkbox" name="hakakses[]" value="%s|8" %s /></td>', $menu->ResourceId, ($akses != null && strpos($akses->Rights, "8") !== false) ? 'checked="checked"' : '');
				printf('<td align="center"><input type="checkbox" name="hakakses[]" value="%s|9" id="c9%d" %s /></td>', $menu->ResourceId, $nmr, ($akses != null && strpos($akses->Rights, "9") !== false) ? 'checked="checked"' : '');
				print('</tr>');
				$nmr++;
			}

			// Hmm spt biasa yang terakhir tidak ter print untuk tag close nya
			if ($m1 != "") {
				print("</table></div>");
			}
			?>
		</div>
	</form>
</fieldset>
<script type="text/javascript">
    $(document).ready(function () {
        //$('input[id^="c"]').on('change', function() {
        //    var idx = this.id.value();
        //    alert(idx);
            //alert($('#'+idx).value());
        //});
    });

    function toggleCheckbox(token,idx) {
       // alert(this.id);
    }
</script>
</body>
</html>
