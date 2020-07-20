<?php
class JnsTransaksi extends EntityBase {
	public $Id;
	public $IsDeleted = false;
    public $EntityId;
	public $EntityCd;
    public $KdKlpTransaksi;
    public $KlpJnsTransaksi;
    public $KdJnsTransaksi;
    public $JnsTransaksi;
    public $PosisiKas;
    public $Satuan;
    public $Tarif = 0;
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
        $this->KdKlpTransaksi = $row["kd_klptransaksi"];
        $this->KlpJnsTransaksi = $row["klp_transaksi"];
        $this->KdJnsTransaksi = $row["kd_jnstransaksi"];
        $this->JnsTransaksi = $row["jns_transaksi"];
        $this->PosisiKas = $row["posisi_kas"];
        $this->Satuan = $row["satuan"];
        $this->Tarif = $row["tarif"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
	}

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return JnsTransaksi[]
	 */
	public function LoadAll($orderBy = "a.id", $includeDeleted = false) {
		if ($includeDeleted) {
			$this->connector->CommandText = "SELECT a.*, b.entity_cd, c.klp_transaksi FROM m_jnstransaksi AS a JOIN sys_company AS b ON a.entity_id = b.entity_id Join m_klptransaksi AS c On a.kd_klptransaksi = c.kd_klptransaksi ORDER BY $orderBy";
		} else {
			$this->connector->CommandText = "SELECT a.*, b.entity_cd, c.klp_transaksi FROM m_jnstransaksi AS a JOIN sys_company AS b ON a.entity_id = b.entity_id Join m_klptransaksi AS c On a.kd_klptransaksi = c.kd_klptransaksi WHERE a.is_deleted = 0 ORDER BY $orderBy";
		}
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new JnsTransaksi();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	/**
	 * @param int $id
	 * @return JnsTransaksi
	 */
	public function FindById($id) {
		$this->connector->CommandText = "SELECT a.*, b.entity_cd, c.klp_transaksi FROM m_jnstransaksi AS a JOIN sys_company AS b ON a.entity_id = b.entity_id Join m_klptransaksi AS c On a.kd_klptransaksi = c.kd_klptransaksi WHERE a.id = ?id";
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
	 * @return JnsTransaksi
	 */
	public function LoadById($id) {
		return $this->FindById($id);
	}

	/**
	 * @param int $eti
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return JnsTransaksi[]
	 */
	public function LoadByEntityId($eti, $orderBy = "a.id", $includeDeleted = false) {
		if ($includeDeleted) {
			$this->connector->CommandText = "SELECT a.*, b.entity_cd, c.klp_transaksi FROM m_jnstransaksi AS a JOIN sys_company AS b ON a.entity_id = b.entity_id Join m_klptransaksi AS c On a.kd_klptransaksi = c.kd_klptransaksi WHERE a.entity_id = ?eti ORDER BY $orderBy";
		} else {
			$this->connector->CommandText ="SELECT a.*, b.entity_cd, c.klp_transaksi FROM m_jnstransaksi AS a JOIN sys_company AS b ON a.entity_id = b.entity_id Join m_klptransaksi AS c On a.kd_klptransaksi = c.kd_klptransaksi WHERE a.entity_id = ?eti And a.is_deleted = 0 ORDER BY $orderBy";
		}
		$this->connector->AddParameter("?eti", $eti);
		$rs = $this->connector->ExecuteQuery();
        $result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new JnsTransaksi();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	public function Insert() {
		$this->connector->CommandText = 'INSERT INTO m_jnstransaksi(posisi_kas,entity_id,kd_jnstransaksi,jns_transaksi,kd_klptransaksi,satuan,tarif,createby_id,create_time) VALUES(?posisi_kas,?entity_id,?kd_jnstransaksi,?jns_transaksi,?kd_klptransaksi,?satuan,?tarif,?createby_id,now())';
		$this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?kd_jnstransaksi", $this->KdJnsTransaksi);
        $this->connector->AddParameter("?jns_transaksi", $this->JnsTransaksi);
        $this->connector->AddParameter("?kd_klptransaksi", $this->KdKlpTransaksi);
        $this->connector->AddParameter("?posisi_kas", $this->PosisiKas);
        $this->connector->AddParameter("?satuan", $this->Satuan);
        $this->connector->AddParameter("?tarif", $this->Tarif);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
		return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText = 'UPDATE m_jnstransaksi SET posisi_kas = ?posisi_kas, entity_id = ?entity_id,kd_jnstransaksi = ?kd_jnstransaksi,jns_transaksi = ?jns_transaksi,kd_klptransaksi = ?kd_klptransaksi,satuan = ?satuan,tarif = ?tarif,updateby_id = ?updateby_id, update_time = now() WHERE id = ?id';
        $this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?kd_jnstransaksi", $this->KdJnsTransaksi);
        $this->connector->AddParameter("?jns_transaksi", $this->JnsTransaksi);
        $this->connector->AddParameter("?kd_klptransaksi", $this->KdKlpTransaksi);
        $this->connector->AddParameter("?posisi_kas", $this->PosisiKas);
        $this->connector->AddParameter("?satuan", $this->Satuan);
        $this->connector->AddParameter("?tarif", $this->Tarif);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = "Delete From m_jnstransaksi WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

    public function GetAutoKode($entityId = 0) {
        // function untuk menggenerate kode sku
        $kode = "JT".$entityId."001";
        $sqx = "SELECT max(a.kd_jnstransaksi) AS pKode FROM m_jnstransaksi a WHERE a.entity_id = $entityId And left(a.kd_jnstransaksi,3) = 'JT".$entityId."'";
        $this->connector->CommandText = $sqx;
        $rs = $this->connector->ExecuteQuery();
        if ($rs != null) {
            $row = $rs->FetchAssoc();
            $pkode = $row["pKode"];
            if (strlen($pkode) == 6){
                $counter = (int)(right($pkode,3))+1;
                $kode = "JT".$entityId.str_pad($counter,3,'0',STR_PAD_LEFT);
            }
        }
        return $kode;
    }
}
