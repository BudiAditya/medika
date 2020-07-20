<?php
class LabController extends AppController {
	private $userCompanyId;
	private $userCabangId;
	private $userUid;

	protected function Initialize() {
		require_once(MODEL . "master/lab.php");
		require_once(MODEL . "master/user_admin.php");
		$this->userCompanyId = $this->persistence->LoadState("entity_id");
		$this->userCabangId = $this->persistence->LoadState("cabang_id");
        $this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();
		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 40);
		$settings["columns"][] = array("name" => "a.kd_lab", "display" => "Kode", "width" => 150);
		$settings["columns"][] = array("name" => "a.nm_lab", "display" => "Nama Lab", "width" => 350);

		$settings["filters"][] = array("name" => "a.nm_lab", "display" => "Lab");
		$settings["filters"][] = array("name" => "a.kode", "display" => "Kode");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Data Laboratorium";

			if ($acl->CheckUserAccess("lab", "add", "master")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.lab/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("lab", "edit", "master")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.lab/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
											   "Error" => "Mohon memilih data Lab terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu lab",
											   "Info" => "Apakah anda yakin mau merubah data Lab yang dipilih ?");
			}
			if ($acl->CheckUserAccess("lab", "delete", "master")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.lab/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
											   "Error" => "Mohon memilih data Lab terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu lab",
											   "Info" => "Apakah anda yakin mau menghapus data Lab yang dipilih ?");
			}

			$settings["def_filter"] = 0;
			$settings["def_order"] = 2;
			$settings["singleSelect"] = true;
		} else {
			$settings["from"] = "m_laboratorium AS a";
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
		$lab = new Lab();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$lab->EntityId = 1;
			$lab->KdLab = $this->GetPostValue("KdLab");
			$lab->NmLab = $this->GetPostValue("NmLab");
            $lab->CreatebyId = $this->userUid;
			if ($this->DoInsert($lab)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.lab','Add New Lab -> Kode: '.$lab->KdLab.' - '.$lab->NmLab,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data Lab: '%s' Dengan Kode: %s telah berhasil disimpan.", $lab->NmLab, $lab->KdLab));
				redirect_url("master.lab");
			} else {
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $lab->EntityCd));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		}
		// untuk kirim variable ke view
		$this->Set("lab", $lab);
	}

	private function DoInsert(Lab $lab) {

		if ($lab->KdLab == "") {
			$this->Set("error", "Kode masih kosong");
			return false;
		}

		if ($lab->NmLab == "") {
			$this->Set("error", "Nama Lab masih kosong");
			return false;
		}
		if ($lab->Insert() == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function edit($id = null) {
		$loader = null;
		$lab = new Lab();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$lab->Id = $id;
			$lab->EntityId = 1;
			$lab->KdLab = $this->GetPostValue("KdLab");
			$lab->NmLab = $this->GetPostValue("NmLab");
            $lab->UpdatebyId = $this->userUid;
			if ($this->DoUpdate($lab)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.lab','Update Lab -> Kode: '.$lab->KdLab.' - '.$lab->NmLab,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data Lab: '%s' Dengan Kode: %s telah berhasil diupdate.", $lab->NmLab, $lab->KdLab));
				redirect_url("master.lab");
			} else {
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $lab->EntityCd));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		} else {
			if ($id == null) {
				$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan edit data !");
				redirect_url("master.lab");
			}
			$lab = $lab->FindById($id);
			if ($lab == null) {
				$this->persistence->SaveState("error", "Data Lab yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
				redirect_url("master.lab");
			}
		}
        $this->Set("lab", $lab);
	}

	private function DoUpdate(Lab $lab) {
		if ($lab->KdLab == "") {
			$this->Set("error", "Kode  masih kosong");
			return false;
		}
		if ($lab->NmLab == "") {
			$this->Set("error", "Nama Lab masih kosong");
			return false;
		}
		if ($lab->Update($lab->Id) == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function delete($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan hapus data !");
			redirect_url("master.lab");
		}
		$log = new UserAdmin();
		$lab = new Lab();
		$lab = $lab->FindById($id);
		if ($lab == null) {
			$this->persistence->SaveState("error", "Data yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
			redirect_url("master.lab");
		}

		if ($lab->Delete($lab->Id) == 1) {
			$log = $log->UserActivityWriter($this->userCabangId,'master.lab','Delete Lab -> Kode: '.$lab->KdLab.' - '.$lab->NmLab,'-','Success');
			$this->persistence->SaveState("info", sprintf("Data Lab: '%s' Dengan Kode: %s telah berhasil dihapus.", $lab->NmLab, $lab->KdLab));
			redirect_url("master.lab");
		} else {
			$this->persistence->SaveState("error", sprintf("Gagal menghapus data Lab: '%s'. Message: %s", $lab->NmLab, $this->connector->GetErrorMessage()));
		}
		redirect_url("master.lab");
	}

	public function optlistbyentity($EntityId = null, $sLabId = null) {
		$buff = '<option value="">-- PILIH POLIKLINIK --</option>';
		if ($EntityId == null) {
			print($buff);
			return;
		}
		$lab = new Lab();
		$labs = $lab->LoadByEntityId($EntityId);
		foreach ($labs as $lab) {
			if ($lab->Id == $sLabId) {
				$buff .= sprintf('<option value="%d" selected="selected">%s - %s</option>', $lab->Id, $lab->KdLab, $lab->NmLab);
			} else {
				$buff .= sprintf('<option value="%d">%s - %s</option>', $lab->Id, $lab->KdLab, $lab->NmLab);
			}
		}
		print($buff);
	}
}
