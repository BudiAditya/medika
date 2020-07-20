<?php
class Penyakit extends EntityBase {
	public $Id;
	public $KdPenyakit;
    public $NmPenyakit;
    public $CiriCiri;
    public $Keterangan;
    public $KdKelompok;
    public $Status;
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
		$this->KdPenyakit = $row["kd_penyakit"];
        $this->NmPenyakit = $row["nm_penyakit"];
        $this->CiriCiri = $row["ciri_ciri"];
        $this->Keterangan = $row["keterangan"];
        $this->Status = $row["status"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
	}

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Klppenyakit[]
	 */
	public function LoadAll($orderBy = "a.id", $includeDeleted = false) {
		$this->connector->CommandText = "SELECT a.* FROM m_penyakit AS a ORDER BY $orderBy";
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Penyakit();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	/**
	 * @param int $id
	 * @return Klppenyakit
	 */
	public function FindById($id) {
		$this->connector->CommandText = "SELECT a.* FROM m_penyakit AS a WHERE a.id = ?id";
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
	 * @return Klppenyakit
	 */
	public function LoadById($id) {
		return $this->FindById($id);
	}

	public function Insert() {
		$this->connector->CommandText = 'INSERT INTO m_penyakit(nm_penyakit,kd_penyakit,ciri_ciri,keterangan,kd_kelompok,status,createby_id,create_time) VALUES(?nm_penyakit,?kd_penyakit,?ciri_ciri,?keterangan,?kd_kelompok,?status,?createby_id,now())';
        $this->connector->AddParameter("?nm_penyakit", $this->NmPenyakit);
        $this->connector->AddParameter("?kd_penyakit", $this->KdPenyakit);
        $this->connector->AddParameter("?ciri_ciri", $this->CiriCiri);
        $this->connector->AddParameter("?keterangan", $this->Keterangan);
        $this->connector->AddParameter("?kd_kelompok", $this->KdKelompok);
        $this->connector->AddParameter("?status", $this->Status);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
		return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText = 'UPDATE m_penyakit SET ciri_ciri=?ciri_ciri,keterangan=?keterangan,kd_kelompok=?kd_kelompok,status=?status,nm_penyakit = ?nm_penyakit,kd_penyakit = ?kd_penyakit,updateby_id = ?updateby_id, update_time = now() WHERE id = ?id';
        $this->connector->AddParameter("?nm_penyakit", $this->NmPenyakit);
        $this->connector->AddParameter("?kd_penyakit", $this->KdPenyakit);
        $this->connector->AddParameter("?ciri_ciri", $this->CiriCiri);
        $this->connector->AddParameter("?keterangan", $this->Keterangan);
        $this->connector->AddParameter("?kd_kelompok", $this->KdKelompok);
        $this->connector->AddParameter("?status", $this->Status);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = "Delete a From m_penyakit a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

    public function GetJSonPenyakit($filter = null,$sort = 'a.nm_penyakit',$order = 'ASC') {
        $sql = "SELECT a.id,a.kd_penyakit,a.nm_penyakit,a.ciri_ciri FROM m_penyakit as a Where a.is_deleted = 0";
        if ($filter != null){
            $sql.= " And (a.kd_penyakit Like '%$filter%' Or a.nm_penyakit Like '%$filter%' Or a.ciri_ciri Like '%$filter%')";
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
