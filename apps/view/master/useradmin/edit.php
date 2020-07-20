<!DOCTYPE HTML>
<html>
<?php /** @var $userAdmin UserAdmin */ /** @var $karyawans Karyawan[] */ ?>
<head>
    <title>ERASYS - Ubah User System</title>
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
    <script type="text/javascript">
        $(document).ready(function () {
            var elements = ["SbuId","UserId", "EmployeeId", "UserEmail", "UserPwd1", "UserPwd2", "UserLvl", "AllowMultipleLogin", "IsAktif","BtUpdate"];
            BatchFocusRegister(elements);
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
    <legend><b>Ubah Data User System</b></legend>
    <form id="frm" action="<?php print($helper->site_url("master.useradmin/edit/".$userAdmin->UserUid)); ?>" method="post">
        <input type="hidden" name="EntityId" id="EntityId" value="<?php print($userAdmin->EntityId ? null : 1);?>"/>
        <input type="hidden" name="CabangId" id="CabangId" value="<?php print($userAdmin->CabangId ? null : 1);?>"/>
        <table cellpadding="2" cellspacing="1">
            <tr>
                <td><label for="SbuId">Bisnis Unit</label></td>
                <td><select name="SbuId" id="SbuId" style="width: 150px;" required>
                        <option value="0"> 0 - Bebas </option>
                        <?php
                        /** @var $sbuList Sbu[] */
                        foreach ($sbuList as $sbu){
                            if ($userAdmin->SbuId == $sbu->Id){
                                printf("<option value='%d' selected='selected'> %s - %s </option>",$sbu->Id,$sbu->Id,$sbu->SbuName);
                            }else{
                                printf("<option value='%d'> %s - %s </option>",$sbu->Id,$sbu->Id,$sbu->SbuName);
                            }
                        }
                        ?>
                    </select></td>
            </tr>
            <tr>
                <td><label for="UserId">User ID</label></td>
                <td><input type="text" name="UserId" id="UserId" maxlength="50" style="width: 145px;" value="<?php print($userAdmin->UserId);?>" autofocus required/></td>
            </tr>
            <tr>
                <td><label for="EmployeeId">Nama Lengkap</label></td>
                <td colspan="3"><select class="easyui-combobox" name="EmployeeId" id="EmployeeId" style="width: 225px;" required>
                        <option value="0"></option>
                        <?php
                        foreach ($karyawans as $karyawan) {
                            if ($karyawan->Id == $userAdmin->EmployeeId) {
                                printf('<option value="%d" selected="selected">%s</option>', $karyawan->Id, $karyawan->Nama);
                            } else {
                                printf('<option value="%d">%s</option>', $karyawan->Id, $karyawan->Nama);
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="UserEmail">Alamat Email</label></td>
                <td colspan="2"><input type="email" name="UserEmail" id="UserEmail" maxlength="100" size="40" value="<?php print($userAdmin->UserEmail);?>"/></td>
            </tr>
            <tr>
                <td><label for="UserPwd1">Password</label></td>
                <td><input type="password" name="UserPwd1" id="UserPwd1" maxlength="50" style="width: 145px;" value=""/></td>
            </tr>
            <tr>
                <td><label for="UserPwd2">Konf. Passwd</label></td>
                <td><input type="password" name="UserPwd2" id="UserPwd2" maxlength="50" style="width: 145px;" value=""/></td>
            </tr>
            <tr>
                <td><label for="UserLvl">User Level</label></td>
                <td><select name="UserLvl" id="UserLvl" style="width: 150px;">
                        <?php
                        while ($row = $userLvl->FetchAssoc()) {
                            if ($userAdmin->UserLvl == $row['code']){
                                printf("<option value='%d' selected='selected'>%s</option>",$row['code'],$row['short_desc']);
                            }else{
                                printf("<option value='%d'>%s</option>",$row['code'],$row['short_desc']);
                            }
                        }
                        ?>
                    </select></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <input type="checkbox" id="AllowMultipleLogin" name="AllowMultipleLogin" <?php print($userAdmin->AllowMultipleLogin == 1 ? 'checked="checked"' : ''); ?> />
                    <label for="AllowMultipleLogin">Boleh Multiple Login</label>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <input type="checkbox" id="IsAktif" name="IsAktif" <?php print($userAdmin->IsAktif == 1 ? 'checked="checked"' : ''); ?> />
                    <label for="IsAktif">User Aktif</label>
                    &nbsp;&nbsp;&nbsp;
                    <input type="checkbox" id="IsForcePeriod" name="IsForcePeriod" <?php print($userAdmin->IsForceAccountingPeriod == 1 ? 'checked="checked"' : ''); ?> />
                    <label for="IsForcePeriod">Force Select Accounting Period</label>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <button id="BtUpdate" type="submit">Update</button>
                    <a href="<?php print($helper->site_url("master.useradmin")); ?>">Daftar User</a>
                </td>
            </tr>
        </table>
    </form>
</fieldset>
</body>
</html>
