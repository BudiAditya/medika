<?php
class PembelianController extends AppController {
	private $userCompanyId;
	private $userCabangId;
	private $userUid;

	protected function Initialize() {
		require_once(MODEL . "asset/pembelian.php");
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
        $settings["columns"][] = array("name" => "a.tgl_pembelian", "display" => "Tanggal", "width" => 60);
        $settings["columns"][] = array("name" => "a.no_bukti", "display" => "No.Bukti", "width" => 80);
        $settings["columns"][] = array("name" => "b.nm_relasi", "display" => "Supplier", "width" => 150);
        $settings["columns"][] = array("name" => "a.kd_asset", "display" => "Kode Asset", "width" => 60);
		$settings["columns"][] = array("name" => "a.nm_asset", "display" => "Nama Asset", "width" => 200);
        $settings["columns"][] = array("name" => "format(a.qty,0)", "display" => "QTY", "width" => 40, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.harga,0)", "display" => "Harga", "width" => 70, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.jumlah,0)", "display" => "Jumlah", "width" => 80, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.masa_manfaat,0)", "display" => "Masa Manfaat (Thn)", "width" => 100, "align" => "center");
        $settings["columns"][] = array("name" => "concat(format(a.depr_year,0),' %')", "display" => "Penyusutan (%/Thn)", "width" => 100, "align" => "right");
        $settings["columns"][] = array("name" => "if(a.sts_pembelian = 1,'Posted',if(a.sts_pembelian = 2,'Paid',if(a.sts_pembelian = 3,'Void','Draft')))", "display" => "Status", "width" => 60);
        
        //filtering
		$settings["filters"][] = array("name" => "a.kd_asset", "display" => "Kode");
        $settings["filters"][] = array("name" => "a.nm_asset", "display" => "Nama Asset");
        $settings["filters"][] = array("name" => "a.no_reff", "display" => "No. Reff");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Daftar Pembelian Asset";

			if ($acl->CheckUserAccess("pembelian", "add", "asset")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "asset.pembelian/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("pembelian", "edit", "asset")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "asset.pembelian/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
											   "Error" => "Mohon memilih data terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu data",
											   "Info" => "Apakah anda yakin mau merubah data yang dipilih ?");
			}
            if ($acl->CheckUserAccess("pembelian", "view", "asset")) {
                $settings["actions"][] = array("Text" => "View", "Url" => "asset.pembelian/view/%s", "Class" => "bt_view", "ReqId" => 1,"Confirm" => "");
            }
			if ($acl->CheckUserAccess("pembelian", "delete", "asset")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "asset.pembelian/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
											   "Error" => "Mohon memilih data terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu data",
											   "Info" => "Apakah anda yakin mau menghapus data yang dipilih ?");
			}
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("asset.pembelian", "approve")) {
                $settings["actions"][] = array("Text" => "Posting", "Url" => "asset.pembelian/posting/%s", "Class" => "bt_approve", "ReqId" => 1,
                    "Error" => "Mohon memilih Data Transaksi terlebih dahulu sebelum proses posting.",
                    "Confirm" => "Posting data transaksi yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
                $settings["actions"][] = array("Text" => "Unposting", "Url" => "asset.pembelian/unposting/%s", "Class" => "bt_reject", "ReqId" => 1,
                    "Error" => "Mohon memilih Data Transaksi terlebih dahulu sebelum proses pembatalan.",
                    "Confirm" => "Batalkan posting data transaksi yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
            }
            if ($acl->CheckUserAccess("pembelian", "view", "asset")) {
                $settings["actions"][] = array("Text" => "separator", "Url" => null);
                $settings["actions"][] = array("Text" => "Laporan Pembelian Asset", "Url" => "asset.pembelian/report", "Class" => "bt_report", "ReqId" => 0);
            }
            //sort and method
			$settings["def_filter"] = 0;
			$settings["def_order"] = 1;
			$settings["singleSelect"] = true;
		} else {
		    //database
			$settings["from"] = "t_pembelian as a Left Join m_relasi as b On a.kd_relasi = b.kd_relasi";
			$settings["where"] = "a.is_deleted = 0 AND a.entity_id = " . $this->userCompanyId;
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

	public function add() {
        require_once(MODEL . "asset/klpasset.php");
        require_once(MODEL . "master/sbu.php");
        require_once(MODEL . "master/relasi.php");
        require_once(MODEL . "accounting/jurnal.php");
        require_once(MODEL . "master/bank.php");
		$loader = null;
		$pembelian = new Pembelian();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$pembelian->EntityId = $this->userCompanyId;
			$pembelian->SbuId = $this->GetPostValue("SbuId");
            $pembelian->TglPembelian = strtotime($this->GetPostValue("TglPembelian"));
            $pembelian->JnsPembelian = $this->GetPostValue("JnsPembelian");
            $pembelian->BankId = $this->GetPostValue("BankId");
            $pembelian->KdKlpAsset = $this->GetPostValue("KdKlpAsset");
			$pembelian->KdAsset = $this->GetPostValue("KdAsset");
            $pembelian->NmAsset = $this->GetPostValue("NmAsset");
            $pembelian->KdRelasi = $this->GetPostValue("KdRelasi");
            $pembelian->NoReff = $this->GetPostValue("NoReff");
            $pembelian->Qty = $this->GetPostValue("Qty");
            $pembelian->Harga = $this->GetPostValue("Harga");
            $pembelian->Jumlah = $this->GetPostValue("Jumlah");
            $pembelian->MasaManfaat = $this->GetPostValue("MasaManfaat");
            $pembelian->ApreYear = $this->GetPostValue("ApreYear");
            $pembelian->DeprYear = $this->GetPostValue("DeprYear");
            $pembelian->StsPembelian = 0;
            $pembelian->CreatebyId = $this->userUid;
			if ($this->IsValid($pembelian)) {
                $jurnal = new Jurnal();
                if ($pembelian->JnsPembelian == 1) {
                    $jurnal->KdVoucher = 'BKK';
                } else {
                    $jurnal->KdVoucher = 'BPK';
                }
                $jurnal->EntityId = $this->userCompanyId;
                $jurnal->TglVoucher = date('Y-m-d', $pembelian->TglPembelian);
                $pembelian->NoBukti = $jurnal->GetJurnalDocNo($jurnal->KdVoucher);
                if ($pembelian->Insert() == 1) {
                    $log = $log->UserActivityWriter($this->userCabangId, 'asset.pembelian', 'Add New Pembelian Asset -> No: ' . $pembelian->NoBukti . ' - ' . $pembelian->NmAsset, '-', 'Success');
                    $this->persistence->SaveState("info", sprintf("Data Pembelian Asset No: %s telah berhasil disimpan.", $pembelian->NoBukti));
                    redirect_url("asset.pembelian");
                } else {
                    if ($this->connector->GetHasError()) {
                        if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
                            $this->Set("error", sprintf("No.Bukti: '%s' telah ada pada database !", $pembelian->NoBukti));
                        } else {
                            $this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
                        }
                    }
                }
            }
		}
		// untuk kirim variable ke view
        $loader = new Klpasset();
		$klpasset = $loader->LoadByEntityId($this->userCompanyId);
        $this->Set("klpasset", $klpasset);
		$loader = new Sbu();
		$sbu = $loader->LoadAll();
        $this->Set("sbulist", $sbu);
        $loader = new Relasi();
        $relasi = $loader->LoadByEntityId($this->userCompanyId);
        $this->Set("relasilist", $relasi);
        $loader = new Bank();
        $banks = $loader->LoadByEntityId($this->userCompanyId);
        $this->Set("banks", $banks);
        $this->Set("pembelian", $pembelian);
	}

	private function IsValid(Pembelian $pembelian) {
		if ($pembelian->JnsPembelian == "" || $pembelian->JnsPembelian == 0) {
			$this->Set("error", "Jenis Pembelian belum dipilih!");
			return false;
		}
        if ($pembelian->JnsPembelian == 1 && ($pembelian->BankId == "" || $pembelian->BankId == "0" || $pembelian->BankId == null)) {
            $this->Set("error", "Kas/Bank belum dipilih!");
            return false;
        }
        if ($pembelian->JnsPembelian <> 1){
		    $pembelian->BankId = "";
        }
        if ($pembelian->KdAsset == "") {
            $this->Set("error", "Kode belum diisi");
            return false;
        }
		if ($pembelian->NmAsset == "") {
			$this->Set("error", "Nama Asset belum diisi");
			return false;
		}
        if ($pembelian->KdKlpAsset == "") {
            $this->Set("error", "Kelompok Asset belum diisi");
            return false;
        }
        if ($pembelian->Qty == "" || $pembelian->Qty == 0) {
            $this->Set("error", "Quantity Asset belum diisi");
            return false;
        }
        if ($pembelian->Harga == "" || $pembelian->Harga == 0) {
            $this->Set("error", "Harga Satuan Asset belum diisi");
            return false;
        }
        if ($pembelian->Jumlah == "" || $pembelian->Jumlah == 0) {
            $pembelian->Jumlah = $pembelian->Qty * $pembelian->Harga;
        }
        if ($pembelian->MasaManfaat == "" || $pembelian->MasaManfaat == 0) {
            $this->Set("error", "Masa Manfaat Asset belum diisi");
            return false;
        }
        if ($pembelian->DeprYear == "" || $pembelian->DeprYear == 0) {
            $this->Set("error", "Depresiasi Asset belum diisi");
            return false;
        }
		return true;
	}

	public function edit($id = null) {
        require_once(MODEL . "asset/klpasset.php");
        require_once(MODEL . "master/sbu.php");
        require_once(MODEL . "master/relasi.php");
        require_once(MODEL . "master/bank.php");
        $loader = null;
        $pembelian = new Pembelian();
        $log = new UserAdmin();
        if (count($this->postData) > 0) {
            // OK user ada kirim data kita proses
            $pembelian->EntityId = $this->userCompanyId;
            $pembelian->SbuId = $this->GetPostValue("SbuId");
            $pembelian->NoBukti = $this->GetPostValue("NoBukti");
            $pembelian->TglPembelian = strtotime($this->GetPostValue("TglPembelian"));
            $pembelian->JnsPembelian = $this->GetPostValue("JnsPembelian");
            $pembelian->BankId = $this->GetPostValue("BankId");
            $pembelian->KdKlpAsset = $this->GetPostValue("KdKlpAsset");
            $pembelian->KdAsset = $this->GetPostValue("KdAsset");
            $pembelian->NmAsset = $this->GetPostValue("NmAsset");
            $pembelian->KdRelasi = $this->GetPostValue("KdRelasi");
            $pembelian->NoReff = $this->GetPostValue("NoReff");
            $pembelian->Qty = $this->GetPostValue("Qty");
            $pembelian->Harga = $this->GetPostValue("Harga");
            $pembelian->Jumlah = $this->GetPostValue("Jumlah");
            $pembelian->MasaManfaat = $this->GetPostValue("MasaManfaat");
            $pembelian->ApreYear = $this->GetPostValue("ApreYear");
            $pembelian->DeprYear = $this->GetPostValue("DeprYear");
            $pembelian->StsPembelian = 0;
            $pembelian->CreatebyId = $this->userUid;
            if ($this->IsValid($pembelian)) {
                if ($pembelian->Update($id)) {
                    $log = $log->UserActivityWriter($this->userCabangId, 'asset.pembelian', 'Update Pembelian Asset -> No: ' . $pembelian->NoBukti . ' - ' . $pembelian->NmAsset, '-', 'Success');
                    $this->persistence->SaveState("info", sprintf("Data Pembelian Asset No: %s telah berhasil disimpan.", $pembelian->NoBukti));
                    redirect_url("asset.pembelian");
                } else {
                    if ($this->connector->GetHasError()) {
                        if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
                            $this->Set("error", sprintf("No.Bukti: '%s' telah ada pada database !", $pembelian->NoBukti));
                        } else {
                            $this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
                        }
                    }
                }
            }
        } else {
			if ($id == null) {
				$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan edit data !");
				redirect_url("asset.pembelian");
			}
			$pembelian = $pembelian->FindById($id);
			if ($pembelian == null) {
				$this->persistence->SaveState("error", "Data Pembelian yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
				redirect_url("asset.pembelian");
			}
            if ($pembelian->StsPembelian > 0) {
                $this->persistence->SaveState("error", "Data Pembelian tidak berstatus -Draft- (Tidak boleh di-ubah)");
                redirect_url("asset.pembelian");
            }
		}
        // untuk kirim variable ke view
        $loader = new Klpasset();
        $klpasset = $loader->LoadByEntityId($this->userCompanyId);
        $this->Set("klpasset", $klpasset);
        $loader = new Sbu();
        $sbu = $loader->LoadAll();
        $this->Set("sbulist", $sbu);
        $loader = new Relasi();
        $relasi = $loader->LoadByEntityId($this->userCompanyId);
        $this->Set("relasilist", $relasi);
        $loader = new Bank();
        $banks = $loader->LoadByEntityId($this->userCompanyId);
        $this->Set("banks", $banks);
        $this->Set("pembelian", $pembelian);
	}

    public function view($id = null) {
        require_once(MODEL . "asset/klpasset.php");
        require_once(MODEL . "master/sbu.php");
        require_once(MODEL . "master/relasi.php");
        require_once(MODEL . "master/bank.php");
        $loader = null;
        $pembelian = new Pembelian();
        $log = new UserAdmin();
        if ($id == null) {
            $this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan edit data !");
            redirect_url("asset.pembelian");
        }
        $pembelian = $pembelian->FindById($id);
        if ($pembelian == null) {
            $this->persistence->SaveState("error", "Data Asset yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
            redirect_url("asset.pembelian");
        }
        // untuk kirim variable ke view
        $loader = new Klpasset();
        $klpasset = $loader->LoadByEntityId($this->userCompanyId);
        $this->Set("klpasset", $klpasset);
        $loader = new Sbu();
        $sbu = $loader->LoadAll();
        $this->Set("sbulist", $sbu);
        $loader = new Relasi();
        $relasi = $loader->LoadByEntityId($this->userCompanyId);
        $this->Set("relasilist", $relasi);
        $loader = new Bank();
        $banks = $loader->LoadByEntityId($this->userCompanyId);
        $this->Set("banks", $banks);
        $this->Set("pembelian", $pembelian);
    }

	public function delete($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan hapus data !");
			redirect_url("asset.pembelian");
		}
		$log = new UserAdmin();
		$pembelian = new Pembelian();
		$pembelian = $pembelian->FindById($id);
		if ($pembelian == null) {
			$this->persistence->SaveState("error", "Data yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
			redirect_url("asset.pembelian");
		}
        if ($pembelian->StsPembelian > 0) {
            $this->persistence->SaveState("error", "Data Pembelian tidak berstatus -Draft- (Tidak boleh di-hapus)");
            redirect_url("asset.pembelian");
        }
		if ($pembelian->Delete($pembelian->Id) == 1) {
            $log = $log->UserActivityWriter($this->userCabangId, 'asset.pembelian', 'Delete Pembelian Asset -> No: ' . $pembelian->NoBukti . ' - ' . $pembelian->NmAsset, '-', 'Success');
            $this->persistence->SaveState("info", sprintf("Data Pembelian Asset No: %s telah berhasil dihapus.", $pembelian->NoBukti));
            redirect_url("asset.pembelian");
		} else {
			$this->persistence->SaveState("error", sprintf("Gagal menghapus data Pembelian Asset No: %s. Message: %s", $pembelian->NoBukti, $this->connector->GetErrorMessage()));
		}
		redirect_url("asset.pembelian");
	}

    public function posting($id = null) {
        if ($id == null) {
            $this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan posting data !");
            redirect_url("asset.pembelian");
        }
        $log = new UserAdmin();
        $pembelian = new Pembelian();
        $pembelian = $pembelian->FindById($id);
        if ($pembelian == null) {
            $this->persistence->SaveState("error", "Data yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
            redirect_url("asset.pembelian");
        }
        if ($pembelian->StsPembelian > 0) {
            $this->persistence->SaveState("error", "Data Pembelian tidak berstatus -Draft- (Tidak boleh di-posting)");
            redirect_url("asset.pembelian");
        }
        if ($pembelian->Posting($pembelian->NoBukti,$this->userUid) == 1) {
            $log = $log->UserActivityWriter($this->userCabangId, 'asset.pembelian', 'Posting Pembelian Asset -> No: ' . $pembelian->NoBukti . ' - ' . $pembelian->NmAsset, '-', 'Success');
            $this->persistence->SaveState("info", sprintf("Data Pembelian Asset No: %s telah berhasil diposting", $pembelian->NoBukti));
            redirect_url("asset.pembelian");
        } else {
            $this->persistence->SaveState("error", sprintf("Gagal posting data Pembelian Asset No: %s. Message: %s", $pembelian->NoBukti, $this->connector->GetErrorMessage()));
        }
        redirect_url("asset.pembelian");
    }

    public function unposting($id = null) {
        if ($id == null) {
            $this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan posting data !");
            redirect_url("asset.pembelian");
        }
        $log = new UserAdmin();
        $pembelian = new Pembelian();
        $pembelian = $pembelian->FindById($id);
        if ($pembelian == null) {
            $this->persistence->SaveState("error", "Data yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
            redirect_url("asset.pembelian");
        }
        if ($pembelian->StsPembelian <> 1) {
            $this->persistence->SaveState("error", "Data Pembelian tidak berstatus -Posted- (Tidak boleh di-unposting)");
            redirect_url("asset.pembelian");
        }
        if ($pembelian->Unposting($pembelian->NoBukti,$this->userUid) == 1) {
            $log = $log->UserActivityWriter($this->userCabangId, 'asset.pembelian', 'Posting Pembelian Asset -> No: ' . $pembelian->NoBukti . ' - ' . $pembelian->NmAsset, '-', 'Success');
            $this->persistence->SaveState("info", sprintf("Data Pembelian Asset No: %s telah berhasil diposting", $pembelian->NoBukti));
            redirect_url("asset.pembelian");
        } else {
            $this->persistence->SaveState("error", sprintf("Gagal posting data Pembelian Asset No: %s. Message: %s", $pembelian->NoBukti, $this->connector->GetErrorMessage()));
        }
        redirect_url("asset.pembelian");
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
            $pembelian = new Pembelian();
            $reports = $pembelian->Load4Report($this->userCompanyId,$sbi,$kasset);
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
