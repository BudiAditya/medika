<?php
class TindakanController extends AppController {
	private $userCompanyId;
	private $userCabangId;
	private $userUid;

	protected function Initialize() {
		require_once(MODEL . "pelayanan/perawatan.php");
        require_once(MODEL . "pelayanan/tindakan.php");
		require_once(MODEL . "master/user_admin.php");
		$this->userCompanyId = $this->persistence->LoadState("entity_id");
		$this->userCabangId = $this->persistence->LoadState("cabang_id");
        $this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();
		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 40);
		$settings["columns"][] = array("name" => "a.no_reg", "display" => "No. Register", "width" => 70);
        $settings["columns"][] = array("name" => "b.nm_poliklinik", "display" => "Poliklinik", "width" => 70);
        $settings["columns"][] = array("name" => "concat(b.tgl_masuk,' ',b.jam_masuk)", "display" => "Tgl & Jam Masuk", "width" => 90);
        $settings["columns"][] = array("name" => "b.nm_pasien", "display" => "Nama Pasien", "width" => 150);
        $settings["columns"][] = array("name" => "b.jkelamin", "display" => "L/P", "width" => 12);
        $settings["columns"][] = array("name" => "b.umur_pasien", "display" => "Umur", "width" => 70);
        $settings["columns"][] = array("name" => "if(b.cara_bayar = 1,'Umum',if(b.cara_bayar = 2,'BPJS',if(b.cara_bayar = 3,'Asuransi','N/A')))", "display" => "Cara Bayar", "width" => 60);
        $settings["columns"][] = array("name" => "a.kd_jasa", "display" => "Kode", "width" => 50);
        $settings["columns"][] = array("name" => "a.nm_jasa", "display" => "Nama Layanan", "width" => 150);
        $settings["columns"][] = array("name" => "a.qty", "display" => "QTY", "width" => 30, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.tarif_harga,0)", "display" => "Tarif", "width" => 60, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.tarif_harga * a.qty,0)", "display" => "Jumlah", "width" => 60, "align" => "right");
        $settings["columns"][] = array("name" => "a.keterangan", "display" => "Keterangan", "width" => 150);
        $settings["columns"][] = array("name" => "if(a.trx_status = 0,'Draft',if(a.trx_status = 1,'Posted','Void'))", "display" => "Status", "width" => 50);


		$settings["filters"][] = array("name" => "a.no_reg", "display" => "No.Registrasi");
		$settings["filters"][] = array("name" => "b.no_rm", "display" => "No.Rekam Medik");
        $settings["filters"][] = array("name" => "b.nm_pasien", "display" => "Nama Pasien");
        $settings["filters"][] = array("name" => "b.nm_poliklinik", "display" => "Poliklinik");
        $settings["filters"][] = array("name" => "b.nm_dokter", "display" => "Nama Dokter");
        $settings["filters"][] = array("name" => "a.kode", "display" => "Kode Layanan");
        $settings["filters"][] = array("name" => "a.nm_jasa", "display" => "Nama Layanan");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Data Layanan/Tindakan Pasien Dirawat";
            //button action
			if ($acl->CheckUserAccess("tindakan", "add", "pelayanan")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "pelayanan.tindakan/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("tindakan", "edit", "pelayanan")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "pelayanan.tindakan/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
											   "Error" => "Mohon memilih data layanan terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu data",
											   "Info" => "Apakah anda yakin mau merubah data layanan yang dipilih ?");
			}
            if ($acl->CheckUserAccess("tindakan", "view", "pelayanan")) {
                $settings["actions"][] = array("Text" => "View", "Url" => "pelayanan.tindakan/view/%s", "Class" => "bt_view", "ReqId" => 1,
                    "Error" => "Mohon memilih data layanan terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu data");
            }
			if ($acl->CheckUserAccess("tindakan", "delete", "pelayanan")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "pelayanan.tindakan/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
											   "Error" => "Mohon memilih data layanan terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu data",
											   "Info" => "Apakah anda yakin mau menghapus data layanan yang dipilih ?");
			}

			$settings["def_filter"] = 0;
			$settings["def_order"] = 1;
			$settings["singleSelect"] = true;
		} else {
			$settings["from"] = "t_tindakan AS a Join vw_t_perawatan AS b On a.no_reg = b.no_reg";
            //if ($_GET["query"] == "") {
            //    $_GET["query"] = null;
            //    $settings["where"] = "a.reg_status = 1 And a.is_deleted = 0 And a.entity_id = " . $this->userCompanyId;
            //} else {
                $settings["where"] = "b.is_deleted = 0 And b.jns_rawat = 1 And b.entity_id = " . $this->userCompanyId;
            //}
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

    public function add() {
        require_once(MODEL . "master/karyawan.php");
        require_once(MODEL . "master/dokter.php");
        //require_once(MODEL . "master/klpjasa.php");
        require_once(MODEL . "master/jasa.php");
        $dokter = null;
        $petugas = null;
        $loader = null;
        $layanan = new Tindakan();
        $log = new UserAdmin();
        if (count($this->postData) > 0) {
            // OK user ada kirim data kita proses
            $layanan->NoReg = $this->GetPostValue("NoReg");
            $layanan->TglLayanan = strtotime($this->GetPostValue("TglLayanan"));
            $layanan->JamLayanan = $this->GetPostValue("JamLayanan");
            $layanan->KdDokter = $this->GetPostValue("KdDokter");
            $layanan->KdPetugas = $this->GetPostValue("KdPetugas");
            $layanan->KdJasa = $this->GetPostValue("KdJasa");
            $layanan->NmJasa = $this->GetPostValue("NmJasa");
            $layanan->TarifHarga = $this->GetPostValue("TarifHarga");
            $layanan->Qty = $this->GetPostValue("Qty");
            $layanan->Keterangan = $this->GetPostValue("Keterangan");
            $layanan->CreatebyId = $this->userUid;
            if ($this->DoInsert($layanan)) {
                $log = $log->UserActivityWriter($this->userCabangId, 'pelayanan.tindakan', 'Add New Layanan -> No: ' . $layanan->NoReg . ' - ' . $layanan->KdJasa, '-', 'Success');
                $this->persistence->SaveState("info", sprintf("Data Layanan No: %s - %s telah berhasil disimpan..", $layanan->KdJasa, $layanan->NmJasa));
                redirect_url("pelayanan.tindakan");
            } else {
                if ($this->connector->GetHasError()) {
                    if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
                        $this->Set("error", sprintf("Kode Layanan: '%s' telah ada pada database !", $layanan->KdJasa));
                    } else {
                        $this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
                    }
                }
            }
        }else{
            $layanan->JamLayanan = date('h:i');
        }
        // untuk kirim variable ke view
        $loader = new Perawatan();
        $pasienlist = $loader->GetActivePatient($this->userCompanyId,1);
        $petugas = new Karyawan();
        $petugas = $petugas->LoadAll();
        $dokter = new Dokter();
        $dokter = $dokter->LoadAll();
        //$klpjasa = new Klpjasa();
        //$klpjasa = $klpjasa->LoadAll('a.klp_jasa');
        $jasa = new Jasa();
        $jasa = $jasa->LoadAll('a.kd_jasa');
        $this->Set("pasienlist", $pasienlist);
        $this->Set("petugaslist", $petugas);
        $this->Set("dokterlist", $dokter);
        //$this->Set("klpjasa", $klpjasa);
        $this->Set("jasa", $jasa);
        $this->Set("layanan", $layanan);
    }

    private function DoInsert(Tindakan $layanan) {
        //data validation here
        if ($layanan->NoReg == "") {
            $this->Set("error", "Nomor Registrasi masih kosong");
            return false;
        }

        if ($layanan->KdJasa == "") {
            $this->Set("error", "Kode Jasa/Layanan harus diisi");
            return false;
        }

        if ($layanan->TarifHarga == "" || $layanan->TarifHarga == 0) {
            $this->Set("error", "Tarif/Harga Jasa/Layanan harus diisi");
            return false;
        }

        if ($layanan->Qty == "" || $layanan->Qty == 0) {
            $this->Set("error", "Qty Jasa/Layanan harus diisi");
            return false;
        }

        if ($layanan->Insert() == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function edit($id) {
        require_once(MODEL . "master/karyawan.php");
        require_once(MODEL . "master/dokter.php");
        //require_once(MODEL . "master/klpjasa.php");
        require_once(MODEL . "master/jasa.php");
        $dokter = null;
        $petugas = null;
        $loader = null;
        $layanan = new Tindakan();
        $log = new UserAdmin();
        if (count($this->postData) > 0) {
            // OK user ada kirim data kita proses
            $layanan->Id = $id;
            $layanan->NoReg = $this->GetPostValue("NoReg");
            $layanan->TglLayanan = strtotime($this->GetPostValue("TglLayanan"));
            $layanan->JamLayanan = $this->GetPostValue("JamLayanan");
            $layanan->KdDokter = $this->GetPostValue("KdDokter");
            $layanan->KdPetugas = $this->GetPostValue("KdPetugas");
            $layanan->KdJasa = $this->GetPostValue("KdJasa");
            $layanan->NmJasa = $this->GetPostValue("NmJasa");
            $layanan->TarifHarga = $this->GetPostValue("TarifHarga");
            $layanan->Qty = $this->GetPostValue("Qty");
            $layanan->Keterangan = $this->GetPostValue("Keterangan");
            $layanan->UpdatebyId = $this->userUid;
            if ($this->DoUpdate($layanan)) {
                $log = $log->UserActivityWriter($this->userCabangId, 'pelayanan.tindakan', 'Ubah Data Layanan -> No: ' . $layanan->NoReg . ' - ' . $layanan->KdJasa, '-', 'Success');
                $this->persistence->SaveState("info", sprintf("Data Layanan No: %s - %s telah berhasil diupdate..", $layanan->KdJasa, $layanan->NmJasa));
                redirect_url("pelayanan.tindakan");
            } else {
                if ($this->connector->GetHasError()) {
                    if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
                        $this->Set("error", sprintf("Kode Layanan: '%s' telah ada pada database !", $layanan->KdJasa));
                    } else {
                        $this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
                    }
                }
            }
        }else{
            $layanan = $layanan->LoadById($id);
            if ($layanan == null){
                $this->Set("error", sprintf("Data Layanan tidak ditemukan, mungkin sudah dihapus!"));
                redirect_url("pelayanan.tindakan");
            }
        }
        // untuk kirim variable ke view
        $loader = new Perawatan();
        $pasienlist = $loader->GetActivePatient($this->userCompanyId,1);
        $petugas = new Karyawan();
        $petugas = $petugas->LoadAll();
        $dokter = new Dokter();
        $dokter = $dokter->LoadAll();
        //$klpjasa = new Klpjasa();
        //$klpjasa = $klpjasa->LoadAll('a.klp_jasa');
        $jasa = new Jasa();
        $jasa = $jasa->LoadAll('a.kd_jasa');
        $this->Set("pasienlist", $pasienlist);
        $this->Set("petugaslist", $petugas);
        $this->Set("dokterlist", $dokter);
        //$this->Set("klpjasa", $klpjasa);
        $this->Set("jasa", $jasa);
        $this->Set("layanan", $layanan);
    }

    private function DoUpdate(Tindakan $layanan) {
        //data validation here
        if ($layanan->NoReg == "") {
            $this->Set("error", "Nomor Registrasi masih kosong");
            return false;
        }

        if ($layanan->KdJasa == "") {
            $this->Set("error", "Kode Jasa/Layanan harus diisi");
            return false;
        }

        if ($layanan->TarifHarga == "" || $layanan->TarifHarga == 0) {
            $this->Set("error", "Tarif/Harga Jasa/Layanan harus diisi");
            return false;
        }

        if ($layanan->Qty == "" || $layanan->Qty == 0) {
            $this->Set("error", "Qty Jasa/Layanan harus diisi");
            return false;
        }

        if ($layanan->Update($layanan->Id)) {
            return true;
        } else {
            return false;
        }
    }

    public function view($id) {
        require_once(MODEL . "master/karyawan.php");
        require_once(MODEL . "master/dokter.php");
        //require_once(MODEL . "master/klpjasa.php");
        require_once(MODEL . "master/jasa.php");
        $dokter = null;
        $petugas = null;
        $loader = null;
        $layanan = new Tindakan();
        $layanan = $layanan->LoadById($id);
        if ($layanan == null){
            $this->Set("error", sprintf("Data Layanan tidak ditemukan, mungkin sudah dihapus!"));
            redirect_url("pelayanan.tindakan");
        }
        // untuk kirim variable ke view
        $loader = new Perawatan();
        $pasienlist = $loader->GetActivePatient($this->userCompanyId,1);
        $petugas = new Karyawan();
        $petugas = $petugas->LoadAll();
        $dokter = new Dokter();
        $dokter = $dokter->LoadAll();
        //$klpjasa = new Klpjasa();
        //$klpjasa = $klpjasa->LoadAll('a.klp_jasa');
        $jasa = new Jasa();
        $jasa = $jasa->LoadAll('a.kd_jasa');
        $this->Set("pasienlist", $pasienlist);
        $this->Set("petugaslist", $petugas);
        $this->Set("dokterlist", $dokter);
        //$this->Set("klpjasa", $klpjasa);
        $this->Set("jasa", $jasa);
        $this->Set("layanan", $layanan);
    }
}
