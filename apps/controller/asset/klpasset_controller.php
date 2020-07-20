<?php
class KlpAssetController extends AppController {
	private $userCompanyId;
	private $userCabangId;
	private $userUid;

	protected function Initialize() {
		require_once(MODEL . "asset/klpasset.php");
		require_once(MODEL . "master/user_admin.php");
		$this->userCompanyId = $this->persistence->LoadState("entity_id");
		$this->userCabangId = $this->persistence->LoadState("cabang_id");
        $this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();
		//contents
		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 40);
		$settings["columns"][] = array("name" => "a.kd_klpasset", "display" => "Kode", "width" => 50);
		$settings["columns"][] = array("name" => "a.klp_asset", "display" => "Kelompok Asset", "width" => 150);
        $settings["columns"][] = array("name" => "a.keterangan", "display" => "Keterangan", "width" => 300);
        $settings["columns"][] = array("name" => "a.asset_acc_no", "display" => "Akun Asset", "width" => 80, "align" => "left");
        $settings["columns"][] = array("name" => "a.apre_acc_no", "display" => "Akun Apresiasi", "width" => 80, "align" => "left");
        $settings["columns"][] = array("name" => "a.depr_acc_no", "display" => "Akun Depresiasi", "width" => 80, "align" => "left");
        $settings["columns"][] = array("name" => "a.cost_acc_no", "display" => "Akun Biaya", "width" => 80, "align" => "left");
        $settings["columns"][] = array("name" => "a.revn_acc_no", "display" => "Akun Pendapatan", "width" => 80, "align" => "left");
        //filtering
		$settings["filters"][] = array("name" => "a.klp_asset", "display" => "Kelompok");
		$settings["filters"][] = array("name" => "a.kd_klpasset", "display" => "Kode");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Data Kelompok Asset";

			if ($acl->CheckUserAccess("klpasset", "add", "asset")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "asset.klpasset/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("klpasset", "edit", "asset")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "asset.klpasset/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
											   "Error" => "Mohon memilih data terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu data",
											   "Info" => "Apakah anda yakin mau merubah data yang dipilih ?");
			}
			if ($acl->CheckUserAccess("klpasset", "delete", "asset")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "asset.klpasset/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
											   "Error" => "Mohon memilih data terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu data",
											   "Info" => "Apakah anda yakin mau menghapus data yang dipilih ?");
			}
            //sort and method
			$settings["def_filter"] = 0;
			$settings["def_order"] = 1;
			$settings["singleSelect"] = true;
		} else {
		    //database
			$settings["from"] = "m_klpasset AS a";
			$settings["where"] = "a.is_deleted = 0 AND a.entity_id = " . $this->userCompanyId;
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

	public function add() {
        require_once(MODEL . "master/coadetail.php");
		$loader = null;
		$klpasset = new KlpAsset();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$klpasset->EntityId = $this->userCompanyId;
			$klpasset->KdKlpAsset = $this->GetPostValue("KdKlpAsset");
			$klpasset->KlpAsset = $this->GetPostValue("KlpAsset");
            $klpasset->Keterangan = $this->GetPostValue("Keterangan");
            $klpasset->AssetAccNo = $this->GetPostValue("AssetAccNo");
            $klpasset->ApreAccNo = $this->GetPostValue("ApreAccNo");
            $klpasset->DeprAccNo = $this->GetPostValue("DeprAccNo");
            $klpasset->CostAccNo = $this->GetPostValue("CostAccNo");
            $klpasset->RevnAccNo = $this->GetPostValue("RevnAccNo");
            $klpasset->CreatebyId = $this->userUid;
			if ($this->DoInsert($klpasset)) {
				$log = $log->UserActivityWriter($this->userCabangId,'asset.klpasset','Add New KlpAsset -> Kode: '.$klpasset->KdKlpAsset.' - '.$klpasset->KlpAsset,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data KlpAsset: '%s' Dengan Kode: %s telah berhasil disimpan.", $klpasset->KlpAsset, $klpasset->KdKlpAsset));
				redirect_url("asset.klpasset");
			} else {
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $klpasset->EntityCd));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		}
		// untuk kirim variable ke view
        $loader = new CoaDetail();
		$akun = $loader->LoadAll($this->userCompanyId);
		$this->Set("akun", $akun);
        $this->Set("klpasset", $klpasset);
	}

	private function DoInsert(KlpAsset $klpasset) {
		if ($klpasset->KdKlpAsset == "") {
			$this->Set("error", "Kode belum diisi!");
			return false;
		}
		if ($klpasset->KlpAsset == "") {
			$this->Set("error", "Kelompok Asset belum diisi!");
			return false;
		}

		if ($klpasset->Insert() == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function edit($id = null) {
        require_once(MODEL . "master/coadetail.php");
		$loader = null;
		$klpasset = new KlpAsset();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$klpasset->Id = $id;
			$klpasset->EntityId = $this->userCompanyId;
			$klpasset->KdKlpAsset = $this->GetPostValue("KdKlpAsset");
			$klpasset->KlpAsset = $this->GetPostValue("KlpAsset");
            $klpasset->Keterangan = $this->GetPostValue("Keterangan");
            $klpasset->AssetAccNo = $this->GetPostValue("AssetAccNo");
            $klpasset->ApreAccNo = $this->GetPostValue("ApreAccNo");
            $klpasset->DeprAccNo = $this->GetPostValue("DeprAccNo");
            $klpasset->CostAccNo = $this->GetPostValue("CostAccNo");
            $klpasset->RevnAccNo = $this->GetPostValue("RevnAccNo");
            $klpasset->UpdatebyId = $this->userUid;
			if ($this->DoUpdate($klpasset)) {
				$log = $log->UserActivityWriter($this->userCabangId,'asset.klpasset','Update KlpAsset -> Kode: '.$klpasset->KdKlpAsset.' - '.$klpasset->KlpAsset,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data KlpAsset: '%s' Dengan Kode: %s telah berhasil diupdate.", $klpasset->KlpAsset, $klpasset->KdKlpAsset));
				redirect_url("asset.klpasset");
			} else {
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $klpasset->EntityCd));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		} else {
			if ($id == null) {
				$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan edit data !");
				redirect_url("asset.klpasset");
			}
			$klpasset = $klpasset->FindById($id);
			if ($klpasset == null) {
				$this->persistence->SaveState("error", "Data KlpAsset yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
				redirect_url("asset.klpasset");
			}
		}
        // untuk kirim variable ke view
        $loader = new CoaDetail();
        $akun = $loader->LoadAll($this->userCompanyId);
        $this->Set("akun", $akun);
        $this->Set("klpasset", $klpasset);
	}

	private function DoUpdate(KlpAsset $klpasset) {
		if ($klpasset->KdKlpAsset == "") {
            $this->Set("error", "Kode belum diisi!");
            return false;
        }
        if ($klpasset->KlpAsset == "") {
            $this->Set("error", "Kelompok Asset belum diisi!");
            return false;
        }

		if ($klpasset->Update($klpasset->Id) == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function delete($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan hapus data !");
			redirect_url("asset.klpasset");
		}
		$log = new UserAdmin();
		$klpasset = new KlpAsset();
		$klpasset = $klpasset->FindById($id);
		if ($klpasset == null) {
			$this->persistence->SaveState("error", "Data yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
			redirect_url("asset.klpasset");
		}

		if ($klpasset->Delete($klpasset->Id) == 1) {
			$log = $log->UserActivityWriter($this->userCabangId,'asset.klpasset','Delete KlpAsset -> Kode: '.$klpasset->KdKlpAsset.' - '.$klpasset->KlpAsset,'-','Success');
			$this->persistence->SaveState("info", sprintf("Data KlpAsset: '%s' Dengan Kode: %s telah berhasil dihapus.", $klpasset->KlpAsset, $klpasset->KdKlpAsset));
			redirect_url("asset.klpasset");
		} else {
			$this->persistence->SaveState("error", sprintf("Gagal menghapus data KlpAsset: '%s'. Message: %s", $klpasset->KlpAsset, $this->connector->GetErrorMessage()));
		}
		redirect_url("asset.klpasset");
	}

	public function optlistbyentity($EntityId = null, $sKlpAssetId = null) {
		$buff = '<option value="">-- PILIH KELOMPOK --</option>';
		if ($EntityId == null) {
			print($buff);
			return;
		}
		$klpasset = new KlpAsset();
		$klpassets = $klpasset->LoadByEntityId($EntityId);
		foreach ($klpassets as $klpasset) {
			if ($klpasset->Id == $sKlpAssetId) {
				$buff .= sprintf('<option value="%d" selected="selected">%s - %s</option>', $klpasset->Id, $klpasset->KdKlpAsset, $klpasset->KlpAsset);
			} else {
				$buff .= sprintf('<option value="%d">%s - %s</option>', $klpasset->Id, $klpasset->KdKlpAsset, $klpasset->KlpAsset);
			}
		}
		print($buff);
	}

    public function AutoKode(){
        $kode = new KlpAsset();
        $kode = $kode->GetAutoKode($this->userCompanyId);
        print $kode;
    }
}
