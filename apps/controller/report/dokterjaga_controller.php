<?php
class DokterJagaController extends AppController {
	private $userCompanyId;
	private $userCabangId;
	private $userSbuId;
	private $userUid;

	protected function Initialize() {
		$this->userCompanyId = $this->persistence->LoadState("entity_id");
		$this->userCabangId = $this->persistence->LoadState("cabang_id");
        $this->userSbuId = $this->persistence->LoadState("sbu_id");
        $this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
	}

	public function index() {
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/dokter.php");
        require_once(MODEL . "pelayanan/dokterjaga.php");
        // Intelligent time detection...
        $month = (int)date("n");
        $year = (int)date("Y");
        $loader = null;
        if (count($this->postData) > 0) {
            // proses rekap disini
            $sKdDokter = $this->GetPostValue("KdDokter");
            $sStartDate = strtotime($this->GetPostValue("StartDate"));
            $sEndDate = strtotime($this->GetPostValue("EndDate"));
            $sOutput = $this->GetPostValue("Output");
            $sJnsLaporan = $this->GetPostValue("JnsLaporan");
            // tahun transaksi harus sama
            if (date("Y", $sStartDate) == date("Y", $sEndDate)) {
                // ambil data yang diperlukan
                $dokterjaga = new DokterJaga();
                $reports = $dokterjaga->Load4Report($sKdDokter,$sStartDate,$sEndDate,$sJnsLaporan);
            } else {
                $reports = null;
                $this->persistence->SaveState("error", "Maaf Data Transaksi yang diminta harus dari tahun yang sama.");
                redirect_url("report/dokterjaga");
            }
        }else{
            $sJnsLaporan = 1;
            $sKdDokter = null;
            $sStartDate = mktime(0, 0, 0, $month, 1, $year);
            $sEndDate = time();
            $sOutput = 0;
            $reports = null;
        }
        $loader = new Dokter();
        $dokters = $loader->LoadByEntityId($this->userCompanyId);
        $loader = new Company($this->userCompanyId);
        $this->Set("company_name", $loader->CompanyName);
        $this->Set("StartDate",$sStartDate);
        $this->Set("EndDate",$sEndDate);
        $this->Set("Output",$sOutput);
        $this->Set("JnsLaporan",$sJnsLaporan);
        $this->Set("KdDokter",$sKdDokter);
        $this->Set("Reports",$reports);
        $this->Set("Dokters",$dokters);
    }
}
