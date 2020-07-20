<?php
class Hutang extends EntityBase {
	public $Id;
	public $EntityId = 1;
	public $SbuId = 2;
	public $NoReff;
    public $JnsHutang = 0;
    public $KdRelasi;
    public $TglHutang;
    public $Keterangan;
    public $NoBukti;
    public $JumHutang = 0;
    public $JumTerbayar = 0;
    public $TglTerbayar;
    public $BankId = 0;
    public $CreateMode = 0;
    public $StsHutang = 0;
    public $CreatebyId = 0;
    public $UpdatebyId = 0;
    public $NoBuktiBayar;

	public function __construct($id = null) {
		parent::__construct();
		if (is_numeric($id)) {
			$this->FindById($id);
		}
	}

	public function FillProperties(array $row) {
		$this->Id = $row["id"];
		$this->EntityId = $row["entity_id"];
        $this->SbuId = $row["sbu_id"];
        $this->NoReff = $row["no_reff"];
        $this->JnsHutang = $row["jns_hutang"];
        $this->KdRelasi = $row["kd_relasi"];
        $this->TglHutang = strtotime($row["tgl_hutang"]);
        $this->NoBukti = $row["no_bukti"];
        $this->Keterangan = $row["keterangan"];
        $this->JumHutang = $row["jum_hutang"];
        $this->JumTerbayar = $row["jum_terbayar"];
        $this->TglTerbayar = strtotime($row["tgl_terbayar"]);
        $this->BankId = $row["bank_id"];
        $this->StsHutang = $row["sts_hutang"];
        $this->CreateMode = $row["create_mode"];
        $this->NoBuktiBayar = $row["no_bukti_bayar"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
	}

    public function FormatTglHutang($format = HUMAN_DATE) {
        return is_int($this->TglHutang) ? date($format, $this->TglHutang) : date($format);
    }

    public function FormatTglTerbayar($format = HUMAN_DATE) {
        return is_int($this->TglTerbayar) ? date($format, $this->TglTerbayar) : date($format);
    }

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Hutang[]
	 */
	public function LoadAll($orderBy = "a.id") {
		$this->connector->CommandText = "SELECT a.* FROM t_hutang AS a Where a.is_deleted = 0 ORDER BY $orderBy";
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Hutang();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	/**
	 * @param int $id
	 * @return Hutang
	 */
	public function FindById($id) {
		$this->connector->CommandText = "SELECT a.* FROM t_hutang AS a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$row = $rs->FetchAssoc();
		$this->FillProperties($row);
		return $this;
	}

    public function LoadById($id) {
		return $this->FindById($id);
	}

	public function Insert() {
	    $sql = "INSERT INTO t_hutang (keterangan,entity_id, sbu_id, no_reff, jns_hutang, kd_relasi, tgl_hutang, no_bukti, jum_hutang, jum_terbayar, sts_hutang, create_mode, createby_id, create_time)";
        $sql.= "Values(?keterangan,?entity_id, ?sbu_id, ?no_reff, ?jns_hutang, ?kd_relasi, ?tgl_hutang, ?no_bukti, ?jum_hutang, ?jum_terbayar, ?sts_hutang, ?create_mode, ?createby_id, now())";
		$this->connector->CommandText = $sql;
        $this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?sbu_id", $this->SbuId);
        $this->connector->AddParameter("?no_reff", $this->NoReff);
        $this->connector->AddParameter("?jns_hutang", $this->JnsHutang);
        $this->connector->AddParameter("?kd_relasi", $this->KdRelasi);
        $this->connector->AddParameter("?tgl_hutang", $this->TglHutang != null ? date('Y-m-d',$this->TglHutang) : null);
        $this->connector->AddParameter("?no_bukti", $this->NoBukti);
        $this->connector->AddParameter("?keterangan", $this->Keterangan);
        $this->connector->AddParameter("?jum_hutang", $this->JumHutang);
        $this->connector->AddParameter("?jum_terbayar", $this->JumTerbayar);
        $this->connector->AddParameter("?sts_hutang", $this->StsHutang);
        $this->connector->AddParameter("?create_mode", $this->CreateMode);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
        return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
	    $sql =  'UPDATE t_hutang a SET a.keterangan = ?keterangan, a.entity_id = ?entity_id, a.sbu_id = ?sbu_id, a.no_reff = ?no_reff,a.jns_hutang = ?jns_hutang,a.kd_relasi = ?kd_relasi,a.tgl_hutang = ?tgl_hutang,a.no_bukti = ?no_bukti,a.jum_hutang = ?jum_hutang,a.jum_terbayar = ?jum_terbayar,a.tgl_terbayar = ?tgl_terbayar,a.bank_id = ?bank_id,a.create_mode = ?create_mode,a.no_bukti_bayar = ?no_bukti_bayar,a.sts_hutang = ?sts_hutang,a.updateby_id = ?updateby_id,a.update_time = now() WHERE id = ?id';
		$this->connector->CommandText = $sql;
        $this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?sbu_id", $this->SbuId);
        $this->connector->AddParameter("?no_reff", $this->NoReff);
        $this->connector->AddParameter("?jns_hutang", $this->JnsHutang);
        $this->connector->AddParameter("?kd_relasi", $this->KdRelasi);
        $this->connector->AddParameter("?tgl_hutang", $this->TglHutang != null ? date('Y-m-d',$this->TglHutang) : null);
        $this->connector->AddParameter("?no_bukti", $this->NoBukti);
        $this->connector->AddParameter("?keterangan", $this->Keterangan);
        $this->connector->AddParameter("?jum_hutang", $this->JumHutang);
        $this->connector->AddParameter("?jum_terbayar", $this->JumTerbayar);
        $this->connector->AddParameter("?sts_hutang", $this->StsHutang);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
        $this->connector->AddParameter("?create_mode", $this->CreateMode);
        $this->connector->AddParameter("?id", $id);
        $this->connector->AddParameter("?tgl_terbayar", $this->TglTerbayar != null ? date('Y-m-d',$this->TglTerbayar) : null);
        $this->connector->AddParameter("?no_bukti_bayar", $this->NoBuktiBayar);
        $this->connector->AddParameter("?bank_id", $this->BankId);
		return $this->connector->ExecuteNonQuery();
	}

	public function ProsesBayar($id){
	    $result = 0;
        $sql =  'UPDATE t_hutang a SET a.jum_terbayar = ?jum_terbayar,a.tgl_terbayar = ?tgl_terbayar,a.bank_id = ?bank_id,a.no_bukti_bayar = ?no_bukti_bayar,a.updateby_id = ?updateby_id,a.update_time = now() WHERE id = ?id';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?id", $id);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
        $this->connector->AddParameter("?tgl_terbayar", $this->TglTerbayar != null ? date('Y-m-d',$this->TglTerbayar) : null);
        $this->connector->AddParameter("?no_bukti_bayar", $this->NoBuktiBayar);
        $this->connector->AddParameter("?jum_terbayar", $this->JumTerbayar);
        $this->connector->AddParameter("?bank_id", $this->BankId);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs){
            $rs = $this->Paid($this->NoBuktiBayar,$this->UpdatebyId);
        }
        return $rs;
    }

	public function Delete($id) {
		$this->connector->CommandText = "Delete a From t_hutang a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

    public function DeleteByNoReff($noReg) {
        $this->connector->CommandText = "Delete a From t_hutang a WHERE a.no_reff = ?no_reff";
        $this->connector->AddParameter("?no_reff", $noReg);
        return $this->connector->ExecuteNonQuery();
    }

    public function Paid($nobukti,$uId){
        $sqx = "Select fcPaidHutang(?id,?user_id) AS valresult";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?id", $nobukti);
        $this->connector->AddParameter("?user_id", $uId);
        $rs = $this->connector->ExecuteQuery();
        $row = $rs->FetchAssoc();
        return strval($row["valresult"]);
    }

    public function Unpaid($nobukti,$uId){
        $sqx = "Select fcUnpaidHutang(?id,?user_id) AS valresult";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?id", $nobukti);
        $this->connector->AddParameter("?user_id", $uId);
        $rs = $this->connector->ExecuteQuery();
        $row = $rs->FetchAssoc();
        return strval($row["valresult"]);
    }
}
