<?php
class ItemTypeController extends AppController {
	private $userCompanyId;
	private $userCabangId;
    private $userUid;

	protected function Initialize() {
		require_once(MODEL . "apotek/itemtype.php");
		require_once(MODEL . "master/user_admin.php");
		$this->userCompanyId = $this->persistence->LoadState("entity_id");
		$this->userCabangId = $this->persistence->LoadState("cabang_id");
        $this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();

		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 40);
		$settings["columns"][] = array("name" => "a.type_code", "display" => "Kode", "width" => 100);
		$settings["columns"][] = array("name" => "a.type_name", "display" => "Jenis Barang/Obat", "width" => 350);
        $settings["columns"][] = array("name" => "a.type_descs", "display" => "Keterangan", "width" => 450);

		$settings["filters"][] = array("name" => "a.type_code", "display" => "Kode");
		$settings["filters"][] = array("name" => "a.type_name", "display" => "Jenis");
        $settings["filters"][] = array("name" => "a.type_descs", "display" => "Keterangan");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Daftar Jenis Barang/Obat";

			if ($acl->CheckUserAccess("itemtype", "add", "apotek")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "apotek.itemtype/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("itemtype", "edit", "apotek")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "apotek.itemtype/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
											   "Error" => "Mohon memilih data itemtype terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu itemtype",
											   "Info" => "Apakah anda yakin mau merubah data itemtype yang dipilih ?");
			}
			if ($acl->CheckUserAccess("itemtype", "delete", "apotek")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "apotek.itemtype/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
											   "Error" => "Mohon memilih data itemtype terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu itemtype",
											   "Info" => "Apakah anda yakin mau menghapus data itemtype yang dipilih ?");
			}

			$settings["def_filter"] = 0;
			$settings["def_order"] = 2;
			$settings["singleSelect"] = true;
		} else {
			$settings["from"] = "m_apt_item_type AS a";
            $settings["where"] = "a.is_deleted = 0";
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

	public function add() {
		$itemtype = new ItemType();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$itemtype->TypeCode = $this->GetPostValue("TypeCode");
			$itemtype->TypeName = $this->GetPostValue("TypeName");
			$itemtype->TypeDescs = $this->GetPostValue("TypeDescs");
			$itemtype->CreatebyId = $this->userUid;
			if ($this->DoInsert($itemtype)) {
				$log = $log->UserActivityWriter($this->userCabangId,'apotek.itemtype','Add New ItemType -> Kode: '.$itemtype->TypeCode.' - '.$itemtype->TypeName,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data ItemType: '%s' Dengan Kode: %s telah berhasil disimpan.", $itemtype->TypeCode, $itemtype->TypeName));
				redirect_url("apotek.itemtype");
			} else {
				$log = $log->UserActivityWriter($this->userCabangId,'apotek.itemtype','Add New ItemType -> Kode: '.$itemtype->TypeCode.' - '.$itemtype->TypeName,'-','Failed');
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $itemtype->TypeCode));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		}
		// untuk kirim variable ke view
		$this->Set("itemtype", $itemtype);
	}

	private function DoInsert(ItemType $itemtype) {

		if ($itemtype->TypeCode == "") {
			$this->Set("error", "Kode Jenis masih kosong");
			return false;
		}
		if ($itemtype->TypeName == "") {
			$this->Set("error", "Jenis Barang masih kosong");
			return false;
		}

		if ($itemtype->Insert() == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function edit($id = null) {
		$itemtype = new ItemType();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$itemtype->Id = $id;
            $itemtype->TypeCode = $this->GetPostValue("TypeCode");
            $itemtype->TypeName = $this->GetPostValue("TypeName");
            $itemtype->TypeDescs = $this->GetPostValue("TypeDescs");
            $itemtype->UpdatebyId = $this->userUid;
            if ($this->DoUpdate($itemtype)) {
                $log = $log->UserActivityWriter($this->userCabangId,'apotek.itemtype','Update ItemType -> Kode: '.$itemtype->TypeCode.' - '.$itemtype->TypeName,'-','Success');
                $this->persistence->SaveState("info", sprintf("Data ItemType: '%s' Dengan Kode: %s telah berhasil diubah.", $itemtype->TypeCode, $itemtype->TypeName));
                redirect_url("apotek.itemtype");
            } else {
                $log = $log->UserActivityWriter($this->userCabangId,'apotek.itemtype','Update ItemType -> Kode: '.$itemtype->TypeCode.' - '.$itemtype->TypeName,'-','Failed');
                if ($this->connector->GetHasError()) {
                    if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
                        $this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $itemtype->TypeCode));
                    } else {
                        $this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
                    }
                }
            }
		} else {
			if ($id == null) {
				$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan edit data !");
				redirect_url("apotek.itemtype");
			}
			$itemtype = $itemtype->FindById($id);
			if ($itemtype == null) {
				$this->persistence->SaveState("error", "Data ItemType yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
				redirect_url("apotek.itemtype");
			}
		}

		// untuk kirim variable ke view
		$this->Set("itemtype", $itemtype);
	}

	private function DoUpdate(ItemType $itemtype) {
        if ($itemtype->TypeCode == "") {
            $this->Set("error", "Kode Jenis masih kosong");
            return false;
        }
        if ($itemtype->TypeName == "") {
            $this->Set("error", "Jenis Barang masih kosong");
            return false;
        }

		if ($itemtype->Update($itemtype->Id) == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function delete($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Anda harus memilih data itemtype sebelum melakukan hapus data !");
			redirect_url("apotek.itemtype");
		}
		$log = new UserAdmin();
		$itemtype = new ItemType();
		$itemtype = $itemtype->FindById($id);
		if ($itemtype == null) {
			$this->persistence->SaveState("error", "Data itemtype yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
			redirect_url("apotek.itemtype");
		}

		if ($itemtype->Delete($itemtype->Id) == 1) {
			$log = $log->UserActivityWriter($this->userCabangId,'apotek.itemtype','Delete ItemType -> Kode: '.$itemtype->TypeCode.' - '.$itemtype->TypeName,'-','Success');
			$this->persistence->SaveState("info", sprintf("Data ItemType: '%s' Dengan Kode: %s telah berhasil dihapus.", $itemtype->TypeName, $itemtype->TypeCode));
			redirect_url("apotek.itemtype");
		} else {
			$log = $log->UserActivityWriter($this->userCabangId,'apotek.itemtype','Delete ItemType -> Kode: '.$itemtype->TypeCode.' - '.$itemtype->TypeName,'-','Failed');
			$this->persistence->SaveState("error", sprintf("Gagal menghapus data itemtype: '%s'. Message: %s", $itemtype->TypeCode, $this->connector->GetErrorMessage()));
		}
		redirect_url("apotek.itemtype");
	}
}
