<?php
class PenyakitController extends AppController {
	private $userCompanyId;
	private $userCabangId;
	private $userUid;

	protected function Initialize() {
		require_once(MODEL . "master/penyakit.php");
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
		$settings["columns"][] = array("name" => "a.kd_penyakit", "display" => "Kode", "width" => 70);
		$settings["columns"][] = array("name" => "a.nm_penyakit", "display" => "Nama Penyakit", "width" => 150);
        $settings["columns"][] = array("name" => "a.ciri_ciri", "display" => "Ciri Ciri", "width" => 450);
        $settings["columns"][] = array("name" => "a.keterangan", "display" => "Keterangan", "width" => 150);
        $settings["columns"][] = array("name" => "b.kelompok", "display" => "Kategori", "width" => 70);
        $settings["columns"][] = array("name" => "a.status", "display" => "Status", "width" => 70);
        //filtering
		$settings["filters"][] = array("name" => "a.nm_penyakit", "display" => "Penyakit");
		$settings["filters"][] = array("name" => "a.kd_penyakit", "display" => "Kode");
        $settings["filters"][] = array("name" => "a.ciri_ciri", "display" => "Ciri2");
        $settings["filters"][] = array("name" => "a.status", "display" => "Status");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Data Jenis Penyakit";
            //button
            /*
			if ($acl->CheckUserAccess("penyakit", "add", "master")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.penyakit/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("penyakit", "edit", "master")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.penyakit/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
											   "Error" => "Mohon memilih data terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu data",
											   "Info" => "Apakah anda yakin mau merubah data yang dipilih ?");
			}
			if ($acl->CheckUserAccess("penyakit", "delete", "master")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.penyakit/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
											   "Error" => "Mohon memilih data terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu data",
											   "Info" => "Apakah anda yakin mau menghapus data yang dipilih ?");
			}
            */
            //sort and method
			$settings["def_filter"] = 0;
			$settings["def_order"] = 2;
			$settings["singleSelect"] = true;
		} else {
		    //database
			$settings["from"] = "m_penyakit AS a Join m_klppenyakit  AS b On a.kd_kelompok = b.kode";
			//$settings["where"] = "a.is_deleted = 0";
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

	public function add() {
		$loader = null;
		$klp = new Penyakit();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$klp->Kode = $this->GetPostValue("Kode");
			$klp->Kelompok = $this->GetPostValue("Kelompok");
            $klp->CreatebyId = $this->userUid;
			if ($this->DoInsert($klp)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.penyakit','Add New Kelompok -> Kode: '.$klp->Kode.' - '.$klp->Kelompok,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data Kelompok: '%s' Dengan Kode: %s telah berhasil disimpan.", $klp->Kelompok, $klp->Kode));
				redirect_url("master.penyakit");
			} else {
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $klp->EntityCd));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		}
		// untuk kirim variable ke view
		$this->Set("klp", $klp);
	}

	private function DoInsert(Penyakit $klp) {

		if ($klp->Kode == "") {
			$this->Set("error", "Kode belum diisi");
			return false;
		}
		if ($klp->Kelompok == "") {
			$this->Set("error", "Kelompok Penyakit belum diisi");
			return false;
		}
		if ($klp->Insert() == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function edit($id = null) {
		$loader = null;
		$klp = new Penyakit();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$klp->Id = $id;
			$klp->Kode = $this->GetPostValue("Kode");
			$klp->Kelompok = $this->GetPostValue("Kelompok");
            $klp->UpdatebyId = $this->userUid;
			if ($this->DoUpdate($klp)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.penyakit','Update Kelompok -> Kode: '.$klp->Kode.' - '.$klp->Kelompok,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data Kelompok: '%s' Dengan Kode: %s telah berhasil diupdate.", $klp->Kelompok, $klp->Kode));
				redirect_url("master.penyakit");
			} else {
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $klp->EntityCd));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		} else {
			if ($id == null) {
				$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan edit data !");
				redirect_url("master.penyakit");
			}
			$klp = $klp->FindById($id);
			if ($klp == null) {
				$this->persistence->SaveState("error", "Data Kelompok yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
				redirect_url("master.penyakit");
			}
		}
        $this->Set("klp", $klp);
	}

	private function DoUpdate(Penyakit $klp) {
		if ($klp->Kode == "") {
			$this->Set("error", "Kode  belum diisi");
			return false;
		}
		if ($klp->Kelompok == "") {
			$this->Set("error", "Nama Kelompok belum diisi");
			return false;
		}
		if ($klp->Update($klp->Id) == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function delete($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan hapus data !");
			redirect_url("master.penyakit");
		}
		$log = new UserAdmin();
		$klp = new Penyakit();
		$klp = $klp->FindById($id);
		if ($klp == null) {
			$this->persistence->SaveState("error", "Data yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
			redirect_url("master.penyakit");
		}

		if ($klp->Delete($klp->Id) == 1) {
			$log = $log->UserActivityWriter($this->userCabangId,'master.penyakit','Delete Kelompok -> Kode: '.$klp->Kode.' - '.$klp->Kelompok,'-','Success');
			$this->persistence->SaveState("info", sprintf("Data Kelompok: '%s' Dengan Kode: %s telah berhasil dihapus.", $klp->Kelompok, $klp->Kode));
			redirect_url("master.penyakit");
		} else {
			$this->persistence->SaveState("error", sprintf("Gagal menghapus data Kelompok: '%s'. Message: %s", $klp->Kelompok, $this->connector->GetErrorMessage()));
		}
		redirect_url("master.penyakit");
	}

	public function optlistbyentity($EntityId = null, $sKelompokId = null) {
		$buff = '<option value="">-- PILIH KELOMPOK --</option>';
		if ($EntityId == null) {
			print($buff);
			return;
		}
		$klp = new Penyakit();
		$klps = $klp->LoadAll();
		foreach ($klps as $klp) {
			if ($klp->Id == $sKelompokId) {
				$buff .= sprintf('<option value="%d" selected="selected">%s - %s</option>', $klp->Id, $klp->Kode, $klp->Kelompok);
			} else {
				$buff .= sprintf('<option value="%d">%s - %s</option>', $klp->Id, $klp->Kode, $klp->Kelompok);
			}
		}
		print($buff);
	}
}
