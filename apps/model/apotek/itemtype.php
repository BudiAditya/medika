<?php
class ItemType extends EntityBase {
	public $Id;
    public $TypeCode;
	public $TypeName;
	public $TypeDescs;
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
		$this->TypeCode = $row["type_code"];
		$this->TypeName = $row["type_name"];
		$this->TypeDescs = $row["type_descs"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
	}

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return ItemType[]
	 */
	public function LoadAll($orderBy = "a.type_code") {
		$this->connector->CommandText = "SELECT a.* FROM m_apt_item_type AS a Where a.is_deleted = 0 ORDER BY $orderBy";
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new ItemType();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	/**
	 * @param int $id
	 * @return ItemType
	 */
	public function FindById($id) {
        $this->connector->CommandText = "SELECT a.* FROM m_apt_item_type AS a WHERE a.id = ?id";
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
	 * @return ItemType
	 */
	public function LoadById($id) {
		return $this->FindById($id);
	}

	public function Insert() {
		$this->connector->CommandText = 'INSERT INTO m_apt_item_type(type_code,type_name,type_descs,createby_id,create_time) VALUES(?type_code,?type_name,?type_descs,?createby_id,now())';
		$this->connector->AddParameter("?type_code", $this->TypeCode);
        $this->connector->AddParameter("?type_name", $this->TypeName);
        $this->connector->AddParameter("?type_descs", $this->TypeDescs);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
		return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText = 'UPDATE m_apt_item_type SET type_code = ?type_code,type_name = ?type_name,type_descs = ?type_descs,updateby_id = ?updateby_id,update_time = now() WHERE id = ?id';
        $this->connector->AddParameter("?type_code", $this->TypeCode);
        $this->connector->AddParameter("?type_name", $this->TypeName);
        $this->connector->AddParameter("?type_descs", $this->TypeDescs);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = "UPDATE m_apt_item_type SET is_deleted = 1 WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}
}
