<?php
class PerawatanController extends AppController {
	private $userCompanyId;
	private $userCabangId;
	private $userUid;
    private $userSbuId;

	protected function Initialize() {
		require_once(MODEL . "pelayanan/perawatan.php");
		require_once(MODEL . "master/user_admin.php");
		$this->userCompanyId = $this->persistence->LoadState("entity_id");
		$this->userCabangId = $this->persistence->LoadState("cabang_id");
        $this->userSbuId = $this->persistence->LoadState("sbu_id");
        $this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();
		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 40);
		$settings["columns"][] = array("name" => "a.no_reg", "display" => "No. Register", "width" => 90);
        $settings["columns"][] = array("name" => "a.no_rm", "display" => "No. RM", "width" => 60);
        $settings["columns"][] = array("name" => "concat(a.tgl_masuk,' ',a.jam_masuk)", "display" => "Tgl & Jam Masuk", "width" => 90);
        $settings["columns"][] = array("name" => "a.nm_pasien", "display" => "Nama Pasien", "width" => 150);
        $settings["columns"][] = array("name" => "a.jkelamin", "display" => "L/P", "width" => 12);
        $settings["columns"][] = array("name" => "a.umur_pasien", "display" => "Umur", "width" => 70);
        $settings["columns"][] = array("name" => "if(a.cara_bayar = 1,'Umum',if(a.cara_bayar = 2,'BPJS',if(a.cara_bayar = 3,'Asuransi','N/A')))", "display" => "Cara Bayar", "width" => 50);
        //$settings["columns"][] = array("name" => "if(a.jns_rujukan = 1,'Sendiri',if(a.jns_rujukan = 2,'Dokter',if(a.jns_rujukan = 3,'RS Lain','N/A')))", "display" => "Rujukan", "width" => 50);
        //$settings["columns"][] = array("name" => "a.asal_rujukan", "display" => "Asal Rujukan", "width" => 70);
        //$settings["columns"][] = array("name" => "if(a.jns_rawat = 1,'Jalan',if(a.jns_rawat = 2,'Inap','N/A'))", "display" => "Rawat", "width" => 40);
        $settings["columns"][] = array("name" => "a.nm_poliklinik", "display" => "Poliklinik", "width" => 70);
        $settings["columns"][] = array("name" => "a.kmr_rawat", "display" => "Kamar Rawat", "width" => 70);
        $settings["columns"][] = array("name" => "a.kd_kamar", "display" => "No Kamar", "width" => 70);
        $settings["columns"][] = array("name" => "a.nm_dokter", "display" => "Nama Dokter", "width" => 100);
        //$settings["columns"][] = array("name" => "a.keluhan", "display" => "Keluhan", "width" => 100);
        $settings["columns"][] = array("name" => "concat(a.tgl_keluar,' ',a.jam_keluar)", "display" => "Tgl & Jam Keluar", "width" => 90);
        $settings["columns"][] = array("name" => "if(a.lama_rawat <> '0',concat(a.lama_rawat,' hr'),'-')", "display" => "Lama Rawat", "width" => 60, "align" => "right");
        $settings["columns"][] = array("name" => "if(a.reg_status = 1,'Dirawat',if(a.reg_status = 2,'Pulang','N/A'))", "display" => "Status", "width" => 50);

		$settings["filters"][] = array("name" => "a.no_reg", "display" => "No.Registrasi");
		$settings["filters"][] = array("name" => "a.no_rm", "display" => "No.Rekam Medik");
        $settings["filters"][] = array("name" => "a.nm_pasien", "display" => "Nama Pasien");
        $settings["filters"][] = array("name" => "a.nm_poliklinik", "display" => "Poliklinik");
        $settings["filters"][] = array("name" => "a.nm_dokter", "display" => "Nama Dokter");
        $settings["filters"][] = array("name" => "if(a.cara_bayar = 1,'Umum',if(a.cara_bayar = 2,'BPJS',if(a.cara_bayar = 3,'Asuransi','N/A')))", "display" => "Jenis Perawatan");
        $settings["filters"][] = array("name" => "if(a.jns_rujukan = 1,'Sendiri',if(a.jns_rujukan = 2,'Dokter',if(a.jns_rujukan = 3,'RS Lain','N/A')))", "display" => "Jenis Rujukan");
        //$settings["filters"][] = array("name" => "if(a.jns_rawat = 1,'Jalan',if(a.jns_rawat = 2,'Inap','N/A'))", "display" => "Jenis Rawat");
        $settings["filters"][] = array("name" => "if(a.reg_status = 1,'Dirawat',if(a.reg_status = 2,'Pulang','N/A'))", "display" => "Status Rawat");
        $settings["filters"][] = array("name" => "a.asal_rujukan", "display" => "Asal Rujukan");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Daftar Pasien Dirawat";
            //button action
			if ($acl->CheckUserAccess("perawatan", "add", "pelayanan")) {
                $settings["actions"][] = array("Text" => "Add", "Url" => "pelayanan.perawatan/addirect", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("perawatan", "edit", "pelayanan")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "pelayanan.perawatan/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
											   "Error" => "Mohon memilih data registrasi terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu data",
											   "Info" => "Apakah anda yakin mau merubah data yang dipilih ?");
			}
            if ($acl->CheckUserAccess("perawatan", "view", "pelayanan")) {
                $settings["actions"][] = array("Text" => "View", "Url" => "pelayanan.perawatan/view/%s", "Class" => "bt_view", "ReqId" => 1);
            }
			if ($acl->CheckUserAccess("perawatan", "delete", "pelayanan")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "pelayanan.perawatan/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
											   "Error" => "Mohon memilih data registrasi terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu data",
											   "Info" => "Apakah anda yakin mau menghapus data registrasi yang dipilih ?");
			}
            if ($acl->CheckUserAccess("tindakan", "add", "pelayanan")) {
                $settings["actions"][] = array("Text" => "separator", "Url" => null);
                $settings["actions"][] = array("Text" => "Input Jasa/Tindakan/Layanan", "Url" => "pelayanan.tindakan/add/%s", "Class" => "bt_create_new", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Pasien terlebih dahulu sebelum proses input tindakan.\nPERHATIAN: Pilih tepat 1 data pasien",
                    "Confirm" => "Input Data Jasa/Tindakan/Layanan?");
            }
            if ($acl->CheckUserAccess("billing", "add", "pelayanan")) {
                $settings["actions"][] = array("Text" => "separator", "Url" => null);
                $settings["actions"][] = array("Text" => "Proses Pasien Pulang", "Url" => "pelayanan.perawatan/keluar/%s", "Class" => "bt_edit", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Pasien terlebih dahulu sebelum proses pasien pulang.\nPERHATIAN: Pilih tepat 1 data pasien",
                    "Confirm" => "Proses Pasien Pulang?");
                $settings["actions"][] = array("Text" => "separator", "Url" => null);
                $settings["actions"][] = array("Text" => "Batal Proses Pasien Pulang", "Url" => "pelayanan.perawatan/batal/%s", "Class" => "bt_delete", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Pasien terlebih dahulu sebelum proses pembatalan.\nPERHATIAN: Pilih tepat 1 data pasien",
                    "Confirm" => "Batalkan Data Pasien Pulang?");
            }

			$settings["def_filter"] = 0;
			$settings["def_order"] = 1;
			$settings["singleSelect"] = true;
		} else {
			$settings["from"] = "vw_t_perawatan AS a";
            if ($_GET["query"] == "") {
                $_GET["query"] = null;
                $settings["where"] = "a.reg_status = 1 And a.is_deleted = 0 And a.entity_id = " . $this->userCompanyId;
            } else {
                $settings["where"] = "a.is_deleted = 0 And a.entity_id = " . $this->userCompanyId;
            }
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

    public function add($pasienId = 0) {
        require_once(MODEL . "pelayanan/pasien.php");
        require_once(MODEL . "pelayanan/tindakan.php");
        require_once(MODEL . "master/karyawan.php");
        require_once(MODEL . "master/poliklinik.php");
        require_once(MODEL . "master/dokter.php");
        require_once(MODEL . "master/kamar.php");
        $pasien = null;
        $poli = null;
        $dokter = null;
        $petugas = null;
		$loader = null;
		$perawatan = new Perawatan();
		$log = new UserAdmin();
		$ok = null;
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$perawatan->EntityId = $this->userCompanyId;
			$perawatan->NoRm = $this->GetPostValue("NoRm");
            $perawatan->CaraBayar = $this->GetPostValue("CaraBayar");
            $perawatan->JnsRujukan = $this->GetPostValue("JnsRujukan");
            $perawatan->AsalRujukan = $this->GetPostValue("AsalRujukan");
            $perawatan->JnsRawat = $this->GetPostValue("JnsRawat");
            $perawatan->KdKamar = $this->GetPostValue("KdKamar");
            $perawatan->TglMasuk = strtotime($this->GetPostValue("TglMasuk"));
            $perawatan->JamMasuk = $this->GetPostValue("JamMasuk");
            $perawatan->Keluhan = $this->GetPostValue("Keluhan");
            $perawatan->KdPetugas = $this->GetPostValue("KdPetugas");
            $perawatan->KdDokter = $this->GetPostValue("KdDokter");
            $perawatan->KdPoliklinik = $this->GetPostValue("KdPoliklinik");
            $perawatan->TgiBadan = $this->GetPostValue("TgiBadan");
            $perawatan->BrtBadan = $this->GetPostValue("BrtBadan");
            $perawatan->DiagnosaUtama = $this->GetPostValue("DiagnosaUtama");
            $perawatan->DiagnosaKedua = $this->GetPostValue("DiagnosaKedua");
            $perawatan->RegStatus = $this->GetPostValue("RegStatus");
            $perawatan->KdUtama = $this->GetPostValue("KdUtama");
            $perawatan->KdKedua = $this->GetPostValue("KdKedua");
            $perawatan->NmPjawab = $this->GetPostValue("NmPjawab");
            $perawatan->NoKtpPjawab = $this->GetPostValue("NoKtpPjawab");
            $perawatan->AlamatPjawab = $this->GetPostValue("AlamatPjawab");
            $perawatan->HubPjawab = $this->GetPostValue("HubPjawab");
            $perawatan->NoHpPjawab = $this->GetPostValue("NoHpPjawab");
            $perawatan->CreatebyId = $this->userUid;
            $perawatan->NoReg = $perawatan->AutoRegNo($perawatan->TglMasuk);
			if ($this->DoInsert($perawatan)) {
			    $pasien = new Pasien();
                if ($perawatan->JnsRawat == 1 || $perawatan->JnsRawat == 7 || $perawatan->JnsRawat == 8 || $perawatan->JnsRawat == 9) {
                    $ok = $pasien->setLastRalan($perawatan->NoRm, date('Y-m-d', $perawatan->TglMasuk));
                }else{
                    $ok = $pasien->setLastRanap($perawatan->NoRm, date('Y-m-d', $perawatan->TglMasuk));
                }
			    $pasien = $pasien->setIsRawat($perawatan->NoRm);
			    if ($perawatan->TgiBadan > 0 && $perawatan->BrtBadan > 0) {
                    $pasien = new Pasien();
                    $pasien = $pasien->UpdateTBB($perawatan->NoRm, $perawatan->TgiBadan, $perawatan->BrtBadan);
                }
                //insert autocharge like karcis
                $this->autocharge($perawatan->NoReg);

				$log = $log->UserActivityWriter($this->userCabangId,'pelayanan.perawatan','Add New Perawatan -> No: '.$perawatan->NoReg.' - '.$perawatan->NoRm,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data Registrasi No: %s (RM: %s) telah berhasil disimpan..", $perawatan->NoRm, $perawatan->NoReg));
				redirect_url("pelayanan.perawatan");
			} else {
                $pasien = new Pasien();
                $pasien = $pasien->FindById($pasienId);
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("No. Registrasi: '%s' telah ada pada database !", $perawatan->NoReg));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		}else{
		    if ($pasienId > 0) {
                //check if any active registration first
                $pasien = new Pasien();
                $pasien = $pasien->FindById($pasienId);
                if ($pasien == null) {
                    $this->Set("error", sprintf("Data Pasien tidak ditemukan!"));
                    redirect_url("pelayanan.perawatan");
                }
                if ($pasien->StsPasien == 0){
                    $this->Set("error", sprintf("Pasien sudah meninggal!"));
                    redirect_url("pelayanan.pasien");
                }
                $perawatan = $perawatan->FindActiveReg($pasien->NoRm);
                if ($perawatan == null) {
                    //if no active reg / new reg
                    $perawatan = new Perawatan();
                    $perawatan->JamMasuk = date('h:i');
                } else {
                    $this->Set("error", sprintf("Pasien ini masih dirawat!"));
                    redirect_url("pelayanan.perawatan/view/" . $perawatan->Id);
                }
                $perawatan->CaraBayar = $pasien->JnsPasien;
                $perawatan->BrtBadan = $pasien->BrtBadan;
                $perawatan->TgiBadan = $pasien->TgiBadan;
            }else{
                $pasien = new Pasien();
                $pasien = $pasien->LoadAll();
            }
        }
		// untuk kirim variable ke view
        $loader = new Pasien();
        $jhb = $loader->GetHubPjawab();
        $this->Set("jhbs", $jhb);
		$this->Set("pasien", $pasien);
		$poli = new Poliklinik();
		$poli = $poli->LoadAll();
        $this->Set("poli", $poli);
        $kamar = new Kamar();
        $kamar = $kamar->LoadAll();
        $this->Set("kamar", $kamar);
        $petugas = new Karyawan();
        $petugas = $petugas->LoadAll();
        $this->Set("petugas", $petugas);
        $dokter = new Dokter();
        $dokter = $dokter->LoadAll();
        $this->Set("dokter", $dokter);
        $this->Set("perawatan", $perawatan);
	}

    public function addirect() {
        require_once(MODEL . "pelayanan/pasien.php");
        require_once(MODEL . "master/karyawan.php");
        require_once(MODEL . "master/poliklinik.php");
        require_once(MODEL . "master/dokter.php");
        require_once(MODEL . "master/kamar.php");
        $pasien = null;
        $poli = null;
        $dokter = null;
        $petugas = null;
        $loader = null;
        $perawatan = new Perawatan();
        $log = new UserAdmin();
        $ok = null;
        if (count($this->postData) > 0) {
            // OK user ada kirim data kita proses
            $perawatan->EntityId = $this->userCompanyId;
            $perawatan->NoRm = $this->GetPostValue("NoRm");
            $perawatan->CaraBayar = $this->GetPostValue("CaraBayar");
            $perawatan->JnsRujukan = $this->GetPostValue("JnsRujukan");
            $perawatan->AsalRujukan = $this->GetPostValue("AsalRujukan");
            $perawatan->JnsRawat = $this->GetPostValue("JnsRawat");
            $perawatan->KdKamar = $this->GetPostValue("KdKamar");
            $perawatan->TglMasuk = strtotime($this->GetPostValue("TglMasuk"));
            $perawatan->JamMasuk = $this->GetPostValue("JamMasuk");
            $perawatan->Keluhan = $this->GetPostValue("Keluhan");
            $perawatan->KdPetugas = $this->GetPostValue("KdPetugas");
            $perawatan->KdDokter = $this->GetPostValue("KdDokter");
            $perawatan->KdPoliklinik = $this->GetPostValue("KdPoliklinik");
            $perawatan->TgiBadan = $this->GetPostValue("TgiBadan");
            $perawatan->BrtBadan = $this->GetPostValue("BrtBadan");
            $perawatan->DiagnosaUtama = $this->GetPostValue("DiagnosaUtama");
            $perawatan->DiagnosaKedua = $this->GetPostValue("DiagnosaKedua");
            $perawatan->RegStatus = $this->GetPostValue("RegStatus");
            $perawatan->KdUtama = $this->GetPostValue("KdUtama");
            $perawatan->KdKedua = $this->GetPostValue("KdKedua");
            $perawatan->NmPjawab = $this->GetPostValue("NmPjawab");
            $perawatan->NoKtpPjawab = $this->GetPostValue("NoKtpPjawab");
            $perawatan->AlamatPjawab = $this->GetPostValue("AlamatPjawab");
            $perawatan->HubPjawab = $this->GetPostValue("HubPjawab");
            $perawatan->NoHpPjawab = $this->GetPostValue("NoHpPjawab");
            $perawatan->CreatebyId = $this->userUid;
            $perawatan->NoReg = $perawatan->AutoRegNo($this->userCompanyId,$perawatan->TglMasuk);
            if ($this->DoInsert($perawatan)) {
                $pasien = new Pasien();
                if ($perawatan->JnsRawat == 1 || $perawatan->JnsRawat == 7 || $perawatan->JnsRawat == 8 || $perawatan->JnsRawat == 9) {
                    $ok = $pasien->setLastRalan($perawatan->NoRm, date('Y-m-d', $perawatan->TglMasuk));
                }else{
                    $ok = $pasien->setLastRanap($perawatan->NoRm, date('Y-m-d', $perawatan->TglMasuk));
                }
                $pasien = $pasien->setIsRawat($perawatan->NoRm);
                if ($perawatan->TgiBadan > 0 && $perawatan->BrtBadan > 0) {
                    $pasien = new Pasien();
                    $pasien = $pasien->UpdateTBB($perawatan->NoRm, $perawatan->TgiBadan, $perawatan->BrtBadan);
                }
                //insert autocharge like karcis
                $this->autocharge($perawatan->NoReg);

                $log = $log->UserActivityWriter($this->userCabangId,'pelayanan.perawatan','Add New Perawatan -> No: '.$perawatan->NoReg.' - '.$perawatan->NoRm,'-','Success');
                $this->persistence->SaveState("info", sprintf("Data Registrasi No: %s (RM: %s) telah berhasil disimpan..", $perawatan->NoRm, $perawatan->NoReg));
                redirect_url("pelayanan.perawatan");
            } else {
                $pasien = new Pasien();
                $pasien = $pasien->FindByNoRm($perawatan->NoRm);
                if ($this->connector->GetHasError()) {
                    if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
                        $this->Set("error", sprintf("No. Registrasi: '%s' telah ada pada database !", $perawatan->NoReg));
                    } else {
                        $this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
                    }
                }
            }
        }else{
            $perawatan = new Perawatan();
            $perawatan->JamMasuk = date('h:i');
        }
        // untuk kirim variable ke view
        $loader = new Pasien();
        $jhb = $loader->GetHubPjawab();
        $this->Set("jhbs", $jhb);
        $this->Set("pasien", $pasien);
        $poli = new Poliklinik();
        $poli = $poli->LoadAll();
        $this->Set("poli", $poli);
        $kamar = new Kamar();
        $kamar = $kamar->LoadAll();
        $this->Set("kamar", $kamar);
        $petugas = new Karyawan();
        $petugas = $petugas->LoadAll();
        $this->Set("petugas", $petugas);
        $dokter = new Dokter();
        $dokter = $dokter->LoadAll();
        $this->Set("dokter", $dokter);
        $this->Set("perawatan", $perawatan);
    }

	private function DoInsert(Perawatan $perawatan) {
        require_once(MODEL . "pelayanan/pasien.php");
        //data validation here
		if ($perawatan->NoReg == "") {
			$this->Set("error", "Nomor Registrasi masih kosong");
			return false;
		}
		if ($perawatan->NoRm == "") {
			$this->Set("error", "Nama Pasien harus diisi");
			return false;
		}
		if ($perawatan->CaraBayar == 0 || $perawatan->CaraBayar == '' || $perawatan->CaraBayar == null){
            $this->Set("error", "Cara Bayar belum dipilih!");
            return false;
        }
		if ($perawatan->CaraBayar == 2){
		    $pasien = new Pasien();
		    $pasien = $pasien->FindByNoRm($perawatan->NoRm);
		    if ($pasien->NoBpjs == null || $pasien->NoBpjs == ''){
                $this->Set("error", "No. Kartu BPJS Pasien salah atau belum diisi!");
                return false;
            }
        }
		if ($perawatan->Insert() == 1) {
			return true;
		} else {
            $this->Set("error", "Gagal menyimpan data ke database!, pastikan datanya sudah benar dan lengkap.");
            //$this->Set("error",$this->connector->GetErrorCode());
			return false;
		}
	}

    public function edit($id) {
        require_once(MODEL . "pelayanan/pasien.php");
        require_once(MODEL . "master/karyawan.php");
        require_once(MODEL . "master/poliklinik.php");
        require_once(MODEL . "master/dokter.php");
        require_once(MODEL . "master/kamar.php");
        $pasien = null;
        $poli = null;
        $dokter = null;
        $petugas = null;
        $loader = null;
        $perawatan = new Perawatan();
        $log = new UserAdmin();
        $ok = null;
        if (count($this->postData) > 0) {
            // OK user ada kirim data kita proses
            $perawatan->Id = $id;
            $perawatan->EntityId = $this->userCompanyId;
            $perawatan->NoReg = $this->GetPostValue("NoReg");
            $perawatan->NoRm = $this->GetPostValue("NoRm");
            $perawatan->CaraBayar = $this->GetPostValue("CaraBayar");
            $perawatan->JnsRujukan = $this->GetPostValue("JnsRujukan");
            $perawatan->AsalRujukan = $this->GetPostValue("AsalRujukan");
            $perawatan->JnsRawat = $this->GetPostValue("JnsRawat");
            $perawatan->KdKamar = $this->GetPostValue("KdKamar");
            $perawatan->TglMasuk = strtotime($this->GetPostValue("TglMasuk"));
            $perawatan->JamMasuk = $this->GetPostValue("JamMasuk");
            $perawatan->Keluhan = $this->GetPostValue("Keluhan");
            $perawatan->KdPetugas = $this->GetPostValue("KdPetugas");
            $perawatan->KdDokter = $this->GetPostValue("KdDokter");
            $perawatan->KdPoliklinik = $this->GetPostValue("KdPoliklinik");
            $perawatan->TgiBadan = $this->GetPostValue("TgiBadan");
            $perawatan->BrtBadan = $this->GetPostValue("BrtBadan");
            $perawatan->DiagnosaUtama = $this->GetPostValue("DiagnosaUtama");
            $perawatan->DiagnosaKedua = $this->GetPostValue("DiagnosaKedua");
            $perawatan->RegStatus = $this->GetPostValue("RegStatus");
            $perawatan->KdUtama = $this->GetPostValue("KdUtama");
            $perawatan->KdKedua = $this->GetPostValue("KdKedua");
            $perawatan->NmPjawab = $this->GetPostValue("NmPjawab");
            $perawatan->NoKtpPjawab = $this->GetPostValue("NoKtpPjawab");
            $perawatan->AlamatPjawab = $this->GetPostValue("AlamatPjawab");
            $perawatan->HubPjawab = $this->GetPostValue("HubPjawab");
            $perawatan->NoHpPjawab = $this->GetPostValue("NoHpPjawab");
            $perawatan->UpdatebyId = $this->userUid;
            if ($this->DoUpdate($perawatan)) {
                $pasien = new Pasien();
                if ($perawatan->JnsRawat == 1 || $perawatan->JnsRawat == 7 || $perawatan->JnsRawat == 8 || $perawatan->JnsRawat == 9) {
                    $ok = $pasien->setLastRalan($perawatan->NoRm, date('Y-m-d', $perawatan->TglMasuk));
                }else{
                    $ok = $pasien->setLastRanap($perawatan->NoRm, date('Y-m-d', $perawatan->TglMasuk));
                }
                $pasien = $pasien->setIsRawat($perawatan->NoRm);
                if ($perawatan->TgiBadan > 0 && $perawatan->BrtBadan > 0) {
                    $pasien = new Pasien();
                    $pasien = $pasien->UpdateTBB($perawatan->NoRm, $perawatan->TgiBadan, $perawatan->BrtBadan);
                }
                $log = $log->UserActivityWriter($this->userCabangId,'pelayanan.perawatan','Update Data Registrasi -> No: '.$perawatan->NoReg.' - '.$perawatan->NoRm,'-','Success');
                $this->persistence->SaveState("info", sprintf("Data Registrasi No: %s (RM: %s) telah berhasil diupdate..", $perawatan->NoRm, $perawatan->NoReg));
                redirect_url("pelayanan.perawatan");
            } else {
                $perawatan = $perawatan->LoadById($id);
                $pasien = new Pasien();
                $pasien = $pasien->FindByNoRm($perawatan->NoRm);
                if ($this->connector->GetHasError()) {
                    if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
                        $this->Set("error", sprintf("No. Req: '%s' telah ada pada database !", $perawatan->NoReg));
                    } else {
                        $this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
                    }
                }
            }
        }else{
            //get perawatan data by id
            if ($id == null) {
                $this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan edit data !");
                redirect_url("pelayanan.perawatan");
            }else {
                $perawatan = $perawatan->LoadById($id);
                if ($perawatan == null) {
                    $this->persistence->SaveState("error", "Data Registrasi yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
                    redirect_url("pelayanan.perawatan");
                }
                if ($perawatan->RegStatus == 2) {
                    $this->persistence->SaveState("error", "Data Registrasi No: ".$perawatan->NoReg." sudah berstatus -Pulang-, tidak boleh diubah!");
                    redirect_url("pelayanan.perawatan");
                }
                //get data pasien
                $pasien = new Pasien();
                $pasien = $pasien->FindByNoRm($perawatan->NoRm);
                if ($pasien->NoRm == null) {
                    $this->Set("error", sprintf("Data Pasien tidak ditemukan!"));
                    redirect_url("pelayanan.perawatan");
                }
            }
        }
        // untuk kirim variable ke view
        $loader = new Pasien();
        $jhb = $loader->GetHubPjawab();
        $this->Set("jhbs", $jhb);
        $this->Set("pasien", $pasien);
        $poli = new Poliklinik();
        $poli = $poli->LoadAll();
        $this->Set("poli", $poli);
        $kamar = new Kamar();
        $kamar = $kamar->LoadAll();
        $this->Set("kamar", $kamar);
        $petugas = new Karyawan();
        $petugas = $petugas->LoadAll();
        $this->Set("petugas", $petugas);
        $dokter = new Dokter();
        $dokter = $dokter->LoadAll();
        $this->Set("dokter", $dokter);
        $this->Set("perawatan", $perawatan);
    }


	private function DoUpdate(Perawatan $perawatan) {
        require_once(MODEL . "pelayanan/pasien.php");
        //data validation here
        if ($perawatan->NoReg == "") {
            $this->Set("error", "Nomor Registrasi masih kosong");
            return false;
        }
        if ($perawatan->NoRm == "") {
            $this->Set("error", "Nama Pasien harus diisi");
            return false;
        }
        if ($perawatan->CaraBayar == 0 || $perawatan->CaraBayar == '' || $perawatan->CaraBayar == null){
            $this->Set("error", "Cara Bayar belum dipilih!");
            return false;
        }
        if ($perawatan->CaraBayar == 2){
            $pasien = new Pasien();
            $pasien = $pasien->FindByNoRm($perawatan->NoRm);
            if ($pasien->NoBpjs == null || $pasien->NoBpjs == ''){
                $this->Set("error", "No. Kartu BPJS Pasien belum diisi");
                return false;
            }
        }
		if ($perawatan->Update($perawatan->Id) == 1) {
			return true;
		} else {
			return false;
		}
	}

    public function view($id) {
        require_once(MODEL . "pelayanan/pasien.php");
        require_once(MODEL . "master/karyawan.php");
        require_once(MODEL . "master/poliklinik.php");
        require_once(MODEL . "master/dokter.php");
        require_once(MODEL . "pelayanan/tindakan.php");
        require_once(MODEL . "master/kamar.php");
        $pasien = null;
        $poli = null;
        $dokter = null;
        $petugas = null;
        $loader = null;
        $perawatan = new Perawatan();
        $log = new UserAdmin();
        //get perawatan data by id
        if ($id == null) {
            $this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan view data !");
            redirect_url("pelayanan.perawatan");
        }else {
            $perawatan = $perawatan->LoadById($id);
            if ($perawatan == null) {
                $this->persistence->SaveState("error", "Data Registrasi yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
                redirect_url("pelayanan.perawatan");
            }
            //get data pasien
            $pasien = new Pasien();
            $pasien = $pasien->FindByNoRm($perawatan->NoRm);
            if ($pasien->NoRm == null) {
                $this->Set("error", sprintf("Data Pasien tidak ditemukan!"));
                redirect_url("pelayanan.perawatan");
            }
        }
        // untuk kirim variable ke view
        $loader = new Pasien();
        $jhb = $loader->GetHubPjawab();
        $this->Set("jhbs", $jhb);
        $this->Set("pasien", $pasien);
        $poli = new Poliklinik();
        $poli = $poli->LoadAll();
        $this->Set("poli", $poli);
        $kamar = new Kamar();
        $kamar = $kamar->LoadAll();
        $this->Set("kamar", $kamar);
        $petugas = new Karyawan();
        $petugas = $petugas->LoadAll();
        $this->Set("petugas", $petugas);
        $dokter = new Dokter();
        $dokter = $dokter->LoadAll();
        $this->Set("dokter", $dokter);
        $this->Set("perawatan", $perawatan);
        $layanan = new Tindakan();
        $layanan = $layanan->LoadByNoReg($perawatan->NoReg);
        $this->Set("layananlist", $layanan);
    }

	public function delete($id = null) {
        require_once(MODEL . "pelayanan/pasien.php");
        require_once(MODEL . "pelayanan/tindakan.php");
		if ($id == null) {
			$this->persistence->SaveState("error", "Anda harus memilih data perawatan sebelum melakukan hapus data !");
			redirect_url("pelayanan.perawatan");
		}
		$log = new UserAdmin();
		$perawatan = new Perawatan();
		$perawatan = $perawatan->FindById($id);
		if ($perawatan == null) {
			$this->persistence->SaveState("error", "Data yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
			redirect_url("pelayanan.perawatan");
		}
        if ($perawatan->RegStatus == 2) {
            $this->persistence->SaveState("error", "Data Registrasi sudah berstatus -Pulang- tidak boleh dihapus!");
            redirect_url("pelayanan.perawatan");
        }
        $no_rm = $perawatan->NoRm;
		if ($perawatan->Delete($perawatan->Id)) {
			$log = $log->UserActivityWriter($this->userCabangId,'pelayanan.perawatan','Delete Perawatan -> No: '.$perawatan->NoReg.' - '.$perawatan->NoRm,'-','Success');
			$this->persistence->SaveState("info", sprintf("Data Registrasi No: %s telah berhasil dihapus.", $perawatan->NoReg));
			//set status pasien
            $pasien = new Pasien();
            $pasien = $pasien->setIsRawat($no_rm);
            //hapus semua tindakan yg pernah diberikan
            $tindakan = new Tindakan();
            $tindakan = $tindakan->DeleteByNoReg($perawatan->NoReg);
			redirect_url("pelayanan.perawatan");
		} else {
			$this->persistence->SaveState("error", sprintf("Gagal menghapus data Perawatan No: '%s'. Message: %s", $perawatan->NoReg, $this->connector->GetErrorMessage()));
		}
		redirect_url("pelayanan.perawatan");
	}

    public function keluar($id){
        require_once(MODEL . "pelayanan/pasien.php");
        require_once(MODEL . "master/karyawan.php");
        require_once(MODEL . "master/poliklinik.php");
        require_once(MODEL . "master/dokter.php");
        require_once(MODEL . "pelayanan/tindakan.php");
        require_once(MODEL . "pelayanan/billing.php");
        require_once(MODEL . "master/kamar.php");
        $pasien = null;
        $poli = null;
        $dokter = null;
        $petugas = null;
        $loader = null;
        $no_rm = null;
        $perawatan = new Perawatan();
        $log = new UserAdmin();
        if (count($this->postData) > 0) {
            $perawatan->Id = $this->GetPostValue("RegId");
            $perawatan->NoReg = $this->GetPostValue("NoReg");
            $no_rm = $this->GetPostValue("NoRm");
            $perawatan->TglKeluar = strtotime($this->GetPostValue("TglKeluar"));
            $perawatan->JamKeluar = $this->GetPostValue("JamKeluar");
            $perawatan->StsKeluar = $this->GetPostValue("StsKeluar");
            $totals = $this->GetPostValue("DtgSendiri");
            $totalb = $this->GetPostValue("DtgBpjs");
            $perawatan->UpdatebyId = $this->userUid;
            #1. Update data perawatan
            if ($perawatan->UpdatePasienKeluar($perawatan->Id)== 1){
                $log = $log->UserActivityWriter($this->userCabangId,'pelayanan.perawatan','Update Pasien Keluar -> No: '.$perawatan->NoReg,'-','Success');
                //update pasien master
                $pasien = new Pasien();
                $pasien = $pasien->setIsRawat($no_rm);
                if ($perawatan->StsKeluar == 3){
                    $pasien = new Pasien();
                    $pasien = $pasien->setIsMeninggal($no_rm);
                }
                //create billing
                $billing = new Billing();
                $billing->NoReg = $this->GetPostValue("NoReg");
                $billing->NominalTindakan = $totals + $totalb;
                $perawatan = new Perawatan();
                $perawatan = $perawatan->LoadById($id);
                $cek = new Pasien();
                $cek = $cek->FindByNoRm($perawatan->NoRm);
                $nokk = $cek->NoKK;
                $cek = new Perawatan();
                $cek = $cek->CheckDitanggungPerusahaan($nokk);
                if ($cek == 1){
                    if ($perawatan->CaraBayar == 2) {
                        $billing->DtgPerusahaan = $totals;
                        $billing->DtgBpjs = $totalb;
                        $billing->DtgSendiri = 0;
                    }else{
                        $billing->DtgPerusahaan = $totals;
                        $billing->DtgBpjs = 0;
                        $billing->DtgSendiri = 0;
                    }
                }else {
                    if ($perawatan->CaraBayar == 2) {
                        $billing->DtgPerusahaan = 0;
                        $billing->DtgBpjs = $totalb;
                        $billing->DtgSendiri = $totals;
                    }else{
                        $billing->DtgPerusahaan = 0;
                        $billing->DtgBpjs = 0;
                        $billing->DtgSendiri = $totals + $totalb;
                    }
                }
                $billing->TglBayar = null;
                $billing->StsBilling = 0;
                $billing->CreatebyId = $this->userUid;
                if ($billing->Insert()) {
                    $this->persistence->SaveState("info", sprintf("Proses pasien keluar No: %s berhasil!", $perawatan->NoReg));
                    redirect_url("pelayanan.perawatan");
                }
            }else{
                $this->persistence->SaveState("error", "Gagal update data pasien keluar No: ".$perawatan->NoReg);
                redirect_url("pelayanan.perawatan/keluar/".$perawatan->Id);
            }
        }else{
            //get perawatan data by id
            if ($id == null) {
                $this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan view data !");
                redirect_url("pelayanan.perawatan");
            }else {
                $perawatan = $perawatan->LoadById($id);
                if ($perawatan == null) {
                    $this->persistence->SaveState("error", "Data Pasien yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
                    redirect_url("pelayanan.perawatan");
                }
                if ($perawatan->RegStatus == 2) {
                    $this->persistence->SaveState("error", "Data Pasien yang dipilih sudah berstatus -Pulang-..");
                    redirect_url("pelayanan.perawatan");
                }
                //cek data tindakan
                $layanan = new Tindakan();
                $layanan = $layanan->LoadByNoReg($perawatan->NoReg);
                if ($layanan == null){
                    $this->Set("error", sprintf("Data Layanan/Jasa/Tindakan belum diinput! *Tidak bisa proses pulang*"));
                    redirect_url("pelayanan.perawatan");
                }
                //get data pasien
                $pasien = new Pasien();
                $pasien = $pasien->FindByNoRm($perawatan->NoRm);
                if ($pasien->NoRm == null) {
                    $this->Set("error", sprintf("Data Pasien tidak ditemukan!"));
                    redirect_url("pelayanan.perawatan");
                }
                if ($perawatan->TglKeluar == null || $perawatan->TglKeluar == ''){
                    $perawatan->TglKeluar = date('d-m-Y');
                    $perawatan->JamKeluar = date('H:i');
                }
            }
        }
        // untuk kirim variable ke view
        $loader = new Pasien();
        $jhb = $loader->GetHubPjawab();
        $this->Set("jhbs", $jhb);
        $this->Set("pasien", $pasien);
        $poli = new Poliklinik();
        $poli = $poli->LoadAll();
        $this->Set("poli", $poli);
        $kamar = new Kamar();
        $kamar = $kamar->LoadAll();
        $this->Set("kamar", $kamar);
        $petugas = new Karyawan();
        $petugas = $petugas->LoadAll();
        $this->Set("petugas", $petugas);
        $dokter = new Dokter();
        $dokter = $dokter->LoadAll();
        $this->Set("dokter", $dokter);
        $this->Set("perawatan", $perawatan);
        $layanan = new Tindakan();
        $layanan = $layanan->LoadByNoReg($perawatan->NoReg);
        $this->Set("layananlist", $layanan);
    }

    public function batal($id){
        require_once(MODEL . "pelayanan/billing.php");
        $perawatan = new Perawatan();
        $log = new UserAdmin();
        $nrg = null;
        //get perawatan data by id
        if ($id == null) {
            $this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan proses pembatalan!");
            redirect_url("pelayanan.perawatan");
        }else {
            $perawatan = $perawatan->LoadById($id);
            if ($perawatan == null) {
                $this->persistence->SaveState("error", "Data Pasien yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
                redirect_url("pelayanan.perawatan");
            }
            if ($perawatan->RegStatus == 2) {
                $nrg = $perawatan->NoReg;
                $perawatan->UpdatebyId = $this->userUid;
                if ($perawatan->BatalPasienKeluar($id)){
                    $billing = new Billing();
                    if ($billing->DeleteByNoReg($nrg)){
                        $log = $log->UserActivityWriter($this->userCabangId,'pelayanan.perawatan','Batalkan Pasien Keluar -> No: '.$nrg,'-','Success');
                    }
                }
                redirect_url("pelayanan.perawatan");
            }else{
                $this->persistence->SaveState("error", "Data Pasien yang dipilih belum berstatus -Pulang-");
                redirect_url("pelayanan.perawatan");
            }

        }
    }

	public function getAutoRegNo($regDate = null){
	    $perawatan = new  Perawatan();
	    print ($perawatan->AutoRegNo($regDate));
    }

    public function GetJSonListPenyakit(){
        require_once(MODEL . "master/penyakit.php");
        $filter = isset($_POST['q']) ? strval($_POST['q']) : '';
        $lkode = new Penyakit();
        $lkode = $lkode->GetJSonPenyakit($filter);
        echo json_encode($lkode);
    }

    public function GetJSonInActivePatient(){
        $filter = isset($_POST['q']) ? strval($_POST['q']) : '';
        $apasien = new Perawatan();
        $apasien = $apasien->GetJSonInActivePatient($this->userCompanyId,$filter);
        echo json_encode($apasien);
    }

    public function autocharge($reg_no){
        require_once(MODEL . "pelayanan/tindakan.php");
        require_once(MODEL . "master/jasa.php");
        $layanan = new Tindakan();
        $log = new UserAdmin();
        $isbaru = true;
        $norm = null;
        $perawatan = new Perawatan();
        $perawatan = $perawatan->FindByNoReg($reg_no);
        if ($perawatan != null){
            // OK user ada kirim data kita proses
            $layanan->NoReg = $reg_no;
            $layanan->TglLayanan = $perawatan->TglMasuk;
            $layanan->JamLayanan = $perawatan->JamMasuk;
            $layanan->JnsRawat = $perawatan->JnsRawat;
            $jasa = new Jasa();
            $jasa = $jasa->FindAutoCharge();
            if ($jasa != null) {
                $layanan->KdJasa = $jasa->KdJasa;
                $layanan->UraianJasa = $jasa->UraianJasa;
                $layanan->Qty = 1;
                $layanan->NmJasa = $jasa->NmJasa;
                if ($layanan->JnsRawat == 1) {
                    $layanan->TarifHarga = $jasa->TPoli;
                }elseif ($layanan->JnsRawat == 7){
                    $layanan->TarifHarga = $jasa->TPolSp;
                }elseif ($layanan->JnsRawat == 8){
                    $layanan->TarifHarga = $jasa->TPolRd;
                }elseif ($layanan->JnsRawat == 9){
                    $layanan->TarifHarga = $jasa->TPolKb;
                }elseif ($layanan->JnsRawat == 10){
                    $layanan->TarifHarga = $jasa->TPersalinan;
                }else{
                    $layanan->TarifHarga = $jasa->TIgd;
                }
                if ($perawatan->CaraBayar == 2){
                    $layanan->IsBpjs = 1;
                }else{
                    $layanan->IsBpjs = 0;
                }
            }
            $layanan->CreatebyId = $this->userUid;
            if ($layanan->Insert()) {
                $log = $log->UserActivityWriter($this->userCabangId, 'pelayanan.tindakan', 'Add New Layanan -> No: ' . $layanan->NoReg . ' - ' . $layanan->KdJasa, '-', 'Success');
            }
        }
    }
}
