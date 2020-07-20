<?php
class KlpBilling extends EntityBase {
	public $Id;
	public $IsDeleted = false;
    public $EntityId;
	public $EntityCd;
    public $KdKlpBilling;
    public $KlpBilling;
    public $Keterangan;
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
        $this->KdKlpBilling = $row["kd_klpbilling"];
        $this->KlpBilling = $row["klp_billing"];
        $this->Keterangan = $row["keterangan"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
	}

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return KlpBilling[]
	 */
	public function LoadAll($orderBy = "a.id") {
		$this->connector->CommandText = "SELECT a.*, b.entity_cd FROM m_klpbilling AS a JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.is_deleted = 0 ORDER BY $orderBy";
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new KlpBilling();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	/**
	 * @param int $id
	 * @return KlpBilling
	 */
	public function FindById($id) {
		$this->connector->CommandText = "SELECT a.*, b.entity_cd FROM m_klpbilling AS a JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.id = ?id";
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
	 * @return KlpBilling
	 */
	public function LoadById($id) {
		return $this->FindById($id);
	}

	/**
	 * @param int $eti
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return KlpBilling[]
	 */
	public function LoadByEntityId($eti, $orderBy = "a.id") {
		$this->connector->CommandText = "SELECT a.*, b.entity_cd FROM m_klpbilling AS a JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.is_deleted = 0 AND a.entity_id = ?eti ORDER BY $orderBy";
		$this->connector->AddParameter("?eti", $eti);
		$rs = $this->connector->ExecuteQuery();
        $result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new KlpBilling();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	public function Insert() {
		$this->connector->CommandText = 'INSERT INTO m_klpbilling(entity_id,klp_billing,kd_klpbilling,keterangan,createby_id,create_time) VALUES(?entity_id,?klp_billing,?kd_klpbilling,?keterangan,?createby_id,now())';
		$this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?klp_billing", $this->KlpBilling);
        $this->connector->AddParameter("?kd_klpbilling", $this->KdKlpBilling);
        $this->connector->AddParameter("?keterangan", $this->Keterangan);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
		return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText = 'UPDATE m_klpbilling a SET a.keterangan = ?keterangan, a.entity_id = ?entity_id, a.klp_billing = ?klp_billing, a.kd_klpbilling = ?kd_klpbilling, a.updateby_id = ?updateby_id, a.update_time = now() WHERE a.id = ?id';
        $this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?klp_billing", $this->KlpBilling);
        $this->connector->AddParameter("?kd_klpbilling", $this->KdKlpBilling);
        $this->connector->AddParameter("?keterangan", $this->Keterangan);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = "Delete a From m_klpbilling a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

    public function GetAutoKode($entityId = 0) {
        // function untuk menggenerate kode sku
        $kode = "B".$entityId."001";
        $sqx = "SELECT max(a.kd_klpbilling) AS pKode FROM m_klpbilling a WHERE a.entity_id = $entityId And left(a.kd_klpbilling,2) = 'B".$entityId."'";
        $this->connector->CommandText = $sqx;
        $rs = $this->connector->ExecuteQuery();
        if ($rs != null) {
            $row = $rs->FetchAssoc();
            $pkode = $row["pKode"];
            if (strlen($pkode) == 5){
                $counter = (int)(right($pkode,3))+1;
                $kode = "B".$entityId.str_pad($counter,3,'0',STR_PAD_LEFT);
            }
        }
        return $kode;
    }
}
