<?php
class BillingController extends AppController {
	private $userCompanyId;
	private $userCabangId;
	private $userUid;
    private $userSbuId;

	protected function Initialize() {
		require_once(MODEL . "pelayanan/billing.php");
        require_once(MODEL . "pelayanan/perawatan.php");
		require_once(MODEL . "master/user_admin.php");
		$this->userCompanyId = $this->persistence->LoadState("entity_id");
		$this->userCabangId = $this->persistence->LoadState("cabang_id");
        $this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
        $this->userSbuId = $this->persistence->LoadState("user_sbuid");
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();
		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 40);
		$settings["columns"][] = array("name" => "a.no_reg", "display" => "No. Register", "width" => 90);
        $settings["columns"][] = array("name" => "concat(b.tgl_masuk,' ',b.jam_masuk)", "display" => "Tgl & Jam Masuk", "width" => 90);
        $settings["columns"][] = array("name" => "b.nm_pasien", "display" => "Nama Pasien", "width" => 150);
        $settings["columns"][] = array("name" => "b.jkelamin", "display" => "L/P", "width" => 12);
        $settings["columns"][] = array("name" => "b.umur_pasien", "display" => "Umur", "width" => 70);
        $settings["columns"][] = array("name" => "if(b.cara_bayar = 1,'Umum',if(b.cara_bayar = 2,'BPJS',if(b.cara_bayar = 3,'Asuransi','N/A')))", "display" => "Cara Bayar", "width" => 50);
        $settings["columns"][] = array("name" => "b.kmr_rawat", "display" => "Kamar Rawat", "width" => 70);
        $settings["columns"][] = array("name" => "concat(b.tgl_keluar,' ',b.jam_keluar)", "display" => "Tgl & Jam Keluar", "width" => 90);
        $settings["columns"][] = array("name" => "if(b.lama_rawat <> '0',concat(b.lama_rawat,' hr'),'-')", "display" => "Lama Rawat", "width" => 60, "align" => 'right');
        $settings["columns"][] = array("name" => "format(a.nominal_tindakan,0)", "display" => "Biaya", "width" => 60, "align" => 'right');
        $settings["columns"][] = array("name" => "format(a.nominal_deposit,0)", "display" => "Deposit", "width" => 60, "align" => 'right');
        $settings["columns"][] = array("name" => "format(a.dtg_bpjs,0)", "display" => "BPJS", "width" => 60, "align" => 'right');
        $settings["columns"][] = array("name" => "format(a.dtg_perusahaan,0)", "display" => "Perusahaan", "width" => 60, "align" => 'right');
        $settings["columns"][] = array("name" => "format(a.dtg_sendiri,0)", "display" => "Sendiri", "width" => 60, "align" => 'right');
        $settings["columns"][] = array("name" => "format(a.jum_bayar,0)", "display" => "Jumlah Bayar", "width" => 60, "align" => 'right');
        $settings["columns"][] = array("name" => "a.tgl_bayar", "display" => "Tgl Bayar", "width" => 55);
        $settings["columns"][] = array("name" => "if(a.sts_billing = 1,'Terbayar',if(a.sts_billing = 2,'Batal','Draft'))", "display" => "Status", "width" => 50);
        $settings["columns"][] = array("name" => "a.no_bukti", "display" => "Bukti Kas", "width" => 70);

		$settings["filters"][] = array("name" => "a.no_reg", "display" => "No.Registrasi");
		$settings["filters"][] = array("name" => "b.no_rm", "display" => "No.Rekam Medik");
        $settings["filters"][] = array("name" => "b.nm_pasien", "display" => "Nama Pasien");
        $settings["filters"][] = array("name" => "if(b.cara_bayar = 1,'Umum',if(b.cara_bayar = 2,'BPJS',if(b.cara_bayar = 3,'Asuransi','N/A')))", "display" => "Jenis Billing");
        $settings["filters"][] = array("name" => "if(b.jns_rujukan = 1,'Sendiri',if(b.jns_rujukan = 2,'Dokter',if(b.jns_rujukan = 3,'RS Lain','N/A')))", "display" => "Jenis Rujukan");
        $settings["filters"][] = array("name" => "if(a.sts_billing = 1,'Terbayar',if(a.sts_billing = 2,'Batal','Draft'))", "display" => "Status Bayar");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Daftar Billing Pasien";
            //button action
            if ($acl->CheckUserAccess("billing", "view", "pelayanan")) {
                $settings["actions"][] = array("Text" => "PERINCIAN BILLING", "Url" => "pelayanan.billing/view/%s", "Class" => "bt_view", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Billing terlebih dahulu.\nPERHATIAN: Pilih tepat 1 data",
                    "Confirm" => "View Perincian Billing ini?");
            }
            if ($acl->CheckUserAccess("billing", "add", "pelayanan")) {
                $settings["actions"][] = array("Text" => "separator", "Url" => null);
                $settings["actions"][] = array("Text" => "PROSES PEMBAYARAN", "Url" => "pelayanan.billing/proses/%s", "Class" => "bt_create_new", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Billing terlebih dahulu sebelum proses pembayaran.\nPERHATIAN: Pilih tepat 1 data",
                    "Confirm" => "Proses Pembayaran billing ini?");
            }
            if ($acl->CheckUserAccess("billing", "delete", "pelayanan")) {
                $settings["actions"][] = array("Text" => "separator", "Url" => null);
                $settings["actions"][] = array("Text" => "BATALKAN PEMBAYARAN", "Url" => "pelayanan.billing/batal/%s","Class" => "bt_delete", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Billing terlebih dahulu sebelum proses pembatalan.\nPERHATIAN: Pilih tepat 1 data",
                    "Confirm" => "Anda yakin akan membatalkan pembayaran billing ini?");
            }

			$settings["def_filter"] = 0;
			$settings["def_order"] = 1;
			$settings["singleSelect"] = true;
		} else {
			$settings["from"] = "t_billing AS a Join vw_t_perawatan AS b On a.no_reg = b.no_reg";
            if ($_GET["query"] == "") {
                $_GET["query"] = null;
                $settings["where"] = "a.sts_billing = 0 And a.is_deleted = 0 And b.entity_id = " . $this->userCompanyId;
            } else {
                $settings["where"] = "a.is_deleted = 0  And b.entity_id = " . $this->userCompanyId;
            }
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

    public function proses($id) {
        require_once(MODEL . "pelayanan/pasien.php");
        require_once(MODEL . "master/karyawan.php");
        require_once(MODEL . "master/poliklinik.php");
        require_once(MODEL . "master/dokter.php");
        require_once(MODEL . "pelayanan/tindakan.php");
        require_once(MODEL . "master/kamar.php");
        //require_once(MODEL . "cashbook/cbtrx.php");
        require_once(MODEL . "accounting/jurnal.php");
        $pasien = null;
        $poli = null;
        $dokter = null;
        $petugas = null;
        $loader = null;
        $result = 0;
        $billing = new Billing();
        $log = new UserAdmin();
        $cbayar = 0;
        $perawatan = null;
        $layanan = null;
        //get perawatan data by id
        if (count($this->postData) > 0) {
            $billing->Id = $id;
            $billing->NoReg = $this->GetPostValue("NoReg");
            $billing->TglBayar = strtotime($this->GetPostValue("TglBayar"));
            $billing->NominalTindakan = $this->GetPostValue("NominalTindakan");
            $billing->DtgBpjs = $this->GetPostValue("DtgBpjs");
            $billing->DtgPerusahaan = $this->GetPostValue("DtgPerusahaan");
            $billing->DtgSendiri = $this->GetPostValue("DtgSendiri");
            $billing->JumBayar = $this->GetPostValue("JumBayar");
            $billing->StsBilling = 1;
            $billing->UpdatebyId = $this->userUid;
            $cbayar = $this->GetPostValue("CaraBayar");
            //validasi disini
            if ($this->IsValid($billing,$cbayar)) {
                //get nomor bukti
                $jurnal = new Jurnal();
                //TODO: Jika ditanggung perusahaan jurnalnya gimana?
                if ($cbayar == 1 && $billing->JumBayar > 0) {
                    $jurnal->KdVoucher = 'BKM';
                } else {
                    $jurnal->KdVoucher = 'BPT';
                }
                $jurnal->EntityId = $this->userCompanyId;
                $jurnal->TglVoucher = date('Y-m-d', $billing->TglBayar);
                $billing->NoBukti = $jurnal->GetJurnalDocNo($jurnal->KdVoucher);
                #1. Update data perawatan
                if ($billing->Update($billing->Id)) {
                    //posting ke accounting dan kas
                    $result = $billing->PostingByNoReg($billing->NoReg, $this->userUid);
                    //update status tindakan
                    $layanan = new Tindakan();
                    $layanan = $layanan->SetTindakanStatus($billing->NoReg, 1);
                    //catat aktifitas pada log
                    $log = $log->UserActivityWriter($this->userCabangId, 'pelayanan.perawatan', 'Proses Pembayaran Pasien  No: ' . $billing->NoReg, '-', 'Success');
                    $this->persistence->SaveState("info", sprintf("Proses pembayaran pasien No: %s berhasil!", $billing->NoReg));
                    redirect_url("pelayanan.billing/view/" . $billing->Id);
                } else {
                    $this->persistence->SaveState("error", "Gagal proses pembayaran pasien No: " . $billing->NoReg);
                    redirect_url("pelayanan.billing/proses/" . $billing->Id);
                }
            }
        }else {
            if ($id == null) {
                $this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan proses ini!");
                redirect_url("pelayanan.billing");
            } else {
                //$billing = new Billing();
                $billing = $billing->LoadById($id);
                if ($billing == null) {
                    $this->persistence->SaveState("error", "Data Billing yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
                    redirect_url("pelayanan.billing");
                }
                $perawatan = new Perawatan();
                $perawatan = $perawatan->FindByNoReg($billing->NoReg);
                if ($perawatan == null) {
                    $this->persistence->SaveState("error", "Data Registrasi yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
                    redirect_url("pelayanan.billing");
                }
                //get data pasien
                $pasien = new Pasien();
                $pasien = $pasien->FindByNoRm($perawatan->NoRm);
                if ($pasien->NoRm == null) {
                    $this->Set("error", sprintf("Data Pasien tidak ditemukan!"));
                    redirect_url("pelayanan.billing");
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
        $this->Set("billing", $billing);
        $cek = new Perawatan();
        $cek = $cek->FindByNoReg($billing->NoReg);
        $NoRM = $cek->NoRm;
        $cek =  new Pasien();
        $cek = $cek->FindByNoRm($NoRM);
        $NoKK = $cek->NoKK;
        $cek = new Perawatan();
        $IsDTP = $cek->CheckDitanggungPerusahaan($NoKK);
        $this->Set("IsDTP", $IsDTP);
        $layanan = new Tindakan();
        $layanan = $layanan->LoadByNoReg($perawatan->NoReg);
        $this->Set("layananlist", $layanan);
    }

    private function IsValid(Billing $billing,$cbayar){
	    /*
        if ($cbayar == 1 && ($billing->JumBayar > 0 && ($billing->JumBayar < $billing->NominalTindakan))){
            $this->Set("error", "Jumlah Pembayaran harus = Jumlah Biaya, atau = 0 jika dimasukan piutang");
            return false;
        }
        if ($cbayar == 2 && $billing->JumBayar > 0){
            $this->Set("error", "Jumlah Pembayaran harus = 0 untuk Pasien BPJS");
            return false;
        }
        if ($cbayar == 2 && $billing->DtgBpjs <> $billing->NominalTindakan){
            $this->Set("error", "Jumlah Potongan BPJS harus = Jumlah Biaya yang ditagih");
            return false;
        }
	    */
	    return true;
    }

    public function batal($id){
        require_once(MODEL . "cashbook/transaksi.php");
        require_once(MODEL . "pelayanan/tindakan.php");
        $no_bukti = null;
	    $billing = new Billing();
        $billing = $billing->LoadById($id);
        if ($billing == null) {
            $this->persistence->SaveState("error", "Data Billing yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
            redirect_url("pelayanan.billing");
        }
        if ($billing->StsBilling == 1){
            $no_bukti = $billing->NoBukti;
            $billing->TglBayar = null;
            $billing->NoBukti = null;
            $billing->StsBilling = 0;
            if ($billing->Update($id)== 1){
                //unposting dari accounting
                $result = $billing->UnpostingByNoReg($billing->NoReg,$this->userUid);
                //update status tindakan
                $layanan = new Tindakan();
                $layanan = $layanan->SetTindakanStatus($billing->NoReg,0);
                //catat aktifitas pada log
            }
            $this->persistence->SaveState("info", "Pembayaran Billing No: ".$billing->NoReg." berhasil..");
            redirect_url("pelayanan.billing");
        }else{
            $this->persistence->SaveState("error", "Data Billing No: ".$billing->NoReg." masih berstatus -DRAFT-");
            redirect_url("pelayanan.billing");
        }
    }

    public function view($id) {
        require_once(MODEL . "pelayanan/pasien.php");
        require_once(MODEL . "master/karyawan.php");
        require_once(MODEL . "master/poliklinik.php");
        require_once(MODEL . "master/dokter.php");
        require_once(MODEL . "pelayanan/tindakan.php");
        require_once(MODEL . "master/kamar.php");
        require_once(MODEL . "cashbook/transaksi.php");
        $pasien = null;
        $poli = null;
        $dokter = null;
        $petugas = null;
        $loader = null;
        $billing = new Billing();
        if ($id == null) {
            $this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan proses ini!");
            redirect_url("pelayanan.billing");
        } else {
            $billing = $billing->LoadById($id);
            if ($billing == null) {
                $this->persistence->SaveState("error", "Data Billing yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
                redirect_url("pelayanan.billing");
            }
            $perawatan = new Perawatan();
            $perawatan = $perawatan->FindByNoReg($billing->NoReg);
            if ($perawatan == null) {
                $this->persistence->SaveState("error", "Data Registrasi yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
                redirect_url("pelayanan.billing");
            }
            //get data pasien
            $pasien = new Pasien();
            $pasien = $pasien->FindByNoRm($perawatan->NoRm);
            if ($pasien->NoRm == null) {
                $this->Set("error", sprintf("Data Pasien tidak ditemukan!"));
                redirect_url("pelayanan.billing");
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
        $this->Set("billing", $billing);
        //$layanan = new Tindakan();
        //$layanan = $layanan->LoadByNoReg($perawatan->NoReg);
        //$this->Set("layananlist", $layanan);
        $cek = new Perawatan();
        $cek = $cek->FindByNoReg($billing->NoReg);
        $NoRM = $cek->NoRm;
        $cek =  new Pasien();
        $cek = $cek->FindByNoRm($NoRM);
        $NoKK = $cek->NoKK;
        $cek = new Perawatan();
        $IsDTP = $cek->CheckDitanggungPerusahaan($NoKK);
        $this->Set("IsDTP", $IsDTP);
        $layanan = new Tindakan();
        $layanan = $layanan->LoadBillingTindakanByNoReg($billing->NoReg);
        $this->Set("rslayanan", $layanan);
    }

    public function kwitansi($id) {
        require_once(MODEL . "pelayanan/pasien.php");
        require_once(MODEL . "pelayanan/tindakan.php");
        $pasien = null;
        $loader = null;
        $perawatan = null;
        $layanan = null;
        $billing = new Billing();
        if ($id == null) {
            $this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan proses ini!");
            redirect_url("pelayanan.billing");
        } else {
            $billing = $billing->LoadById($id);
            if ($billing == null) {
                $this->persistence->SaveState("error", "Data Billing yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
                redirect_url("pelayanan.billing");
            }
            if ($billing->StsBilling != 1) {
                $this->persistence->SaveState("error", "Billing belum terbayar!");
                redirect_url("pelayanan.billing");
            }
            $perawatan = new Perawatan();
            $perawatan = $perawatan->FindByNoReg($billing->NoReg);
            if ($perawatan == null) {
                $this->persistence->SaveState("error", "Data Registrasi yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
                redirect_url("pelayanan.billing");
            }
            //get data pasien
            $pasien = new Pasien();
            $pasien = $pasien->FindByNoRm($perawatan->NoRm);
            if ($pasien->NoRm == null) {
                $this->Set("error", sprintf("Data Pasien tidak ditemukan!"));
                redirect_url("pelayanan.billing");
            }
        }
        // untuk kirim variable ke view
        $this->Set("pasien", $pasien);
        $this->Set("perawatan", $perawatan);
        $this->Set("billing", $billing);
        $layanan = new Tindakan();
        $layanan = $layanan->LoadBillingTindakanByNoReg($billing->NoReg);
        $this->Set("rslayanan", $layanan);
    }
}
