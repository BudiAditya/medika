<?php
class KlpBillingController extends AppController {
	private $userCompanyId;
	private $userCabangId;
	private $userUid;

	protected function Initialize() {
		require_once(MODEL . "master/klpbilling.php");
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
		$settings["columns"][] = array("name" => "a.kd_klpbilling", "display" => "Kode", "width" => 50);
		$settings["columns"][] = array("name" => "a.klp_billing", "display" => "Kelompok Billing", "width" => 200);
        $settings["columns"][] = array("name" => "a.keterangan", "display" => "Keterangan", "width" => 300, "align" => "center");
        //filtering
		$settings["filters"][] = array("name" => "a.klp_billing", "display" => "Kelompok");
		$settings["filters"][] = array("name" => "a.kd_klpbilling", "display" => "Kode");
        $settings["filters"][] = array("name" => "a.keterangan", "display" => "Keterangan");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Data Kelompok Billing";

			if ($acl->CheckUserAccess("klpbilling", "add", "master")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.klpbilling/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("klpbilling", "edit", "master")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.klpbilling/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
											   "Error" => "Mohon memilih data terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu data",
											   "Info" => "Apakah anda yakin mau merubah data yang dipilih ?");
			}
			if ($acl->CheckUserAccess("klpbilling", "delete", "master")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.klpbilling/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
											   "Error" => "Mohon memilih data terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu data",
											   "Info" => "Apakah anda yakin mau menghapus data yang dipilih ?");
			}
            //sort and method
			$settings["def_filter"] = 0;
			$settings["def_order"] = 2;
			$settings["singleSelect"] = true;
		} else {
		    //database
			$settings["from"] = "m_klpbilling AS a";
			if ($this->userCompanyId == 1 || $this->userCompanyId == null) {
				$settings["where"] = "a.is_deleted = 0";
			} else {
				$settings["where"] = "a.is_deleted = 0 AND a.entity_id = " . $this->userCompanyId;
			}
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

	public function add() {
		$loader = null;
		$klpbilling = new KlpBilling();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$klpbilling->EntityId = $this->userCompanyId;
			$klpbilling->KdKlpBilling = $this->GetPostValue("KdKlpBilling");
			$klpbilling->KlpBilling = $this->GetPostValue("KlpBilling");
            $klpbilling->Keterangan = $this->GetPostValue("Keterangan");
            $klpbilling->CreatebyId = $this->userUid;
			if ($this->DoInsert($klpbilling)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.klpbilling','Add New KlpBilling -> Kode: '.$klpbilling->KdKlpBilling.' - '.$klpbilling->KlpBilling,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data KlpBilling: '%s' Dengan Kode: %s telah berhasil disimpan.", $klpbilling->KlpBilling, $klpbilling->KdKlpBilling));
				redirect_url("master.klpbilling");
			} else {
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $klpbilling->EntityCd));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		}
		// untuk kirim variable ke view
		$this->Set("klpbilling", $klpbilling);
	}

	private function DoInsert(KlpBilling $klpbilling) {
		if ($klpbilling->KdKlpBilling == "") {
			$this->Set("error", "Kode belum diisi!");
			return false;
		}
		if ($klpbilling->KlpBilling == "") {
			$this->Set("error", "Kelompok Jasa belum diisi!");
			return false;
		}
		if ($klpbilling->Insert() == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function edit($id = null) {
		$loader = null;
		$klpbilling = new KlpBilling();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$klpbilling->Id = $id;
			$klpbilling->EntityId = $this->userCompanyId;
			$klpbilling->KdKlpBilling = $this->GetPostValue("KdKlpBilling");
			$klpbilling->KlpBilling = $this->GetPostValue("KlpBilling");
            $klpbilling->Keterangan = $this->GetPostValue("Keterangan");
            $klpbilling->UpdatebyId = $this->userUid;
			if ($this->DoUpdate($klpbilling)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.klpbilling','Update KlpBilling -> Kode: '.$klpbilling->KdKlpBilling.' - '.$klpbilling->KlpBilling,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data KlpBilling: '%s' Dengan Kode: %s telah berhasil diupdate.", $klpbilling->KlpBilling, $klpbilling->KdKlpBilling));
				redirect_url("master.klpbilling");
			} else {
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $klpbilling->EntityCd));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		} else {
			if ($id == null) {
				$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan edit data !");
				redirect_url("master.klpbilling");
			}
			$klpbilling = $klpbilling->FindById($id);
			if ($klpbilling == null) {
				$this->persistence->SaveState("error", "Data KlpBilling yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
				redirect_url("master.klpbilling");
			}
		}
        $this->Set("klpbilling", $klpbilling);
	}

	private function DoUpdate(KlpBilling $klpbilling) {
		if ($klpbilling->KdKlpBilling == "") {
            $this->Set("error", "Kode belum diisi!");
            return false;
        }
        if ($klpbilling->KlpBilling == "") {
            $this->Set("error", "Kelompok Jasa belum diisi!");
            return false;
        }
        if ($klpbilling->Update($klpbilling->Id) == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function delete($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan hapus data !");
			redirect_url("master.klpbilling");
		}
		$log = new UserAdmin();
		$klpbilling = new KlpBilling();
		$klpbilling = $klpbilling->FindById($id);
		if ($klpbilling == null) {
			$this->persistence->SaveState("error", "Data yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
			redirect_url("master.klpbilling");
		}

		if ($klpbilling->Delete($klpbilling->Id) == 1) {
			$log = $log->UserActivityWriter($this->userCabangId,'master.klpbilling','Delete KlpBilling -> Kode: '.$klpbilling->KdKlpBilling.' - '.$klpbilling->KlpBilling,'-','Success');
			$this->persistence->SaveState("info", sprintf("Data KlpBilling: '%s' Dengan Kode: %s telah berhasil dihapus.", $klpbilling->KlpBilling, $klpbilling->KdKlpBilling));
			redirect_url("master.klpbilling");
		} else {
			$this->persistence->SaveState("error", sprintf("Gagal menghapus data KlpBilling: '%s'. Message: %s", $klpbilling->KlpBilling, $this->connector->GetErrorMessage()));
		}
		redirect_url("master.klpbilling");
	}

	public function AutoKode(){
	    $kode = new KlpBilling();
	    $kode = $kode->GetAutoKode($this->userCompanyId);
	    print $kode;
    }
}
