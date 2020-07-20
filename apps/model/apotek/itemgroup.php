<?php
class ItemGroup extends EntityBase {
	public $Id;
    public $GroupCode;
	public $GroupName;
	public $GroupDescs;
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
		$this->GroupCode = $row["group_code"];
		$this->GroupName = $row["group_name"];
		$this->GroupDescs = $row["group_descs"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
	}

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return ItemGroup[]
	 */
	public function LoadAll($orderBy = "a.group_code") {
		$this->connector->CommandText = "SELECT a.* FROM m_apt_item_group AS a Where a.is_deleted = 0 ORDER BY $orderBy";
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new ItemGroup();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	/**
	 * @param int $id
	 * @return ItemGroup
	 */
	public function FindById($id) {
        $this->connector->CommandText = "SELECT a.* FROM m_apt_item_group AS a WHERE a.id = ?id";
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
	 * @return ItemGroup
	 */
	public function LoadById($id) {
		return $this->FindById($id);
	}

	public function Insert() {
		$this->connector->CommandText = 'INSERT INTO m_apt_item_group(group_code,group_name,group_descs,createby_id,create_time) VALUES(?group_code,?group_name,?group_descs,?createby_id,now())';
		$this->connector->AddParameter("?group_code", $this->GroupCode);
        $this->connector->AddParameter("?group_name", $this->GroupName);
        $this->connector->AddParameter("?group_descs", $this->GroupDescs);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
		return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText = 'UPDATE m_apt_item_group SET group_code = ?group_code,group_name = ?group_name,group_descs = ?group_descs,updateby_id = ?updateby_id,update_time = now() WHERE id = ?id';
        $this->connector->AddParameter("?group_code", $this->GroupCode);
        $this->connector->AddParameter("?group_name", $this->GroupName);
        $this->connector->AddParameter("?group_descs", $this->GroupDescs);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = "UPDATE m_apt_item_group SET is_deleted = 1 WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}
}
