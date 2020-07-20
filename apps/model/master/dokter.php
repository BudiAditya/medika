<?php
class Dokter extends EntityBase {
	public $Id;
	public $IsDeleted = false;
    public $EntityId;
	public $EntityCd;
	public $KdDokter;
    public $NmDokter;
	public $Spesialisasi;
    public $Alumni;
    public $NoSip;
    public $MulaiPraktek;
    public $DokStatus = 1;
    public $Jkelamin;
    public $T4Lahir;
    public $TglLahir;
    public $Alamat;
    public $SystemuserId;
    public $Handphone;
    public $Fphoto;
    public $HariPraktek;
    public $CreatebyId;
    public $UpdatebyId;
    public $JvPoli = 0;
    public $JvInapum = 0;
    public $JvBpjs = 0;
    public $JkVitel = 0;

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
		$this->KdDokter = $row["kd_dokter"];
        $this->NmDokter = $row["nm_dokter"];
		$this->Spesialisasi = $row["spesialisasi"];
        $this->Alumni = $row["alumni"];
        $this->NoSip = $row["no_sip"];
        $this->MulaiPraktek = strtotime($row["mulai_praktek"]);
        $this->DokStatus = $row["dok_status"];
        $this->Jkelamin = $row["jkelamin"];
        $this->T4Lahir = $row["t4_lahir"];
        $this->TglLahir = strtotime($row["tgl_lahir"]);
        $this->Alamat = $row["alamat"];
        $this->SystemuserId = $row["systemuser_id"];
        $this->Handphone = $row["handphone"];
        $this->Fphoto = $row["fphoto"];
        $this->HariPraktek = $row["hari_praktek"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
        $this->JvPoli = $row["jv_poli"];
        $this->JvInapum = $row["jv_inapum"];
        $this->JvBpjs = $row["jv_bpjs"];
        $this->JkVitel = $row["jk_vitel"];
	}

    public function FormatMulaiPraktek($format = HUMAN_DATE) {
        return is_int($this->MulaiPraktek) ? date($format, $this->MulaiPraktek) : null;
    }

    public function FormatTglLahir($format = HUMAN_DATE) {
        return is_int($this->TglLahir) ? date($format, $this->TglLahir) : null;
    }

    public function LoadAll($orderBy = "a.nm_dokter", $includeDeleted = false) {
		if ($includeDeleted) {
			$this->connector->CommandText =
            "SELECT a.*, b.entity_cd FROM m_dokter AS a JOIN sys_company AS b ON a.entity_id = b.entity_id ORDER BY $orderBy";
		} else {
			$this->connector->CommandText =
            "SELECT a.*, b.entity_cd FROM m_dokter AS a JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.is_deleted = 0 ORDER BY $orderBy";
		}
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Dokter();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	/**
	 * @param int $id
	 * @return Spesialisasi
	 */
	public function FindById($id) {
		$this->connector->CommandText =
        "SELECT a.*, b.entity_cd FROM m_dokter AS a JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$row = $rs->FetchAssoc();
		$this->FillProperties($row);
		return $this;
	}

    public function FindByKode($kode) {
        $this->connector->CommandText =
            "SELECT a.*, b.entity_cd FROM m_dokter AS a JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.kd_dokter = ?kode";
        $this->connector->AddParameter("?kode", $kode);
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
	 * @return Spesialisasi
	 */
	public function LoadById($id) {
		return $this->FindById($id);
	}

	/**
	 * @param int $eti
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Spesialisasi[]
	 */
	public function LoadByEntityId($eti, $orderBy = "a.kd_dokter", $includeDeleted = false) {
		if ($includeDeleted) {
			$this->connector->CommandText = "SELECT a.*, b.entity_cd FROM m_dokter AS a JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.entity_id = ?eti ORDER BY $orderBy";
		} else {
			$this->connector->CommandText = "SELECT a.*, b.entity_cd FROM m_dokter AS a JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.is_deleted = 0 AND a.entity_id = ?eti ORDER BY $orderBy";
		}
		$this->connector->AddParameter("?eti", $eti);
		$rs = $this->connector->ExecuteQuery();
        $result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Dokter();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	public function Insert() {
		$this->connector->CommandText =
        'INSERT INTO m_dokter(jv_poli,jv_inapum,jv_bpjs,jk_vitel,hari_praktek,fphoto,entity_id,kd_dokter,nm_dokter,spesialisasi,alumni,no_sip,mulai_praktek,dok_status,jkelamin,t4_lahir,tgl_lahir,alamat,systemuser_id,handphone,createby_id,create_time)
        VALUES(?jv_poli,?jv_inapum,?jv_bpjs,?jk_vitel,?hari_praktek,?fphoto,?entity_id,?kd_dokter,?nm_dokter,?spesialisasi,?alumni,?no_sip,?mulai_praktek,?dok_status,?jkelamin,?t4_lahir,?tgl_lahir,?alamat,?systemuser_id,?handphone,?createby_id,now())';
		$this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?kd_dokter", $this->KdDokter);
        $this->connector->AddParameter("?nm_dokter", $this->NmDokter);
        $this->connector->AddParameter("?spesialisasi", $this->Spesialisasi);
        $this->connector->AddParameter("?alumni", $this->Alumni);
        $this->connector->AddParameter("?no_sip", $this->NoSip);
        $this->connector->AddParameter("?mulai_praktek", $this->FormatMulaiPraktek(SQL_DATETIME));
        $this->connector->AddParameter("?dok_status", $this->DokStatus);
        $this->connector->AddParameter("?jkelamin", $this->Jkelamin);
        $this->connector->AddParameter("?t4_lahir", $this->T4Lahir);
        $this->connector->AddParameter("?tgl_lahir", $this->FormatTglLahir(SQL_DATETIME));
        $this->connector->AddParameter("?alamat", $this->Alamat);
        $this->connector->AddParameter("?systemuser_id", $this->SystemuserId,"char");
        $this->connector->AddParameter("?handphone", $this->Handphone,"char");
        $this->connector->AddParameter("?fphoto", $this->Fphoto);
        $this->connector->AddParameter("?hari_praktek", $this->HariPraktek);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
        $this->connector->AddParameter("?jv_poli", $this->JvPoli);
        $this->connector->AddParameter("?jv_inapum", $this->JvInapum);
        $this->connector->AddParameter("?jv_bpjs", $this->JvBpjs);
        $this->connector->AddParameter("?jk_vitel", $this->JkVitel);
		return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText =
        'UPDATE m_dokter SET
            entity_id = ?entity_id,
            kd_dokter = ?kd_dokter,
            nm_dokter = ?nm_dokter,
            spesialisasi = ?spesialisasi,
            alumni = ?alumni,
            no_sip = ?no_sip,
            mulai_praktek = ?mulai_praktek,
            dok_status = ?dok_status,
            jkelamin = ?jkelamin,
            t4_lahir = ?t4_lahir,
            tgl_lahir = ?tgl_lahir,
            alamat = ?alamat,
            systemuser_id = ?systemuser_id,
            handphone = ?handphone,
            fphoto = ?fphoto,
            updateby_id = ?updateby_id,
            update_time = now(),
            hari_praktek = ?hari_praktek,
            jv_poli = ?jv_poli,
            jv_inapum = ?jv_inapum,
            jv_bpjs = ?jv_bpjs,
            jk_vitel = ?jk_vitel
        WHERE id = ?id';
        $this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?kd_dokter", $this->KdDokter);
        $this->connector->AddParameter("?nm_dokter", $this->NmDokter);
        $this->connector->AddParameter("?spesialisasi", $this->Spesialisasi);
        $this->connector->AddParameter("?alumni", $this->Alumni);
        $this->connector->AddParameter("?no_sip", $this->NoSip);
        $this->connector->AddParameter("?mulai_praktek", $this->FormatMulaiPraktek(SQL_DATETIME));
        $this->connector->AddParameter("?dok_status", $this->DokStatus);
        $this->connector->AddParameter("?jkelamin", $this->Jkelamin);
        $this->connector->AddParameter("?t4_lahir", $this->T4Lahir);
        $this->connector->AddParameter("?tgl_lahir", $this->FormatTglLahir(SQL_DATETIME));
        $this->connector->AddParameter("?alamat", $this->Alamat);
        $this->connector->AddParameter("?systemuser_id", $this->SystemuserId,"char");
        $this->connector->AddParameter("?handphone", $this->Handphone,"char");
        $this->connector->AddParameter("?fphoto", $this->Fphoto);
        $this->connector->AddParameter("?hari_praktek", $this->HariPraktek);
		$this->connector->AddParameter("?id", $id);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
        $this->connector->AddParameter("?jv_poli", $this->JvPoli);
        $this->connector->AddParameter("?jv_inapum", $this->JvInapum);
        $this->connector->AddParameter("?jv_bpjs", $this->JvBpjs);
        $this->connector->AddParameter("?jk_vitel", $this->JkVitel);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = "Delete a From m_dokter a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);

		return $this->connector->ExecuteNonQuery();
	}

    public function GetAutoKode($entityId = 0) {
        // function untuk menggenerate kode sku
        $kode = "D".$entityId."001";
        $sqx = "SELECT max(a.kd_dokter) AS pKode FROM m_dokter a WHERE a.entity_id = $entityId And left(a.kd_dokter,2) = 'D".$entityId."'";
        $this->connector->CommandText = $sqx;
        $rs = $this->connector->ExecuteQuery();
        if ($rs != null) {
            $row = $rs->FetchAssoc();
            $pkode = $row["pKode"];
            if (strlen($pkode) == 5){
                $counter = (int)(right($pkode,3))+1;
                $kode = "D".$entityId.str_pad($counter,3,'0',STR_PAD_LEFT);
            }
        }
        return $kode;
    }

    public function GetRsDokter($entityId = 0, $orderBy = 'a.nm_dokter') {
        $sql = "SELECT a.id,a.kd_dokter,a.nm_dokter,a.spesialisasi FROM m_dokter as a Where a.is_deleted = 0 ";
        if ($entityId > 0) {
            $sql.= " And a.entity_id = " . $entityId;
        }
        $this->connector->CommandText = $sql;
        $sql.= " Order By $orderBy";
        $this->connector->CommandText = $sql;
        $rows = array();
        $rs = $this->connector->ExecuteQuery();
        while ($row = $rs->FetchAssoc()){
            $rows[] = $row;
        }
        return $rows;
    }

    public function GetJSonDokter($entityId = 0, $filter = null,$sort = 'a.nm_dokter',$order = 'ASC') {
        $sql = "SELECT a.id,a.kd_dokter,a.nm_dokter,a.spesialisasi FROM m_dokter as a Where a.is_deleted = 0 ";
        if ($entityId > 0) {
            $sql.= " And a.entity_id = " . $entityId;
        }
        if ($filter != null){
            $sql.= " And (a.kd_dokter Like '%$filter%' Or a.nm_dokter Like '%$filter%')";
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
