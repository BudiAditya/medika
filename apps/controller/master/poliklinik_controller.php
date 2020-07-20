<?php
class PoliklinikController extends AppController {
	private $userCompanyId;
	private $userCabangId;
	private $userUid;

	protected function Initialize() {
		require_once(MODEL . "master/poliklinik.php");
		require_once(MODEL . "master/user_admin.php");
		$this->userCompanyId = $this->persistence->LoadState("entity_id");
		$this->userCabangId = $this->persistence->LoadState("cabang_id");
        $this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();
		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 40);
		$settings["columns"][] = array("name" => "a.kd_poliklinik", "display" => "Kode", "width" => 150);
		$settings["columns"][] = array("name" => "a.nm_poliklinik", "display" => "Nama Poliklinik", "width" => 350);

		$settings["filters"][] = array("name" => "a.nm_poliklinik", "display" => "Poliklinik");
		$settings["filters"][] = array("name" => "a.kode", "display" => "Kode");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Data Poliklinik";

			if ($acl->CheckUserAccess("poliklinik", "add", "master")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.poliklinik/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("poliklinik", "edit", "master")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.poliklinik/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
											   "Error" => "Mohon memilih data poliklinik terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu poliklinik",
											   "Info" => "Apakah anda yakin mau merubah data poliklinik yang dipilih ?");
			}
			if ($acl->CheckUserAccess("poliklinik", "delete", "master")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.poliklinik/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
											   "Error" => "Mohon memilih data poliklinik terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu poliklinik",
											   "Info" => "Apakah anda yakin mau menghapus data poliklinik yang dipilih ?");
			}

			$settings["def_filter"] = 0;
			$settings["def_order"] = 2;
			$settings["singleSelect"] = true;
		} else {
			$settings["from"] = "m_poliklinik AS a";
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
		$poli = new Poliklinik();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$poli->EntityId = 1;
			$poli->KdPoliklinik = $this->GetPostValue("KdPoliklinik");
			$poli->NmPoliklinik = $this->GetPostValue("NmPoliklinik");
			$poli->CreatebyId = $this->userUid;

			if ($this->DoInsert($poli)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.poliklinik','Add New Poliklinik -> Kode: '.$poli->KdPoliklinik.' - '.$poli->NmPoliklinik,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data Poliklinik: '%s' Dengan Kode: %s telah berhasil disimpan.", $poli->NmPoliklinik, $poli->KdPoliklinik));
				redirect_url("master.poliklinik");
			} else {
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $poli->EntityCd));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		}
		// untuk kirim variable ke view
		$this->Set("poli", $poli);
	}

	private function DoInsert(Poliklinik $poli) {

		if ($poli->KdPoliklinik == "") {
			$this->Set("error", "Kode masih kosong");
			return false;
		}

		if ($poli->NmPoliklinik == "") {
			$this->Set("error", "Nama Poli masih kosong");
			return false;
		}
		if ($poli->Insert() == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function edit($id = null) {
		$loader = null;
		$poli = new Poliklinik();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$poli->Id = $id;
			$poli->EntityId = 1;
			$poli->KdPoliklinik = $this->GetPostValue("KdPoliklinik");
			$poli->NmPoliklinik = $this->GetPostValue("NmPoliklinik");
            $poli->UpdatebyId = $this->userUid;
			if ($this->DoUpdate($poli)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.poliklinik','Update Poliklinik -> Kode: '.$poli->KdPoliklinik.' - '.$poli->NmPoliklinik,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data Poliklinik: '%s' Dengan Kode: %s telah berhasil diupdate.", $poli->NmPoliklinik, $poli->KdPoliklinik));
				redirect_url("master.poliklinik");
			} else {
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $poli->EntityCd));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		} else {
			if ($id == null) {
				$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan edit data !");
				redirect_url("master.poliklinik");
			}
			$poli = $poli->FindById($id);
			if ($poli == null) {
				$this->persistence->SaveState("error", "Data Poliklinik yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
				redirect_url("master.poliklinik");
			}
		}
        $this->Set("poli", $poli);
	}

	private function DoUpdate(Poliklinik $poli) {
		if ($poli->KdPoliklinik == "") {
			$this->Set("error", "Kode  masih kosong");
			return false;
		}
		if ($poli->NmPoliklinik == "") {
			$this->Set("error", "Nama Poli masih kosong");
			return false;
		}
		if ($poli->Update($poli->Id) == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function delete($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan hapus data !");
			redirect_url("master.poliklinik");
		}
		$log = new UserAdmin();
		$poli = new Poliklinik();
		$poli = $poli->FindById($id);
		if ($poli == null) {
			$this->persistence->SaveState("error", "Data yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
			redirect_url("master.poliklinik");
		}

		if ($poli->Delete($poli->Id) == 1) {
			$log = $log->UserActivityWriter($this->userCabangId,'master.poliklinik','Delete Poliklinik -> Kode: '.$poli->KdPoliklinik.' - '.$poli->NmPoliklinik,'-','Success');
			$this->persistence->SaveState("info", sprintf("Data Poliklinik: '%s' Dengan Kode: %s telah berhasil dihapus.", $poli->NmPoliklinik, $poli->KdPoliklinik));
			redirect_url("master.poliklinik");
		} else {
			$this->persistence->SaveState("error", sprintf("Gagal menghapus data poliklinik: '%s'. Message: %s", $poli->NmPoliklinik, $this->connector->GetErrorMessage()));
		}
		redirect_url("master.poliklinik");
	}

	public function optlistbyentity($EntityId = null, $sPoliId = null) {
		$buff = '<option value="">-- PILIH POLIKLINIK --</option>';
		if ($EntityId == null) {
			print($buff);
			return;
		}
		$poliklinik = new Poliklinik();
		$polikliniks = $poliklinik->LoadByEntityId($EntityId);
		foreach ($polikliniks as $poliklinik) {
			if ($poliklinik->Id == $sPoliId) {
				$buff .= sprintf('<option value="%d" selected="selected">%s - %s</option>', $poliklinik->Id, $poliklinik->KdPoliklinik, $poliklinik->NmPoliklinik);
			} else {
				$buff .= sprintf('<option value="%d">%s - %s</option>', $poliklinik->Id, $poliklinik->KdPoliklinik, $poliklinik->NmPoliklinik);
			}
		}
		print($buff);
	}
}
