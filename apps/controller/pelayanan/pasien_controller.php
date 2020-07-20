<?php
class PasienController extends AppController {
	private $userCompanyId;
	private $userCabangId;
	private $userUid;

	protected function Initialize() {
		require_once(MODEL . "pelayanan/pasien.php");
		require_once(MODEL . "master/user_admin.php");
		$this->userCompanyId = $this->persistence->LoadState("entity_id");
		$this->userCabangId = $this->persistence->LoadState("cabang_id");
        $this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();
		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 40);
		$settings["columns"][] = array("name" => "a.no_rm", "display" => "No. RM", "width" => 80);
		$settings["columns"][] = array("name" => "a.nm_pasien", "display" => "Nama Pasien", "width" => 150);
        $settings["columns"][] = array("name" => "if(a.jkelamin = 'L','Laki-Laki','Perempuan')", "display" => "Gender", "width" => 50);
        $settings["columns"][] = array("name" => "if(a.jns_pasien = 1,'Umum',if(a.jns_pasien = 2,'BPJS',if(a.jns_pasien = 3,'Asuransi','-')))", "display" => "Jenis", "width" => 50);
        $settings["columns"][] = array("name" => "a.no_ktp", "display" => "No. KTP", "width" => 100);
        $settings["columns"][] = array("name" => "a.no_bpjs", "display" => "No. BPJS", "width" => 100);
        //$settings["columns"][] = array("name" => "a.gol_darah", "display" => "Gol Darah", "width" => 50);
        //$settings["columns"][] = array("name" => "a.t4_lahir", "display" => "Lahir di", "width" => 100);
        $settings["columns"][] = array("name" => "a.tgl_lahir", "display" => "Tgl. Lahir", "width" => 60);
        $settings["columns"][] = array("name" => "a.umur", "display" => "Umur", "width" => 70);
        $settings["columns"][] = array("name" => "a.alamat", "display" => "Alamat", "width" => 200);
        $settings["columns"][] = array("name" => "if(a.pernah_ranap = 1,'Pernah','')", "display" => "Rwt Inap", "width" => 60);
        $settings["columns"][] = array("name" => "if(a.pernah_ralan = 1,'Pernah','')", "display" => "Rwt Jalan", "width" => 60);
        $settings["columns"][] = array("name" => "if(a.sts_pasien = 1,if(a.is_dirawat = 1,'Dirawat',''),'Meninggal')", "display" => "Status", "width" => 50);
        /*
        $settings["columns"][] = array("name" => "a.nm_desa", "display" => "Desa", "width" => 80);
        $settings["columns"][] = array("name" => "a.nm_kecamatan", "display" => "Kecamatan", "width" => 80);
        $settings["columns"][] = array("name" => "a.nm_kabkota", "display" => "Kab/Kota", "width" => 80);
        $settings["columns"][] = array("name" => "a.nm_propinsi", "display" => "Propinsi", "width" => 80);
        */
        //$settings["columns"][] = array("name" => "a.no_hp", "display" => "No. HP", "width" => 80);
        //$settings["columns"][] = array("name" => "a.nm_pekerjaan", "display" => "Pekerjaan", "width" => 80);
        //$settings["columns"][] = array("name" => "a.sts_kawin", "display" => "Sts Kawin", "width" => 50);
        //$settings["columns"][] = array("name" => "a.tgi_badan", "display" => "T/B", "width" => 50);
        //$settings["columns"][] = array("name" => "a.brt_badan", "display" => "B/B", "width" => 100);
        //filters
		$settings["filters"][] = array("name" => "a.nm_pasien", "display" => "Nama Pasien");
		$settings["filters"][] = array("name" => "a.no_ktp", "display" => "No. KTP");
        $settings["filters"][] = array("name" => "a.no_bpjs", "display" => "No. BPJS");
        $settings["filters"][] = array("name" => "a.no_rm", "display" => "No. RM");
        $settings["filters"][] = array("name" => "a.alamat", "display" => "Alamat");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Daftar Pasien";
            //button action
			if ($acl->CheckUserAccess("pasien", "add", "pelayanan")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "pelayanan.pasien/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("pasien", "edit", "pelayanan")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "pelayanan.pasien/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
											   "Error" => "Mohon memilih data Pasien terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu pasien",
											   "Confirm" => "Apakah anda yakin mau merubah data Pasien yang dipilih ?");
			}
            if ($acl->CheckUserAccess("pasien", "view", "pelayanan")) {
                $settings["actions"][] = array("Text" => "View", "Url" => "pelayanan.pasien/view/%s", "Class" => "bt_view", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Pasien terlebih dahulu.\nPERHATIAN: Pilih tepat 1 data",
					"Confirm" => "");
            }
			if ($acl->CheckUserAccess("pasien", "delete", "pelayanan")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "pelayanan.pasien/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
											   "Error" => "Mohon memilih data Pasien terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu pasien",
											   "Info" => "Apakah anda yakin mau menghapus data Pasien yang dipilih ?");
			}
            if ($acl->CheckUserAccess("perawatan", "add", "pelayanan")) {
                $settings["actions"][] = array("Text" => "separator", "Url" => null);
                $settings["actions"][] = array("Text" => "Registrasi Kunjungan Pasien", "Url" => "pelayanan.perawatan/add/%s", "Class" => "bt_create_new", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Pasien terlebih dahulu sebelum proses Registrasi Kunjungan.\nPERHATIAN: Pilih tepat 1 data pasien",
                    "Confirm" => "Anda akan dibawa ke halaman untuk membuat Registrasi Kunjungan Pasien.\nDetail data akan di-isi pada halaman berikutnya.\n\nKlik 'OK' untuk berpindah halaman.");
            }
            if ($acl->CheckUserAccess("pasien", "view", "pelayanan")) {
                $settings["actions"][] = array("Text" => "separator", "Url" => null);
                $settings["actions"][] = array("Text" => "History Kunjungan Pasien", "Url" => "pelayanan.pasien/history/%s", "Class" => "bt_report", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Pasien terlebih dahulu untuk melihat history kunjungan\nPERHATIAN: Pilih tepat 1 data pasien",
                    "Confirm" => "");
            }
            //sort and default filter
			$settings["def_filter"] = 0;
			$settings["def_order"] = 1;
			$settings["singleSelect"] = true;
		} else {
			$settings["from"] = "vw_m_pasien AS a";
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
		$pasien = new Pasien();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
		    // OK user ada kirim data kita proses
			$pasien->EntityId = $this->userCompanyId;
			$pasien->NoRm = $this->GetPostValue("NoRm");
			$pasien->NmPasien = $this->GetPostValue("NmPasien");
            $pasien->NoKtp = $this->GetPostValue("NoKtp");
            $pasien->NoBpjs = $this->GetPostValue("NoBpjs");
            $pasien->Alamat = $this->GetPostValue("Alamat");
            $pasien->Jkelamin = $this->GetPostValue("Jkelamin");
            $pasien->T4Lahir = $this->GetPostValue("T4Lahir");
            $pasien->TglLahir = $this->GetPostValue("TglLahir");
            $pasien->GolDarah = $this->GetPostValue("GolDarah");
            $pasien->NoHp = $this->GetPostValue("NoHp");
            $pasien->StsKawin = $this->GetPostValue("StsKawin");
            $pasien->PekerjaanId = $this->GetPostValue("PekerjaanId");
            $pasien->TgiBadan = $this->GetPostValue("TgiBadan");
            $pasien->BrtBadan = $this->GetPostValue("BrtBadan");
            $pasien->PernahOperasi = $this->GetPostValue("PernahOperasi");
            $pasien->RiwayatAlergi = $this->GetPostValue("RiwayatAlergi");
            $pasien->RpKeluarga = $this->GetPostValue("RpKeluarga");
            $pasien->JnsPasien = $this->GetPostValue("JnsPasien");
            $pasien->CreatebyId = $this->userUid;
            $pasien->NmIbu = $this->GetPostValue("NmIbu");
            $pasien->NoKK = $this->GetPostValue("NoKK");
			if ($this->DoInsert($pasien)) {
				$log = $log->UserActivityWriter($this->userCabangId,'pelayanan.pasien','Add New Pasien -> RM: '.$pasien->NoRm.' - '.$pasien->NmPasien,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data Pasien: %s RM: %s telah berhasil disimpan.", $pasien->NmPasien, $pasien->NoRm));
				redirect_url("pelayanan.pasien");
			} else {
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("RM: '%s' telah ada pada database !", $pasien->EntityCd));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		}
		//ambil data yg diperlukan
        $loader = new Pasien();
		$rsp = $loader->GetPekerjaanList();
		// untuk kirim variable ke view
		$this->Set("rspekerjaan", $rsp);
        $this->Set("pasien", $pasien);
	}

	private function DoInsert(Pasien $pasien) {

		if ($pasien->NoRm == "") {
			$this->Set("error", "No. RM masih kosong");
			return false;
		}

		if ($pasien->NmPasien == "") {
			$this->Set("error", "Nama Pasien masih kosong");
			return false;
		}
		if ($pasien->Insert() == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function edit($id = null) {
		$loader = null;
		$pasien = new Pasien();
		$log = new UserAdmin();
		$NmPasienLama = null;
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$pasien->Id = $id;
            $pasien->EntityId = $this->userCompanyId;
            $pasien->NoRm = $this->GetPostValue("NoRm");
            $NmPasienLama = $this->GetPostValue("NmPasienLama");
            $pasien->NmPasien = $this->GetPostValue("NmPasien");
            $pasien->NoKtp = $this->GetPostValue("NoKtp");
            $pasien->NoBpjs = $this->GetPostValue("NoBpjs");
            $pasien->Alamat = $this->GetPostValue("Alamat");
            $pasien->Jkelamin = $this->GetPostValue("Jkelamin");
            $pasien->T4Lahir = $this->GetPostValue("T4Lahir");
            $pasien->TglLahir = $this->GetPostValue("TglLahir");
            $pasien->GolDarah = $this->GetPostValue("GolDarah");
            $pasien->NoHp = $this->GetPostValue("NoHp");
            $pasien->StsKawin = $this->GetPostValue("StsKawin");
            $pasien->PekerjaanId = $this->GetPostValue("PekerjaanId");
            $pasien->TgiBadan = $this->GetPostValue("TgiBadan");
            $pasien->BrtBadan = $this->GetPostValue("BrtBadan");
            $pasien->PernahOperasi = $this->GetPostValue("PernahOperasi");
            $pasien->RiwayatAlergi = $this->GetPostValue("RiwayatAlergi");
            $pasien->RpKeluarga = $this->GetPostValue("RpKeluarga");
            $pasien->JnsPasien = $this->GetPostValue("JnsPasien");
            $pasien->UpdatebyId = $this->userUid;
            $pasien->NmIbu = $this->GetPostValue("NmIbu");
            $pasien->NoKK = $this->GetPostValue("NoKK");
			if ($this->DoUpdate($pasien,$NmPasienLama)) {
				$log = $log->UserActivityWriter($this->userCabangId,'pelayanan.pasien','Update Pasien -> Kode: '.$pasien->NoRm.' - '.$pasien->NmPasien,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data Pasien: %s RM: %s telah berhasil diupdate.", $pasien->NmPasien, $pasien->NoRm));
				redirect_url("pelayanan.pasien");
			} else {
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("RM: '%s' telah ada pada database !", $pasien->EntityCd));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		} else {
			if ($id == null) {
				$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan edit data !");
				redirect_url("pelayanan.pasien");
			}
			$pasien = $pasien->FindById($id);
			if ($pasien == null) {
				$this->persistence->SaveState("error", "Data Pasien yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
				redirect_url("pelayanan.pasien");
			}
		}
        //ambil data yg diperlukan
        $loader = new Pasien();
        $rsp = $loader->GetPekerjaanList();
        // untuk kirim variable ke view
        $this->Set("rspekerjaan", $rsp);
        $this->Set("pasien", $pasien);
	}

	private function DoUpdate(Pasien $pasien,$nplama) {
		if ($pasien->NoRm == "") {
			$this->Set("error", "Kode  masih kosong");
			return false;
		}
		if ($pasien->NmPasien == "") {
			$this->Set("error", "Nama Pasien masih kosong");
			return false;
		}
        if ($pasien->NmPasien != $nplama) {
		    if (substr($nplama,0,1) != substr($pasien->NmPasien,0,1)){
                $pasien->NmPasien = $nplama;
                $this->Set("error", "Huruf pertama nama pasien harus huruf -".strtoupper(substr($nplama,0,1))."-");
                return false;
            }
        }
		if ($pasien->Update($pasien->Id) == 1) {
			return true;
		} else {
			return false;
		}
	}

    public function view($id = null) {
        $loader = null;
        $pasien = new Pasien();
        $pasien = $pasien->FindById($id);
        if ($pasien == null) {
            $this->persistence->SaveState("error", "Data Pasien yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
            redirect_url("pelayanan.pasien");
        }
        //ambil data yg diperlukan
        $loader = new Pasien();
        $rsp = $loader->GetPekerjaanList();
        // untuk kirim variable ke view
        $this->Set("rspekerjaan", $rsp);
        $this->Set("pasien", $pasien);
    }

	public function delete($id = null) {
        require_once(MODEL . "pelayanan/perawatan.php");
		if ($id == null) {
			$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan hapus data !");
			redirect_url("pelayanan.pasien");
		}
		$log = new UserAdmin();
		$pasien = new Pasien();
		$pasien = $pasien->FindById($id);
		if ($pasien == null) {
			$this->persistence->SaveState("error", "Data yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
			redirect_url("pelayanan.pasien");
		}
        $registrasi = new Perawatan();
		$registrasi = $registrasi->FindByNoRm($pasien->NoRm);
        if ($registrasi == null) {
            if ($pasien->Delete($pasien->Id)) {
                $log = $log->UserActivityWriter($this->userCabangId, 'pelayanan.pasien', 'Delete Pasien -> No: ' . $pasien->NoRm . ' - ' . $pasien->NmPasien, '-', 'Success');
                $this->persistence->SaveState("info", sprintf("Data Pasien: %s [%s] telah berhasil dihapus.", $pasien->NmPasien, $pasien->NoRm));
            } else {
                $this->persistence->SaveState("error", sprintf("Gagal menghapus data Pasien: '%s'. Message: %s", $pasien->NmPasien, $this->connector->GetErrorMessage()));
            }
        }else{
            $this->persistence->SaveState("error", sprintf("Data Pasien: %s [%s] tidak boleh dihapus!", $pasien->NmPasien, $pasien->NoRm));
        }
		redirect_url("pelayanan.pasien");
	}

	public function optlistbyentity($EntityId = null, $sPasienId = null) {
		$buff = '<option value="">-- PILIH PASIEN --</option>';
		if ($EntityId == null) {
			print($buff);
			return;
		}
		$pasien = new Pasien();
		$pasiens = $pasien->LoadByEntityId($EntityId);
		foreach ($pasiens as $pasien) {
			if ($pasien->Id == $sPasienId) {
				$buff .= sprintf('<option value="%d" selected="selected">%s - %s</option>', $pasien->Id, $pasien->NoRm, $pasien->NmPasien);
			} else {
				$buff .= sprintf('<option value="%d">%s - %s</option>', $pasien->Id, $pasien->NoRm, $pasien->NmPasien);
			}
		}
		print($buff);
	}
	
	public function getAutoNoRm($jk,$inisial){
	    $pasien = new  Pasien();
	    print ($pasien->GetAutoNoRm($this->userCompanyId,$jk,$inisial));
    }

    public function checkUmur($tglLahir = '2000-01-01'){
	    $date1 = new DateTime($tglLahir);
	    $date2 = $date1->diff(new DateTime(date('Y-m-d')));
	    print($date2->y.'thn '.$date2->m.'bln '.$date2->d.'hr');
    }

    public function history($id = null) {
        $loader = null;
        $pasien = new Pasien();
        $pasien = $pasien->FindById($id);
        if ($pasien == null) {
            $this->persistence->SaveState("error", "Data Pasien yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
            redirect_url("pelayanan.pasien");
        }
        //ambil data yg diperlukan
        $no_rm = $pasien->NoRm;
        $loader = new Pasien();
        $rs = $loader->getHistory($no_rm);
        // untuk kirim variable ke view
        $this->Set("pasien", $pasien);
        $this->Set("rsdata", $rs);
    }

    public function getJsonPasien(){
        $filter = isset($_POST['q']) ? strval($_POST['q']) : '';
        $pasien = new Pasien();
        $paslists = $pasien->GetJSonPasien($this->userCompanyId,$filter);
        echo json_encode($paslists);
    }

    public function getPlainPasien($noRM){
        $pasien = new Pasien();
        $pasien = $pasien->FindByNoRm($noRM);
        $data = null;
        if ($pasien == null){
            $data = "ER|0";
        }else{
            $data = "OK|".$pasien->NmPasien."|".$pasien->Usia."|".$pasien->NoHp;
        }
        print($data);
    }
}
