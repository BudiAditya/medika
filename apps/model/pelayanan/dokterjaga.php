<?php
class DokterJaga extends EntityBase {
	public $Id;
	public $Tanggal;
    public $KdDokter;
    public $Keterangan;
    public $StsFee = 0;
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
		$this->Tanggal = $row["tanggal"];
        $this->KdDokter = $row["kd_dokter"];
        $this->Keterangan = $row["keterangan"];
        $this->StsFee = $row["sts_fee"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
	}

    public function FormatTanggal($format = HUMAN_DATE) {
        return is_int($this->Tanggal) ? date($format, $this->Tanggal) : date($format);
    }

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return DokterJaga[]
	 */
	public function LoadAll($orderBy = "a.id") {
		$this->connector->CommandText = "SELECT a.* FROM t_dokter_jaga AS a ORDER BY $orderBy";
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new DokterJaga();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	/**
	 * @param int $id
	 * @return DokterJaga
	 */
	public function FindById($id) {
		$this->connector->CommandText = "SELECT a.* FROM t_dokter_jaga AS a WHERE a.id = ?id";
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
		$this->connector->CommandText = 'INSERT INTO t_dokter_jaga (tanggal,kd_dokter,keterangan,sts_fee,createby_id,create_time) VALUES(?tanggal,?kd_dokter,?keterangan,?sts_fee,?createby_id,now())';
        $this->connector->AddParameter("?tanggal", $this->Tanggal != null ? date('Y-m-d',$this->Tanggal) : null);
        $this->connector->AddParameter("?kd_dokter", $this->KdDokter);
        $this->connector->AddParameter("?keterangan", $this->Keterangan);
        $this->connector->AddParameter("?sts_fee", $this->StsFee);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
        return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText = 'UPDATE t_dokter_jaga SET tanggal = ?tanggal,kd_dokter = ?kd_dokter,keterangan = ?keterangan,sts_fee = ?sts_fee,updateby_id = ?updateby_id,update_time = now() WHERE id = ?id';
        $this->connector->AddParameter("?tanggal", $this->Tanggal != null ? date('Y-m-d',$this->Tanggal) : null);
        $this->connector->AddParameter("?kd_dokter", $this->KdDokter);
        $this->connector->AddParameter("?keterangan", $this->Keterangan);
        $this->connector->AddParameter("?sts_fee", $this->StsFee);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
        $this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = "Delete a From t_dokter_jaga a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Load4Report($kdDokter = null,$startDate,$endDate,$jnsLaporan = 2){
        if ($jnsLaporan == 1) {
            if ($kdDokter == '' || $kdDokter == null) {
                $sql = "Select a.kd_dokter,a.nm_dokter,coalesce(sum(a.qty),0) AS hari,coalesce(sum(a.qty * a.fee_dokter_jaga),0) AS fee_dokter From vw_t_dokter_jaga AS a Where a.tanggal BETWEEN ?startdate and ?enddate Group By a.kd_dokter,a.nm_dokter";
            }else{
                $sql = "Select a.kd_dokter,a.nm_dokter,coalesce(sum(a.qty),0) AS hari,coalesce(sum(a.qty * a.fee_dokter_jaga),0) AS fee_dokter From vw_t_dokter_jaga AS a Where a.kd_dokter = ?kddokter And a.tanggal BETWEEN ?startdate and ?enddate Group By a.kd_dokter,a.nm_dokter";
            }
        }else{
            if ($kdDokter == '' || $kdDokter == null) {
                $sql = "Select a.* From vw_t_dokter_jaga AS a Where a.tanggal BETWEEN ?startdate and ?enddate Order By a.tanggal,a.kd_dokter";
            }else{
                $sql = "Select a.* From vw_t_dokter_jaga AS a Where a.kd_dokter = ?kddokter And a.tanggal BETWEEN ?startdate and ?enddate Order By a.tanggal,a.kd_dokter";
            }
        }
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?kddokter", $kdDokter);
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        return $this->connector->ExecuteQuery();
    }
}
