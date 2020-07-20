<?php
class Sbu extends EntityBase {
	public $Id;
	public $EntityId = 1;
	public $SbuName;
	public $Pic;
	public $IsPayroll = 0;

	public function __construct($id = null) {
		parent::__construct();
		if (is_numeric($id)) {
			$this->LoadById($id);
		}
	}

    public function FillProperties(array $row) {
		$this->Id = $row["id"];
        $this->EntityId = $row["entity_id"];
		$this->SbuName = $row["sbu_name"];
		$this->Pic = $row["pic"];
        $this->IsPayroll = $row["is_payroll"];
	}

    public function LoadAll($entityId = 1,$orderBy = "a.id") {
		if ($entityId > 0) {
			$this->connector->CommandText = "SELECT a.* FROM m_sbu AS a Where a.entity_id = $entityId ORDER BY $orderBy";
		} else {
			$this->connector->CommandText = "SELECT a.* FROM m_sbu AS a ORDER BY $orderBy";
		}
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Sbu();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

    public function LoadById($id) {
		return $this->FindById($id);
	}
    
	public function FindById($id) {
        $this->connector->CommandText = "SELECT a.* FROM m_sbu AS a WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $this->FillProperties($rs->FetchAssoc());
        return $this;
	}

	public function Insert() {
		$this->connector->CommandText = 'INSERT INTO m_sbu(entity_id,sbu_name,pic) VALUES(?entity_id,?sbu_name,?pic)';
		$this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?sbu_name", $this->SbuName);
        $this->connector->AddParameter("?pic", $this->Pic);
		return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText = 'UPDATE m_sbu AS a SET a.entity_id = ?entity_id,a.sbu_name = ?sbu_name,a.pic = ?pic WHERE a.id = ?id';
        $this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?sbu_name", $this->SbuName);
        $this->connector->AddParameter("?pic", $this->Pic);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = "Delete a From m_sbu AS a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}
}

