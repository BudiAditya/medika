<?php
class MedrekController extends AppController {
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
        require_once(MODEL . "pelayanan/tindakan.php");
        // Intelligent time detection...
        $month = (int)date("n");
        $year = (int)date("Y");
        $loader = null;
        if (count($this->postData) > 0) {
            // proses rekap disini
            $sStartDate = strtotime($this->GetPostValue("StartDate"));
            $sEndDate = strtotime($this->GetPostValue("EndDate"));
            $sOutput = $this->GetPostValue("Output");
            $sJnsLaporan = $this->GetPostValue("JnsLaporan");
            //$sJnsPasien = $this->GetPostValue("JnsPasien");
            // tahun transaksi harus sama
            if (date("Y", $sStartDate) == date("Y", $sEndDate)) {
                // ambil data yang diperlukan
                $layanan = new Tindakan();
                $reports = $layanan->LoadMedrek4Report($sStartDate,$sEndDate,$sJnsLaporan);
            } else {
                $reports = null;
                $this->persistence->SaveState("error", "Maaf Data Transaksi yang diminta harus dari tahun yang sama.");
                redirect_url("report/medrek");
            }
        }else{
            $sJnsLaporan = 1;
            //$sJnsPasien = 0;
            $sStartDate = mktime(0, 0, 0, $month, 1, $year);
            $sEndDate = time();
            $sOutput = 0;
            $reports = null;
        }
        $loader = new Company($this->userCompanyId);
        $this->Set("company_name", $loader->CompanyName);
        $this->Set("StartDate",$sStartDate);
        $this->Set("EndDate",$sEndDate);
        $this->Set("Output",$sOutput);
        $this->Set("JnsLaporan",$sJnsLaporan);
        //$this->Set("JnsPasien",$sJnsPasien);
        $this->Set("reports",$reports);
	}
}
