<?php
class Perawatan extends EntityBase {
	public $Id;
	public $IsDeleted = false;
    public $EntityId;
	public $EntityCd;
    public $NoReg;
    public $NoRm;
    public $CaraBayar = 1;
    public $JnsRujukan = 1;
    public $JnsRawat = 1;
    public $TglMasuk;
    public $JamMasuk;
    public $Keluhan;
    public $KdPetugas;
    public $KdPoliklinik;
    public $KdDokter;
    public $NmPjawab;
    public $NoKtpPjawab;
    public $AlamatPjawab;
    public $HubPjawab = 0;
    public $NoHpPjawab;
    public $RegStatus = 1;
    public $TglKeluar = null;
    public $JamKeluar = null;
    public $CreatebyId;
    public $UpdatebyId;
    public $AsalRujukan;
    public $KdKelas;
    public $KdKamar;
    public $KmrRawat;
    public $StsKeluar;
    public $KdUtama;
    public $KdKedua;
    public $DiagnosaUtama;
    public $DiagnosaKedua;
    public $TgiBadan;
    public $BrtBadan;
    public $LamaRawat = 1;

	public function __construct($id = null) {
		parent::__construct();
		if (is_numeric($id)) {
			$this->FindById($id);
		}
	}

	public function FillProperties(array $row) {
		$this->Id = $row["id"];
		$this->IsDeleted = $row["is_deleted"] == 1;
		$this->EntityId = $row["entity_id"];
		$this->EntityCd = $row["entity_cd"];
        $this->NoReg = $row["no_reg"];
        $this->NoRm = $row["no_rm"];
        $this->CaraBayar = $row["cara_bayar"];
        $this->JnsRujukan = $row["jns_rujukan"];
        $this->JnsRawat = $row["jns_rawat"];
        $this->TglMasuk = strtotime($row["tgl_masuk"]);
        $this->JamMasuk = $row["jam_masuk"];
        $this->Keluhan = $row["keluhan"];
        $this->KdPetugas = $row["kd_petugas"];
        $this->KdPoliklinik = $row["kd_poliklinik"];
        $this->KdDokter = $row["kd_dokter"];
        $this->NmPjawab = $row["nm_pjawab"];
        $this->NoKtpPjawab = $row["no_ktp_pjawab"];
        $this->AlamatPjawab = $row["alamat_pjawab"];
        $this->HubPjawab = $row["hub_pjawab"];
        $this->NoHpPjawab = $row["no_hp_pjawab"];
        $this->RegStatus = $row["reg_status"];
        $this->TglKeluar = strtotime($row["tgl_keluar"]);
        $this->JamKeluar = $row["jam_keluar"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
        $this->AsalRujukan = $row["asal_rujukan"];
        $this->KdKelas = $row["kd_kelas"];
        $this->KdKamar = $row["kd_kamar"];
        $this->KmrRawat = $row["kmr_rawat"];
        $this->DiagnosaUtama = $row["diagnosa_utama"];
        $this->DiagnosaKedua = $row["diagnosa_kedua"];
        $this->TgiBadan = $row["tgi_badan"];
        $this->BrtBadan = $row["brt_badan"];
        $this->KdUtama = $row["kd_utama"];
        $this->KdKedua = $row["kd_kedua"];
        $this->LamaRawat = $row["lama_rawat"];
	}

    public function FormatTglMasuk($format = HUMAN_DATE) {
        return is_int($this->TglMasuk) ? date($format, $this->TglMasuk) : date($format);
    }

    public function FormatTglKeluar($format = HUMAN_DATE) {
        return is_int($this->TglKeluar) ? date($format, $this->TglKeluar) : null;
    }

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Perawatan[]
	 */
	public function LoadAll($orderBy = "a.id", $includeDeleted = false) {
		if ($includeDeleted) {
			$this->connector->CommandText = "SELECT a.*, b.entity_cd FROM vw_t_perawatan AS a JOIN sys_company AS b ON a.entity_id = b.entity_id ORDER BY $orderBy";
		} else {
			$this->connector->CommandText = "SELECT a.*, b.entity_cd FROM vw_t_perawatan AS a JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.is_deleted = 0 ORDER BY $orderBy";
		}
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Perawatan();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	/**
	 * @param int $id
	 * @return Perawatan
	 */
	public function FindById($id) {
		$this->connector->CommandText = "SELECT a.*, b.entity_cd FROM vw_t_perawatan AS a JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.id = ?id And a.is_deleted = 0";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$row = $rs->FetchAssoc();
		$this->FillProperties($row);
		return $this;
	}

    public function FindByNoReg($noReg) {
        $this->connector->CommandText = "SELECT a.*, b.entity_cd FROM vw_t_perawatan AS a JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.no_reg = ?no_reg And a.is_deleted = 0";
        $this->connector->AddParameter("?no_reg", $noReg);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $row = $rs->FetchAssoc();
        $this->FillProperties($row);
        return $this;
    }

    public function FindActiveReg($noPasien) {
        $this->connector->CommandText = "SELECT a.*, b.entity_cd FROM vw_t_perawatan AS a JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.no_rm = ?no_rm And a.reg_status = 1 And a.is_deleted = 0";
        $this->connector->AddParameter("?no_rm", $noPasien);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $row = $rs->FetchAssoc();
        $this->FillProperties($row);
        return $this;
    }

    public function FindLastReg($noPasien) {
        $this->connector->CommandText = "SELECT a.*, b.entity_cd FROM vw_t_perawatan AS a JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.no_rm = ?no_rm And a.is_deleted = 0 Order by a.no_reg Desc Limit 1";
        $this->connector->AddParameter("?no_rm", $noPasien);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $row = $rs->FetchAssoc();
        $this->FillProperties($row);
        return $this;
    }

    public function FindByNoRm($noPasien) {
        $this->connector->CommandText = "SELECT a.*, b.entity_cd FROM vw_t_perawatan AS a JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.no_rm = ?no_rm And a.is_deleted = 0 Limit 1";
        $this->connector->AddParameter("?no_rm", $noPasien);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $row = $rs->FetchAssoc();
        $this->FillProperties($row);
        return $this;
    }

    public function VisitCount($noPasien){
	    $visits = 0;
        $this->connector->CommandText = "SELECT count(*) as jum_visit From t_perawatan AS a WHERE a.no_rm = ?no_rm And a.is_deleted = 0";
        $this->connector->AddParameter("?no_rm", $noPasien);
        $rs = $this->connector->ExecuteQuery();
        $row = $rs->FetchAssoc();
        $visits = $row["jum_visit"];
        return $visits;
    }

	/**
	 * @param int $id
	 * @return Perawatan
	 */
	public function LoadById($id) {
		return $this->FindById($id);
	}

	/**
	 * @param int $eti
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Perawatan[]
	 */
	public function LoadByEntityId($eti, $orderBy = "a.id", $includeDeleted = false) {
		if ($includeDeleted) {
			$this->connector->CommandText = "SELECT a.*, b.entity_cd FROM vw_t_perawatan AS a	JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.entity_id = ?eti ORDER BY $orderBy";
		} else {
			$this->connector->CommandText = "SELECT a.*, b.entity_cd FROM vw_t_perawatan AS a JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.is_deleted = 0 AND a.entity_id = ?eti ORDER BY $orderBy";
		}
		$this->connector->AddParameter("?eti", $eti);
		$rs = $this->connector->ExecuteQuery();
        $result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Perawatan();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	public function Insert() {
	    $sql = 'INSERT INTO t_perawatan(kd_utama,kd_kedua,tgi_badan,brt_badan,kd_kelas,kd_kamar,diagnosa_utama,diagnosa_kedua,asal_rujukan,kd_dokter,entity_id,no_reg,no_rm,cara_bayar,jns_rujukan,jns_rawat,tgl_masuk,jam_masuk,keluhan,kd_petugas,kd_poliklinik,nm_pjawab,no_ktp_pjawab,alamat_pjawab,hub_pjawab,no_hp_pjawab,reg_status,createby_id,create_time) VALUES(?kd_utama,?kd_kedua,?tgi_badan,?brt_badan,?kd_kelas,?kd_kamar,?diagnosa_utama,?diagnosa_kedua,?asal_rujukan,?kd_dokter,?entity_id,?no_reg,?no_rm,?cara_bayar,?jns_rujukan,?jns_rawat,?tgl_masuk,?jam_masuk,?keluhan,?kd_petugas,?kd_poliklinik,?nm_pjawab,?no_ktp_pjawab,?alamat_pjawab,?hub_pjawab,?no_hp_pjawab,?reg_status,?createby_id,now())';
	    $rs = null;
		$this->connector->CommandText = $sql;
		$this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?no_rm", $this->NoRm);
        $this->connector->AddParameter("?no_reg", $this->NoReg);
        $this->connector->AddParameter("?cara_bayar", $this->CaraBayar);
        $this->connector->AddParameter("?jns_rujukan", $this->JnsRujukan);
        $this->connector->AddParameter("?jns_rawat", $this->JnsRawat);
        $this->connector->AddParameter("?tgl_masuk", date('Y-m-d',$this->TglMasuk));
        $this->connector->AddParameter("?jam_masuk", $this->JamMasuk);
        $this->connector->AddParameter("?keluhan", $this->Keluhan);
        $this->connector->AddParameter("?kd_petugas", $this->KdPetugas);
        $this->connector->AddParameter("?kd_poliklinik", $this->KdPoliklinik);
        $this->connector->AddParameter("?kd_dokter", $this->KdDokter);
        $this->connector->AddParameter("?nm_pjawab", $this->NmPjawab);
        $this->connector->AddParameter("?no_ktp_pjawab", $this->NoKtpPjawab);
        $this->connector->AddParameter("?alamat_pjawab", $this->AlamatPjawab);
        $this->connector->AddParameter("?hub_pjawab", $this->HubPjawab);
        $this->connector->AddParameter("?no_hp_pjawab", $this->NoHpPjawab, "char");
        $this->connector->AddParameter("?reg_status", $this->RegStatus);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
        $this->connector->AddParameter("?asal_rujukan", $this->AsalRujukan);
        $this->connector->AddParameter("?kd_kelas", $this->KdKelas);
        $this->connector->AddParameter("?kd_kamar", $this->KdKamar);
        $this->connector->AddParameter("?diagnosa_utama", $this->DiagnosaUtama);
        $this->connector->AddParameter("?diagnosa_kedua", $this->DiagnosaKedua);
        $this->connector->AddParameter("?tgi_badan", $this->TgiBadan);
        $this->connector->AddParameter("?brt_badan", $this->BrtBadan);
        $this->connector->AddParameter("?kd_utama", $this->KdUtama);
        $this->connector->AddParameter("?kd_kedua", $this->KdKedua);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1) {
            $this->connector->CommandText = "SELECT LAST_INSERT_ID();";
            $this->Id = (int)$this->connector->ExecuteScalar();
            //$sql = "Insert Into t_perawatan_history Select a.* From t_perawatan a Where a.id = ".$this->Id;
            //$this->connector->CommandText = $sql;
            //$this->connector->ExecuteNonQuery();
        }
        return $rs;
	}

	public function Update($id) {
		$this->connector->CommandText = 'UPDATE t_perawatan SET 
entity_id = ?entity_id,
no_rm = ?no_rm,
no_reg = ?no_reg,
cara_bayar = ?cara_bayar,
jns_rujukan = ?jns_rujukan,
jns_rawat = ?jns_rawat,
tgl_masuk = ?tgl_masuk,
jam_masuk = ?jam_masuk, 
keluhan = ?keluhan, 
kd_petugas = ?kd_petugas,  
kd_poliklinik = ?kd_poliklinik,
kd_dokter = ?kd_dokter,
nm_pjawab = ?nm_pjawab, 
no_ktp_pjawab = ?no_ktp_pjawab,  
alamat_pjawab = ?alamat_pjawab, 
hub_pjawab = ?hub_pjawab,
no_hp_pjawab = ?no_hp_pjawab,
reg_status = ?reg_status, 
updateby_id = ?updateby_id, 
asal_rujukan = ?asal_rujukan,
kd_kelas = ?kd_kelas,
kd_kamar = ?kd_kamar,
diagnosa_utama = ?diagnosa_utama,
diagnosa_kedua = ?diagnosa_kedua,
tgi_badan = ?tgi_badan,
brt_badan = ?brt_badan,
kd_utama = ?kd_utama,
kd_kedua = ?kd_kedua,
update_time = now() WHERE id = ?id';
        $this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?no_rm", $this->NoRm);
        $this->connector->AddParameter("?no_reg", $this->NoReg);
        $this->connector->AddParameter("?cara_bayar", $this->CaraBayar);
        $this->connector->AddParameter("?jns_rujukan", $this->JnsRujukan);
        $this->connector->AddParameter("?jns_rawat", $this->JnsRawat);
        $this->connector->AddParameter("?tgl_masuk", date('Y-m-d',$this->TglMasuk));
        $this->connector->AddParameter("?jam_masuk", $this->JamMasuk);
        $this->connector->AddParameter("?keluhan", $this->Keluhan);
        $this->connector->AddParameter("?kd_petugas", $this->KdPetugas);
        $this->connector->AddParameter("?kd_poliklinik", $this->KdPoliklinik);
        $this->connector->AddParameter("?kd_dokter", $this->KdDokter);
        $this->connector->AddParameter("?nm_pjawab", $this->NmPjawab);
        $this->connector->AddParameter("?no_ktp_pjawab", $this->NoKtpPjawab);
        $this->connector->AddParameter("?alamat_pjawab", $this->AlamatPjawab);
        $this->connector->AddParameter("?hub_pjawab", $this->HubPjawab);
        $this->connector->AddParameter("?no_hp_pjawab", $this->NoHpPjawab,"char");
        $this->connector->AddParameter("?reg_status", $this->RegStatus);
        //$this->connector->AddParameter("?tgl_keluar", date('Y-m-d',$this->TglKeluar));
        //$this->connector->AddParameter("?jam_keluar", $this->JamKeluar);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
        $this->connector->AddParameter("?asal_rujukan", $this->AsalRujukan);
        $this->connector->AddParameter("?kd_kelas", $this->KdKelas);
        $this->connector->AddParameter("?kd_kamar", $this->KdKamar);
        $this->connector->AddParameter("?diagnosa_utama", $this->DiagnosaUtama);
        $this->connector->AddParameter("?diagnosa_kedua", $this->DiagnosaKedua);
        $this->connector->AddParameter("?tgi_badan", $this->TgiBadan);
        $this->connector->AddParameter("?brt_badan", $this->BrtBadan);
        $this->connector->AddParameter("?kd_utama", $this->KdUtama);
        $this->connector->AddParameter("?kd_kedua", $this->KdKedua);
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1) {
            $sql = "Insert Into t_perawatan_history Select a.* From t_perawatan a Where a.id = ".$id;
            $this->connector->CommandText = $sql;
            $this->connector->ExecuteNonQuery();
        }
        return $rs;
	}

	public function UpdatePasienKeluar($id){
        $this->connector->CommandText = "Update t_perawatan a Set a.tgl_keluar = ?tgl_keluar, a.jam_keluar = ?jam_keluar, a.reg_status = 2, a.sts_keluar = ?sts_keluar, updateby_id = ?updateby_id, update_time = now() WHERE a.id = ?id";
        $this->connector->AddParameter("?tgl_keluar", date('Y-m-d',$this->TglKeluar));
        $this->connector->AddParameter("?jam_keluar", $this->JamKeluar);
        $this->connector->AddParameter("?sts_keluar", $this->StsKeluar);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
        $this->connector->AddParameter("?id", $this->Id);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs) {
            $sql = "Insert Into t_perawatan_history Select a.* From t_perawatan a Where a.id = ".$id;
            $this->connector->CommandText = $sql;
            $this->connector->ExecuteNonQuery();
        }
        return $rs;
    }

    public function BatalPasienKeluar($id){
        $this->connector->CommandText = "Update t_perawatan a Set a.tgl_keluar = null, a.jam_keluar = null, a.reg_status = 1, a.sts_keluar = 0, updateby_id = ?updateby_id, update_time = now() WHERE a.id = ?id";
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
        $this->connector->AddParameter("?id", $this->Id);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs) {
            $sql = "Insert Into t_perawatan_history Select a.* From t_perawatan a Where a.id = ".$id;
            $this->connector->CommandText = $sql;
            $this->connector->ExecuteNonQuery();
        }
        return $rs;
    }

	public function Delete($id) {
		$this->connector->CommandText = "Update t_perawatan a Set a.is_deleted = 1 WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

    public function AutoRegNo($tglMasuk = null){
        if ($tglMasuk == null){
            $tglMasuk = strtotime(date('Y-m-d'));
        }
        $zrgNo = null;
        $trgNo = 'R'.right(date('Y', $tglMasuk),2).date('md',$tglMasuk);
        $sqx = "Select coalesce(max(a.no_reg),'".$trgNo."') As trgNo From t_perawatan a Where left(a.no_reg,7) = '".$trgNo."'";
        $this->connector->CommandText = $sqx;
        $rs = $this->connector->ExecuteQuery();
        $row = $rs->FetchAssoc();
        $zrgNo = $row["trgNo"];
        if ($trgNo == $zrgNo){
            $trgNo.= "001";
        }else {
            $trgNo = $trgNo . str_pad(intval(right($zrgNo, 3)) + 1, 3, '0', STR_PAD_LEFT);
        }
        return $trgNo;
    }

    public function GetActivePatient($entityId = 0, $jnsRawat = 0){
        $sqx = "Select a.no_reg,a.no_rm,a.nm_pasien,a.nm_poliklinik From vw_t_perawatan AS a Where a.is_deleted = 0 And a.jns_rawat = $jnsRawat And a.reg_status = 1 And a.entity_id = $entityId Order By a.no_reg";
        $this->connector->CommandText = $sqx;
        return $this->connector->ExecuteQuery();
    }

    public function GetJSonActivePatient($entityId = 0, $filter = null,$sort = 'a.no_reg',$order = 'ASC') {
        $sql = "SELECT a.id,a.no_reg,a.no_rm,a.nm_pasien,a.kmr_rawat,concat(a.tgl_masuk,' ',a.jam_masuk) as tgl_masuk,a.cara_bayar,a.jns_rawat FROM vw_t_perawatan as a Where a.is_deleted = 0 And a.reg_status = 1";
        if ($entityId > 0){
            $sql.= " and a.entity_id = $entityId";
        }
        if ($filter != null){
            $sql.= " And (a.no_reg Like '%$filter%' Or a.nm_pasien Like '%$filter%')";
        }
        $this->connector->CommandText = $sql;
        $data['count'] = $this->connector->ExecuteQuery()->GetNumRows();
        $sql.= " Order By $sort $order";
        $this->connector->CommandText = $sql;
        $rows = array();
        $rs = $this->connector->ExecuteQuery();
        while ($row = $rs->FetchAssoc()){
            $rows[] = $row;
        }
        $result = array('total'=>$data['count'],'rows'=>$rows);
        return $result;
    }

    public function GetJSonListPenyakit(){
        $filter = isset($_POST['q']) ? strval($_POST['q']) : '';
        $apasien = new Perawatan();
        $apasien = $apasien->GetJSonActivePatient($this->userCompanyId,$filter);
        echo json_encode($apasien);
    }

    public function GetJSonInActivePatient($entityId = 0, $filter = null,$sort = 'a.nm_pasien',$order = 'ASC') {
        $sql = "SELECT a.id,a.no_rm,a.nm_pasien,a.no_ktp,a.no_bpjs,a.jkelamin,a.gol_darah,a.t4_lahir,a.tgl_lahir,a.alamat,a.no_hp,a.umur,a.sts_kawin_desc,a.tgi_badan,a.brt_badan FROM vw_m_pasien as a Where a.is_deleted = 0 And a.is_dirawat = 0 And a.sts_pasien = 1";
        if ($entityId > 0){
            $sql.= " and a.entity_id = $entityId";
        }
        if ($filter != null){
            $sql.= " And (a.no_rm Like '%$filter%' Or a.nm_pasien Like '%$filter%')";
        }
        $this->connector->CommandText = $sql;
        $data['count'] = $this->connector->ExecuteQuery()->GetNumRows();
        $sql.= " Order By $sort $order";
        $this->connector->CommandText = $sql;
        $rows = array();
        $rs = $this->connector->ExecuteQuery();
        while ($row = $rs->FetchAssoc()){
            $rows[] = $row;
        }
        $result = array('total'=>$data['count'],'rows'=>$rows);
        return $result;
    }

    public function Load4Report($jnsPasien = 0,$jnsRawat = 0,$kdPoli = null,$kdKamar = null,$kdDokter = null,$stsKeluar = 0,$startDate,$endDate,$jnsLaporan = 1)
    {
        $sql = null;
        if ($jnsLaporan == 1) {
            $sql = "Select a.* From vw_t_perawatan AS a Where a.is_deleted = 0 And a.tgl_masuk BETWEEN ?startdate And ?enddate";
            if ($jnsPasien > 0) {
                $sql .= " And a.cara_bayar = $jnsPasien";
            }
            if ($jnsRawat > 0) {
                $sql .= " And a.jns_rawat = $jnsRawat";
            }
            if ($stsKeluar > -1) {
                if ($stsKeluar == 0) {
                    $sql .= " And a.sts_keluar = 0";
                }else{
                    $sql .= " And a.sts_keluar > 0";
                }
            }
            if ($kdPoli <> null){
                $sql .= " And a.kd_poliklinik = '".$kdPoli."'";
            }
            if ($kdKamar <> null){
                $sql .= " And a.kd_kamar = '".$kdKamar."'";
            }
            if ($kdDokter <> null){
                $sql .= " And a.kd_dokter = '".$kdDokter."'";
            }
            $sql .= " Order By a.tgl_masuk,a.no_reg";
        }elseif ($jnsLaporan == 2){
            $sql = "Select a.jkelamin,a.cara_bayar,a.kmr_rawat,a.nm_poliklinik,a.nm_dokter,coalesce(sum(a.qty),0) AS qty";
            $sql .= " From vw_t_perawatan AS a Where a.tgl_masuk BETWEEN ?startdate And ?enddate";
            if ($jnsPasien > 0) {
                $sql .= " And a.cara_bayar = $jnsPasien";
            }
            if ($jnsRawat > 0) {
                $sql .= " And a.jns_rawat = $jnsRawat";
            }
            if ($kdPoli <> null){
                $sql .= " And a.kd_poliklinik = '".$kdPoli."'";
            }
            if ($kdKamar <> null){
                $sql .= " And a.kd_kamar = '".$kdKamar."'";
            }
            if ($kdDokter <> null){
                $sql .= " And a.kd_dokter = '".$kdDokter."'";
            }
            $sql .= " Group By a.jkelamin, a.cara_bayar,a.kmr_rawat,a.nm_poliklinik,a.nm_dokter";
        }
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        return $this->connector->ExecuteQuery();
    }

    public function CheckDitanggungPerusahaan($noKK){
        //fungsi pengecekan apakah pasien termasuk keluarga karyawan atau keluarga investor berdasarkan nomor kartu keluarga
        if (strlen(trim($noKK)) < 16){
            return 0;
        }else {
            $jdt = 0;
            $sql = "Select a.* From m_karyawan a Where a.no_kk = '" . $noKK . "' And a.is_aktif = 1";
            $this->connector->CommandText = $sql;
            $jdt = $this->connector->ExecuteQuery()->GetNumRows();
            if ($jdt > 0) {
                return 1;
            } else {
                $sql = "Select a.* From m_investor a Where a.no_kk = '" . $noKK . "' And a.is_aktif = 1";
                $this->connector->CommandText = $sql;
                $jdt = $this->connector->ExecuteQuery()->GetNumRows();
                if ($jdt > 0) {
                    return 1;
                } else {
                    return 0;
                }
            }
        }
    }
}
