<?php
class PayrollController extends AppController {
	private $userCompanyId;
	private $userLevel;
	private $userCabangId;
	private $userUid;

	protected function Initialize() {
		require_once(MODEL . "personalia/payroll.php");
        require_once(MODEL . "master/gaji.php");
        require_once(MODEL . "master/karyawan.php");
        require_once(MODEL . "master/user_admin.php");
		$this->userCompanyId = $this->persistence->LoadState("entity_id");
		$this->userCabangId = $this->persistence->LoadState("cabang_id");
		$this->userLevel = $this->persistence->LoadState("user_lvl");
        $this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
	}

    public function index() {
        $router = Router::GetInstance();
        $settings = array();
        $settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 40);
        $settings["columns"][] = array("name" => "a.nik", "display" => "NIK", "width" => 60);
        $settings["columns"][] = array("name" => "a.nama", "display" => "Nama Karyawan", "width" => 150);
        //$settings["columns"][] = array("name" => "a.nm_panggilan", "display" => "Nama Panggilan", "width" => 100);
        $settings["columns"][] = array("name" => "a.alamat", "display" => "Alamat", "width" => 400);
        $settings["columns"][] = array("name" => "c.dept_cd", "display" => "Bagian", "width" => 50);
        $settings["columns"][] = array("name" => "a.jabatan", "display" => "Jabatan", "width" => 50);
        $settings["columns"][] = array("name" => "a.handphone", "display" => "Handphone", "width" => 100);
        $settings["columns"][] = array("name" => "if(a.jkelamin = 'L','Laki-laki',if(a.jkelamin='P','Perempuan','-'))", "display" => "Gender", "width" => 100);

        $settings["filters"][] = array("name" => "a.nik", "display" => "Nik");
        $settings["filters"][] = array("name" => "a.nama", "display" => "Nama Karyawan");

        if (!$router->IsAjaxRequest) {
            $acl = AclManager::GetInstance();
            $settings["title"] = "Data Gaji Karyawan";

            if ($acl->CheckUserAccess("payroll", "edit", "personalia")) {
                $settings["actions"][] = array("Text" => "Data Gaji PT", "Url" => "personalia/payroll/editmaster/1/%s", "Class" => "bt_edit", "ReqId" => 1);
                $settings["actions"][] = array("Text" => "separator", "Url" => null);
                $settings["actions"][] = array("Text" => "Data Gaji Klinik", "Url" => "personalia/payroll/editmaster/2/%s", "Class" => "bt_edit", "ReqId" => 1);
                $settings["actions"][] = array("Text" => "separator", "Url" => null);
                $settings["actions"][] = array("Text" => "Data Gaji Apotek", "Url" => "personalia/payroll/editmaster/3/%s", "Class" => "bt_edit", "ReqId" => 1);
            }

            if ($acl->CheckUserAccess("payroll", "edit", "personalia")) {
                $settings["actions"][] = array("Text" => "separator", "Url" => null);
                $settings["actions"][] = array("Text" => "Proses Hitung Gaji", "Url" => "personalia/payroll/proses", "Class" => "bt_create_new", "ReqId" => 0);
            }

            if ($acl->CheckUserAccess("payroll", "view", "personalia")) {
                $settings["actions"][] = array("Text" => "separator", "Url" => null);
                $settings["actions"][] = array("Text" => "Daftar Gaji Karyawan", "Url" => "personalia/payroll/view", "Class" => "bt_view", "ReqId" => 0);
            }

            $settings["def_filter"] = 0;
            $settings["def_order"] = 1;
            $settings["singleSelect"] = true;
        } else {
            $settings["from"] = "m_karyawan AS a JOIN sys_company AS b ON a.entity_id = b.entity_id LEFT JOIN sys_dept As c on a.dept_id = c.id";
            if ($this->userLevel > 3) {
                $settings["where"] = "a.is_deleted = 0";
            } else {
                $settings["where"] = "a.is_deleted = 0 AND a.entity_id = " . $this->userCompanyId;
            }
        }

        $dispatcher = Dispatcher::CreateInstance();
        $dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
    }

    public function editmaster($sbi = 0,$id = null) {
        require_once(MODEL . "master/department.php");
        require_once(MODEL . "master/dokter.php");
        require_once(MODEL . "master/sbu.php");
        $loader = null;
        $log = new UserAdmin();
        $gaji = new Gaji();
        $fmode = 0; // form mode 1 = New, 2 = Editing
        $karyawan = new Karyawan();
        $karyawan = $karyawan->FindById($id);
        if ($karyawan == null) {
            $this->persistence->SaveState("error", "Data Nama yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
            redirect_url("personalia/payroll");
        }
        if (count($this->postData) > 0) {
            // OK user ada kirim data kita proses
            $gaji->Id = $this->GetPostValue("Id");
            $gaji->SbuId = $sbi;
            $gaji->Nik = $karyawan->Nik;
            $gaji->KdDokter = $this->GetPostValue("KdDokter");
            $gaji->Gapok = $this->GetPostValue("Gapok");
            $gaji->TjJabatan = $this->GetPostValue("TjJabatan");
            $gaji->TjProfesi = $this->GetPostValue("TjProfesi");
            $gaji->BpjsKes = $this->GetPostValue("BpjsKes");
            $gaji->BpjsTk = $this->GetPostValue("BpjsTk");
            $gaji->IsFeeProfit = $this->GetPostValue("IsFeeProfit");
            $gaji->IsFeeJasmed = $this->GetPostValue("IsFeeJasmed");
            $gaji->IsFeeTikhus = $this->GetPostValue("IsFeeTikhus");
            $gaji->UpdatebyId = $this->userUid;
            $fmode = $this->GetPostValue("fMode");
            if ($fmode == 1){
                if ($gaji->Insert()) {
                    $log = $log->UserActivityWriter($this->userCabangId,'master.gaji','Add Data Gaji Karyawan -> NIK: '.$gaji->Nik,'-','Success');
                    $this->persistence->SaveState("info", sprintf("Data Gaji Karyawan: %s telah berhasil dibuat.", $gaji->Nik));
                    redirect_url("personalia/payroll");
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'master.gaji','Update Gaji -> NIK: '.$gaji->Nik,'-','Failed');
                    if ($this->connector->GetHasError()) {
                        if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
                            $this->Set("error", sprintf("Nik: '%s' telah ada pada database !", $gaji->Nik));
                        } else {
                            $this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
                        }
                    }
                }
            }else {
                if ($gaji->Update($gaji->Id)) {
                    $log = $log->UserActivityWriter($this->userCabangId,'master.gaji','Update Data Gaji Karyawan -> NIK: '.$gaji->Nik,'-','Success');
                    $this->persistence->SaveState("info", sprintf("Data Gaji Karyawan: %s telah berhasil diupdate.", $gaji->Nik));
                    redirect_url("personalia/payroll");
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'master.gaji','Update Gaji -> NIK: '.$gaji->Nik,'-','Failed');
                    if ($this->connector->GetHasError()) {
                        if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
                            $this->Set("error", sprintf("Nik: '%s' telah ada pada database !", $gaji->Nik));
                        } else {
                            $this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
                        }
                    }
                }
            }
        } else {
            if ($id == null) {
                $this->persistence->SaveState("error", "Anda harus memilih data karyawan sebelum melakukan edit data !");
                redirect_url("personalia/payroll");
            }
            $gaji = $gaji->FindByNik($this->userCompanyId,$sbi,$karyawan->Nik);
            if ($gaji == null){
                $gaji = new Gaji();
                $fmode = 1;
            }else{
                $fmode = 2;
            }
        }
        // load data for combo box
        $loader = new Department();
        $depts = $loader->LoadAll();
        $loader = new Dokter();
        $dokters = $loader->LoadByEntityId($this->userCompanyId);
        $loader = new Sbu();
        $sbus = $loader->LoadAll($this->userCompanyId);
        // untuk kirim variable ke view
        $this->Set("kid", $id);
        $this->Set("sbi", $sbi);
        $this->Set("karyawan", $karyawan);
        $this->Set("gaji", $gaji);
        $this->Set("depts", $depts);
        $this->Set("dokters", $dokters);
        $this->Set("sbus", $sbus);
        $this->Set("fmode", $fmode);
    }

	public function proses() {
        require_once(MODEL . "master/sbu.php");
	    $entityId = $this->userCompanyId;
        $jdt = 0;
        $payroll = null;
        $loader = null;
        if (count($this->postData) > 0) {
            $paySbuId = $this->GetPostValue("SbuId");
            $payTahun = $this->GetPostValue("Tahun");
            $payBulan = $this->GetPostValue("Bulan");
            $payroll = new Payroll();
            $jdt = $payroll->GeneratePayroll($entityId,$paySbuId, $payTahun,$payBulan,$this->userUid);
            if ($jdt > 0){
                redirect_url("personalia/payroll/view/".$paySbuId.'/'.$payTahun."/".$payBulan);
            }
        }else{
            $paySbuId = 0;
            $payTahun = date('Y');
            $payBulan = date('m');
        }
        $loader = new Sbu();
        $sbus = $loader->LoadAll($this->userCompanyId);
        $this->Set("dtpayroll", $payroll);
        $this->Set("Tahun", $payTahun);
        $this->Set("Bulan", $payBulan);
        $this->Set("SbuList", $sbus);
        $this->Set("SbuId", $paySbuId);
	}

    public function view($sbi = 1, $thn = 0, $bln = 0){
        require_once(MODEL . "master/sbu.php");
	    $payroll = null;
        if (count($this->postData) > 0) {
            $paySbuId = $this->GetPostValue("SbuId");
            $payTahun = $this->GetPostValue("Tahun");
            $payBulan = $this->GetPostValue("Bulan");
            $payOutput = $this->GetPostValue("Output");
        }else{
            if ($thn == 0) {
                $payTahun = date('Y');
            }else{
                $payTahun = $thn;
            }
            if($bln == 0) {
                $payBulan = date('m');
            }else{
                $payBulan = $bln;
            }
            $paySbuId = $sbi;
            $payOutput = 0;
        }
        $loader = new Sbu();
        $sbus = $loader->LoadAll($this->userCompanyId);
        $payroll = new Payroll();
        $payroll = $payroll->LoadAll($this->userCompanyId,$paySbuId,$payTahun,$payBulan);
        $loader = new Sbu();
        $sbuName = $loader->LoadById($paySbuId)->SbuName;
        $this->Set("dtpayroll", $payroll);
        $this->Set("Tahun", $payTahun);
        $this->Set("Bulan", $payBulan);
        $this->Set("SbuList", $sbus);
        $this->Set("SbuId", $paySbuId);
        $this->Set("SbuName", $sbuName);
        $this->Set("Output", $payOutput);
    }

    public function edit($id = null) {
        $payslip = new Payroll();
        $log = new UserAdmin();
        if (count($this->postData) > 0) {
            // OK user ada kirim data kita proses
            $payslip->Id = $id;
            $payslip->Nik = $this->GetPostValue("Nik");
            $payslip->Tahun = $this->GetPostValue("Tahun");
            $payslip->Bulan = $this->GetPostValue("Bulan");
            $payslip->Gapok = $this->GetPostValue("Gapok");
            $payslip->TjJabatan = $this->GetPostValue("TjJabatan");
            $payslip->TjProfesi = $this->GetPostValue("TjProfesi");
            $payslip->BpjsKes = $this->GetPostValue("BpjsKes");
            $payslip->BpjsTk = $this->GetPostValue("BpjsTk");
            $payslip->FeeProfit = $this->GetPostValue("FeeProfit");
            $payslip->FeeJasmed = $this->GetPostValue("FeeJasmed");
            $payslip->FeeTikhus = $this->GetPostValue("FeeTikhus");
            $payslip->Thr = $this->GetPostValue("Thr");
            $payslip->PotAbsensi = $this->GetPostValue("PotAbsensi");
            $payslip->PotPiutang = $this->GetPostValue("PotPiutang");
            $payslip->PotBpjsKes = $this->GetPostValue("PotBpjsKes");
            $payslip->PotLain = $this->GetPostValue("PotLain");
            $payslip->TrxStatus = 1;
            $payslip->UpdatebyId = $this->userUid;
            if ($payslip->Update($id)) {
                $log = $log->UserActivityWriter($this->userCabangId,'personalia.payroll','Update Slip Gaji Karyawan -> NIK: '.$payslip->Nik.' - '.$payslip->Nama.' Periode:'.$payslip->Tahun.$payslip->Bulan,'-','Success');
                $this->persistence->SaveState("info", sprintf("Data Slip Gaji Karyawan: %s NIK: %s telah berhasil diupdate.", $payslip->Nama, $payslip->Nik));
                redirect_url("personalia/payroll/view/".$payslip->Tahun."/".$payslip->Bulan);
            }
        } else {
            if ($id == null) {
                $this->persistence->SaveState("error", "Anda harus memilih data slip gaji sebelum melakukan edit data !");
                redirect_url("personalia/payroll/view/".$payslip->Tahun."/".$payslip->Bulan);
            }
            $payslip = $payslip->FindById($id);
            if ($payslip == null) {
                $this->persistence->SaveState("error", "Data Slip Gaji tidak ditemukan!");
                redirect_url("personalia/payroll");
            }
        }
        // untuk kirim variable ke view
        $this->Set("payslip", $payslip);
    }

    public function delete($thn,$bln,$id = null) {
        $payslip = new Payroll();
        if ($id == null) {
            $this->persistence->SaveState("error", "Anda harus memilih data slip gaji sebelum melakukan hapus data !");
            redirect_url("personalia/payroll/view/".$thn."/".$bln);
        }
        $payslip = $payslip->FindById($id);
        if ($payslip == null) {
            $this->persistence->SaveState("error", "Data Slip Gaji tidak ditemukan!");
        }else {
            if ($payslip->Delete($id)) {
                $this->persistence->SaveState("info", "Data Slip Gaji berhasil dihapus..");
            }
        }
        redirect_url("personalia/payroll/view/".$thn."/".$bln);
    }

    public function card(){
        require_once(MODEL . "master/sbu.php");
        require_once(MODEL . "master/karyawan.php");
        $payroll = null;
        if (count($this->postData) > 0) {
            $paySbuId = $this->GetPostValue("SbuId");
            $payTahun = $this->GetPostValue("Tahun");
            $payNik = $this->GetPostValue("Nik");
            $payOutput = $this->GetPostValue("Output");
        }else{
            $payTahun = date('Y');
            $payNik = null;
            $paySbuId = 1;
            $payOutput = 0;
        }
        $loader = new Sbu();
        $sbus = $loader->LoadAll($this->userCompanyId);
        $payroll = new Payroll();
        $payroll = $payroll->LoadCard($this->userCompanyId,$paySbuId,$payTahun,$payNik);
        $loader = new Sbu();
        $sbuName = $loader->LoadById($paySbuId)->SbuName;
        $loader = new Karyawan();
        $listKaryawan = $loader->LoadByEntityId($this->userCompanyId);
        if ($payNik != null) {
            $loader = new Karyawan();
            $nmKaryawan = $loader->FindByNik($this->userCompanyId, $payNik);
            $nmKaryawan = $nmKaryawan->Nik . ' - ' . $nmKaryawan->Nama;
        }else{
            $nmKaryawan = "";
        }
        $this->Set("lstKaryawan", $listKaryawan);
        $this->Set("dtpayroll", $payroll);
        $this->Set("Tahun", $payTahun);
        $this->Set("Nik", $payNik);
        $this->Set("SbuList", $sbus);
        $this->Set("SbuId", $paySbuId);
        $this->Set("SbuName", $sbuName);
        $this->Set("NmKaryawan", $nmKaryawan);
        $this->Set("Output", $payOutput);
    }
}
