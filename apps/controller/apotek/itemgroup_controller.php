<?php
class ItemGroupController extends AppController {
    private $userCompanyId;
    private $userCabangId;
    private $userUid;

    protected function Initialize() {
        require_once(MODEL . "apotek/itemgroup.php");
        require_once(MODEL . "master/user_admin.php");
        $this->userCompanyId = $this->persistence->LoadState("entity_id");
        $this->userCabangId = $this->persistence->LoadState("cabang_id");
        $this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
    }

    public function index() {
        $router = Router::GetInstance();
        $settings = array();

        $settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 40);
        $settings["columns"][] = array("name" => "a.group_code", "display" => "Kode", "width" => 100);
        $settings["columns"][] = array("name" => "a.group_name", "display" => "Golongan Barang/Obat", "width" => 350);
        $settings["columns"][] = array("name" => "a.group_descs", "display" => "Keterangan", "width" => 450);

        $settings["filters"][] = array("name" => "a.group_code", "display" => "Kode");
        $settings["filters"][] = array("name" => "a.group_name", "display" => "Golongan");
        $settings["filters"][] = array("name" => "a.group_descs", "display" => "Keterangan");

        if (!$router->IsAjaxRequest) {
            $acl = AclManager::GetInstance();
            $settings["title"] = "Daftar Golongan Barang/Obat";

            if ($acl->CheckUserAccess("itemgroup", "add", "apotek")) {
                $settings["actions"][] = array("Text" => "Add", "Url" => "apotek.itemgroup/add", "Class" => "bt_add", "ReqId" => 0);
            }
            if ($acl->CheckUserAccess("itemgroup", "edit", "apotek")) {
                $settings["actions"][] = array("Text" => "Edit", "Url" => "apotek.itemgroup/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
                    "Error" => "Mohon memilih data itemgroup terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu itemgroup",
                    "Info" => "Apakah anda yakin mau merubah data itemgroup yang dipilih ?");
            }
            if ($acl->CheckUserAccess("itemgroup", "delete", "apotek")) {
                $settings["actions"][] = array("Text" => "Delete", "Url" => "apotek.itemgroup/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
                    "Error" => "Mohon memilih data itemgroup terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu itemgroup",
                    "Info" => "Apakah anda yakin mau menghapus data itemgroup yang dipilih ?");
            }

            $settings["def_filter"] = 0;
            $settings["def_order"] = 2;
            $settings["singleSelect"] = true;
        } else {
            $settings["from"] = "m_apt_item_group AS a";
            $settings["where"] = "a.is_deleted = 0";
        }

        $dispatcher = Dispatcher::CreateInstance();
        $dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
    }

    public function add() {
        $itemgroup = new ItemGroup();
        $log = new UserAdmin();
        if (count($this->postData) > 0) {
            // OK user ada kirim data kita proses
            $itemgroup->GroupCode = $this->GetPostValue("GroupCode");
            $itemgroup->GroupName = $this->GetPostValue("GroupName");
            $itemgroup->GroupDescs = $this->GetPostValue("GroupDescs");
            $itemgroup->CreatebyId = $this->userUid;
            if ($this->DoInsert($itemgroup)) {
                $log = $log->UserActivityWriter($this->userCabangId,'apotek.itemgroup','Add New ItemGroup -> Kode: '.$itemgroup->GroupCode.' - '.$itemgroup->GroupName,'-','Success');
                $this->persistence->SaveState("info", sprintf("Data ItemGroup: '%s' Dengan Kode: %s telah berhasil disimpan.", $itemgroup->GroupCode, $itemgroup->GroupName));
                redirect_url("apotek.itemgroup");
            } else {
                $log = $log->UserActivityWriter($this->userCabangId,'apotek.itemgroup','Add New ItemGroup -> Kode: '.$itemgroup->GroupCode.' - '.$itemgroup->GroupName,'-','Failed');
                if ($this->connector->GetHasError()) {
                    if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
                        $this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $itemgroup->GroupCode));
                    } else {
                        $this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
                    }
                }
            }
        }
        // untuk kirim variable ke view
        $this->Set("itemgroup", $itemgroup);
    }

    private function DoInsert(ItemGroup $itemgroup) {

        if ($itemgroup->GroupCode == "") {
            $this->Set("error", "Kode Golongan masih kosong");
            return false;
        }
        if ($itemgroup->GroupName == "") {
            $this->Set("error", "Golongan Barang masih kosong");
            return false;
        }

        if ($itemgroup->Insert() == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function edit($id = null) {
        $itemgroup = new ItemGroup();
        $log = new UserAdmin();
        if (count($this->postData) > 0) {
            // OK user ada kirim data kita proses
            $itemgroup->Id = $id;
            $itemgroup->GroupCode = $this->GetPostValue("GroupCode");
            $itemgroup->GroupName = $this->GetPostValue("GroupName");
            $itemgroup->GroupDescs = $this->GetPostValue("GroupDescs");
            $itemgroup->UpdatebyId = $this->userUid;
            if ($this->DoUpdate($itemgroup)) {
                $log = $log->UserActivityWriter($this->userCabangId,'apotek.itemgroup','Update ItemGroup -> Kode: '.$itemgroup->GroupCode.' - '.$itemgroup->GroupName,'-','Success');
                $this->persistence->SaveState("info", sprintf("Data ItemGroup: '%s' Dengan Kode: %s telah berhasil diubah.", $itemgroup->GroupCode, $itemgroup->GroupName));
                redirect_url("apotek.itemgroup");
            } else {
                $log = $log->UserActivityWriter($this->userCabangId,'apotek.itemgroup','Update ItemGroup -> Kode: '.$itemgroup->GroupCode.' - '.$itemgroup->GroupName,'-','Failed');
                if ($this->connector->GetHasError()) {
                    if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
                        $this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $itemgroup->GroupCode));
                    } else {
                        $this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
                    }
                }
            }
        } else {
            if ($id == null) {
                $this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan edit data !");
                redirect_url("apotek.itemgroup");
            }
            $itemgroup = $itemgroup->FindById($id);
            if ($itemgroup == null) {
                $this->persistence->SaveState("error", "Data ItemGroup yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
                redirect_url("apotek.itemgroup");
            }
        }

        // untuk kirim variable ke view
        $this->Set("itemgroup", $itemgroup);
    }

    private function DoUpdate(ItemGroup $itemgroup) {
        if ($itemgroup->GroupCode == "") {
            $this->Set("error", "Kode Golongan masih kosong");
            return false;
        }
        if ($itemgroup->GroupName == "") {
            $this->Set("error", "Golongan Barang masih kosong");
            return false;
        }

        if ($itemgroup->Update($itemgroup->Id) == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function delete($id = null) {
        if ($id == null) {
            $this->persistence->SaveState("error", "Anda harus memilih data itemgroup sebelum melakukan hapus data !");
            redirect_url("apotek.itemgroup");
        }
        $log = new UserAdmin();
        $itemgroup = new ItemGroup();
        $itemgroup = $itemgroup->FindById($id);
        if ($itemgroup == null) {
            $this->persistence->SaveState("error", "Data itemgroup yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
            redirect_url("apotek.itemgroup");
        }

        if ($itemgroup->Delete($itemgroup->Id) == 1) {
            $log = $log->UserActivityWriter($this->userCabangId,'apotek.itemgroup','Delete ItemGroup -> Kode: '.$itemgroup->GroupCode.' - '.$itemgroup->GroupName,'-','Success');
            $this->persistence->SaveState("info", sprintf("Data ItemGroup: '%s' Dengan Kode: %s telah berhasil dihapus.", $itemgroup->GroupName, $itemgroup->GroupCode));
            redirect_url("apotek.itemgroup");
        } else {
            $log = $log->UserActivityWriter($this->userCabangId,'apotek.itemgroup','Delete ItemGroup -> Kode: '.$itemgroup->GroupCode.' - '.$itemgroup->GroupName,'-','Failed');
            $this->persistence->SaveState("error", sprintf("Gagal menghapus data itemgroup: '%s'. Message: %s", $itemgroup->GroupCode, $this->connector->GetErrorMessage()));
        }
        redirect_url("apotek.itemgroup");
    }
}
