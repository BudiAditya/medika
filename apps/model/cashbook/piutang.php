<?php
class Piutang extends EntityBase {
	public $Id;
	public $EntityId = 1;
	public $SbuId = 2;
	public $NoReg;
    public $JnsPiutang = 0;
    public $NoRmPasien;
    public $TglPiutang;
    public $Keterangan;
    public $NoBukti;
    public $JumPiutang = 0;
    public $JumTerbayar = 0;
    public $TglTerbayar;
    public $BankId = 0;
    public $CreateMode = 0;
    public $StsPiutang = 0;
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
        $this->NoReg = $row["no_reg"];
        $this->JnsPiutang = $row["jns_piutang"];
        $this->NoRmPasien = $row["no_rm_pasien"];
        $this->TglPiutang = strtotime($row["tgl_piutang"]);
        $this->NoBukti = $row["no_bukti"];
        $this->Keterangan = $row["keterangan"];
        $this->JumPiutang = $row["jum_piutang"];
        $this->JumTerbayar = $row["jum_terbayar"];
        $this->TglTerbayar = strtotime($row["tgl_terbayar"]);
        $this->BankId = $row["bank_id"];
        $this->StsPiutang = $row["sts_piutang"];
        $this->CreateMode = $row["create_mode"];
        $this->NoBuktiBayar = $row["no_bukti_bayar"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
	}

    public function FormatTglPiutang($format = HUMAN_DATE) {
        return is_int($this->TglPiutang) ? date($format, $this->TglPiutang) : date($format);
    }

    public function FormatTglTerbayar($format = HUMAN_DATE) {
        return is_int($this->TglTerbayar) ? date($format, $this->TglTerbayar) : date($format);
    }

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Piutang[]
	 */
	public function LoadAll($orderBy = "a.id") {
		$this->connector->CommandText = "SELECT a.* FROM t_piutang AS a Where a.is_deleted = 0 ORDER BY $orderBy";
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Piutang();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	/**
	 * @param int $id
	 * @return Piutang
	 */
	public function FindById($id) {
		$this->connector->CommandText = "SELECT a.* FROM t_piutang AS a WHERE a.id = ?id";
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
	    $sql = "INSERT INTO t_piutang (keterangan,entity_id, sbu_id, no_reg, jns_piutang, no_rm_pasien, tgl_piutang, no_bukti, jum_piutang, jum_terbayar, sts_piutang, create_mode, createby_id, create_time)";
        $sql.= "Values(?keterangan,?entity_id, ?sbu_id, ?no_reg, ?jns_piutang, ?no_rm_pasien, ?tgl_piutang, ?no_bukti, ?jum_piutang, ?jum_terbayar, ?sts_piutang, ?create_mode, ?createby_id, now())";
		$this->connector->CommandText = $sql;
        $this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?sbu_id", $this->SbuId);
        $this->connector->AddParameter("?no_reg", $this->NoReg);
        $this->connector->AddParameter("?jns_piutang", $this->JnsPiutang);
        $this->connector->AddParameter("?no_rm_pasien", $this->NoRmPasien);
        $this->connector->AddParameter("?tgl_piutang", $this->TglPiutang != null ? date('Y-m-d',$this->TglPiutang) : null);
        $this->connector->AddParameter("?no_bukti", $this->NoBukti);
        $this->connector->AddParameter("?keterangan", $this->Keterangan);
        $this->connector->AddParameter("?jum_piutang", $this->JumPiutang);
        $this->connector->AddParameter("?jum_terbayar", $this->JumTerbayar);
        $this->connector->AddParameter("?sts_piutang", $this->StsPiutang);
        $this->connector->AddParameter("?create_mode", $this->CreateMode);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
        return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
	    $sql =  'UPDATE t_piutang a SET a.keterangan = ?keterangan, a.entity_id = ?entity_id, a.sbu_id = ?sbu_id, a.no_reg = ?no_reg,a.jns_piutang = ?jns_piutang,a.no_rm_pasien = ?no_rm_pasien,a.tgl_piutang = ?tgl_piutang,a.no_bukti = ?no_bukti,a.jum_piutang = ?jum_piutang,a.jum_terbayar = ?jum_terbayar,a.tgl_terbayar = ?tgl_terbayar,a.bank_id = ?bank_id,a.create_mode = ?create_mode,a.no_bukti_bayar = ?no_bukti_bayar,a.sts_piutang = ?sts_piutang,a.updateby_id = ?updateby_id,a.update_time = now() WHERE id = ?id';
		$this->connector->CommandText = $sql;
        $this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?sbu_id", $this->SbuId);
        $this->connector->AddParameter("?no_reg", $this->NoReg);
        $this->connector->AddParameter("?jns_piutang", $this->JnsPiutang);
        $this->connector->AddParameter("?no_rm_pasien", $this->NoRmPasien);
        $this->connector->AddParameter("?tgl_piutang", $this->TglPiutang != null ? date('Y-m-d',$this->TglPiutang) : null);
        $this->connector->AddParameter("?no_bukti", $this->NoBukti);
        $this->connector->AddParameter("?keterangan", $this->Keterangan);
        $this->connector->AddParameter("?jum_piutang", $this->JumPiutang);
        $this->connector->AddParameter("?jum_terbayar", $this->JumTerbayar);
        $this->connector->AddParameter("?sts_piutang", $this->StsPiutang);
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
        $sql =  'UPDATE t_piutang a SET a.jum_terbayar = ?jum_terbayar,a.tgl_terbayar = ?tgl_terbayar,a.bank_id = ?bank_id,a.no_bukti_bayar = ?no_bukti_bayar,a.updateby_id = ?updateby_id,a.update_time = now() WHERE id = ?id';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?id", $id);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
        $this->connector->AddParameter("?tgl_terbayar", $this->TglTerbayar != null ? date('Y-m-d',$this->TglTerbayar) : null);
        $this->connector->AddParameter("?no_bukti_bayar", $this->NoBuktiBayar);
        $this->connector->AddParameter("?jum_terbayar", $this->JumTerbayar);
        $this->connector->AddParameter("?bank_id", $this->BankId);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs){
            $result = $this->Paid($this->NoBuktiBayar,$this->UpdatebyId);
        }
        return $rs;
    }

	public function Delete($id) {
		$this->connector->CommandText = "Delete a From t_piutang a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

    public function DeleteByNoReg($noReg) {
        $this->connector->CommandText = "Delete a From t_piutang a WHERE a.no_reg = ?no_reg";
        $this->connector->AddParameter("?no_reg", $noReg);
        return $this->connector->ExecuteNonQuery();
    }

    public function Posting($nobukti,$uId){
	    $sqx = "Select fcPostingPiutang(?id,?user_id) AS valresult";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?id", $nobukti);
        $this->connector->AddParameter("?user_id", $uId);
        $rs = $this->connector->ExecuteQuery();
        $row = $rs->FetchAssoc();
        return strval($row["valresult"]);
    }

    public function Unposting($nobukti,$uId){
        $sqx = "Select fcUnpostingPiutang(?id,?user_id) AS valresult";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?id", $nobukti);
        $this->connector->AddParameter("?user_id", $uId);
        $rs = $this->connector->ExecuteQuery();
        $row = $rs->FetchAssoc();
        return strval($row["valresult"]);
    }

    public function Paid($nobukti,$uId){
        $sqx = "Select fcPaidPiutang(?id,?user_id) AS valresult";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?id", $nobukti);
        $this->connector->AddParameter("?user_id", $uId);
        $rs = $this->connector->ExecuteQuery();
        $row = $rs->FetchAssoc();
        return strval($row["valresult"]);
    }

    public function Unpaid($nobukti,$uId){
        $sqx = "Select fcUnpaidPiutang(?id,?user_id) AS valresult";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?id", $nobukti);
        $this->connector->AddParameter("?user_id", $uId);
        $rs = $this->connector->ExecuteQuery();
        $row = $rs->FetchAssoc();
        return strval($row["valresult"]);
    }
}
