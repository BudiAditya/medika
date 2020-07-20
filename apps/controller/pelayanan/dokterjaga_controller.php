<?php
class DokterJagaController extends AppController {
	private $userCompanyId;
	private $userCabangId;
	private $userUid;

	protected function Initialize() {
		require_once(MODEL . "pelayanan/dokterjaga.php");
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
		$settings["columns"][] = array("name" => "a.tanggal", "display" => "Tanggal", "width" => 60);
		$settings["columns"][] = array("name" => "a.kd_dokter", "display" => "Kode", "width" => 60);
        $settings["columns"][] = array("name" => "b.nm_dokter", "display" => "Nama Dokter", "width" => 200);
        $settings["columns"][] = array("name" => "a.keterangan", "display" => "Keterangan", "width" => 300);
        //filtering
		$settings["filters"][] = array("name" => "a.tanggal", "display" => "Tanggal");
		$settings["filters"][] = array("name" => "a.kd_dokter", "display" => "Kode Dokter");
        $settings["filters"][] = array("name" => "b.nm_dokter", "display" => "Nama Dokter");
        $settings["filters"][] = array("name" => "a.keterangan", "display" => "Keterangan");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Data Dokter Jaga";

			if ($acl->CheckUserAccess("dokterjaga", "add", "pelayanan")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "pelayanan.dokterjaga/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("dokterjaga", "edit", "pelayanan")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "pelayanan.dokterjaga/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
											   "Error" => "Mohon memilih data terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu data",
											   "Info" => "Apakah anda yakin mau merubah data yang dipilih ?");
			}
			if ($acl->CheckUserAccess("dokterjaga", "delete", "pelayanan")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "pelayanan.dokterjaga/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
											   "Error" => "Mohon memilih data terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu data",
											   "Info" => "Apakah anda yakin mau menghapus data yang dipilih ?");
			}
            //sort and method
			$settings["def_filter"] = 0;
			$settings["def_order"] = 2;
			$settings["singleSelect"] = true;
		} else {
		    //database
			$settings["from"] = "t_dokter_jaga AS a JOIN m_dokter AS b On a.kd_dokter = b.kd_dokter";
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

	public function add() {
        require_once(MODEL . "master/dokter.php");
		$loader = null;
		$dokterjaga = new DokterJaga();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$dokterjaga->Tanggal = strtotime($this->GetPostValue("Tanggal"));
			$dokterjaga->KdDokter = $this->GetPostValue("KdDokter");
            $dokterjaga->Keterangan = $this->GetPostValue("Keterangan");
            $dokterjaga->CreatebyId = $this->userUid;
			if ($this->DoInsert($dokterjaga)) {
				$log = $log->UserActivityWriter($this->userCabangId,'pelayanan.dokterjaga','Add New Dokter Jaga -> Kode: '.$dokterjaga->KdDokter.' - '.$dokterjaga->Keterangan,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data DokterJaga: %s - %s telah berhasil disimpan.", $dokterjaga->KdDokter, $dokterjaga->Keterangan));
				redirect_url("pelayanan.dokterjaga");
			} else {
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $dokterjaga->KdDokter));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		}
		// untuk kirim variable ke view
        $loader = new Dokter();
		$dokter = $loader->LoadAll();
        $this->Set("dokter", $dokter);
		$this->Set("dokterjaga", $dokterjaga);
	}

	private function DoInsert(DokterJaga $dokterjaga) {
		if ($dokterjaga->KdDokter == "") {
			$this->Set("error", "Kode Dokter belum diisi!");
			return false;
		}
		if ($dokterjaga->Insert() == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function edit($id = null) {
        require_once(MODEL . "master/dokter.php");
		$loader = null;
		$dokterjaga = new DokterJaga();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$dokterjaga->Id = $id;
            $dokterjaga->Tanggal = strtotime($this->GetPostValue("Tanggal"));
            $dokterjaga->KdDokter = $this->GetPostValue("KdDokter");
            $dokterjaga->Keterangan = $this->GetPostValue("Keterangan");
            $dokterjaga->UpdatebyId = $this->userUid;
			if ($this->DoUpdate($dokterjaga)) {
				$log = $log->UserActivityWriter($this->userCabangId,'pelayanan.dokterjaga','Update Dokter Jaga -> Kode: '.$dokterjaga->KdDokter.' - '.$dokterjaga->Keterangan,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data Dokter Jaga: %s telah berhasil diupdate.", $dokterjaga->KdDokter));
				redirect_url("pelayanan.dokterjaga");
			} else {
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $dokterjaga->KdDokter));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		} else {
			if ($id == null) {
				$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan edit data !");
				redirect_url("pelayanan.dokterjaga");
			}
			$dokterjaga = $dokterjaga->FindById($id);
			if ($dokterjaga == null) {
				$this->persistence->SaveState("error", "Data Dokter Jaga yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
				redirect_url("pelayanan.dokterjaga");
			}
		}
		//load data yg diperlukan
        $loader = new Dokter();
        $dokter = $loader->LoadAll();
        $this->Set("dokter", $dokter);
        $this->Set("dokterjaga", $dokterjaga);
	}

	private function DoUpdate(DokterJaga $dokterjaga) {
		if ($dokterjaga->KdDokter == "") {
            $this->Set("error", "Kode Dokter belum diisi!");
            return false;
        }
        if ($dokterjaga->Update($dokterjaga->Id) == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function delete($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan hapus data !");
			redirect_url("pelayanan.dokterjaga");
		}
		$log = new UserAdmin();
		$dokterjaga = new DokterJaga();
		$dokterjaga = $dokterjaga->FindById($id);
		if ($dokterjaga == null) {
			$this->persistence->SaveState("error", "Data yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
			redirect_url("pelayanan.dokterjaga");
		}

		if ($dokterjaga->Delete($dokterjaga->Id) == 1) {
			$log = $log->UserActivityWriter($this->userCabangId,'pelayanan.dokterjaga','Delete Dokter Jaga -> Kode: '.$dokterjaga->KdDokter.' - '.$dokterjaga->Keterangan,'-','Success');
			$this->persistence->SaveState("info", sprintf("Data Dokter Jaga: %s telah berhasil dihapus.", $dokterjaga->KdDokter));
			redirect_url("pelayanan.dokterjaga");
		} else {
			$this->persistence->SaveState("error", sprintf("Gagal menghapus data Dokter Jaga: '%s'. Message: %s", $dokterjaga->KdDokter, $this->connector->GetErrorMessage()));
		}
		redirect_url("pelayanan.dokterjaga");
	}
}
