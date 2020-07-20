<?php
class Pasien extends EntityBase {
	public $Id;
	public $IsDeleted = false;
    public $EntityId;
	public $EntityCd;
    public $NoRm;
    public $NmPasien;
    public $NoKtp;
    public $NoBpjs;
    public $Jkelamin;
    public $GolDarah;
    public $T4Lahir;
    public $TglLahir;
    public $Alamat;
    public $DesaId;
    public $Desa;
    public $KecamatanId;
    public $Kecamatan;
    public $KabkotaId;
    public $Kabkota;
    public $PropinsiId;
    public $Propinsi;
    public $NoHp;
    public $StsKawin;
    public $StsKawinDesc;
    public $PekerjaanId;
    public $Pekerjaan;
    public $TgiBadan;
    public $BrtBadan;
    public $PernahOperasi;
    public $RiwayatAlergi;
    public $RpKeluarga;
    public $JnsPasien;
    public $Fphoto;
    public $CreatebyId;
    public $UpdatebyId;
    public $Umur;
    public $StsPasien = 1;
    public $NmIbu;
    public $LastRanap = null;
    public $LastRalan = null;
    public $NoKK = null;
    public $Usia;

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
        $this->NoRm = $row["no_rm"];
        $this->NmPasien = $row["nm_pasien"];
        $this->NoKtp = $row["no_ktp"];
        $this->NoBpjs = $row["no_bpjs"];
        $this->Jkelamin = $row["jkelamin"];
        $this->GolDarah = $row["gol_darah"];
        $this->T4Lahir = $row["t4_lahir"];
        $this->TglLahir = strtotime($row["tgl_lahir"]);
        $this->Alamat = $row["alamat"];
        $this->DesaId = $row["desa_id"];
        $this->KecamatanId = $row["kecamatan_id"];
        $this->KabkotaId = $row["kabkota_id"];
        $this->PropinsiId = $row["propinsi_id"];
        $this->Desa = $row["nm_desa"];
        $this->Kecamatan = $row["nm_kecamatan"];
        $this->Kabkota = $row["nm_kabkota"];
        $this->Propinsi = $row["nm_propinsi"];
        $this->NoHp = $row["no_hp"];
        $this->StsKawin = $row["sts_kawin"];
        $this->StsKawinDesc = $row["sts_kawin_desc"];
        $this->PekerjaanId = $row["pekerjaan_id"];
        $this->Pekerjaan = $row["nm_pekerjaan"];
        $this->TgiBadan = $row["tgi_badan"];
        $this->BrtBadan = $row["brt_badan"];
        $this->PernahOperasi = $row["pernah_operasi"];
        $this->RiwayatAlergi = $row["riwayat_alergi"];
        $this->RpKeluarga = $row["rp_keluarga"];
        $this->JnsPasien = $row["jns_pasien"];
        $this->Fphoto = $row["fphoto"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
        $this->Umur = $row["umur"];
        $this->StsPasien = $row["sts_pasien"];
        $this->NmIbu = $row["nm_ibu"];
        $this->LastRanap = $row["last_ranap"];
        $this->LastRalan = $row["last_ralan"];
        $this->NoKK = $row["no_kk"];
        $this->Usia = $row["usia"];
	}

    public function FormatTglLahir($format = HUMAN_DATE) {
        return is_int($this->TglLahir) ? date($format, $this->TglLahir) : null;
    }
	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Pasien[]
	 */
	public function LoadAll($orderBy = "a.id", $includeDeleted = false) {
		if ($includeDeleted) {
			$this->connector->CommandText = "SELECT a.*, b.entity_cd FROM vw_m_pasien AS a JOIN sys_company AS b ON a.entity_id = b.entity_id ORDER BY $orderBy";
		} else {
			$this->connector->CommandText = "SELECT a.*, b.entity_cd FROM vw_m_pasien AS a JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.is_deleted = 0 ORDER BY $orderBy";
		}
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Pasien();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	/**
	 * @param int $id
	 * @return Pasien
	 */
	public function FindById($id) {
		$this->connector->CommandText = "SELECT a.*, b.entity_cd FROM vw_m_pasien AS a JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$row = $rs->FetchAssoc();
		$this->FillProperties($row);
		return $this;
	}

    public function FindByNoRm($noRM) {
        $this->connector->CommandText = "SELECT a.*, b.entity_cd FROM vw_m_pasien AS a JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.no_rm = ?no_rm";
        $this->connector->AddParameter("?no_rm", $noRM);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $row = $rs->FetchAssoc();
        $this->FillProperties($row);
        return $this;
    }

    public function FindByNoKK($noKK) {
        $this->connector->CommandText = "SELECT a.*, b.entity_cd FROM vw_m_pasien AS a JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.no_kk = ?no_kk";
        $this->connector->AddParameter("?no_kk", $noKK);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $row = $rs->FetchAssoc();
        $this->FillProperties($row);
        return $this;
    }

	/**
	 * @param int $id
	 * @return Pasien
	 */
	public function LoadById($id) {
		return $this->FindById($id);
	}

	/**
	 * @param int $eti
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Pasien[]
	 */
	public function LoadByEntityId($eti, $orderBy = "a.id", $includeDeleted = false) {
		if ($includeDeleted) {
			$this->connector->CommandText = "SELECT a.*, b.entity_cd FROM vw_m_pasien AS a	JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.entity_id = ?eti ORDER BY $orderBy";
		} else {
			$this->connector->CommandText = "SELECT a.*, b.entity_cd FROM vw_m_pasien AS a JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.is_deleted = 0 AND a.entity_id = ?eti ORDER BY $orderBy";
		}
		$this->connector->AddParameter("?eti", $eti);
		$rs = $this->connector->ExecuteQuery();
        $result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Pasien();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	public function Insert() {
	    $sqx = 'INSERT INTO m_pasien(no_kk,last_ranap,last_ralan,nm_ibu,entity_id,no_rm,nm_pasien,no_ktp,no_bpjs,jkelamin,gol_darah,t4_lahir,tgl_lahir,alamat,desa_id,kecamatan_id,kabkota_id,propinsi_id,no_hp,sts_kawin,pekerjaan_id,tgi_badan,brt_badan,pernah_operasi,riwayat_alergi,rp_keluarga,jns_pasien,fphoto,createby_id,create_time)';
	    $sqx.= ' VALUES(?no_kk,?last_ranap,?last_ralan,?nm_ibu,?entity_id,?no_rm,?nm_pasien,?no_ktp,?no_bpjs,?jkelamin,?gol_darah,?t4_lahir,?tgl_lahir,?alamat,?desa_id,?kecamatan_id,?kabkota_id,?propinsi_id,?no_hp,?sts_kawin,?pekerjaan_id,?tgi_badan,?brt_badan,?pernah_operasi,?riwayat_alergi,?rp_keluarga,?jns_pasien,?fphoto,?createby_id,now())';
		$this->connector->CommandText = $sqx;
		$this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?no_rm", $this->NoRm, "char");
        $this->connector->AddParameter("?nm_pasien", $this->NmPasien);
        $this->connector->AddParameter("?no_ktp", $this->NoKtp, "char");
        $this->connector->AddParameter("?no_bpjs", $this->NoBpjs, "char");
        $this->connector->AddParameter("?jkelamin", $this->Jkelamin);
        $this->connector->AddParameter("?gol_darah", $this->GolDarah);
        $this->connector->AddParameter("?t4_lahir", $this->T4Lahir);
        $this->connector->AddParameter("?tgl_lahir", $this->TglLahir);
        $this->connector->AddParameter("?alamat", $this->Alamat);
        $this->connector->AddParameter("?desa_id", $this->DesaId);
        $this->connector->AddParameter("?kecamatan_id", $this->KecamatanId);
        $this->connector->AddParameter("?kabkota_id", $this->KabkotaId);
        $this->connector->AddParameter("?propinsi_id", $this->PropinsiId);
        $this->connector->AddParameter("?no_hp", $this->NoHp, "char");
        $this->connector->AddParameter("?sts_kawin", $this->StsKawin);
        $this->connector->AddParameter("?pekerjaan_id", $this->PekerjaanId);
        $this->connector->AddParameter("?tgi_badan", $this->TgiBadan);
        $this->connector->AddParameter("?brt_badan", $this->BrtBadan);
        $this->connector->AddParameter("?pernah_operasi", $this->PernahOperasi);
        $this->connector->AddParameter("?riwayat_alergi", $this->RiwayatAlergi);
        $this->connector->AddParameter("?rp_keluarga", $this->RpKeluarga);
        $this->connector->AddParameter("?jns_pasien", $this->JnsPasien);
        $this->connector->AddParameter("?nm_ibu", $this->NmIbu);
        $this->connector->AddParameter("?fphoto", $this->Fphoto);
        $this->connector->AddParameter("?last_ranap", $this->LastRanap);
        $this->connector->AddParameter("?last_ralan", $this->LastRalan);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
        $this->connector->AddParameter("?no_kk", $this->NoKK);
		return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText = 'UPDATE m_pasien 
SET 
no_rm = ?no_rm,
nm_pasien = ?nm_pasien,
no_ktp = ?no_ktp,
no_bpjs = ?no_bpjs, 
jkelamin = ?jkelamin, 
gol_darah = ?gol_darah, 
t4_lahir = ?t4_lahir,
tgl_lahir = ?tgl_lahir, 
alamat = ?alamat, 
desa_id = ?desa_id, 
kecamatan_id = ?kecamatan_id, 
kabkota_id = ?kabkota_id,
propinsi_id = ?propinsi_id,
no_hp = ?no_hp,
sts_kawin = ?sts_kawin,
pekerjaan_id = ?pekerjaan_id,
tgi_badan = ?tgi_badan,
brt_badan = ?brt_badan,
pernah_operasi = ?pernah_operasi,
rp_keluarga = ?rp_keluarga,
riwayat_alergi = ?riwayat_alergi,
jns_pasien = ?jns_pasien,
fphoto = ?fphoto, 
updateby_id = ?updateby_id, 
update_time = now() ,
nm_ibu = ?nm_ibu,
last_ranap = ?last_ranap,
last_ralan = ?last_ralan,
no_kk = ?no_kk
WHERE id = ?id';
        $this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?no_rm", $this->NoRm, "char");
        $this->connector->AddParameter("?nm_pasien", $this->NmPasien);
        $this->connector->AddParameter("?no_ktp", $this->NoKtp, "char");
        $this->connector->AddParameter("?no_bpjs", $this->NoBpjs, "char");
        $this->connector->AddParameter("?jkelamin", $this->Jkelamin);
        $this->connector->AddParameter("?gol_darah", $this->GolDarah);
        $this->connector->AddParameter("?t4_lahir", $this->T4Lahir);
        $this->connector->AddParameter("?tgl_lahir", $this->TglLahir);
        $this->connector->AddParameter("?alamat", $this->Alamat);
        $this->connector->AddParameter("?desa_id", $this->DesaId);
        $this->connector->AddParameter("?kecamatan_id", $this->KecamatanId);
        $this->connector->AddParameter("?kabkota_id", $this->KabkotaId);
        $this->connector->AddParameter("?propinsi_id", $this->PropinsiId);
        $this->connector->AddParameter("?no_hp", $this->NoHp, "char");
        $this->connector->AddParameter("?sts_kawin", $this->StsKawin);
        $this->connector->AddParameter("?pekerjaan_id", $this->PekerjaanId);
        $this->connector->AddParameter("?tgi_badan", $this->TgiBadan);
        $this->connector->AddParameter("?brt_badan", $this->BrtBadan);
        $this->connector->AddParameter("?pernah_operasi", $this->PernahOperasi);
        $this->connector->AddParameter("?riwayat_alergi", $this->RiwayatAlergi);
        $this->connector->AddParameter("?rp_keluarga", $this->RpKeluarga);
        $this->connector->AddParameter("?jns_pasien", $this->JnsPasien);
        $this->connector->AddParameter("?fphoto", $this->Fphoto);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
        $this->connector->AddParameter("?nm_ibu", $this->NmIbu);
        $this->connector->AddParameter("?last_ranap", $this->LastRanap);
        $this->connector->AddParameter("?last_ralan", $this->LastRalan);
        $this->connector->AddParameter("?no_kk", $this->NoKK);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = "Update m_pasien a Set a.is_deleted = 1 WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

    public function GetAutoNoRm($entityId = 1,$jk,$inisial) {
        // function untuk menggenerate kode contact
        $xcode = null;
        $ckode = $jk.left($inisial,1);
        $this->connector->CommandText = "Select fcGetAutoNoRM(?eti,?ins) As valresult";
        $this->connector->AddParameter("?eti", $entityId);
        $this->connector->AddParameter("?ins", $ckode);
        $rs = $this->connector->ExecuteQuery();
        $row = $rs->FetchAssoc();
        return strval($row["valresult"]);
    }

    public function GetPekerjaanList(){
        $this->connector->CommandText = "Select a.id,a.pekerjaan From m_pekerjaan a Order By a.pekerjaan";
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function GetHubPjawab(){
        $this->connector->CommandText = "Select a.code as id,a.short_desc as jns_hubungan From sys_status_code a Where a.key = 'hub_pjawab' Order By a.urutan";
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function GetPropinsiList($id = '71'){
        $sqx = "Select a.id,a.name From m_propinsi a";
        if ($id <> ''){
            $sqx.= " Where a.id = '".$id."'";
        }
        $sqx.= " Order By a.name";
        $this->connector->CommandText = $sqx;
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function getUmur(){
        $date1 = new DateTime(date('Y-m-d',$this->TglLahir));
        $date2 = $date1->diff(new DateTime(date('Y-m-d')));
        return $date2->y.'thn '.$date2->m.'bln '.$date2->d.'hr';
    }

    public function setIsRawat($noRM){
        $cntRawat = 0;
        $sql = "Select count(*) as cntRawat From t_perawatan AS a Where a.no_rm = '".$noRM."' And a.reg_status = 1 And a.is_deleted = 0";
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteQuery();
        $row = $rs->FetchAssoc();
        $cntRawat = $row["cntRawat"];
        if ($cntRawat > 0){
            $sql = "Update m_pasien a Set a.is_dirawat = 1 Where a.no_rm = '".$noRM."'";
        }else{
            $sql = "Update m_pasien a Set a.is_dirawat = 0 Where a.no_rm = '".$noRM."'";
        }
        $this->connector->CommandText = $sql;
        return $this->connector->ExecuteNonQuery();
    }

    public function setIsMeninggal($noRM){
        $sql = "Update m_pasien a Set a.is_dirawat = 0, a.sts_pasien = 0 Where a.no_rm = '".$noRM."'";
        $this->connector->CommandText = $sql;
        return $this->connector->ExecuteNonQuery();
    }

    public function UpdateTBB($noRM,$tgiBadan,$brtBadan){
        $sql = "Update m_pasien a Set a.tgi_badan = $tgiBadan, a.brt_badan = $brtBadan Where a.no_rm = '".$noRM."'";
        $this->connector->CommandText = $sql;
        return $this->connector->ExecuteNonQuery();
    }

    public function setLastRanap($noRM,$tglRanap){
        $sql = "Update m_pasien a Set a.last_ranap = '".$tglRanap."' Where a.no_rm = '".$noRM."'";
        $this->connector->CommandText = $sql;
        return $this->connector->ExecuteNonQuery();
    }

    public function setLastRalan($noRM,$tglRalan){
        $sql = "Update m_pasien a Set a.last_ralan = '".$tglRalan."' Where a.no_rm = '".$noRM."'";
        $this->connector->CommandText = $sql;
        return $this->connector->ExecuteNonQuery();
    }

    public function getHistory($noRM)
    {
        $sql = "Select a.* From vw_t_perawatan AS a Where a.no_rm = '".$noRM."' Order By a.tgl_masuk,a.jam_masuk";
        $this->connector->CommandText = $sql;
        return $this->connector->ExecuteQuery();
    }

    public function GetJSonPasien($entityId = 0, $filter = null,$sort = 'a.nm_pasien',$order = 'ASC') {
        $sql = "SELECT a.id,a.no_rm,a.nm_pasien,a.alamat,a.usia,a.no_ktp,a.no_bpjs,a.no_hp FROM vw_m_pasien as a Where a.is_deleted = 0 ";
        if ($entityId > 0) {
            $sql.= " And a.entity_id = " . $entityId;
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
}
