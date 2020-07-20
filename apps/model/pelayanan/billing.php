<?php
class Billing extends EntityBase {
	public $Id;
	public $IsDeleted = false;
    public $NoReg;
    public $NominalBiaya = 0;
    public $NominalTindakan = 0;
    public $NominalObat = 0;
    public $NominalAlat = 0;
    public $DtgBpjs = 0;
    public $DtgSendiri = 0;
    public $DtgPerusahaan = 0;
    public $JumBayar = 0;
    public $TglBayar;
    public $DiterimaOleh;
    public $Catatan;
    public $StsBilling;
    public $CreatebyId;
    public $UpdatebyId;
    public $NoBukti;

	public function __construct($id = null) {
		parent::__construct();
		if (is_numeric($id)) {
			$this->FindById($id);
		}
	}

	public function FillProperties(array $row) {
		$this->Id = $row["id"];
		$this->IsDeleted = $row["is_deleted"] == 1;
		$this->NoReg = $row["no_reg"];
        $this->NominalBiaya = $row["nominal_biaya"];
        $this->NominalTindakan = $row["nominal_tindakan"];
        $this->NominalObat = $row["nominal_obat"];
        $this->NominalAlat = $row["nominal_alat"];
        $this->DtgBpjs = $row["dtg_bpjs"];
        $this->DtgSendiri = $row["dtg_sendiri"];
        $this->DtgPerusahaan = $row["dtg_perusahaan"];
        $this->JumBayar = $row["jum_bayar"];
        $this->TglBayar = strtotime($row["tgl_bayar"]);
        $this->DiterimaOleh = $row["diterima_oleh"];
        $this->StsBilling = $row["sts_billing"];
        $this->Catatan = $row["catatan"];
        $this->NoBukti = $row["no_bukti"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
	}

    public function FormatTglBayar($format = HUMAN_DATE) {
        return is_int($this->TglBayar) ? date($format, $this->TglBayar) : date($format);
    }

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Billing[]
	 */
	public function LoadAll($orderBy = "a.id") {
		$this->connector->CommandText = "SELECT a.* FROM t_billing AS a Where a.is_deleted = 0 ORDER BY $orderBy";
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Billing();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	/**
	 * @param int $id
	 * @return Billing
	 */
	public function FindById($id) {
		$this->connector->CommandText = "SELECT a.* FROM t_billing AS a WHERE a.id = ?id And a.is_deleted = 0";
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
		$this->connector->CommandText = 'INSERT INTO t_billing (dtg_sendiri,dtg_perusahaan,no_bukti,no_reg,nominal_biaya,nominal_tindakan,nominal_obat,nominal_alat,dtg_bpjs,jum_bayar,catatan,tgl_bayar,diterima_oleh,sts_billing,createby_id,create_time) VALUES(?dtg_sendiri,?dtg_perusahaan,?no_bukti,?no_reg,?nominal_biaya,?nominal_tindakan,?nominal_obat,?nominal_alat,?dtg_bpjs,?jum_bayar,?catatan,?tgl_bayar,?diterima_oleh,?sts_billing,?createby_id,now())';
        $this->connector->AddParameter("?no_reg", $this->NoReg);
        $this->connector->AddParameter("?nominal_biaya", $this->NominalBiaya);
        $this->connector->AddParameter("?nominal_tindakan", $this->NominalTindakan);
        $this->connector->AddParameter("?nominal_obat", $this->NominalObat);
        $this->connector->AddParameter("?nominal_alat", $this->NominalAlat);
        $this->connector->AddParameter("?dtg_bpjs", $this->DtgBpjs);
        $this->connector->AddParameter("?dtg_sendiri", $this->DtgSendiri);
        $this->connector->AddParameter("?dtg_perusahaan", $this->DtgPerusahaan);
        $this->connector->AddParameter("?jum_bayar", $this->JumBayar);
        $this->connector->AddParameter("?tgl_bayar", $this->TglBayar != null ? date('Y-m-d',$this->TglBayar) : null);
        $this->connector->AddParameter("?diterima_oleh", $this->DiterimaOleh);
        $this->connector->AddParameter("?catatan", $this->Catatan);
        $this->connector->AddParameter("?no_bukti", $this->NoBukti);
        $this->connector->AddParameter("?sts_billing", $this->StsBilling);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
        return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText = 'UPDATE t_billing SET
 no_reg = ?no_reg,
nominal_biaya = ?nominal_biaya,
nominal_tindakan = ?nominal_tindakan,
nominal_obat = ?nominal_obat,
nominal_alat = ?nominal_alat,
dtg_bpjs = ?dtg_bpjs, 
jum_bayar = ?jum_bayar,  
tgl_bayar = ?tgl_bayar,
diterima_oleh = ?diterima_oleh, 
catatan = ?catatan,
no_bukti = ?no_bukti,
sts_billing = ?sts_billing, 
updateby_id = ?updateby_id, 
dtg_perusahaan = ?dtg_perusahaan,
dtg_sendiri = ?dtg_sendiri,
update_time = now() WHERE id = ?id';
        $this->connector->AddParameter("?no_reg", $this->NoReg);
        $this->connector->AddParameter("?nominal_biaya", $this->NominalBiaya);
        $this->connector->AddParameter("?nominal_tindakan", $this->NominalTindakan);
        $this->connector->AddParameter("?nominal_obat", $this->NominalObat);
        $this->connector->AddParameter("?nominal_alat", $this->NominalAlat);
        $this->connector->AddParameter("?dtg_bpjs", $this->DtgBpjs);
        $this->connector->AddParameter("?dtg_perusahaan", $this->DtgPerusahaan);
        $this->connector->AddParameter("?dtg_sendiri", $this->DtgSendiri);
        $this->connector->AddParameter("?jum_bayar", $this->JumBayar);
        $this->connector->AddParameter("?tgl_bayar", $this->TglBayar != null ? date('Y-m-d',$this->TglBayar) : null);
        $this->connector->AddParameter("?diterima_oleh", $this->DiterimaOleh);
        $this->connector->AddParameter("?catatan", $this->Catatan);
        $this->connector->AddParameter("?no_bukti", $this->NoBukti);
        $this->connector->AddParameter("?sts_billing", $this->StsBilling);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
        $this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = "Update t_billing a Set a.is_deleted = 1, a.sts_billing = 2 WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

    public function DeleteByNoReg($noReg) {
        $this->connector->CommandText = "Delete a From t_billing a WHERE a.no_reg = ?no_reg";
        $this->connector->AddParameter("?no_reg", $noReg);
        return $this->connector->ExecuteNonQuery();
    }

    public function PostingByNoReg($noReg,$uId){
	    $sqx = "Select fcPostingBilling(?no_reg,?user_id) AS valresult";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?no_reg", $noReg);
        $this->connector->AddParameter("?user_id", $uId);
        $rs = $this->connector->ExecuteQuery();
        $row = $rs->FetchAssoc();
        return strval($row["valresult"]);
    }

    public function UnpostingByNoReg($noReg,$uId){
        $sqx = "Select fcUnpostingBilling(?no_reg,?user_id) AS valresult";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?no_reg", $noReg);
        $this->connector->AddParameter("?user_id", $uId);
        $rs = $this->connector->ExecuteQuery();
        $row = $rs->FetchAssoc();
        return strval($row["valresult"]);
    }
}
