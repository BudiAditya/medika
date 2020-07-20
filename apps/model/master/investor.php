<?php
class Investor extends EntityBase {
	public $Id;
	public $IsDeleted = false;
	public $NikKtp;
    public $NoKK;
    public $Nama;
    public $Alamat;
    public $Npwp;
    public $IsAktif = 1;
    public $Fphoto;
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
		$this->NikKtp = $row["nik_ktp"];
        $this->NoKK = $row["no_kk"];
        $this->Nama = $row["nama"];
        $this->Alamat = $row["alamat"];
        $this->Npwp = $row["npwp"];
        $this->IsAktif = $row["is_aktif"] == 1;
        $this->Fphoto = $row["fphoto"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
	}

    /**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Nama[]
	 */
	public function LoadAll($orderBy = "a.nama") {
		$this->connector->CommandText = "SELECT a.* FROM m_investor AS a WHERE a.is_deleted = 0 ORDER BY $orderBy";
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Investor();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	/**
	 * @param int $id
	 * @return Nama
	 */
	public function FindById($id) {
		$this->connector->CommandText = "SELECT a.*  FROM m_investor AS a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$row = $rs->FetchAssoc();
		$this->FillProperties($row);
		return $this;
	}

    public function FindByNikKtp($nik) {
        $this->connector->CommandText = "SELECT a.* FROM m_investor AS a WHERE a.nik = ?nik";
        $this->connector->AddParameter("?nik", $nik);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $row = $rs->FetchAssoc();
        $this->FillProperties($row);
        return $this;
    }

    public function FindByNoKK($nokk) {
        $this->connector->CommandText = "SELECT a.* FROM m_investor AS a WHERE a.no_kk = ?nokk And a.is_aktif = 1";
        $this->connector->AddParameter("?nokk", $nokk);
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
	 * @return Nama
	 */
	public function LoadById($id) {
		return $this->FindById($id);
	}

	public function Insert() {
		$this->connector->CommandText =
        'INSERT INTO m_investor(nik_ktp,no_kk,nama,alamat,npwp,is_aktif,createby_id,create_time)
        VALUES(?nik_ktp,?no_kk,?nama,?alamat,?npwp,?is_aktif,?createby_id,now())';
        $this->connector->AddParameter("?nama", $this->Nama);
        $this->connector->AddParameter("?alamat", $this->Alamat);
        $this->connector->AddParameter("?npwp", $this->Npwp,"char");
        $this->connector->AddParameter("?is_aktif", $this->IsAktif);
        $this->connector->AddParameter("?nik_ktp", $this->NikKtp);
        $this->connector->AddParameter("?no_kk", $this->NoKK);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
		return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText =
        'UPDATE m_investor SET
            nama = ?nama,
            alamat = ?alamat,
            npwp = ?npwp,
            is_aktif = ?is_aktif,
            updateby_id = ?updateby_id,
            update_time = now(),
            nik_ktp = ?nik_ktp,
            no_kk = ?no_kk
        WHERE id = ?id';
        $this->connector->AddParameter("?nama", $this->Nama);
        $this->connector->AddParameter("?alamat", $this->Alamat);
        $this->connector->AddParameter("?npwp", $this->Npwp,"char");
        $this->connector->AddParameter("?is_aktif", $this->IsAktif);
        $this->connector->AddParameter("?nik_ktp", $this->NikKtp);
        $this->connector->AddParameter("?no_kk", $this->NoKK);
		$this->connector->AddParameter("?id", $id);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = "Delete From m_investor WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}
}
