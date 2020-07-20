<?php
class KunjunganController extends AppController {
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
                redirect_url("pelayanan/report/dokterjaga");
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
}
