<?php
class KamarController extends AppController {
	private $userCompanyId;
	private $userCabangId;
	private $userUid;

	protected function Initialize() {
		require_once(MODEL . "master/kamar.php");
		require_once(MODEL . "master/user_admin.php");
		$this->userCompanyId = $this->persistence->LoadState("entity_id");
		$this->userCabangId = $this->persistence->LoadState("cabang_id");
        $this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();
		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 40);
		$settings["columns"][] = array("name" => "a.kd_kamar", "display" => "Kode", "width" => 100);
		$settings["columns"][] = array("name" => "a.nm_kamar", "display" => "Nama Kamar", "width" => 250);
        $settings["columns"][] = array("name" => "a.kd_kelas", "display" => "Kelas", "width" => 60);
        $settings["columns"][] = array("name" => "format(a.tarif,0)", "display" => "Tarif", "width" => 60, "align" => "right");
        $settings["columns"][] = array("name" => "if(a.status = 0,'Siap',if(a.status = 1,'Terisi','Tdk Siap'))", "display" => "Status", "width" => 60);

		$settings["filters"][] = array("name" => "a.nm_kamar", "display" => "Kamar");
		$settings["filters"][] = array("name" => "a.kd_kamar", "display" => "Kode");
        $settings["filters"][] = array("name" => "a.kd_kelas", "display" => "KdKelas");
        $settings["filters"][] = array("name" => "if(a.status = 0,'Siap',if(a.status = 1,'Terisi','Tdk Siap'))", "display" => "Status");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Data Kamar";

			if ($acl->CheckUserAccess("kamar", "add", "master")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.kamar/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("kamar", "edit", "master")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.kamar/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
											   "Error" => "Mohon memilih data kamar terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu kamar",
											   "Info" => "Apakah anda yakin mau merubah data kamar yang dipilih ?");
			}
			if ($acl->CheckUserAccess("kamar", "delete", "master")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.kamar/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
											   "Error" => "Mohon memilih data kamar terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu kamar",
											   "Info" => "Apakah anda yakin mau menghapus data kamar yang dipilih ?");
			}

			$settings["def_filter"] = 0;
			$settings["def_order"] = 2;
			$settings["singleSelect"] = true;
		} else {
			$settings["from"] = "m_kamar AS a";
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
        require_once(MODEL . "master/kelas.php");
		$loader = null;
		$kamar = new Kamar();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$kamar->EntityId = 1;
			$kamar->KdKamar = $this->GetPostValue("KdKamar");
			$kamar->NmKamar = $this->GetPostValue("NmKamar");
            $kamar->KdKelas = $this->GetPostValue("KdKelas");
            $kamar->Tarif = $this->GetPostValue("Tarif");
            $kamar->Status = $this->GetPostValue("Status");
            $kamar->CreatebyId = $this->userUid;
			if ($this->DoInsert($kamar)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.kamar','Add New Kamar -> Kode: '.$kamar->KdKamar.' - '.$kamar->NmKamar,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data Kamar: '%s' Dengan Kode: %s telah berhasil disimpan.", $kamar->NmKamar, $kamar->KdKamar));
				redirect_url("master.kamar");
			} else {
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $kamar->EntityCd));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		}
		// untuk kirim variable ke view
        $loader = new Kelas();
		$kelas = $loader->LoadAll();
		$this->Set("kelas", $kelas);
        $this->Set("kamar", $kamar);
	}

	private function DoInsert(Kamar $kamar) {
		if ($kamar->KdKamar == "") {
			$this->Set("error", "Kode belum diisi");
			return false;
		}
		if ($kamar->NmKamar == "") {
			$this->Set("error", "Nama Kamar belum diisi");
			return false;
		}
        if ($kamar->KdKelas == "") {
            $this->Set("error", "Kelas Kamar belum diisi");
            return false;
        }
        if ($kamar->Tarif == "" || $kamar->Tarif == 0) {
            $this->Set("error", "Tarif Kamar belum diisi");
            return false;
        }
        if ($kamar->Status == "") {
            $this->Set("error", "Status Kamar belum diisi");
            return false;
        }
		if ($kamar->Insert() == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function edit($id = null) {
        require_once(MODEL . "master/kelas.php");
		$loader = null;
		$kamar = new Kamar();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$kamar->Id = $id;
			$kamar->EntityId = 1;
			$kamar->KdKamar = $this->GetPostValue("KdKamar");
			$kamar->NmKamar = $this->GetPostValue("NmKamar");
            $kamar->KdKelas = $this->GetPostValue("KdKelas");
            $kamar->Tarif = $this->GetPostValue("Tarif");
            $kamar->Status = $this->GetPostValue("Status");
            $kamar->UpdatebyId = $this->userUid;
			if ($this->DoUpdate($kamar)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.kamar','Update Kamar -> Kode: '.$kamar->KdKamar.' - '.$kamar->NmKamar,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data Kamar: '%s' Dengan Kode: %s telah berhasil diupdate.", $kamar->NmKamar, $kamar->KdKamar));
				redirect_url("master.kamar");
			} else {
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $kamar->EntityCd));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		} else {
			if ($id == null) {
				$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan edit data !");
				redirect_url("master.kamar");
			}
			$kamar = $kamar->FindById($id);
			if ($kamar == null) {
				$this->persistence->SaveState("error", "Data Kamar yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
				redirect_url("master.kamar");
			}
		}
        $loader = new Kelas();
        $kelas = $loader->LoadAll();
        $this->Set("kelas", $kelas);
        $this->Set("kamar", $kamar);
	}

	private function DoUpdate(Kamar $kamar) {
        if ($kamar->KdKamar == "") {
            $this->Set("error", "Kode belum diisi");
            return false;
        }
        if ($kamar->NmKamar == "") {
            $this->Set("error", "Nama Kamar belum diisi");
            return false;
        }
        if ($kamar->KdKelas == "") {
            $this->Set("error", "Kelas Kamar belum diisi");
            return false;
        }
        if ($kamar->Tarif == "" || $kamar->Tarif == 0) {
            $this->Set("error", "Tarif Kamar belum diisi");
            return false;
        }
        if ($kamar->Status == "") {
            $this->Set("error", "Status Kamar belum diisi");
            return false;
        }
		if ($kamar->Update($kamar->Id) == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function delete($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan hapus data !");
			redirect_url("master.kamar");
		}
		$log = new UserAdmin();
		$kamar = new Kamar();
		$kamar = $kamar->FindById($id);
		if ($kamar == null) {
			$this->persistence->SaveState("error", "Data yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
			redirect_url("master.kamar");
		}

		if ($kamar->Delete($kamar->Id) == 1) {
			$log = $log->UserActivityWriter($this->userCabangId,'master.kamar','Delete Kamar -> Kode: '.$kamar->KdKamar.' - '.$kamar->NmKamar,'-','Success');
			$this->persistence->SaveState("info", sprintf("Data Kamar: '%s' Dengan Kode: %s telah berhasil dihapus.", $kamar->NmKamar, $kamar->KdKamar));
			redirect_url("master.kamar");
		} else {
			$this->persistence->SaveState("error", sprintf("Gagal menghapus data kamar: '%s'. Message: %s", $kamar->NmKamar, $this->connector->GetErrorMessage()));
		}
		redirect_url("master.kamar");
	}

	public function optlistbyentity($EntityId = null, $sKamarId = null) {
		$buff = '<option value="">-- PILIH KAMAR --</option>';
		if ($EntityId == null) {
			print($buff);
			return;
		}
		$kamar = new Kamar();
		$kamars = $kamar->LoadByEntityId($EntityId);
		foreach ($kamars as $kamar) {
			if ($kamar->Id == $sKamarId) {
				$buff .= sprintf('<option value="%d" selected="selected">%s - %s</option>', $kamar->Id, $kamar->KdKamar, $kamar->NmKamar);
			} else {
				$buff .= sprintf('<option value="%d">%s - %s</option>', $kamar->Id, $kamar->KdKamar, $kamar->NmKamar);
			}
		}
		print($buff);
	}
}
