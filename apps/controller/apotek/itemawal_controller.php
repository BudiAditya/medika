<?php
class ItemAwalController extends AppController {
	private $userCompanyId;
	private $userCabangId;
    private $userUid;

	protected function Initialize() {
		require_once(MODEL . "apotek/itemawal.php");
		require_once(MODEL . "master/user_admin.php");
		$this->userCompanyId = $this->persistence->LoadState("entity_id");
		$this->userCabangId = $this->persistence->LoadState("cabang_id");
        $this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();

		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 40);
		$settings["columns"][] = array("name" => "a.unit_code", "display" => "Kode", "width" => 100);
		$settings["columns"][] = array("name" => "a.unit_name", "display" => "Satuan", "width" => 350);
        $settings["columns"][] = array("name" => "a.unit_value", "display" => "Isi", "width" => 450);

		$settings["filters"][] = array("name" => "a.unit_code", "display" => "Kode");
		$settings["filters"][] = array("name" => "a.unit_name", "display" => "Satuan");
        $settings["filters"][] = array("name" => "a.unit_value", "display" => "Isi");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Daftar Stock Awal Barang/Obat";

			if ($acl->CheckUserAccess("itemawal", "add", "apotek")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "apotek.itemawal/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("itemawal", "edit", "apotek")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "apotek.itemawal/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
											   "Error" => "Mohon memilih data itemawal terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu itemawal",
											   "Info" => "Apakah anda yakin mau merubah data itemawal yang dipilih ?");
			}
			if ($acl->CheckUserAccess("itemawal", "delete", "apotek")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "apotek.itemawal/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
											   "Error" => "Mohon memilih data itemawal terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu itemawal",
											   "Info" => "Apakah anda yakin mau menghapus data itemawal yang dipilih ?");
			}

			$settings["def_filter"] = 0;
			$settings["def_order"] = 2;
			$settings["singleSelect"] = true;
		} else {
			$settings["from"] = "m_apt_item_unit AS a";
            $settings["where"] = "a.is_deleted = 0";
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

	public function add() {
		$itemawal = new ItemAwal();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$itemawal->UnitCode = $this->GetPostValue("UnitCode");
			$itemawal->UnitName = $this->GetPostValue("UnitName");
			$itemawal->UnitValue = $this->GetPostValue("UnitValue");
			$itemawal->CreatebyId = $this->userUid;
			if ($this->DoInsert($itemawal)) {
				$log = $log->UserActivityWriter($this->userCabangId,'apotek.itemawal','Add New ItemAwal -> Kode: '.$itemawal->UnitCode.' - '.$itemawal->UnitName,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data ItemAwal: '%s' Dengan Kode: %s telah berhasil disimpan.", $itemawal->UnitCode, $itemawal->UnitName));
				redirect_url("apotek.itemawal");
			} else {
				$log = $log->UserActivityWriter($this->userCabangId,'apotek.itemawal','Add New ItemAwal -> Kode: '.$itemawal->UnitCode.' - '.$itemawal->UnitName,'-','Failed');
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $itemawal->UnitCode));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		}
		// untuk kirim variable ke view
		$this->Set("itemawal", $itemawal);
	}

	private function DoInsert(ItemAwal $itemawal) {

		if ($itemawal->UnitCode == "") {
			$this->Set("error", "Kode Satuan masih kosong");
			return false;
		}
		if ($itemawal->UnitName == "") {
			$this->Set("error", "Satuan Barang masih kosong");
			return false;
		}

		if ($itemawal->Insert() == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function edit($id = null) {
		$itemawal = new ItemAwal();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$itemawal->Id = $id;
            $itemawal->UnitCode = $this->GetPostValue("UnitCode");
            $itemawal->UnitName = $this->GetPostValue("UnitName");
            $itemawal->UnitValue = $this->GetPostValue("UnitValue");
            $itemawal->UpdatebyId = $this->userUid;
            if ($this->DoUpdate($itemawal)) {
                $log = $log->UserActivityWriter($this->userCabangId,'apotek.itemawal','Update ItemAwal -> Kode: '.$itemawal->UnitCode.' - '.$itemawal->UnitName,'-','Success');
                $this->persistence->SaveState("info", sprintf("Data ItemAwal: '%s' Dengan Kode: %s telah berhasil diubah.", $itemawal->UnitCode, $itemawal->UnitName));
                redirect_url("apotek.itemawal");
            } else {
                $log = $log->UserActivityWriter($this->userCabangId,'apotek.itemawal','Update ItemAwal -> Kode: '.$itemawal->UnitCode.' - '.$itemawal->UnitName,'-','Failed');
                if ($this->connector->GetHasError()) {
                    if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
                        $this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $itemawal->UnitCode));
                    } else {
                        $this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
                    }
                }
            }
		} else {
			if ($id == null) {
				$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan edit data !");
				redirect_url("apotek.itemawal");
			}
			$itemawal = $itemawal->FindById($id);
			if ($itemawal == null) {
				$this->persistence->SaveState("error", "Data ItemAwal yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
				redirect_url("apotek.itemawal");
			}
		}

		// untuk kirim variable ke view
		$this->Set("itemawal", $itemawal);
	}

	private function DoUpdate(ItemAwal $itemawal) {
        if ($itemawal->UnitCode == "") {
            $this->Set("error", "Kode Satuan masih kosong");
            return false;
        }
        if ($itemawal->UnitName == "") {
            $this->Set("error", "Satuan Barang masih kosong");
            return false;
        }

		if ($itemawal->Update($itemawal->Id) == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function delete($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Anda harus memilih data itemawal sebelum melakukan hapus data !");
			redirect_url("apotek.itemawal");
		}
		$log = new UserAdmin();
		$itemawal = new ItemAwal();
		$itemawal = $itemawal->FindById($id);
		if ($itemawal == null) {
			$this->persistence->SaveState("error", "Data itemawal yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
			redirect_url("apotek.itemawal");
		}

		if ($itemawal->Delete($itemawal->Id) == 1) {
			$log = $log->UserActivityWriter($this->userCabangId,'apotek.itemawal','Delete ItemAwal -> Kode: '.$itemawal->UnitCode.' - '.$itemawal->UnitName,'-','Success');
			$this->persistence->SaveState("info", sprintf("Data ItemAwal: '%s' Dengan Kode: %s telah berhasil dihapus.", $itemawal->UnitName, $itemawal->UnitCode));
			redirect_url("apotek.itemawal");
		} else {
			$log = $log->UserActivityWriter($this->userCabangId,'apotek.itemawal','Delete ItemAwal -> Kode: '.$itemawal->UnitCode.' - '.$itemawal->UnitName,'-','Failed');
			$this->persistence->SaveState("error", sprintf("Gagal menghapus data itemawal: '%s'. Message: %s", $itemawal->UnitCode, $this->connector->GetErrorMessage()));
		}
		redirect_url("apotek.itemawal");
	}
}
