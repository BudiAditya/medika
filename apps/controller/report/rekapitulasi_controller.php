<?php
class RekapitulasiController extends AppController {
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

    public function index(){
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "pelayanan/tindakan.php");
        require_once(MODEL . "master/klpjasa.php");
        // Intelligent time detection...
        $month = (int)date("n");
        $year = (int)date("Y");
        $loader = null;
        if (count($this->postData) > 0) {
            // proses rekap disini
            $sKdJenisJasa = $this->GetPostValue("KdJnsJasa");
            $sJnsPasien = $this->GetPostValue("JnsPasien");
            $sJnsRawat = $this->GetPostValue("JnsRawat");
            $sBulan = $this->GetPostValue("Bulan");
            $sTahun = $this->GetPostValue("Tahun");
            $sOutput = $this->GetPostValue("Output");
            $sJnsLaporan = $this->GetPostValue("JnsLaporan");
            $tindakan = new Tindakan();
            $reports = $tindakan->LoadRekapTindakan($sJnsLaporan,$sJnsPasien,$sJnsRawat,$sKdJenisJasa,$sBulan,$sTahun);
        }else{
            $sKdJenisJasa = "";
            $sJnsLaporan = 2;
            $sJnsPasien = 0;
            $sJnsRawat = 0;
            $sBulan = $month;
            $sTahun = $year;
            $sOutput = 0;
            $reports = null;
        }
        //get data
        $loader = new Klpjasa();
        $klpjasa = $loader->LoadByEntityId($this->userCompanyId);
        $this->Set("JnsJasa",$klpjasa);
        $this->Set("KdJnsJasa",$sKdJenisJasa);
        $loader = new Company($this->userCompanyId);
        $this->Set("company_name", $loader->CompanyName);
        $this->Set("Bulan",$sBulan);
        $this->Set("Tahun",$sTahun);
        $this->Set("Output",$sOutput);
        $this->Set("JnsLaporan",$sJnsLaporan);
        $this->Set("JnsPasien",$sJnsPasien);
        $this->Set("JnsRawat",$sJnsRawat);
        $this->Set("Reports",$reports);
    }
}
