<?php
class DokterController extends AppController {
	private $userCompanyId;
	private $userLevel;
	private $userCabangId;
	private $userUid;

	protected function Initialize() {
		require_once(MODEL . "master/dokter.php");
		require_once(MODEL . "master/user_admin.php");
		$this->userCompanyId = $this->persistence->LoadState("entity_id");
		$this->userCabangId = $this->persistence->LoadState("cabang_id");
		$this->userLevel = $this->persistence->LoadState("user_lvl");
        $this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();
		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 40);
		$settings["columns"][] = array("name" => "a.kd_dokter", "display" => "Kode", "width" => 60);
		$settings["columns"][] = array("name" => "a.nm_dokter", "display" => "Nama Dokter", "width" => 250);
        $settings["columns"][] = array("name" => "a.spesialisasi", "display" => "Spesialisasi", "width" => 100);
        $settings["columns"][] = array("name" => "a.hari_praktek", "display" => "Hari Praktek", "width" => 200);
        $settings["columns"][] = array("name" => "a.handphone", "display" => "Handphone", "width" => 100);
        $settings["columns"][] = array("name" => "a.mulai_praktek", "display" => "Mulai", "width" => 100);
        $settings["columns"][] = array("name" => "if(a.dok_status = 0,'Cuti','Aktif')", "display" => "Status", "width" => 100);

		$settings["filters"][] = array("name" => "a.kd_dokter", "display" => "Kode");
		$settings["filters"][] = array("name" => "a.nm_dokter", "display" => "NmDokter Dokter");
        $settings["filters"][] = array("name" => "a.hari_praktek", "display" => "Hari Praktek");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Data Dokter";

			if ($acl->CheckUserAccess("dokter", "add", "master")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.dokter/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("dokter", "edit", "master")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.dokter/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
											   "Error" => "Mohon memilih data dokter terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu dokter",
											   "Info" => "Apakah anda yakin mau merubah data dokter yang dipilih ?");
			}
			if ($acl->CheckUserAccess("dokter", "view", "master")) {
				$settings["actions"][] = array("Text" => "View", "Url" => "master.dokter/view/%s", "Class" => "bt_view", "ReqId" => 1,
						"Error" => "Mohon memilih data dokter terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu dokter");
			}
			if ($acl->CheckUserAccess("dokter", "delete", "master")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.dokter/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
											   "Error" => "Mohon memilih data dokter terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu dokter",
											   "Info" => "Apakah anda yakin mau menghapus data dokter yang dipilih ?");
			}

			$settings["def_filter"] = 0;
			$settings["def_order"] = 2;
			$settings["singleSelect"] = true;
		} else {
			$settings["from"] = "m_dokter AS a JOIN sys_company AS b ON a.entity_id = b.entity_id";
			if ($this->userLevel > 3) {
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
		$log = new UserAdmin();
		$dokter = new Dokter();
		$fpath = null;
		$ftmp = null;
		$fname = null;
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$dokter->EntityId = $this->userCompanyId;
			$dokter->KdDokter = strtoupper($this->GetPostValue("KdDokter"));
			$dokter->NmDokter = $this->GetPostValue("NmDokter");
            $dokter->Spesialisasi = $this->GetPostValue("Spesialisasi");
            $dokter->Alumni = $this->GetPostValue("Alumni");
            $dokter->MulaiPraktek = strtotime($this->GetPostValue("MulaiPraktek"));
            $dokter->DokStatus = $this->GetPostValue("DokStatus");
            $dokter->Jkelamin = $this->GetPostValue("Jkelamin");
            $dokter->T4Lahir = $this->GetPostValue("T4Lahir");
            $dokter->TglLahir = strtotime($this->GetPostValue("TglLahir"));
            $dokter->Alamat = $this->GetPostValue("Alamat");
            $dokter->Handphone = $this->GetPostValue("Handphone");
            $dokter->NoSip = $this->GetPostValue("NoSip");
            $dokter->HariPraktek = $this->GetPostValue("HariPraktek");
            $dokter->Fphoto = null;
			$dokter->CreatebyId = $this->userUid;
			$dokter->JvPoli = $this->GetPostValue("JvPoli");
            $dokter->JvInapum = $this->GetPostValue("JvInapum");
            $dokter->JvBpjs = $this->GetPostValue("JvBpjs");
            $dokter->JkVitel = $this->GetPostValue("JkVitel");
			if (!empty($_FILES['FileName']['tmp_name'])){
				$fpath = 'public/upload/images/';
				$ftmp = $_FILES['FileName']['tmp_name'];
				$fname = $_FILES['FileName']['name'];
				$fpath.= $fname;
				$dokter->Fphoto = $fpath;
				if(!move_uploaded_file($ftmp,$fpath)){
					$this->Set("error", sprintf("Gagal Upload file photo..", $this->connector->GetErrorMessage()));
				}
			}
			if ($this->DoInsert($dokter)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.dokter','Add New Dokter -> NIK: '.$dokter->KdDokter.' - '.$dokter->NmDokter,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data Dokter: '%s' Dengan KdDokter: %s telah berhasil disimpan.", $dokter->NmDokter, $dokter->KdDokter));
				redirect_url("master.dokter");
			} else {
				$log = $log->UserActivityWriter($this->userCabangId,'master.dokter','Add New Dokter -> NIK: '.$dokter->KdDokter.' - '.$dokter->NmDokter,'-','Failed');
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("KdDokter: '%s' telah ada pada database !", $dokter->KdDokter));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		}
		// untuk kirim variable ke view
		$this->Set("dokter", $dokter);
	}

	private function DoInsert(Dokter $dokter) {

		if ($dokter->KdDokter == "") {
			$this->Set("error", "KdDokter dokter masih kosong");
			return false;
		}

		if ($dokter->NmDokter == "") {
			$this->Set("error", "NmDokter dokter masih kosong");
			return false;
		}

		if ($dokter->Insert() == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function edit($id = null) {
		$loader = null;
		$log = new UserAdmin();
		$dokter = new Dokter();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$dokter->Id = $id;
            $dokter->EntityId = $this->userCompanyId;
            $dokter->KdDokter = strtoupper($this->GetPostValue("KdDokter"));
            $dokter->NmDokter = $this->GetPostValue("NmDokter");
            $dokter->Spesialisasi = $this->GetPostValue("Spesialisasi");
            $dokter->Alumni = $this->GetPostValue("Alumni");
            $dokter->MulaiPraktek = strtotime($this->GetPostValue("MulaiPraktek"));
            $dokter->DokStatus = $this->GetPostValue("DokStatus");
            $dokter->Jkelamin = $this->GetPostValue("Jkelamin");
            $dokter->T4Lahir = $this->GetPostValue("T4Lahir");
            $dokter->TglLahir = strtotime($this->GetPostValue("TglLahir"));
            $dokter->Alamat = $this->GetPostValue("Alamat");
            $dokter->Handphone = $this->GetPostValue("Handphone");
            $dokter->NoSip = $this->GetPostValue("NoSip");
            $dokter->HariPraktek = $this->GetPostValue("HariPraktek");
			$dokter->Fphoto = $this->GetPostValue("Fphoto");
            $dokter->UpdatebyId = $this->userUid;
            $dokter->JvPoli = $this->GetPostValue("JvPoli");
            $dokter->JvInapum = $this->GetPostValue("JvInapum");
            $dokter->JvBpjs = $this->GetPostValue("JvBpjs");
            $dokter->JkVitel = $this->GetPostValue("JkVitel");
			if (!empty($_FILES['FileName']['tmp_name'])){
				$fpath = 'public/upload/images/';
				$ftmp = $_FILES['FileName']['tmp_name'];
				$fname = $_FILES['FileName']['name'];
				$fpath.= $fname;
				$dokter->Fphoto = $fpath;
				if(!move_uploaded_file($ftmp,$fpath)){
					$this->Set("error", sprintf("Gagal Upload file photo..", $this->connector->GetErrorMessage()));
				}
			}
			if ($this->DoUpdate($dokter)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.dokter','Update Dokter -> NIK: '.$dokter->KdDokter.' - '.$dokter->NmDokter,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data Dokter: '%s' Dengan KdDokter: %s telah berhasil diupdate.", $dokter->NmDokter, $dokter->KdDokter));
				redirect_url("master.dokter");
			} else {
				$log = $log->UserActivityWriter($this->userCabangId,'master.dokter','Update Dokter -> NIK: '.$dokter->KdDokter.' - '.$dokter->NmDokter,'-','Failed');
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("KdDokter: '%s' telah ada pada database !", $dokter->KdDokter));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		} else {
			if ($id == null) {
				$this->persistence->SaveState("error", "Anda harus memilih data dokter sebelum melakukan edit data !");
				redirect_url("master.dokter");
			}
			$dokter = $dokter->FindById($id);
			if ($dokter == null) {
				$this->persistence->SaveState("error", "Data Dokter yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
				redirect_url("master.dokter");
			}
		}
		// untuk kirim variable ke view
        $this->Set("dokter", $dokter);
	}

	public function view($id = null) {
		$loader = null;
		$log = new UserAdmin();
		$dokter = new Dokter();

		if ($id == null) {
			$this->persistence->SaveState("error", "Anda harus memilih data dokter untuk direview !");
			redirect_url("master.dokter");
		}
		$dokter = $dokter->FindById($id);
		if ($dokter == null) {
			$this->persistence->SaveState("error", "Data Dokter yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
			redirect_url("master.dokter");
		}
        // untuk kirim variable ke view
		$this->Set("dokter", $dokter);
	}

	private function DoUpdate(Dokter $dokter) {
		if ($dokter->KdDokter == "") {
			$this->Set("error", "KdDokter dokter masih kosong");
			return false;
		}

		if ($dokter->NmDokter == "") {
			$this->Set("error", "NmDokter dokter masih kosong");
			return false;
		}

		if ($dokter->Update($dokter->Id) == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function delete($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Anda harus memilih data dokter sebelum melakukan hapus data !");
			redirect_url("master.dokter");
		}
		$log = new UserAdmin();
		$dokter = new Dokter();
		$dokter = $dokter->FindById($id);
		if ($dokter == null) {
			$this->persistence->SaveState("error", "Data dokter yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
			redirect_url("master.dokter");
		}

		if ($dokter->Delete($dokter->Id) == 1) {
			$log = $log->UserActivityWriter($this->userCabangId,'master.dokter','Delete Dokter -> NIK: '.$dokter->KdDokter.' - '.$dokter->NmDokter,'-','Success');
			$this->persistence->SaveState("info", sprintf("Data Dokter: '%s' Dengan KdDokter: %s telah berhasil dihapus.", $dokter->NmDokter, $dokter->KdDokter));
			redirect_url("master.dokter");
		} else {
			$log = $log->UserActivityWriter($this->userCabangId,'master.dokter','Delete Dokter -> NIK: '.$dokter->KdDokter.' - '.$dokter->NmDokter,'-','Success');
			$this->persistence->SaveState("error", sprintf("Gagal menghapus data dokter: '%s'. Message: %s", $dokter->NmDokter, $this->connector->GetErrorMessage()));
		}
		redirect_url("master.dokter");
	}

    public function AutoKode(){
        $kode = new Dokter();
        $kode = $kode->GetAutoKode($this->userCompanyId);
        print $kode;
    }

    public function getJsonDokter(){
        $filter = isset($_POST['q']) ? strval($_POST['q']) : '';
        $dokter = new Dokter();
        $doklists = $dokter->GetJSonDokter($this->userCompanyId,$filter);
        echo json_encode($doklists);
    }
}
