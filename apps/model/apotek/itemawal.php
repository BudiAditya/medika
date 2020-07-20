<?php
class ItemAwal extends EntityBase {
	public $Id;
    public $UnitCode;
	public $UnitName;
	public $UnitValue = 1;
	public $CreatebyId = 0;
	public $UpdatebyId = 0;

	public function __construct($id = null) {
		parent::__construct();
		if (is_numeric($id)) {
			$this->FindById($id);
		}
	}

	public function FillProperties(array $row) {
		$this->Id = $row["id"];
		$this->UnitCode = $row["unit_code"];
		$this->UnitName = $row["unit_name"];
		$this->UnitValue = $row["unit_value"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
	}

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return ItemAwal[]
	 */
	public function LoadAll($orderBy = "a.unit_code") {
		$this->connector->CommandText = "SELECT a.* FROM m_apt_items_stock_awal AS a Where a.is_deleted = 0 ORDER BY $orderBy";
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new ItemAwal();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	/**
	 * @param int $id
	 * @return ItemAwal
	 */
	public function FindById($id) {
        $this->connector->CommandText = "SELECT a.* FROM m_apt_items_stock_awal AS a WHERE a.id = ?id";
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
	 * @return ItemAwal
	 */
	public function LoadById($id) {
		return $this->FindById($id);
	}

	public function Insert() {
		$this->connector->CommandText = 'INSERT INTO m_apt_items_stock_awal(unit_code,unit_name,unit_value,createby_id,create_time) VALUES(?unit_code,?unit_name,?unit_value,?createby_id,now())';
		$this->connector->AddParameter("?unit_code", $this->UnitCode);
        $this->connector->AddParameter("?unit_name", $this->UnitName);
        $this->connector->AddParameter("?unit_value", $this->UnitValue);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
		return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText = 'UPDATE m_apt_items_stock_awal SET unit_code = ?unit_code,unit_name = ?unit_name,unit_value = ?unit_value,updateby_id = ?updateby_id,update_time = now() WHERE id = ?id';
        $this->connector->AddParameter("?unit_code", $this->UnitCode);
        $this->connector->AddParameter("?unit_name", $this->UnitName);
        $this->connector->AddParameter("?unit_value", $this->UnitValue);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = "UPDATE m_apt_items_stock_awal SET is_deleted = 1 WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}
}
