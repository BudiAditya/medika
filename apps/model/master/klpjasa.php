<?php
class Klpjasa extends EntityBase {
	public $Id;
	public $IsDeleted = false;
    public $EntityId;
	public $EntityCd;
    public $KdKlpJasa;
    public $KlpJasa;
    public $PjmKlinik = 0;
    public $PjmOperator = 0;
    public $PjmPelaksana = 0;
    public $TrxTypeCode;
    public $CreatebyId;
    public $UpdatebyId;
    public $IsBpjs = 0;

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
        $this->KdKlpJasa = $row["kd_klpjasa"];
        $this->KlpJasa = $row["klp_jasa"];
        $this->PjmKlinik = $row["pjm_klinik"];
        $this->PjmOperator = $row["pjm_operator"];
        $this->PjmPelaksana = $row["pjm_pelaksana"];
        $this->TrxTypeCode = $row["trxtype_code"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
        $this->IsBpjs = $row["is_bpjs"];
	}

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Klpjasa[]
	 */
	public function LoadAll($orderBy = "a.id") {
		$this->connector->CommandText = "SELECT a.*, b.entity_cd FROM m_klpjasa AS a JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.is_deleted = 0 ORDER BY $orderBy";
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Klpjasa();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	/**
	 * @param int $id
	 * @return Klpjasa
	 */
	public function FindById($id) {
		$this->connector->CommandText = "SELECT a.*, b.entity_cd FROM m_klpjasa AS a JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.id = ?id";
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
	 * @return Klpjasa
	 */
	public function LoadById($id) {
		return $this->FindById($id);
	}

	/**
	 * @param int $eti
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Klpjasa[]
	 */
	public function LoadByEntityId($eti, $orderBy = "a.id") {
		$this->connector->CommandText = "SELECT a.*, b.entity_cd FROM m_klpjasa AS a JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.is_deleted = 0 AND a.entity_id = ?eti ORDER BY $orderBy";
		$this->connector->AddParameter("?eti", $eti);
		$rs = $this->connector->ExecuteQuery();
        $result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Klpjasa();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	public function Insert() {
		$this->connector->CommandText = 'INSERT INTO m_klpjasa(is_bpjs,trxtype_code,entity_id,klp_jasa,kd_klpjasa,pjm_klinik,pjm_operator,pjm_pelaksana,createby_id,create_time) VALUES(?is_bpjs,?trxtype_code,?entity_id,?klp_jasa,?kd_klpjasa,?pjm_klinik,?pjm_operator,?pjm_pelaksana,?createby_id,now())';
		$this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?klp_jasa", $this->KlpJasa);
        $this->connector->AddParameter("?kd_klpjasa", $this->KdKlpJasa);
        $this->connector->AddParameter("?pjm_klinik", $this->PjmKlinik);
        $this->connector->AddParameter("?pjm_operator", $this->PjmOperator);
        $this->connector->AddParameter("?pjm_pelaksana", $this->PjmPelaksana);
        $this->connector->AddParameter("?trxtype_code", $this->TrxTypeCode);
        $this->connector->AddParameter("?is_bpjs", $this->IsBpjs);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
		return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText = 'UPDATE m_klpjasa a SET a.is_bpjs = ?is_bpjs, a.trxtype_code = ?trxtype_code, a.pjm_klinik = ?pjm_klinik, a.pjm_operator = ?pjm_operator, a.pjm_pelaksana = ?pjm_pelaksana, a.entity_id = ?entity_id, a.klp_jasa = ?klp_jasa, a.kd_klpjasa = ?kd_klpjasa, a.updateby_id = ?updateby_id, a.update_time = now() WHERE a.id = ?id';
        $this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?klp_jasa", $this->KlpJasa);
        $this->connector->AddParameter("?kd_klpjasa", $this->KdKlpJasa);
        $this->connector->AddParameter("?pjm_klinik", $this->PjmKlinik);
        $this->connector->AddParameter("?pjm_operator", $this->PjmOperator);
        $this->connector->AddParameter("?pjm_pelaksana", $this->PjmPelaksana);
        $this->connector->AddParameter("?trxtype_code", $this->TrxTypeCode);
        $this->connector->AddParameter("?is_bpjs", $this->IsBpjs);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = "Delete a From m_klpjasa a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

    public function GetAutoKode($entityId = 0) {
        // function untuk menggenerate kode sku
        $kode = "K".$entityId."001";
        $sqx = "SELECT max(a.kd_klpjasa) AS pKode FROM m_klpjasa a WHERE a.entity_id = $entityId And left(a.kd_klpjasa,2) = 'K".$entityId."'";
        $this->connector->CommandText = $sqx;
        $rs = $this->connector->ExecuteQuery();
        if ($rs != null) {
            $row = $rs->FetchAssoc();
            $pkode = $row["pKode"];
            if (strlen($pkode) == 5){
                $counter = (int)(right($pkode,3))+1;
                $kode = "K".$entityId.str_pad($counter,3,'0',STR_PAD_LEFT);
            }
        }
        return $kode;
    }
}
