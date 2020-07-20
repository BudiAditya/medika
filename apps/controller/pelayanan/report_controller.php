<?php
class ReportController extends AppController {
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

	public function medrek() {
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
                redirect_url("pelayanan/report/medrek");
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

    public function jasatindakan() {
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
            $sJnsRawat = $this->GetPostValue("JnsRawat");
            // tahun transaksi harus sama
            if (date("Y", $sStartDate) == date("Y", $sEndDate)) {
                // ambil data yang diperlukan
                $layanan = new Tindakan();
                $reports = $layanan->LoadTindakan4Report($sJnsRawat,$sStartDate,$sEndDate,$sJnsLaporan);
            } else {
                $reports = null;
                $this->persistence->SaveState("error", "Maaf Data Transaksi yang diminta harus dari tahun yang sama.");
                redirect_url("pelayanan/report/jasatindakan");
            }
        }else{
            $sJnsLaporan = 1;
            $sJnsRawat = 0;
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
        $this->Set("JnsRawat",$sJnsRawat);
        $this->Set("reports",$reports);
    }

    public function lab() {
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "pelayanan/tindakan.php");
        // Intelligent time detection...
        $month = (int)date("n");
        $year = (int)date("Y");
        $loader = null;
        $sJnsRawat = 0;
        if (count($this->postData) > 0) {
            // proses rekap disini
            $sStartDate = strtotime($this->GetPostValue("StartDate"));
            $sEndDate = strtotime($this->GetPostValue("EndDate"));
            $sOutput = $this->GetPostValue("Output");
            $sJnsLaporan = $this->GetPostValue("JnsLaporan");
            //$sJnsRawat = $this->GetPostValue("JnsRawat");
            // tahun transaksi harus sama
            if (date("Y", $sStartDate) == date("Y", $sEndDate)) {
                // ambil data yang diperlukan
                $layanan = new Tindakan();
                $reports = $layanan->LoadLab4Report($sStartDate,$sEndDate,$sJnsLaporan);
            } else {
                $reports = null;
                $this->persistence->SaveState("error", "Maaf Data Transaksi yang diminta harus dari tahun yang sama.");
                redirect_url("pelayanan/report/lab");
            }
        }else{
            $sJnsLaporan = 1;
            $sJnsRawat = 0;
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
        $this->Set("JnsRawat",$sJnsRawat);
        $this->Set("reports",$reports);
    }

    public function gizi() {
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "pelayanan/tindakan.php");
        // Intelligent time detection...
        $month = (int)date("n");
        $year = (int)date("Y");
        $loader = null;
        $sJnsRawat = 0;
        if (count($this->postData) > 0) {
            // proses rekap disini
            $sStartDate = strtotime($this->GetPostValue("StartDate"));
            $sEndDate = strtotime($this->GetPostValue("EndDate"));
            $sOutput = $this->GetPostValue("Output");
            $sJnsLaporan = $this->GetPostValue("JnsLaporan");
            //$sJnsRawat = $this->GetPostValue("JnsRawat");
            // tahun transaksi harus sama
            if (date("Y", $sStartDate) == date("Y", $sEndDate)) {
                // ambil data yang diperlukan
                $layanan = new Tindakan();
                $reports = $layanan->LoadGizi4Report($sStartDate,$sEndDate,$sJnsLaporan);
            } else {
                $reports = null;
                $this->persistence->SaveState("error", "Maaf Data Transaksi yang diminta harus dari tahun yang sama.");
                redirect_url("pelayanan/report/gizi");
            }
        }else{
            $sJnsLaporan = 1;
            $sJnsRawat = 0;
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
        $this->Set("JnsRawat",$sJnsRawat);
        $this->Set("reports",$reports);
    }

    public function ambulance() {
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "pelayanan/tindakan.php");
        // Intelligent time detection...
        $month = (int)date("n");
        $year = (int)date("Y");
        $loader = null;
        $sJnsRawat = 0;
        if (count($this->postData) > 0) {
            // proses rekap disini
            $sStartDate = strtotime($this->GetPostValue("StartDate"));
            $sEndDate = strtotime($this->GetPostValue("EndDate"));
            $sOutput = $this->GetPostValue("Output");
            $sJnsLaporan = $this->GetPostValue("JnsLaporan");
            //$sJnsRawat = $this->GetPostValue("JnsRawat");
            // tahun transaksi harus sama
            if (date("Y", $sStartDate) == date("Y", $sEndDate)) {
                // ambil data yang diperlukan
                $layanan = new Tindakan();
                $reports = $layanan->LoadAmbulance4Report($sStartDate,$sEndDate,$sJnsLaporan);
            } else {
                $reports = null;
                $this->persistence->SaveState("error", "Maaf Data Transaksi yang diminta harus dari tahun yang sama.");
                redirect_url("pelayanan/report/ambulance");
            }
        }else{
            $sJnsLaporan = 1;
            $sJnsRawat = 0;
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
        $this->Set("JnsRawat",$sJnsRawat);
        $this->Set("reports",$reports);
    }

    public function jmtikus() {
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "pelayanan/tindakan.php");
        // Intelligent time detection...
        $month = (int)date("n");
        $year = (int)date("Y");
        $loader = null;
        $sJnsPetugas = 0;
        if (count($this->postData) > 0) {
            // proses rekap disini
            $sStartDate = strtotime($this->GetPostValue("StartDate"));
            $sEndDate = strtotime($this->GetPostValue("EndDate"));
            $sOutput = $this->GetPostValue("Output");
            $sJnsLaporan = $this->GetPostValue("JnsLaporan");
            $sJnsPetugas = $this->GetPostValue("JnsPetugas");
            // tahun transaksi harus sama
            if (date("Y", $sStartDate) == date("Y", $sEndDate)) {
                // ambil data yang diperlukan
                $layanan = new Tindakan();
                $reports = $layanan->LoadJmtikus4Report($sJnsPetugas,$sStartDate,$sEndDate,$sJnsLaporan);
            } else {
                $reports = null;
                $this->persistence->SaveState("error", "Maaf Data Transaksi yang diminta harus dari tahun yang sama.");
                redirect_url("pelayanan/report/jmtikus");
            }
        }else{
            $sJnsLaporan = 1;
            $sJnsPetugas = 0;
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
        $this->Set("JnsPetugas",$sJnsPetugas);
        $this->Set("reports",$reports);
    }

    public function jasmed() {
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "pelayanan/tindakan.php");
        // Intelligent time detection...
        $month = (int)date("n");
        $year = (int)date("Y");
        $loader = null;
        //$sJnsPetugas = 0;
        if (count($this->postData) > 0) {
            // proses rekap disini
            $sStartDate = strtotime($this->GetPostValue("StartDate"));
            $sEndDate = strtotime($this->GetPostValue("EndDate"));
            $sOutput = $this->GetPostValue("Output");
            $sJnsLaporan = $this->GetPostValue("JnsLaporan");
            //$sJnsPetugas = $this->GetPostValue("JnsPetugas");
            // tahun transaksi harus sama
            if (date("Y", $sStartDate) == date("Y", $sEndDate)) {
                // ambil data yang diperlukan
                $layanan = new Tindakan();
                $reports = $layanan->LoadJasmed4Report($sStartDate,$sEndDate,$sJnsLaporan);
            } else {
                $reports = null;
                $this->persistence->SaveState("error", "Maaf Data Transaksi yang diminta harus dari tahun yang sama.");
                redirect_url("pelayanan/report/jasmed");
            }
        }else{
            $sJnsLaporan = 1;
            //$sJnsPetugas = 0;
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
        //$this->Set("JnsPetugas",$sJnsPetugas);
        $this->Set("reports",$reports);
    }

    public function dokterjaga() {
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
                redirect_url("pelayanan/report/dokterjaga");
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

    public function kunjungan() {
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/poliklinik.php");
        require_once(MODEL . "master/kamar.php");
        require_once(MODEL . "master/dokter.php");
        require_once(MODEL . "pelayanan/perawatan.php");
        // Intelligent time detection...
        $month = (int)date("n");
        $year = (int)date("Y");
        $loader = null;
        if (count($this->postData) > 0) {
            // proses rekap disini
            $sStsKeluar = $this->GetPostValue("StsKeluar");
            $sJnsPasien = $this->GetPostValue("JnsPasien");
            $sJnsRawat = $this->GetPostValue("JnsRawat");
            $sKdPoli = $this->GetPostValue("KdPoli");
            $sKdKamar = $this->GetPostValue("KdKamar");
            $sKdDokter = $this->GetPostValue("KdDokter");
            $sStartDate = strtotime($this->GetPostValue("StartDate"));
            $sEndDate = strtotime($this->GetPostValue("EndDate"));
            $sOutput = $this->GetPostValue("Output");
            $sJnsLaporan = $this->GetPostValue("JnsLaporan");
            // tahun transaksi harus sama
            if (date("Y", $sStartDate) == date("Y", $sEndDate)) {
                // ambil data yang diperlukan
                $pasien = new Perawatan();
                $reports = $pasien->Load4Report($sJnsPasien,$sJnsRawat,$sKdPoli,$sKdKamar,$sKdDokter,$sStsKeluar,$sStartDate,$sEndDate,$sJnsLaporan);
            } else {
                $reports = null;
                $this->persistence->SaveState("error", "Maaf Data Transaksi yang diminta harus dari tahun yang sama.");
                redirect_url("pelayanan/report/kunjungan");
            }
        }else{
            $sJnsLaporan = 1;
            $sJnsPasien = 0;
            $sJnsRawat = 0;
            $sKdPoli = null;
            $sKdKamar = null;
            $sKdDokter = null;
            $sStsKeluar = -1;
            $sStartDate = mktime(0, 0, 0, $month, 1, $year);
            $sEndDate = time();
            $sOutput = 0;
            $reports = null;
        }
        $loader = new Company($this->userCompanyId);
        $this->Set("company_name", $loader->CompanyName);
        $loader = new Poliklinik();
        $poli = $loader->LoadAll("a.kd_poliklinik");
        $loader = new Kamar();
        $kamar = $loader->LoadAll("a.kd_kamar");
        $loader = new Dokter();
        $dokter = $loader->LoadAll("a.nm_dokter");
        $this->Set("StartDate",$sStartDate);
        $this->Set("EndDate",$sEndDate);
        $this->Set("Output",$sOutput);
        $this->Set("JnsLaporan",$sJnsLaporan);
        $this->Set("JnsPasien",$sJnsPasien);
        $this->Set("JnsRawat",$sJnsRawat);
        $this->Set("poli",$poli);
        $this->Set("kamar",$kamar);
        $this->Set("dokter",$dokter);
        $this->Set("KdPoli",$sKdPoli);
        $this->Set("KdKamar",$sKdKamar);
        $this->Set("KdDokter",$sKdDokter);
        $this->Set("StsKeluar",$sStsKeluar);
        $this->Set("Reports",$reports);
    }

    public function rekapitulasi(){
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
