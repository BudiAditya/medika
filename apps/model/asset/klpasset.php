<?php
class KlpAsset extends EntityBase {
	public $Id;
	public $IsDeleted = false;
    public $EntityId;
	public $EntityCd;
    public $KdKlpAsset;
    public $KlpAsset;
    public $Keterangan;
    public $DeprPerYear = 0;
    public $AssetAccNo;
    public $ApreAccNo;
    public $DeprAccNo;
    public $CostAccNo;
    public $RevnAccNo;
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
        $this->KdKlpAsset = $row["kd_klpasset"];
        $this->KlpAsset = $row["klp_asset"];
        $this->Keterangan = $row["keterangan"];
        $this->AssetAccNo = $row["asset_acc_no"];
        $this->ApreAccNo = $row["apre_acc_no"];
        $this->DeprAccNo = $row["depr_acc_no"];
        $this->CostAccNo = $row["cost_acc_no"];
        $this->RevnAccNo = $row["revn_acc_no"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
	}

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return KlpAsset[]
	 */
	public function LoadAll($orderBy = "a.id") {
		$this->connector->CommandText = "SELECT a.*, b.entity_cd FROM m_klpasset AS a JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.is_deleted = 0 ORDER BY $orderBy";
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new KlpAsset();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	/**
	 * @param int $id
	 * @return KlpAsset
	 */
	public function FindById($id) {
		$this->connector->CommandText = "SELECT a.*, b.entity_cd FROM m_klpasset AS a JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.id = ?id";
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
	 * @return KlpAsset
	 */
	public function LoadById($id) {
		return $this->FindById($id);
	}

	/**
	 * @param int $eti
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return KlpAsset[]
	 */
	public function LoadByEntityId($eti, $orderBy = "a.id") {
		$this->connector->CommandText = "SELECT a.*, b.entity_cd FROM m_klpasset AS a JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.is_deleted = 0 AND a.entity_id = ?eti ORDER BY $orderBy";
		$this->connector->AddParameter("?eti", $eti);
		$rs = $this->connector->ExecuteQuery();
        $result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new KlpAsset();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	public function Insert() {
		$this->connector->CommandText = 'INSERT INTO m_klpasset(keterangan,apre_acc_no,entity_id,klp_asset,kd_klpasset,asset_acc_no,depr_acc_no,cost_acc_no,revn_acc_no,createby_id,create_time) VALUES(?keterangan,?apre_acc_no,?entity_id,?klp_asset,?kd_klpasset,?asset_acc_no,?depr_acc_no,?cost_acc_no,?revn_acc_no,?createby_id,now())';
		$this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?klp_asset", $this->KlpAsset);
        $this->connector->AddParameter("?kd_klpasset", $this->KdKlpAsset);
        $this->connector->AddParameter("?keterangan", $this->Keterangan);
        $this->connector->AddParameter("?asset_acc_no", $this->AssetAccNo);
        $this->connector->AddParameter("?apre_acc_no", $this->ApreAccNo);
        $this->connector->AddParameter("?depr_acc_no", $this->DeprAccNo);
        $this->connector->AddParameter("?cost_acc_no", $this->CostAccNo);
        $this->connector->AddParameter("?revn_acc_no", $this->RevnAccNo);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
		return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText = 'UPDATE m_klpasset a SET a.keterangan = ?keterangan, a.apre_acc_no = ?apre_acc_no, a.revn_acc_no = ?revn_acc_no, a.asset_acc_no = ?asset_acc_no, a.depr_acc_no = ?depr_acc_no, a.cost_acc_no = ?cost_acc_no, a.entity_id = ?entity_id, a.klp_asset = ?klp_asset, a.kd_klpasset = ?kd_klpasset, a.updateby_id = ?updateby_id, a.update_time = now() WHERE a.id = ?id';
        $this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?klp_asset", $this->KlpAsset);
        $this->connector->AddParameter("?kd_klpasset", $this->KdKlpAsset);
        $this->connector->AddParameter("?keterangan", $this->Keterangan);
        $this->connector->AddParameter("?asset_acc_no", $this->AssetAccNo);
        $this->connector->AddParameter("?apre_acc_no", $this->ApreAccNo);
        $this->connector->AddParameter("?depr_acc_no", $this->DeprAccNo);
        $this->connector->AddParameter("?cost_acc_no", $this->CostAccNo);
        $this->connector->AddParameter("?revn_acc_no", $this->RevnAccNo);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = "Delete a From m_klpasset a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

    public function GetAutoKode($entityId = 0) {
        // function untuk menggenerate kode sku
        $kode = "A".$entityId."001";
        $sqx = "SELECT max(a.kd_klpasset) AS pKode FROM m_klpasset a WHERE a.entity_id = $entityId And left(a.kd_klpasset,2) = 'A".$entityId."'";
        $this->connector->CommandText = $sqx;
        $rs = $this->connector->ExecuteQuery();
        if ($rs != null) {
            $row = $rs->FetchAssoc();
            $pkode = $row["pKode"];
            if (strlen($pkode) == 5){
                $counter = (int)(right($pkode,3))+1;
                $kode = "A".$entityId.str_pad($counter,3,'0',STR_PAD_LEFT);
            }
        }
        return $kode;
    }
}
