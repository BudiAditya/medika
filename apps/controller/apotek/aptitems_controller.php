<?php
class AptItemsController extends AppController {
	private $userCompanyId;
	private $userCabangId;
	private $userUid;

	protected function Initialize() {
		require_once(MODEL . "apotek/aptitems.php");
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
        $settings["columns"][] = array("name" => "a.item_code", "display" => "Kode", "width" => 60);
        $settings["columns"][] = array("name" => "a.item_name", "display" => "Nama Barang", "width" => 200);
		$settings["columns"][] = array("name" => "a.item_type_code", "display" => "Jenis", "width" => 100);
        $settings["columns"][] = array("name" => "a.item_group_code", "display" => "Golongan", "width" => 100);
        $settings["columns"][] = array("name" => "format(a.item_stock_qty,0)", "display" => "Stok", "width" => 50, "align" => "right");
        $settings["columns"][] = array("name" => "a.item_unit", "display" => "Satuan", "width" => 50);
        //$settings["columns"][] = array("name" => "format(a.purchase_price,0)", "display" => "Harga Beli", "width" => 80, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.sale_price1,0)", "display" => "Harga Satuan", "width" => 80, "align" => "right");
        $settings["columns"][] = array("name" => "b.nm_relasi", "display" => "Supplier", "width" => 150);
        $settings["columns"][] = array("name" => "if(a.is_bpjs = 0,'','BPJS')", "display" => "BPJS?", "width" => 50);
        $settings["columns"][] = array("name" => "if(a.is_obsolete = 0,'','Obsolet')", "display" => "Obsolet?", "width" => 50);
        $settings["columns"][] = array("name" => "if(a.is_aktif = 0,'Non-Aktif','Aktif')", "display" => "Status", "width" => 50);
        
        //filtering
		$settings["filters"][] = array("name" => "a.item_code", "display" => "Kode");
		$settings["filters"][] = array("name" => "a.item_name", "display" => "Nama Barang");
        $settings["filters"][] = array("name" => "a.item_type_code", "display" => "Jenis");
        $settings["filters"][] = array("name" => "a.item_group_code", "display" => "Golongan");
        $settings["filters"][] = array("name" => "b.contact_name", "display" => "Supplier");
        $settings["filters"][] = array("name" => "if(a.is_bpjs = 0,'-','BPJS')", "display" => "Obat Bpjs");
        $settings["filters"][] = array("name" => "if(a.is_aktif = 0,'Non-Aktif','Aktif')", "display" => "Status Obat");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "MASTER DATA BARANG";

			if ($acl->CheckUserAccess("aptitems", "add", "asset")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "apotek.aptitems/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("aptitems", "edit", "asset")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "apotek.aptitems/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
											   "Error" => "Mohon memilih data terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu data",
											   "Info" => "Apakah anda yakin mau merubah data yang dipilih ?");
			}
            if ($acl->CheckUserAccess("aptitems", "view", "asset")) {
                $settings["actions"][] = array("Text" => "View", "Url" => "apotek.aptitems/view/%s", "Class" => "bt_view", "ReqId" => 1,"Confirm" => "");
            }
			if ($acl->CheckUserAccess("aptitems", "delete", "asset")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "apotek.aptitems/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
											   "Error" => "Mohon memilih data terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu data",
											   "Info" => "Apakah anda yakin mau menghapus data yang dipilih ?");
			}
            if ($acl->CheckUserAccess("aptitems", "view", "asset")) {
                $settings["actions"][] = array("Text" => "separator", "Url" => null);
                $settings["actions"][] = array("Text" => "Laporan Barang & Obat", "Url" => "apotek.aptitems/report", "Class" => "bt_report", "ReqId" => 0);
            }
            //sort and method
			$settings["def_filter"] = 0;
			$settings["def_order"] = 1;
			$settings["singleSelect"] = true;
		} else {
		    //database
			$settings["from"] = "m_apt_items AS a Left Join m_relasi AS b On a.supplier_code = b.kd_relasi";
			$settings["where"] = "a.is_deleted = 0 AND a.entity_id = " . $this->userCompanyId;
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

    public function add() {
	    require_once (MODEL . "master/relasi.php");
        require_once (MODEL . "apotek/itemgroup.php");
        require_once (MODEL . "apotek/itemtype.php");
        require_once (MODEL . "apotek/itemunit.php");
        $loader = null;
        $items = new AptItems();
        $log = new UserAdmin();
        if (count($this->postData) > 0) {
            // OK user ada kirim data kita proses
            $items->EntityId = $this->userCompanyId;
            $items->SbuId = $this->userCabangId;
            $items->ItemCode = $this->GetPostValue("ItemCode");
            $items->ItemName = $this->GetPostValue("ItemName");
            $items->ItemDescs = $this->GetPostValue("ItemDescs");
            $items->ItemTypeCode = $this->GetPostValue("ItemTypeCode");
            $items->ItemGroupCode = $this->GetPostValue("ItemGroupCode");
            $items->SupplierCode = $this->GetPostValue("SupplierCode");
            $items->ItemBarCode = $this->GetPostValue("ItemBarCode");
            $items->ItemUnit = $this->GetPostValue("ItemUnit");
            $items->ItemStockQty = $this->GetPostValue("ItemStockQty");
            $items->MinStockQty = $this->GetPostValue("MinStockQty");
            //$items->StockInOrderQty = $this->GetPostValue("StockInOrderQty");
            //$items->ReorderQty = $this->GetPostValue("ReorderQty");
            $items->CogsValue = $this->GetPostValue("CogsValue");
            $items->PurchasePrice = $this->GetPostValue("PurchasePrice");
            $items->SalePrice1 = $this->GetPostValue("SalePrice1");
            //$items->SalePrice2 = $this->GetPostValue("SalePrice2");
            //$items->SalePrice3 = $this->GetPostValue("SalePrice3");
            //$items->SalePrice4 = $this->GetPostValue("SalePrice4");
            //$items->SalePrice5 = $this->GetPostValue("SalePrice5");
            //$items->SalePrice6 = $this->GetPostValue("SalePrice6");
            $items->SpMarkup1 = $this->GetPostValue("SpMarkup1");
            //$items->SpMarkup2 = $this->GetPostValue("SpMarkup2");
            //$items->SpMarkup3 = $this->GetPostValue("SpMarkup3");
            //$items->SpMarkup4 = $this->GetPostValue("SpMarkup4");
            //$items->SpMarkup5 = $this->GetPostValue("SpMarkup5");
            //$items->SpMarkup6 = $this->GetPostValue("SpMarkup6");
            //$items->TaxPct = $this->GetPostValue("TaxPct");
            //$items->CommisionPct = $this->GetPostValue("CommisionPct");
            //$items->DiscountPct = $this->GetPostValue("DiscountPct");
            $items->IsAktif = $this->GetPostValue("IsAktif");
            //$items->NeedPassword = $this->GetPostValue("NeedPassword");
            //$items->HideOnStruk = $this->GetPostValue("HideOnStruk");
            $items->AllowOutOfStock = $this->GetPostValue("AllowOutOfStock");
            //$items->LastPurchaseDate = $this->GetPostValue("LastPurchaseDate");
            $items->IsObsolete = $this->GetPostValue("IsObsolete");
            $items->IsBpjs = $this->GetPostValue("IsBpjs");
            $items->CreatebyId = $this->userUid;
            if ($this->DoInsert($items)) {
                $log = $log->UserActivityWriter($this->userCabangId,'apotek.aptitems','Add New Items -> Kode: '.$items->ItemCode.' - '.$items->ItemName,'-','Success');
                $this->persistence->SaveState("info", sprintf("Data Items: '%s' Dengan Kode: %s telah berhasil disimpan.", $items->ItemName, $items->ItemCode));
                redirect_url("apotek.aptitems");
            } else {
                if ($this->connector->GetHasError()) {
                    if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
                        $this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $items->ItemCode));
                    } else {
                        $this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
                    }
                }
            }
        }else{
            $kode = new AptItems();
            $kode = $kode->GetAutoItemCode($this->userCompanyId);
            $items->ItemCode = $kode;
            $items->ItemBarCode = $kode;
        }
        //load data relasi
        $loader = new Relasi();
        $relasi = $loader->LoadAll("a.nm_relasi");
        $this->Set("relasi", $relasi);
        //load data item type
        $loader = new ItemType();
        $itemtype = $loader->LoadAll();
        $this->Set("itemtype", $itemtype);
        //load data item group
        $loader = new ItemGroup();
        $itemgroup = $loader->LoadAll();
        $this->Set("itemgroup", $itemgroup);
        //load data item unit
        $loader = new ItemUnit();
        $itemunit = $loader->LoadAll();
        $this->Set("itemunit", $itemunit);
        // kirim variable ke view
        $this->Set("items", $items);
    }

    private function DoInsert(AptItems $items) {

        if ($items->ItemCode == "") {
            $this->Set("error", "Kode Barang masih kosong");
            return false;
        }

        if ($items->ItemName == "") {
            $this->Set("error", "Nama Barang masih kosong");
            return false;
        }
        if ($items->Insert() == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function edit($id = null) {
        require_once (MODEL . "master/relasi.php");
        require_once (MODEL . "apotek/itemgroup.php");
        require_once (MODEL . "apotek/itemtype.php");
        require_once (MODEL . "apotek/itemunit.php");
        $loader = null;
        $items = new AptItems();
        $log = new UserAdmin();
        if (count($this->postData) > 0) {
            // OK user ada kirim data kita proses
            $items->Id = $id;
            $items->EntityId = $this->userCompanyId;
            $items->SbuId = $this->userCabangId;
            $items->ItemCode = $this->GetPostValue("ItemCode");
            $items->ItemName = $this->GetPostValue("ItemName");
            $items->ItemDescs = $this->GetPostValue("ItemDescs");
            $items->ItemTypeCode = $this->GetPostValue("ItemTypeCode");
            $items->ItemGroupCode = $this->GetPostValue("ItemGroupCode");
            $items->SupplierCode = $this->GetPostValue("SupplierCode");
            $items->ItemBarCode = $this->GetPostValue("ItemBarCode");
            $items->ItemUnit = $this->GetPostValue("ItemUnit");
            $items->ItemStockQty = $this->GetPostValue("ItemStockQty");
            $items->MinStockQty = $this->GetPostValue("MinStockQty");
            //$items->StockInOrderQty = $this->GetPostValue("StockInOrderQty");
            //$items->ReorderQty = $this->GetPostValue("ReorderQty");
            $items->CogsValue = $this->GetPostValue("CogsValue");
            $items->PurchasePrice = $this->GetPostValue("PurchasePrice");
            $items->SalePrice1 = $this->GetPostValue("SalePrice1");
            //$items->SalePrice2 = $this->GetPostValue("SalePrice2");
            //$items->SalePrice3 = $this->GetPostValue("SalePrice3");
            //$items->SalePrice4 = $this->GetPostValue("SalePrice4");
            //$items->SalePrice5 = $this->GetPostValue("SalePrice5");
            //$items->SalePrice6 = $this->GetPostValue("SalePrice6");
            $items->SpMarkup1 = $this->GetPostValue("SpMarkup1");
            //$items->SpMarkup2 = $this->GetPostValue("SpMarkup2");
            //$items->SpMarkup3 = $this->GetPostValue("SpMarkup3");
            //$items->SpMarkup4 = $this->GetPostValue("SpMarkup4");
            //$items->SpMarkup5 = $this->GetPostValue("SpMarkup5");
            //$items->SpMarkup6 = $this->GetPostValue("SpMarkup6");
            //$items->TaxPct = $this->GetPostValue("TaxPct");
            //$items->CommisionPct = $this->GetPostValue("CommisionPct");
            //$items->DiscountPct = $this->GetPostValue("DiscountPct");
            $items->IsAktif = $this->GetPostValue("IsAktif");
            //$items->NeedPassword = $this->GetPostValue("NeedPassword");
            //$items->HideOnStruk = $this->GetPostValue("HideOnStruk");
            $items->AllowOutOfStock = $this->GetPostValue("AllowOutOfStock");
            //$items->LastPurchaseDate = $this->GetPostValue("LastPurchaseDate");
            $items->IsObsolete = $this->GetPostValue("IsObsolete");
            $items->IsBpjs = $this->GetPostValue("IsBpjs");
            $items->UpdatebyId = $this->userUid;
            if ($this->DoUpdate($items)) {
                $log = $log->UserActivityWriter($this->userCabangId,'apotek.aptitems','Update Items -> Kode: '.$items->ItemCode.' - '.$items->ItemName,'-','Success');
                $this->persistence->SaveState("info", sprintf("Data Items: '%s' Dengan Kode: %s telah berhasil diupdate.", $items->ItemName, $items->ItemCode));
                redirect_url("apotek.aptitems");
            } else {
                if ($this->connector->GetHasError()) {
                    if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
                        $this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $items->ItemCode));
                    } else {
                        $this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
                    }
                }
            }
        } else {
            if ($id == null) {
                $this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan edit data !");
                redirect_url("apotek.aptitems");
            }
            $items = $items->FindById($id);
            if ($items == null) {
                $this->persistence->SaveState("error", "Data Items yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
                redirect_url("apotek.aptitems");
            }
        }
        //load data relasi
        $loader = new Relasi();
        $relasi = $loader->LoadAll("a.nm_relasi");
        $this->Set("relasi", $relasi);
        //load data item type
        $loader = new ItemType();
        $itemtype = $loader->LoadAll();
        $this->Set("itemtype", $itemtype);
        //load data item group
        $loader = new ItemGroup();
        $itemgroup = $loader->LoadAll();
        $this->Set("itemgroup", $itemgroup);
        //load data item unit
        $loader = new ItemUnit();
        $itemunit = $loader->LoadAll();
        $this->Set("itemunit", $itemunit);
        // kirim variable ke view
        $this->Set("items", $items);
    }

    private function DoUpdate(AptItems $items) {
        if ($items->ItemCode == "") {
            $this->Set("error", "Kode Barang masih kosong");
            return false;
        }
        if ($items->ItemName == "") {
            $this->Set("error", "Nama Barang masih kosong");
            return false;
        }
        if ($items->Update($items->Id) == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function view($id = null) {
        require_once (MODEL . "master/relasi.php");
        require_once (MODEL . "apotek/itemgroup.php");
        require_once (MODEL . "apotek/itemtype.php");
        require_once (MODEL . "apotek/itemunit.php");
        $loader = null;
        $items = new AptItems();
        if ($id == null) {
            $this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan view data !");
            redirect_url("apotek.aptitems");
        }
        $items = $items->FindById($id);
        if ($items == null) {
            $this->persistence->SaveState("error", "Data Items yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
            redirect_url("apotek.aptitems");
        }
        //load data relasi
        $loader = new Relasi();
        $relasi = $loader->LoadAll("a.nm_relasi");
        $this->Set("relasi", $relasi);
        //load data item type
        $loader = new ItemType();
        $itemtype = $loader->LoadAll();
        $this->Set("itemtype", $itemtype);
        //load data item group
        $loader = new ItemGroup();
        $itemgroup = $loader->LoadAll();
        $this->Set("itemgroup", $itemgroup);
        //load data item unit
        $loader = new ItemUnit();
        $itemunit = $loader->LoadAll();
        $this->Set("itemunit", $itemunit);
        // kirim variable ke view
        $this->Set("items", $items);
    }

    public function delete($id = null) {
        if ($id == null) {
            $this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan hapus data !");
            redirect_url("apotek.aptitems");
        }
        $log = new UserAdmin();
        $items = new AptItems();
        $items = $items->FindById($id);
        if ($items == null) {
            $this->persistence->SaveState("error", "Data yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
            redirect_url("apotek.aptitems");
        }

        if ($items->Delete($items->Id) == 1) {
            $log = $log->UserActivityWriter($this->userCabangId,'apotek.aptitems','Delete Items -> Kode: '.$items->ItemCode.' - '.$items->ItemName,'-','Success');
            $this->persistence->SaveState("info", sprintf("Data Items: '%s' Dengan Kode: %s telah berhasil dihapus.", $items->ItemName, $items->ItemCode));
            redirect_url("apotek.aptitems");
        } else {
            $this->persistence->SaveState("error", sprintf("Gagal menghapus data poliklinik: '%s'. Message: %s", $items->ItemName, $this->connector->GetErrorMessage()));
        }
        redirect_url("apotek.aptitems");
    }

    public function GetAutoItemCode(){
	    $kode = new AptItems();
	    $kode = $kode->GetAutoItemCode($this->userCompanyId);
	    print ($kode);
    }

}
