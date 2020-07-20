<?php
class UseraclController extends AppController {
	private $userCompanyId;
    private $userCabangId;

	protected function Initialize() {
		require_once(MODEL . "master/user_admin.php");
		require_once(MODEL . "master/user_acl.php");
		$this->userCompanyId = $this->persistence->LoadState("entity_id");
        $this->userCabangId = $this->persistence->LoadState("cabang_id");
	}

	public function add($uid) {
		$loader = null;
		$skema = null;
		$cbi = $this->userCabangId;
		$userlist = null;
		// find user data
		$log = new UserAdmin();
		$userdata = new UserAdmin();
		$userdata = $userdata->FindById($uid);
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$skema = $this->GetPostValue("hakakses");
			$prevResId = null;
			$hak = null;
			$userAcl = new UserAcl();
            $userAcl->Delete($uid, $cbi);
            foreach ($skema As $aturan) {
                $tokens = explode("|", $aturan);
                $resid = $tokens[0];
                $hak = $tokens[1];
                if ($prevResId != $resid) {
                    if ($userAcl->Rights != "") {
                        $userAcl->Insert();
                    }
                    $prevResId = $resid;
                    $userAcl = new UserAcl();
                    $userAcl->ResourceId = $resid;
                    $userAcl->UserUid = $uid;
                    $userAcl->CabangId = $cbi;
                    $userAcl->Rights = "";
                }
                $userAcl->Rights .= $hak;
            }
            if ($userAcl->Rights != "") {
                $userAcl->Insert();
                $log = $log->UserActivityWriter($this->userCabangId,'master.useracl','Setting User ACL -> User: '.$userdata->UserId.' - '.$userdata->UserName,'-','Success');
            }
            $this->persistence->SaveState("info", sprintf("Data Hak Akses User: '%s' telah berhasil disimpan.", $userdata->UserId));
            redirect_url("master.useradmin");
		} else {
			$userAcl = new UserAcl();
			$hak = $userAcl->LoadAcl($uid,$cbi);
		}
		// load resource data
		$loader = new UserAcl();
		$resources = $loader->LoadAllResources();
        $loader = new UserAcl();
        $userlist = $loader->GetListUserCabAcl();
		$this->Set("resources", $resources);
		$this->Set("userdata", $userdata);
		$this->Set("userlist", $userlist);
		$this->Set("hak", $hak);
	}

	public function view($uid = 0) {
		//load acl
		if ($uid == 0){
			$uid = AclManager::GetInstance()->GetCurrentUser()->Id;
		}
		$userId = null;
		$userdata = new UserAdmin();
		$userdata = $userdata->FindById($uid);
		$userId = $userdata->UserId.' ['.$userdata->UserName.']';
		$userAcl = new UserAcl();
		$aclists = $userAcl->GetUserAclList($uid);
		$this->Set("userId", $userId);
		$this->Set("aclists", $aclists);
	}


	public function copy($uid = null) {
		$srcUid = null;
		$cbi = $this->userCabangId;
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$cdata = $this->GetPostValue("copyFrom");
			$cdata = explode("|",$cdata);
			$srcUid = $cdata[0];
			$srcCbi = $cdata[1];
			$userAcl = new UserAcl();
			$userAcl->Delete($uid,$cbi);
			$userAcl->Copy($srcUid,$srcCbi,$uid,$cbi);
			$this->persistence->SaveState("info", sprintf("Data Hak Akses telah berhasil disalin.."));
			Dispatcher::RedirectUrl("master.useracl/add/".$uid);
		} else {
			$userAcl = new UserAcl();
			$hak = $userAcl->LoadAcl($uid,$cbi);
			Dispatcher::RedirectUrl("master.useracl/add/".$uid);
		}
	}


}
