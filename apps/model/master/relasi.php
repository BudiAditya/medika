<?php
class Relasi extends EntityBase {
	public $Id;
	public $IsDeleted = false;
    public $EntityId;
	public $EntityCd;
    public $KdRelasi;
    public $NmRelasi;
    public $JnsRelasi = 0;
    public $Alamat;
    public $Kabkota;
    public $KdPos;
    public $TelNo;
    public $Npwp;
    public $Cperson;
    public $CpJabatan;
    public $LmKredit = 0;
    public $Status = 1;
    public $CreatebyId;
    public $UpdatebyId;

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
        $this->KdRelasi = $row["kd_relasi"];
        $this->NmRelasi = $row["nm_relasi"];
        $this->JnsRelasi = $row["jns_relasi"];
        $this->Alamat = $row["alamat"];
        $this->Kabkota = $row["kabkota"];
        $this->KdPos = $row["kd_pos"];
        $this->TelNo = $row["tel_no"];
        $this->Npwp = $row["npwp"];
        $this->Cperson = $row["cperson"];
        $this->CpJabatan = $row["cp_jabatan"];
        $this->LmKredit = $row["lm_kredit"];
        $this->Status = $row["status"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
	}

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Relasi[]
	 */
	public function LoadAll($orderBy = "a.id", $includeDeleted = false) {
		if ($includeDeleted) {
			$this->connector->CommandText = "SELECT a.*, b.entity_cd FROM m_relasi AS a JOIN sys_company AS b ON a.entity_id = b.entity_id ORDER BY $orderBy";
		} else {
			$this->connector->CommandText = "SELECT a.*, b.entity_cd FROM m_relasi AS a JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.is_deleted = 0 ORDER BY $orderBy";
		}
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Relasi();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	/**
	 * @param int $id
	 * @return Relasi
	 */
	public function FindById($id) {
		$this->connector->CommandText = "SELECT a.*, b.entity_cd FROM m_relasi AS a JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
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
	 * @return Relasi
	 */
	public function LoadById($id) {
		return $this->FindById($id);
	}

	/**
	 * @param int $eti
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Relasi[]
	 */
	public function LoadByEntityId($eti, $orderBy = "a.id", $includeDeleted = false) {
		if ($includeDeleted) {
			$this->connector->CommandText = "SELECT a.*, b.entity_cd FROM m_relasi AS a	JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.entity_id = ?eti ORDER BY $orderBy";
		} else {
			$this->connector->CommandText = "SELECT a.*, b.entity_cd FROM m_relasi AS a JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.is_deleted = 0 AND a.entity_id = ?eti ORDER BY $orderBy";
		}
		$this->connector->AddParameter("?eti", $eti);
		$rs = $this->connector->ExecuteQuery();
        $result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Relasi();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	public function Insert() {
		$this->connector->CommandText = 'INSERT INTO m_relasi(entity_id,nm_relasi,kd_relasi,jns_relasi,alamat,kabkota,kd_pos,tel_no,npwp,cperson,cp_jabatan,lm_kredit,status,createby_id,create_time) VALUES(?entity_id,?nm_relasi,?kd_relasi,?jns_relasi,?alamat,?kabkota,?kd_pos,?tel_no,?npwp,?cperson,?cp_jabatan,?lm_kredit,status,?createby_id,now())';
		$this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?nm_relasi", $this->NmRelasi);
        $this->connector->AddParameter("?kd_relasi", $this->KdRelasi);
        $this->connector->AddParameter("?jns_relasi", $this->JnsRelasi);
        $this->connector->AddParameter("?alamat", $this->Alamat);
        $this->connector->AddParameter("?kabkota", $this->Kabkota);
        $this->connector->AddParameter("?kd_pos", $this->KdPos);
        $this->connector->AddParameter("?tel_no", $this->TelNo, "char");
        $this->connector->AddParameter("?npwp", $this->Npwp, "char");
        $this->connector->AddParameter("?cperson", $this->Cperson);
        $this->connector->AddParameter("?cp_jabatan", $this->CpJabatan);
        $this->connector->AddParameter("?lm_kredit", $this->LmKredit);
        $this->connector->AddParameter("?status", $this->Status);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
		return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText = 'UPDATE m_relasi SET alamat = ?alamat, kabkota = ?kabkota, kd_pos = ?kd_pos, npwp = ?npwp, cperson = ?cperson, cp_jabatan = ?cp_jabatan, tel_no = ?tel_no, lm_kredit = ?lm_kredit, status = ?status, nm_relasi = ?nm_relasi,updateby_id = ?updateby_id, update_time = now() WHERE id = ?id';
        $this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?nm_relasi", $this->NmRelasi);
        $this->connector->AddParameter("?kd_relasi", $this->KdRelasi);
        $this->connector->AddParameter("?jns_relasi", $this->JnsRelasi);
        $this->connector->AddParameter("?alamat", $this->Alamat);
        $this->connector->AddParameter("?kabkota", $this->Kabkota);
        $this->connector->AddParameter("?kd_pos", $this->KdPos);
        $this->connector->AddParameter("?tel_no", $this->TelNo, "char");
        $this->connector->AddParameter("?npwp", $this->Npwp, "char");
        $this->connector->AddParameter("?cperson", $this->Cperson);
        $this->connector->AddParameter("?cp_jabatan", $this->CpJabatan);
        $this->connector->AddParameter("?lm_kredit", $this->LmKredit);
        $this->connector->AddParameter("?status", $this->Status);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = "Delete From m_relasi WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

    public function GetAutoCode($rtype = 1) {
        // function untuk menggenerate kode contact
        $xcode = null;
        $ckode = null;
        $relcd = null;
        $nol = "0000";
        if ($rtype == 1){
            $ckode = "RS-";
        }else{
            $ckode = "RC-";
        }
        $ins = $ckode;
        $this->connector->CommandText = "SELECT kd_relasi FROM m_relasi WHERE LEFT(kd_relasi,3) = ?ins ORDER BY kd_relasi DESC LIMIT 1";
        $this->connector->AddParameter("?ins", $ins);
        $rs = $this->connector->ExecuteQuery();
        if ($rs != null) {
            $row = $rs->FetchAssoc();
            $relcd = $row["kd_relasi"];
            if ($relcd == "") {
                return $xcode = $ins . "0001";
            } else {
                $num = substr($relcd, 4, 4);
                $num = $num + 1;
                return $xcode = $ins . substr($nol, 0, 4 - strlen($num)) . $num;
            }
        } else {
            return $xcode;
        }
    }

    public function GetJSonRelasi($rtype = 0,$entityId = 0, $filter = null,$sort = 'a.nm_relasi',$order = 'ASC') {
        $sql = "SELECT a.id,a.kd_relasi,a.nm_relasi,a.alamat,a.kabkota FROM m_relasi as a Where a.is_deleted = 0";
        if ($entityId > 0){
            $sql.= " and a.entity_id = $entityId";
        }
        if ($rtype > 0){
            $sql.= " and a.jns_relasi = $rtype";
        }
        if ($filter != null){
            $sql.= " And (a.kd_relasi Like '%$filter%' Or a.nm_relasi Like '%$filter%')";
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
