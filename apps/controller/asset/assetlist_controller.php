<?php
class AssetListController extends AppController {
	private $userCompanyId;
	private $userCabangId;
	private $userUid;

	protected function Initialize() {
		require_once(MODEL . "asset/assetlist.php");
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
        $settings["columns"][] = array("name" => "a.kd_asset", "display" => "Kode", "width" => 60);
        $settings["columns"][] = array("name" => "b.klp_asset", "display" => "Kelompok Asset", "width" => 130);
		$settings["columns"][] = array("name" => "a.nm_asset", "display" => "Nama Asset", "width" => 200);
        $settings["columns"][] = array("name" => "format(a.masa_manfaat,0)", "display" => "Masa Manfaat (Thn)", "width" => 100, "align" => "center");
        $settings["columns"][] = array("name" => "a.thn_perolehan", "display" => "Tahun Perolehan", "width" => 100, "align" => "center");
        $settings["columns"][] = array("name" => "format(a.nilai_perolehan,0)", "display" => "Nilai Awal/Perolehan", "width" => 100, "align" => "right");
        $settings["columns"][] = array("name" => "concat(format(a.depr_year,0),' %')", "display" => "Penyusutan (%/Thn)", "width" => 100, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.nilai_buku,0)", "display" => "Nilai Buku", "width" => 100, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.qty,0)", "display" => "QTY", "width" => 40, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.nilai_buku * a.qty,0)", "display" => "Jumlah Nilai Buku", "width" => 100, "align" => "right");
        //$settings["columns"][] = array("name" => "if(a.is_aktif = 1,'Aktif','Tidak')", "display" => "Aktif", "width" => 60);
        
        //filtering
		$settings["filters"][] = array("name" => "b.klp_asset", "display" => "Kelompok");
		$settings["filters"][] = array("name" => "a.kd_asset", "display" => "Kode");
        $settings["filters"][] = array("name" => "a.nm_asset", "display" => "Nama Asset");
        $settings["filters"][] = array("name" => "a.reff_no", "display" => "No. Reff");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Daftar Asset/Aktiva Tetap";

			if ($acl->CheckUserAccess("assetlist", "add", "asset")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "asset.assetlist/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("assetlist", "edit", "asset")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "asset.assetlist/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
											   "Error" => "Mohon memilih data terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu data",
											   "Info" => "Apakah anda yakin mau merubah data yang dipilih ?");
			}
            if ($acl->CheckUserAccess("assetlist", "view", "asset")) {
                $settings["actions"][] = array("Text" => "View", "Url" => "asset.assetlist/view/%s", "Class" => "bt_view", "ReqId" => 1,"Confirm" => "");
            }
			if ($acl->CheckUserAccess("assetlist", "delete", "asset")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "asset.assetlist/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
											   "Error" => "Mohon memilih data terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu data",
											   "Info" => "Apakah anda yakin mau menghapus data yang dipilih ?");
			}
            if ($acl->CheckUserAccess("assetlist", "view", "asset")) {
                $settings["actions"][] = array("Text" => "separator", "Url" => null);
                $settings["actions"][] = array("Text" => "Laporan Asset & Penyusutan", "Url" => "asset.assetlist/report", "Class" => "bt_report", "ReqId" => 0);
            }
            //sort and method
			$settings["def_filter"] = 0;
			$settings["def_order"] = 1;
			$settings["singleSelect"] = true;
		} else {
		    //database
			$settings["from"] = "m_asset as a Join m_klpasset as b On a.kd_klpasset = b.kd_klpasset";
			$settings["where"] = "a.is_deleted = 0 AND a.entity_id = " . $this->userCompanyId;
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

	public function add() {
        require_once(MODEL . "asset/klpasset.php");
        require_once(MODEL . "master/sbu.php");
		$loader = null;
		$asset = new AssetList();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$asset->EntityId = $this->userCompanyId;
			$asset->SbuId = $this->GetPostValue("SbuId");
			$asset->KdAsset = $this->GetPostValue("KdAsset");
            $asset->NmAsset = $this->GetPostValue("NmAsset");
			$asset->KdKlpAsset = $this->GetPostValue("KdKlpAsset");
            $asset->Keterangan = $this->GetPostValue("Keterangan");
            $asset->ThnPerolehan = $this->GetPostValue("ThnPerolehan");
            $asset->ReffNo = $this->GetPostValue("ReffNo");
            $asset->NilaiPerolehan = $this->GetPostValue("NilaiPerolehan");
            $asset->NilaiBuku = $this->GetPostValue("NilaiBuku");
            $asset->Qty = $this->GetPostValue("Qty");
            $asset->MasaManfaat = $this->GetPostValue("MasaManfaat");
            $asset->ApreYear = $this->GetPostValue("ApreYear");
            $asset->DeprYear = $this->GetPostValue("DeprYear");
            $asset->LastDepr = $this->GetPostValue("LastDepr");
            $asset->CreatebyId = $this->userUid;
			if ($this->DoInsert($asset)) {
				$log = $log->UserActivityWriter($this->userCabangId,'asset.assetlist','Add New Asset -> Kode: '.$asset->KdAsset.' - '.$asset->NmAsset,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data Asset: '%s' Dengan Kode: %s telah berhasil disimpan.", $asset->NmAsset, $asset->KdAsset));
				redirect_url("asset.assetlist");
			} else {
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $asset->EntityCd));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		}
		// untuk kirim variable ke view
        $loader = new Klpasset();
		$klpasset = $loader->LoadByEntityId($this->userCompanyId);
		$loader = new Sbu();
		$sbu = $loader->LoadAll();
        $this->Set("sbulist", $sbu);
        $this->Set("klpasset", $klpasset);
        $this->Set("asset", $asset);
	}

	private function DoInsert(AssetList $asset) {
		if ($asset->KdAsset == "") {
			$this->Set("error", "Kode belum diisi");
			return false;
		}
		if ($asset->NmAsset == "") {
			$this->Set("error", "Nama Asset belum diisi");
			return false;
		}
        if ($asset->KdKlpAsset == "") {
            $this->Set("error", "Kelompok Asset belum diisi");
            return false;
        }
        if ($asset->NilaiPerolehan == "" || $asset->NilaiPerolehan == 0) {
            $this->Set("error", "Nilai Perolehan Asset belum diisi");
            return false;
        }
        if ($asset->NilaiBuku == "" || $asset->NilaiBuku == 0) {
            $this->Set("error", "Nilai Buku Asset belum diisi");
            return false;
        }
        if ($asset->Qty == "" || $asset->Qty == 0) {
            $this->Set("error", "Qty Asset belum diisi");
            return false;
        }
        if ($asset->MasaManfaat == "" || $asset->MasaManfaat == 0) {
            $this->Set("error", "Masa Manfaat Asset belum diisi");
            return false;
        }
        if ($asset->DeprYear == "" || $asset->DeprYear == 0) {
            $this->Set("error", "Depresiasi Asset belum diisi");
            return false;
        }
		if ($asset->Insert() == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function edit($id = null) {
        require_once(MODEL . "asset/klpasset.php");
        require_once(MODEL . "master/sbu.php");
		$loader = null;
		$asset = new AssetList();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$asset->Id = $id;
			$asset->EntityId = $this->userCompanyId;
            $asset->SbuId = $this->GetPostValue("SbuId");
            $asset->KdAsset = $this->GetPostValue("KdAsset");
            $asset->NmAsset = $this->GetPostValue("NmAsset");
            $asset->Keterangan = $this->GetPostValue("Keterangan");
            $asset->KdKlpAsset = $this->GetPostValue("KdKlpAsset");
            $asset->ThnPerolehan = $this->GetPostValue("ThnPerolehan");
            $asset->ReffNo = $this->GetPostValue("ReffNo");
            $asset->NilaiPerolehan = $this->GetPostValue("NilaiPerolehan");
            $asset->NilaiBuku = $this->GetPostValue("NilaiBuku");
            $asset->Qty = $this->GetPostValue("Qty");
            $asset->MasaManfaat = $this->GetPostValue("MasaManfaat");
            $asset->ApreYear = $this->GetPostValue("ApreYear");
            $asset->DeprYear = $this->GetPostValue("DeprYear");
            $asset->LastDepr = $this->GetPostValue("LastDepr");
            $asset->UpdatebyId = $this->userUid;
			if ($this->DoUpdate($asset)) {
				$log = $log->UserActivityWriter($this->userCabangId,'asset.assetlist','Update Asset -> Kode: '.$asset->KdAsset.' - '.$asset->NmAsset,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data Asset: '%s' Dengan Kode: %s telah berhasil diupdate.", $asset->NmAsset, $asset->KdAsset));
				redirect_url("asset.assetlist");
			} else {
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $asset->EntityCd));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		} else {
			if ($id == null) {
				$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan edit data !");
				redirect_url("asset.assetlist");
			}
			$asset = $asset->FindById($id);
			if ($asset == null) {
				$this->persistence->SaveState("error", "Data Asset yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
				redirect_url("asset.assetlist");
			}
		}
        // untuk kirim variable ke view
        $loader = new Klpasset();
        $klpasset = $loader->LoadByEntityId($this->userCompanyId);
        $loader = new Sbu();
        $sbu = $loader->LoadAll();
        $this->Set("sbulist", $sbu);
        $this->Set("klpasset", $klpasset);
        $this->Set("asset", $asset);
	}

	private function DoUpdate(AssetList $asset) {
        if ($asset->KdAsset == "") {
            $this->Set("error", "Kode belum diisi");
            return false;
        }
        if ($asset->NmAsset == "") {
            $this->Set("error", "Nama Asset belum diisi");
            return false;
        }
        if ($asset->KdKlpAsset == "") {
            $this->Set("error", "Kelompok Asset belum diisi");
            return false;
        }
        if ($asset->NilaiPerolehan == "" || $asset->NilaiPerolehan == 0) {
            $this->Set("error", "Nilai Perolehan Asset belum diisi");
            return false;
        }
        if ($asset->NilaiBuku == "" || $asset->NilaiBuku == 0) {
            $this->Set("error", "Nilai Buku Asset belum diisi");
            return false;
        }
        if ($asset->Qty == "" || $asset->Qty == 0) {
            $this->Set("error", "Qty Asset belum diisi");
            return false;
        }
        if ($asset->MasaManfaat == "" || $asset->MasaManfaat == 0) {
            $this->Set("error", "Masa Manfaat Asset belum diisi");
            return false;
        }
        if ($asset->DeprYear == "" || $asset->DeprYear == 0) {
            $this->Set("error", "Depresiasi Asset belum diisi");
            return false;
        }
		if ($asset->Update($asset->Id) == 1) {
			return true;
		} else {
			return false;
		}
	}

    public function view($id = null) {
        require_once(MODEL . "asset/klpasset.php");
        require_once(MODEL . "master/sbu.php");
        $loader = null;
        $asset = new AssetList();
        $log = new UserAdmin();
        if ($id == null) {
            $this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan view data !");
            redirect_url("asset.assetlist");
        }
        $asset = $asset->FindById($id);
        if ($asset == null) {
            $this->persistence->SaveState("error", "Data Asset yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
            redirect_url("asset.assetlist");
        }
        // untuk kirim variable ke view
        $loader = new Klpasset();
        $klpasset = $loader->LoadByEntityId($this->userCompanyId);
        $loader = new Sbu();
        $sbu = $loader->LoadAll();
        $this->Set("sbulist", $sbu);
        $this->Set("klpasset", $klpasset);
        $this->Set("asset", $asset);
    }

	public function delete($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan hapus data !");
			redirect_url("asset.assetlist");
		}
		$log = new UserAdmin();
		$asset = new AssetList();
		$asset = $asset->FindById($id);
		if ($asset == null) {
			$this->persistence->SaveState("error", "Data yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
			redirect_url("asset.assetlist");
		}

		if ($asset->Delete($asset->Id) == 1) {
			$log = $log->UserActivityWriter($this->userCabangId,'asset.assetlist','Delete Asset -> Kode: '.$asset->KdAsset.' - '.$asset->NmAsset,'-','Success');
			$this->persistence->SaveState("info", sprintf("Data Asset: '%s' Dengan Kode: %s telah berhasil dihapus.", $asset->NmAsset, $asset->KdAsset));
			redirect_url("asset.assetlist");
		} else {
			$this->persistence->SaveState("error", sprintf("Gagal menghapus data Asset: '%s'. Message: %s", $asset->NmAsset, $this->connector->GetErrorMessage()));
		}
		redirect_url("asset.assetlist");
	}

    public function AutoKode(){
        $kode = new AssetList();
        $kode = $kode->GetAutoKode($this->userCompanyId);
        print $kode;
    }

    public function report(){
        require_once (MODEL . "asset/klpasset.php");
        $loader = null;
        $periode = 0;
        if (count($this->postData) > 0) {
            $sbi = 0;
            $kasset = $this->GetPostValue("KdKlpAsset");
            $tahun = $this->GetPostValue("Tahun");
            $bulan = $this->GetPostValue("Bulan");
            $periode = (int) $tahun.$bulan;
            $output = $this->GetPostValue("Output");
            $asset = new AssetList();
            $reports = $asset->Load4Report($this->userCompanyId,$sbi,$kasset);
        }else{
            $sbi = 0;
            $kasset = '0';
            $tahun = date('Y');
            $bulan = date('m');
            $periode = date('Ym');
            $output = 0;
            $reports = null;
        }
        //load year range
        $years = range( date("Y") , 2018 );
        $this->Set("Years", $years);
        //load kelompok asset
        $loader = new KlpAsset();
        $klpasset = $loader->LoadByEntityId($this->userCompanyId);
        $this->Set("KlpAsset", $klpasset);
        $this->Set("Tahun", $tahun);
        $this->Set("Bulan", $bulan);
        $this->Set("Periode", $periode);
        $this->Set("Kasset", $kasset);
        $this->Set("Output", $output);
        $this->Set("Reports", $reports);
    }

}
