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
		$settings["columns"][] = array("name" => "b.id", "display" => "ID", "width" => 40);
		$settings["columns"][] = array("name" => "a.no_reg", "display" => "No. Register", "width" => 70);
        $settings["columns"][] = array("name" => "a.jns_rawat_desc", "display" => "Jenis Rawat", "width" => 70);
        $settings["columns"][] = array("name" => "b.nm_poliklinik", "display" => "Poliklinik", "width" => 70);
        //$settings["columns"][] = array("name" => "concat(b.tgl_masuk,' ',b.jam_masuk)", "display" => "Tgl Masuk", "width" => 90);
        $settings["columns"][] = array("name" => "b.tgl_masuk", "display" => "Tgl Masuk", "width" => 60);
        $settings["columns"][] = array("name" => "b.nm_pasien", "display" => "Nama Pasien", "width" => 150);
        $settings["columns"][] = array("name" => "b.jkelamin", "display" => "L/P", "width" => 12);
        $settings["columns"][] = array("name" => "b.umur_pasien", "display" => "Umur", "width" => 70);
        $settings["columns"][] = array("name" => "if(b.cara_bayar = 1,'Umum',if(b.cara_bayar = 2,'BPJS',if(b.cara_bayar = 3,'Asuransi','N/A')))", "display" => "Cara Bayar", "width" => 60);
        $settings["columns"][] = array("name" => "concat(a.tgl_layanan,' ',a.jam_layanan)", "display" => "Waktu Layanan", "width" => 90);
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
			if ($acl->CheckUserAccess("tindakan", "view", "pelayanan")) {
                $settings["actions"][] = array("Text" => "View Tindakan/Layanan", "Url" => "pelayanan.tindakan/add/%s", "Class" => "bt_create_new", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Pasien terlebih dahulu sebelum proses input tindakan.\nPERHATIAN: Pilih tepat 1 data pasien",
                    "Confirm" => "Anda akan dibawa ke halaman untuk Input Data Tindakan.\nDetail data akan di-isi pada halaman berikutnya.\n\nKlik 'OK' untuk berpindah halaman.");
            }

			$settings["def_filter"] = 0;
			$settings["def_order"] = 1;
			$settings["singleSelect"] = true;
		} else {
			$settings["from"] = "vw_t_tindakan AS a Join vw_t_perawatan AS b On a.no_reg = b.no_reg";
            //if ($_GET["query"] == "") {
            //    $_GET["query"] = null;
            //    $settings["where"] = "a.reg_status = 1 And a.is_deleted = 0 And a.entity_id = " . $this->userCompanyId;
            //} else {
                $settings["where"] = "b.is_deleted = 0 And b.entity_id = " . $this->userCompanyId;
            //}
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

    public function add($id) {
        $acl = AclManager::GetInstance();
        require_once(MODEL . "pelayanan/perawatan.php");
        require_once(MODEL . "pelayanan/pasien.php");
        $pasien = null;
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
                $this->persistence->SaveState("error", "Data Pasien yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
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
        // access control
        $anew = false;
        $aedit = false;
        $adelete = false;
        if ($acl->CheckUserAccess("tindakan", "add", "pelayanan")) {
            $anew = true;
        }
        if ($acl->CheckUserAccess("tindakan", "edit", "pelayanan")) {
            $aedit = true;
        }
        if ($acl->CheckUserAccess("tindakan", "delete", "pelayanan")) {
            $adelete = true;
        }
        // untuk kirim variable ke view
        $loader = new Pasien();
        $this->Set("pasien", $pasien);
        $this->Set("perawatan", $perawatan);
        $this->Set("allow_add", $anew);
        $this->Set("allow_edit", $aedit);
        $this->Set("allow_delete", $adelete);
    }

    public function addnew(){
        $layanan = new Tindakan();
        $log = new UserAdmin();
        if (count($this->postData) > 0) {
            // OK user ada kirim data kita proses
            $layanan->NoReg = $this->GetPostValue("NoReg1");
            $layanan->TglLayanan = strtotime($this->GetPostValue("TglLayanan"));
            $layanan->JamLayanan = $this->GetPostValue("JamLayanan");
            $adokter = $this->GetPostValue("aKdDokter");
            if ($adokter != null) {
                $adokter = implode(",", $adokter);
                $layanan->KdDokter = $adokter;
            }else{
                $layanan->KdDokter = null;
            }
            $apetugas = $this->GetPostValue("aKdPetugas");
            if ($apetugas != null) {
                $apetugas = implode(",", $apetugas);
                $layanan->KdPetugas = $apetugas;
            }else{
                $layanan->KdPetugas = null;
            }
            $layanan->JnsRawat = $this->GetPostValue("JnsRawat");
            $layanan->KdJasa = $this->GetPostValue("KdJasa");
            $layanan->NmJasa = $this->GetPostValue("NmJasa");
            $layanan->UraianJasa = $this->GetPostValue("UraianJasa");
            $layanan->TarifHarga = $this->GetPostValue("Tarif");
            $layanan->Qty = $this->GetPostValue("Qty");
            $layanan->Keterangan = $this->GetPostValue("Keterangan");
            $layanan->IsBpjs = $this->GetPostValue("IsBpjs");
            $layanan->CreatebyId = $this->userUid;
            if ($this->DoInsert($layanan)) {
                $log = $log->UserActivityWriter($this->userCabangId, 'pelayanan.tindakan', 'Add New Layanan -> No: ' . $layanan->NoReg . ' - ' . $layanan->KdJasa, '-', 'Success');
                echo json_encode(array(
                    'Id' => $layanan->Id,
                    'NoReg' => $layanan->NoReg
                ));
            } else {
                echo json_encode(array('errorMsg'=>'Save Data gagal!'));
            }
        }
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

    public function update($id){
        $layanan = new Tindakan();
        $log = new UserAdmin();
        if (count($this->postData) > 0) {
            // OK user ada kirim data kita proses
            $layanan->Id = $id;
            $layanan->NoReg = $this->GetPostValue("NoReg1");
            $layanan->TglLayanan = strtotime($this->GetPostValue("TglLayanan"));
            $layanan->JamLayanan = $this->GetPostValue("JamLayanan");
            $adokter = $this->GetPostValue("aKdDokter");
            if ($adokter != null) {
                $adokter = implode(",", $adokter);
                $layanan->KdDokter = $adokter;
            }else{
                $layanan->KdDokter = null;
            }
            $apetugas = $this->GetPostValue("aKdPetugas");
            if ($apetugas != null) {
                $apetugas = implode(",", $apetugas);
                $layanan->KdPetugas = $apetugas;
            }else{
                $layanan->KdPetugas = null;
            }
            $layanan->JnsRawat = $this->GetPostValue("JnsRawat");
            $layanan->KdJasa = $this->GetPostValue("KdJasa");
            $layanan->NmJasa = $this->GetPostValue("NmJasa");
            $layanan->UraianJasa = $this->GetPostValue("UraianJasa");
            $layanan->TarifHarga = $this->GetPostValue("Tarif");
            $layanan->Qty = $this->GetPostValue("Qty");
            $layanan->Keterangan = $this->GetPostValue("Keterangan");
            $layanan->CreatebyId = $this->userUid;
            if ($this->DoUpdate($layanan)) {
                $log = $log->UserActivityWriter($this->userCabangId, 'pelayanan.tindakan', 'Add New Layanan -> No: ' . $layanan->NoReg . ' - ' . $layanan->KdJasa, '-', 'Success');
                echo json_encode(array(
                    'Id' => $layanan->Id,
                    'NoReg' => $layanan->NoReg
                ));
            } else {
                echo json_encode(array('errorMsg'=>'Update Data gagal!'));
            }
        }
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

    public function autocharge($reg_no){
        require_once(MODEL . "pelayanan/perawatan.php");
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
            $norm = $perawatan->NoRm;
            $cekvisit = new Perawatan();
            $jumvisit = $cekvisit->VisitCount($norm);
            if ($jumvisit < 2){
                $isbaru = true;
            }else{
                $isbaru = false;
            }
            $jasa = new Jasa();
            $jasa = $jasa->FindAutoCharge();
            if ($jasa != null) {
                $layanan->KdJasa = $jasa->KdJasa;
                $layanan->NmJasa = $jasa->NmJasa;
                $layanan->UraianJasa = $jasa->UraianJasa;
                $layanan->Qty = 1;
                if ($isbaru){
                    $layanan->TarifHarga = $jasa->KpBaru;
                }else{
                    $layanan->TarifHarga = $jasa->KpLama;
                }
            }
            $layanan->CreatebyId = $this->userUid;
            if ($this->DoInsert($layanan)) {
                $log = $log->UserActivityWriter($this->userCabangId, 'pelayanan.tindakan', 'Add New Layanan -> No: ' . $layanan->NoReg . ' - ' . $layanan->KdJasa, '-', 'Success');
            }
        }
    }

    public function delete(){
      $id = strval($_POST['id']);
      $tindakan = new Tindakan();
      if ($tindakan->Delete($id)){
          echo json_encode(array('success'=>true));
      }else{
          echo json_encode(array('errorMsg'=>'Some errors occured.'));
      }
    }

    public function GetJSonActivePatient(){
        $filter = isset($_POST['q']) ? strval($_POST['q']) : '';
        $apasien = new Perawatan();
        $apasien = $apasien->GetJSonActivePatient($this->userCompanyId,$filter);
        echo json_encode($apasien);
    }

    public function GetJSonTindakanList($noReg){
        $tindakan = new Tindakan();
        $ltindakan = $tindakan->GetTindakanByNoReg($noReg);
        echo json_encode($ltindakan);
    }

    public function GetJSonJasaList($jnr){
        require_once(MODEL . "master/jasa.php");
        $jnsRawat = $jnr;
        $filter = isset($_POST['q']) ? strval($_POST['q']) : '';
        $jasa = new Jasa();
        $jasa = $jasa->GetJSonJasa($this->userCompanyId,$jnsRawat,$filter);
        echo json_encode($jasa);
    }

    public function GetJSonPetugasList(){
        require_once(MODEL . "master/karyawan.php");
        //$filter = isset($_POST['q']) ? strval($_POST['q']) : '';
        $petugas = new Karyawan();
        $petugas = $petugas->GetRsKaryawan($this->userCompanyId);
        echo json_encode($petugas);
    }

    public function GetJSonDokterList(){
        require_once(MODEL . "master/dokter.php");
        //$filter = isset($_POST['q']) ? strval($_POST['q']) : '';
        $dokter = new Dokter();
        $dokter = $dokter->GetRsDokter($this->userCompanyId);
        echo json_encode($dokter);
    }

    public function CheckLimitBpjs($NoReg,$TglTindakan,$KdJasa){
        $retval = null;
        $perawatan = new Perawatan();
        $perawatan = $perawatan->FindByNoReg($NoReg);
        if ($perawatan == null) {
            $retval = '0|0';
        }else{
            if($perawatan->CaraBayar == 2){
               $tindakan = new Tindakan();
               $retval = $tindakan->CheckLimitBpjs($NoReg,$TglTindakan,$KdJasa);
            }else{
                $retval = '0|0';
            }
        }
        print($retval);
    }
}
