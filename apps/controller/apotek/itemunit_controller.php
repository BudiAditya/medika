<?php
class ItemUnitController extends AppController {
	private $userCompanyId;
	private $userCabangId;
    private $userUid;

	protected function Initialize() {
		require_once(MODEL . "apotek/itemunit.php");
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
			$settings["title"] = "Daftar Satuan Barang/Obat";

			if ($acl->CheckUserAccess("itemunit", "add", "apotek")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "apotek.itemunit/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("itemunit", "edit", "apotek")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "apotek.itemunit/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
											   "Error" => "Mohon memilih data itemunit terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu itemunit",
											   "Info" => "Apakah anda yakin mau merubah data itemunit yang dipilih ?");
			}
			if ($acl->CheckUserAccess("itemunit", "delete", "apotek")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "apotek.itemunit/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
											   "Error" => "Mohon memilih data itemunit terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu itemunit",
											   "Info" => "Apakah anda yakin mau menghapus data itemunit yang dipilih ?");
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
		$itemunit = new ItemUnit();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$itemunit->UnitCode = $this->GetPostValue("UnitCode");
			$itemunit->UnitName = $this->GetPostValue("UnitName");
			$itemunit->UnitValue = $this->GetPostValue("UnitValue");
			$itemunit->CreatebyId = $this->userUid;
			if ($this->DoInsert($itemunit)) {
				$log = $log->UserActivityWriter($this->userCabangId,'apotek.itemunit','Add New ItemUnit -> Kode: '.$itemunit->UnitCode.' - '.$itemunit->UnitName,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data ItemUnit: '%s' Dengan Kode: %s telah berhasil disimpan.", $itemunit->UnitCode, $itemunit->UnitName));
				redirect_url("apotek.itemunit");
			} else {
				$log = $log->UserActivityWriter($this->userCabangId,'apotek.itemunit','Add New ItemUnit -> Kode: '.$itemunit->UnitCode.' - '.$itemunit->UnitName,'-','Failed');
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $itemunit->UnitCode));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		}
		// untuk kirim variable ke view
		$this->Set("itemunit", $itemunit);
	}

	private function DoInsert(ItemUnit $itemunit) {

		if ($itemunit->UnitCode == "") {
			$this->Set("error", "Kode Satuan masih kosong");
			return false;
		}
		if ($itemunit->UnitName == "") {
			$this->Set("error", "Satuan Barang masih kosong");
			return false;
		}

		if ($itemunit->Insert() == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function edit($id = null) {
		$itemunit = new ItemUnit();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$itemunit->Id = $id;
            $itemunit->UnitCode = $this->GetPostValue("UnitCode");
            $itemunit->UnitName = $this->GetPostValue("UnitName");
            $itemunit->UnitValue = $this->GetPostValue("UnitValue");
            $itemunit->UpdatebyId = $this->userUid;
            if ($this->DoUpdate($itemunit)) {
                $log = $log->UserActivityWriter($this->userCabangId,'apotek.itemunit','Update ItemUnit -> Kode: '.$itemunit->UnitCode.' - '.$itemunit->UnitName,'-','Success');
                $this->persistence->SaveState("info", sprintf("Data ItemUnit: '%s' Dengan Kode: %s telah berhasil diubah.", $itemunit->UnitCode, $itemunit->UnitName));
                redirect_url("apotek.itemunit");
            } else {
                $log = $log->UserActivityWriter($this->userCabangId,'apotek.itemunit','Update ItemUnit -> Kode: '.$itemunit->UnitCode.' - '.$itemunit->UnitName,'-','Failed');
                if ($this->connector->GetHasError()) {
                    if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
                        $this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $itemunit->UnitCode));
                    } else {
                        $this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
                    }
                }
            }
		} else {
			if ($id == null) {
				$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan edit data !");
				redirect_url("apotek.itemunit");
			}
			$itemunit = $itemunit->FindById($id);
			if ($itemunit == null) {
				$this->persistence->SaveState("error", "Data ItemUnit yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
				redirect_url("apotek.itemunit");
			}
		}

		// untuk kirim variable ke view
		$this->Set("itemunit", $itemunit);
	}

	private function DoUpdate(ItemUnit $itemunit) {
        if ($itemunit->UnitCode == "") {
            $this->Set("error", "Kode Satuan masih kosong");
            return false;
        }
        if ($itemunit->UnitName == "") {
            $this->Set("error", "Satuan Barang masih kosong");
            return false;
        }

		if ($itemunit->Update($itemunit->Id) == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function delete($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Anda harus memilih data itemunit sebelum melakukan hapus data !");
			redirect_url("apotek.itemunit");
		}
		$log = new UserAdmin();
		$itemunit = new ItemUnit();
		$itemunit = $itemunit->FindById($id);
		if ($itemunit == null) {
			$this->persistence->SaveState("error", "Data itemunit yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
			redirect_url("apotek.itemunit");
		}

		if ($itemunit->Delete($itemunit->Id) == 1) {
			$log = $log->UserActivityWriter($this->userCabangId,'apotek.itemunit','Delete ItemUnit -> Kode: '.$itemunit->UnitCode.' - '.$itemunit->UnitName,'-','Success');
			$this->persistence->SaveState("info", sprintf("Data ItemUnit: '%s' Dengan Kode: %s telah berhasil dihapus.", $itemunit->UnitName, $itemunit->UnitCode));
			redirect_url("apotek.itemunit");
		} else {
			$log = $log->UserActivityWriter($this->userCabangId,'apotek.itemunit','Delete ItemUnit -> Kode: '.$itemunit->UnitCode.' - '.$itemunit->UnitName,'-','Failed');
			$this->persistence->SaveState("error", sprintf("Gagal menghapus data itemunit: '%s'. Message: %s", $itemunit->UnitCode, $this->connector->GetErrorMessage()));
		}
		redirect_url("apotek.itemunit");
	}
}
