<?php
class RelasiController extends AppController {
	private $userCompanyId;
	private $userCabangId;
	private $userUid;

	protected function Initialize() {
		require_once(MODEL . "master/relasi.php");
		require_once(MODEL . "master/user_admin.php");
		$this->userCompanyId = $this->persistence->LoadState("entity_id");
		$this->userCabangId = $this->persistence->LoadState("cabang_id");
        $this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();
		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 40);
		$settings["columns"][] = array("name" => "a.kd_relasi", "display" => "Kode", "width" => 50);
		$settings["columns"][] = array("name" => "a.nm_relasi", "display" => "Nama Relasi", "width" => 250);
        $settings["columns"][] = array("name" => "if(a.jns_relasi = 1,'Supplier','Customer')", "display" => "Kategori", "width" => 50);
        $settings["columns"][] = array("name" => "a.alamat", "display" => "Alamat", "width" => 250);
        $settings["columns"][] = array("name" => "a.kabkota", "display" => "Kab/Kota", "width" => 100);
        $settings["columns"][] = array("name" => "a.cperson", "display" => "Ats Nama", "width" => 150);
        $settings["columns"][] = array("name" => "a.tel_no", "display" => "No. HP", "width" => 100);
        $settings["columns"][] = array("name" => "if(a.status = 1,'Aktif','Non-Aktif')", "display" => "Status", "width" => 50);

		$settings["filters"][] = array("name" => "a.nm_relasi", "display" => "Relasi");
		$settings["filters"][] = array("name" => "a.kode", "display" => "Kode");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Data Relasi (Supplier & Customer)";
            //button action
			if ($acl->CheckUserAccess("relasi", "add", "master")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.relasi/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("relasi", "edit", "master")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.relasi/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
											   "Error" => "Mohon memilih data Relasi terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu relasi",
											   "Info" => "Apakah anda yakin mau merubah data Relasi yang dipilih ?");
			}
			if ($acl->CheckUserAccess("relasi", "delete", "master")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.relasi/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
											   "Error" => "Mohon memilih data Relasi terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu relasi",
											   "Info" => "Apakah anda yakin mau menghapus data Relasi yang dipilih ?");
			}

			$settings["def_filter"] = 0;
			$settings["def_order"] = 2;
			$settings["singleSelect"] = true;
		} else {
			$settings["from"] = "m_relasi AS a";
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
		$relasi = new Relasi();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$relasi->EntityId = $this->userCompanyId;
			$relasi->KdRelasi = $this->GetPostValue("KdRelasi");
			$relasi->NmRelasi = $this->GetPostValue("NmRelasi");
            $relasi->NmRelasi = $this->GetPostValue("NmRelasi");
            $relasi->JnsRelasi = $this->GetPostValue("JnsRelasi");
            $relasi->Alamat = $this->GetPostValue("Alamat");
            $relasi->Kabkota = $this->GetPostValue("Kabkota");
            $relasi->TelNo = $this->GetPostValue("TelNo");
            $relasi->Cperson = $this->GetPostValue("Cperson");
            $relasi->CpJabatan = $this->GetPostValue("CpJabatan");
            $relasi->KdPos = $this->GetPostValue("KdPos");
            $relasi->Npwp = $this->GetPostValue("Npwp");
            $relasi->LmKredit = $this->GetPostValue("LmKredit");
            $relasi->Status = $this->GetPostValue("Status");
            $relasi->CreatebyId = $this->userUid;
			if ($this->DoInsert($relasi)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.relasi','Add New Relasi -> Kode: '.$relasi->KdRelasi.' - '.$relasi->NmRelasi,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data Relasi: '%s' Dengan Kode: %s telah berhasil disimpan.", $relasi->NmRelasi, $relasi->KdRelasi));
				redirect_url("master.relasi");
			} else {
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $relasi->EntityCd));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		}
		// untuk kirim variable ke view
		$this->Set("relasi", $relasi);
	}

	private function DoInsert(Relasi $relasi) {

		if ($relasi->KdRelasi == "") {
			$this->Set("error", "Kode masih kosong");
			return false;
		}

		if ($relasi->NmRelasi == "") {
			$this->Set("error", "Nama Relasi masih kosong");
			return false;
		}
		if ($relasi->Insert() == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function edit($id = null) {
		$loader = null;
		$relasi = new Relasi();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$relasi->Id = $id;
            $relasi->EntityId = $this->userCompanyId;
            $relasi->KdRelasi = $this->GetPostValue("KdRelasi");
            $relasi->NmRelasi = $this->GetPostValue("NmRelasi");
            $relasi->NmRelasi = $this->GetPostValue("NmRelasi");
            $relasi->JnsRelasi = $this->GetPostValue("JnsRelasi");
            $relasi->Alamat = $this->GetPostValue("Alamat");
            $relasi->Kabkota = $this->GetPostValue("Kabkota");
            $relasi->TelNo = $this->GetPostValue("TelNo");
            $relasi->Cperson = $this->GetPostValue("Cperson");
            $relasi->CpJabatan = $this->GetPostValue("CpJabatan");
            $relasi->KdPos = $this->GetPostValue("KdPos");
            $relasi->Npwp = $this->GetPostValue("Npwp");
            $relasi->LmKredit = $this->GetPostValue("LmKredit");
            $relasi->Status = $this->GetPostValue("Status");
            $relasi->UpdatebyId = $this->userUid;
			if ($this->DoUpdate($relasi)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.relasi','Update Relasi -> Kode: '.$relasi->KdRelasi.' - '.$relasi->NmRelasi,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data Relasi: '%s' Dengan Kode: %s telah berhasil diupdate.", $relasi->NmRelasi, $relasi->KdRelasi));
				redirect_url("master.relasi");
			} else {
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $relasi->EntityCd));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		} else {
			if ($id == null) {
				$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan edit data !");
				redirect_url("master.relasi");
			}
			$relasi = $relasi->FindById($id);
			if ($relasi == null) {
				$this->persistence->SaveState("error", "Data Relasi yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
				redirect_url("master.relasi");
			}
		}
        $this->Set("relasi", $relasi);
	}

	private function DoUpdate(Relasi $relasi) {
		if ($relasi->KdRelasi == "") {
			$this->Set("error", "Kode  masih kosong");
			return false;
		}
		if ($relasi->NmRelasi == "") {
			$this->Set("error", "Nama Relasi masih kosong");
			return false;
		}
		if ($relasi->Update($relasi->Id) == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function delete($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan hapus data !");
			redirect_url("master.relasi");
		}
		$log = new UserAdmin();
		$relasi = new Relasi();
		$relasi = $relasi->FindById($id);
		if ($relasi == null) {
			$this->persistence->SaveState("error", "Data yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
			redirect_url("master.relasi");
		}

		if ($relasi->Delete($relasi->Id) == 1) {
			$log = $log->UserActivityWriter($this->userCabangId,'master.relasi','Delete Relasi -> Kode: '.$relasi->KdRelasi.' - '.$relasi->NmRelasi,'-','Success');
			$this->persistence->SaveState("info", sprintf("Data Relasi: '%s' Dengan Kode: %s telah berhasil dihapus.", $relasi->NmRelasi, $relasi->KdRelasi));
			redirect_url("master.relasi");
		} else {
			$this->persistence->SaveState("error", sprintf("Gagal menghapus data Relasi: '%s'. Message: %s", $relasi->NmRelasi, $this->connector->GetErrorMessage()));
		}
		redirect_url("master.relasi");
	}

	public function optlistbyentity($EntityId = null, $sRelasiId = null) {
		$buff = '<option value="">-- PILIH POLIKLINIK --</option>';
		if ($EntityId == null) {
			print($buff);
			return;
		}
		$relasi = new Relasi();
		$relasis = $relasi->LoadByEntityId($EntityId);
		foreach ($relasis as $relasi) {
			if ($relasi->Id == $sRelasiId) {
				$buff .= sprintf('<option value="%d" selected="selected">%s - %s</option>', $relasi->Id, $relasi->KdRelasi, $relasi->NmRelasi);
			} else {
				$buff .= sprintf('<option value="%d">%s - %s</option>', $relasi->Id, $relasi->KdRelasi, $relasi->NmRelasi);
			}
		}
		print($buff);
	}
	
	public function getAutoCode($rtype = 1){
	    $relasi = new  Relasi();
	    print ($relasi->GetAutoCode($rtype));
    }

    public function getJsonRelasi($rtype = 0){
        $filter = isset($_POST['q']) ? strval($_POST['q']) : '';
        $relasi = new Relasi();
        $relists = $relasi->GetJSonRelasi($rtype,$this->userCompanyId,$filter);
        echo json_encode($relists);
    }
}
