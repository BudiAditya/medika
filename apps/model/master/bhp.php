<?php
class Bhp extends EntityBase {
	public $Id;
	public $IsDeleted = false;
    public $EntityId;
	public $EntityCd;
    public $HargaDasar = 0;
    public $HargaJual = 0;
    public $KdBhp;
    public $NmBhp;
    public $Satuan;
    public $StockAwal = 0;
    public $StockQty = 0;
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
        $this->HargaDasar = $row["harga_dasar"];
        $this->HargaJual = $row["harga_jual"];
        $this->KdBhp = $row["kd_bhp"];
        $this->NmBhp = $row["nm_bhp"];
        $this->Satuan = $row["satuan"];
        $this->StockAwal = $row["stock_awal"];
        $this->StockQty = $row["stock_qty"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
	}

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Bhp[]
	 */
	public function LoadAll($orderBy = "a.id", $includeDeleted = false) {
		if ($includeDeleted) {
			$this->connector->CommandText = "SELECT a.*, b.entity_cd FROM m_bhp AS a JOIN sys_company AS b ON a.entity_id = b.entity_id ORDER BY $orderBy";
		} else {
			$this->connector->CommandText = "SELECT a.*, b.entity_cd FROM m_bhp AS a JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.is_deleted = 0 ORDER BY $orderBy";
		}
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Bhp();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	/**
	 * @param int $id
	 * @return Bhp
	 */
	public function FindById($id) {
		$this->connector->CommandText = "SELECT a.*, b.entity_cd FROM m_bhp AS a JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.id = ?id";
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
	 * @return Bhp
	 */
	public function LoadById($id) {
		return $this->FindById($id);
	}

	/**
	 * @param int $eti
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Bhp[]
	 */
	public function LoadByEntityId($eti, $orderBy = "a.id", $includeDeleted = false) {
		if ($includeDeleted) {
			$this->connector->CommandText = "SELECT a.*, b.entity_cd FROM m_bhp AS a JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.entity_id = ?eti ORDER BY $orderBy";
		} else {
			$this->connector->CommandText ="SELECT a.*, b.entity_cd FROM m_bhp AS a JOIN sys_company AS b ON a.entity_id = b.entity_id WHERE a.entity_id = ?eti And a.is_deleted = 0 ORDER BY $orderBy";
		}
		$this->connector->AddParameter("?eti", $eti);
		$rs = $this->connector->ExecuteQuery();
        $result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Bhp();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	public function Insert() {
		$this->connector->CommandText = 'INSERT INTO m_bhp(entity_id,kd_bhp,nm_bhp,harga_dasar,harga_jual,satuan,stock_awal,stock_qty,createby_id,create_time) VALUES(?entity_id,?kd_bhp,?nm_bhp,?harga_dasar,?harga_jual,?satuan,?stock_awal,?stock_qty,?createby_id,now())';
		$this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?kd_bhp", $this->KdBhp);
        $this->connector->AddParameter("?nm_bhp", $this->NmBhp);
        $this->connector->AddParameter("?harga_dasar", $this->HargaDasar);
        $this->connector->AddParameter("?harga_jual", $this->HargaJual);
        $this->connector->AddParameter("?satuan", $this->Satuan);
        $this->connector->AddParameter("?stock_awal", $this->StockAwal);
        $this->connector->AddParameter("?stock_qty", $this->StockQty);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
		return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText = 'UPDATE m_bhp SET entity_id = ?entity_id,kd_bhp = ?kd_bhp,nm_bhp = ?nm_bhp,harga_dasar = ?harga_dasar,harga_jual=?harga_jual,satuan = ?satuan,stock_awal = ?stock_awal,stock_qty=?stock_qty,updateby_id = ?updateby_id, update_time = now() WHERE id = ?id';
        $this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?kd_bhp", $this->KdBhp);
        $this->connector->AddParameter("?nm_bhp", $this->NmBhp);
        $this->connector->AddParameter("?harga_dasar", $this->HargaDasar);
        $this->connector->AddParameter("?harga_jual", $this->HargaJual);
        $this->connector->AddParameter("?satuan", $this->Satuan);
        $this->connector->AddParameter("?stock_awal", $this->StockAwal);
        $this->connector->AddParameter("?stock_qty", $this->StockQty);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = "Delete From m_bhp WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}
}
