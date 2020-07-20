<?php
class Kamar extends EntityBase {
	public $Id;
	public $IsDeleted = false;
    public $EntityId;
	public $EntityCd;
    public $KdKamar;
    public $NmKamar;
    public $KdKelas;
    public $Tarif;
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
		$this->IsDeleted = $row["is_deleted"] == 1;
		$this->EntityId = $row["entity_id"];
		$this->EntityCd = $row["entity_cd"];
        $this->KdKamar = $row["kd_kamar"];
        $this->NmKamar = $row["nm_kamar"];
        $this->KdKelas = $row["kd_kelas"];
        $this->Tarif = $row["tarif"];
        $this->Status = $row["status"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
	}

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Kamar[]
	 */
	public function LoadAll($orderBy = "a.id", $includeDeleted = false) {
		if ($includeDeleted) {
			$this->connector->CommandText =
"SELECT a.*, b.entity_cd
FROM m_kamar AS a
	JOIN sys_company AS b ON a.entity_id = b.entity_id
ORDER BY $orderBy";
		} else {
			$this->connector->CommandText =
"SELECT a.*, b.entity_cd
FROM m_kamar AS a
	JOIN sys_company AS b ON a.entity_id = b.entity_id
WHERE a.is_deleted = 0
ORDER BY $orderBy";
		}
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Kamar();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	/**
	 * @param int $id
	 * @return Kamar
	 */
	public function FindById($id) {
		$this->connector->CommandText =
"SELECT a.*, b.entity_cd
FROM m_kamar AS a
	JOIN sys_company AS b ON a.entity_id = b.entity_id
WHERE a.id = ?id";
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
	 * @return Kamar
	 */
	public function LoadById($id) {
		return $this->FindById($id);
	}

	/**
	 * @param int $eti
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Kamar[]
	 */
	public function LoadByEntityId($eti, $orderBy = "a.id", $includeDeleted = false) {
		if ($includeDeleted) {
			$this->connector->CommandText =
"SELECT a.*, b.entity_cd
FROM m_kamar AS a
	JOIN sys_company AS b ON a.entity_id = b.entity_id
WHERE a.entity_id = ?eti
ORDER BY $orderBy";
		} else {
			$this->connector->CommandText =
"SELECT a.*, b.entity_cd
FROM m_kamar AS a
	JOIN sys_company AS b ON a.entity_id = b.entity_id
WHERE a.is_deleted = 0 AND a.entity_id = ?eti
ORDER BY $orderBy";
		}
		$this->connector->AddParameter("?eti", $eti);
		$rs = $this->connector->ExecuteQuery();
        $result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Kamar();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	public function Insert() {
		$this->connector->CommandText =
        'INSERT INTO m_kamar(entity_id,nm_kamar,kd_kamar,tarif,kd_kelas,`status`,createby_id,create_time) VALUES(?entity_id,?nm_kamar,?kd_kamar,?tarif,?kd_kelas,?status,?createby_id,now())';
		$this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?nm_kamar", $this->NmKamar);
        $this->connector->AddParameter("?kd_kamar", $this->KdKamar);
        $this->connector->AddParameter("?tarif", $this->Tarif);
        $this->connector->AddParameter("?kd_kelas", $this->KdKelas);
        $this->connector->AddParameter("?status", $this->Status);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
		return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText = 'UPDATE m_kamar SET entity_id = ?entity_id,nm_kamar = ?nm_kamar,kd_kamar = ?kd_kamar,tarif = ?tarif,kd_kelas = ?kd_kelas,`status` = ?status,updateby_id = ?updateby_id, update_time = now() WHERE id = ?id';
        $this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?nm_kamar", $this->NmKamar);
        $this->connector->AddParameter("?kd_kamar", $this->KdKamar);
        $this->connector->AddParameter("?tarif", $this->Tarif);
        $this->connector->AddParameter("?kd_kelas", $this->KdKelas);
        $this->connector->AddParameter("?status", $this->Status);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = "Delete From m_kamar WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}
}
