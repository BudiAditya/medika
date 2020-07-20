<?php
class Payroll extends EntityBase {
	public $Id;
	public $SbuId;
	public $SbuName;
	public $Nik;
	public $Nama;
	public $Bagian;
	public $KdBagian;
	public $Jabatan;
	public $Tahun;
	public $Bulan;
	public $Gapok = 0;
	public $TjJabatan = 0;
	public $TjProfesi = 0;
	public $BpjsKes = 0;
	public $BpjsTk = 0;
	public $Thr = 0;
	public $FeeProfit = 0;
	public $FeeTikhus = 0;
	public $FeeJasmed = 0;
	public $FeeDokterJaga = 0;
	public $TotGaji = 0;
	public $PotAbsensi = 0;
	public $PotPiutang = 0;
	public $PotBpjsKes = 0;
	public $PotLain = 0;
	public $TotPotongan = 0;
	public $TrxStatus = 0;
	public $Thp = 0;
    public $UpdatebyId;

	public function __construct($id = null) {
		parent::__construct();
		if (is_numeric($id)) {
			$this->FindById($id);
		}
	}

	public function FillProperties(array $row) {
		$this->Id = $row["id"];
		$this->SbuId = $row["sbu_id"];
        $this->SbuName = $row["sbu_name"];
        $this->Nik = $row["nik"];
		$this->Nama = $row["nama"];
		$this->Jabatan = $row["jabatan"];
		$this->KdBagian = $row["dept_cd"];
		$this->Bagian = $row["dept_name"];
        $this->Tahun = $row["tahun"];
        $this->Bulan = $row["bulan"];
        $this->Gapok = $row["gapok"];
        $this->TjJabatan = $row["tj_jabatan"];
        $this->TjProfesi = $row["tj_profesi"];
        $this->BpjsKes = $row["bpjs_kes"];
        $this->BpjsTk = $row["bpjs_tk"];
        $this->Thr = $row["thr"];
        $this->FeeProfit = $row["fee_profit"];
        $this->FeeTikhus = $row["fee_tikhus"];
        $this->FeeJasmed = $row["fee_jasmed"];
        $this->FeeDokterJaga = $row["fee_dokter_jaga"];
        $this->PotAbsensi = $row["pot_absensi"];
        $this->PotPiutang = $row["pot_piutang"];
        $this->PotBpjsKes = $row["pot_bpjs_kes"];
        $this->PotLain = $row["pot_lain"];
        $this->TotGaji = $row["gapok"]+$row["tj_jabatan"]+$row["tj_profesi"]+$row["bpjs_kes"]+$row["bpjs_tk"]+$row["thr"]+$row["fee_profit"]+$row["fee_tikhus"]+$row["fee_jasmed"]+$row["fee_dokter_jaga"];
        $this->TotPotongan = $row["pot_piutang"]+$row["pot_absensi"]+$row["pot_bpjs_kes"]+$row["pot_lain"];
        $this->Thp = $this->TotGaji - $this->TotPotongan;
        $this->TrxStatus = $row["trx_status"];
        $this->UpdatebyId = $row["updateby_id"];
	}

    public function LoadAll($entityId = 1,$sbuId = 0,$Tahun = 0,$Bulan = 0, $orderBy = "a.nik") {
	    $sql = "SELECT a.* FROM vw_t_payroll AS a Where a.entity_id = $entityId And a.sbu_id = $sbuId  And a.trx_status < 2";
	    if ($Tahun > 0){
	        $sql.= " And a.tahun = $Tahun";
        }
        if ($Bulan > 0){
            $sql.= " And a.bulan = $Bulan";
        }
        $sql.= " ORDER BY $orderBy";
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs != null) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new Payroll();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

    public function LoadCard($entityId = 1,$sbuId = 0,$Tahun = 0,$Nik = null, $orderBy = "a.bulan") {
        $sql = "SELECT a.* FROM vw_t_payroll AS a Where a.entity_id = $entityId And a.sbu_id = $sbuId  And a.trx_status < 2 And a.nik = '".$Nik."'";
        if ($Tahun > 0){
            $sql.= " And a.tahun = $Tahun";
        }
        $sql.= " ORDER BY $orderBy";
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs != null) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new Payroll();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

	public function FindById($id) {
		$this->connector->CommandText = "SELECT a.* FROM vw_t_payroll AS a WHERE a.id = ?id";
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

	public function Update($id) {
		$this->connector->CommandText = 'UPDATE t_payroll 
        SET gapok = ?gapok,
        tj_jabatan = ?tj_jabatan,
        tj_profesi = ?tj_profesi,
        bpjs_kes = ?bpjs_kes,
        bpjs_tk = ?bpjs_tk,
        thr = ?thr,
        fee_profit = ?fee_profit,
        fee_tikhus = ?fee_tikhus,
        fee_jasmed = ?fee_jasmed,
        nik = ?nik,
        tahun = ?tahun,     
        bulan = ?bulan,          
        pot_absensi = ?pot_absensi,
        pot_piutang = ?pot_piutang,
        pot_bpjs_kes = ?pot_bpjs_kes,
        pot_lain = ?pot_lain,
        updateby_id = ?updateby_id,
        update_time = now(),
        trx_status = ?trx_status
        WHERE id = ?id';
        $this->connector->AddParameter("?nik", $this->Nik);
        $this->connector->AddParameter("?gapok", $this->Gapok);
        $this->connector->AddParameter("?tj_jabatan", $this->TjJabatan);
        $this->connector->AddParameter("?tj_profesi", $this->TjProfesi);
        $this->connector->AddParameter("?bpjs_kes", $this->BpjsKes);
        $this->connector->AddParameter("?bpjs_tk", $this->BpjsTk);
        $this->connector->AddParameter("?thr", $this->Thr);
        $this->connector->AddParameter("?tahun", $this->Tahun);
        $this->connector->AddParameter("?bulan", $this->Bulan);
        $this->connector->AddParameter("?fee_profit", $this->FeeProfit);
        $this->connector->AddParameter("?fee_tikhus", $this->FeeTikhus);
        $this->connector->AddParameter("?fee_jasmed", $this->FeeJasmed);
        $this->connector->AddParameter("?pot_absensi", $this->PotAbsensi);
        $this->connector->AddParameter("?pot_piutang", $this->PotPiutang);
        $this->connector->AddParameter("?pot_bpjs_kes", $this->PotBpjsKes);
        $this->connector->AddParameter("?pot_lain", $this->PotLain);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
        $this->connector->AddParameter("?trx_status", $this->TrxStatus);
        $this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete ($id){
        $this->connector->CommandText = "Delete a From t_payroll AS a Where a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        return $this->connector->ExecuteNonQuery();
    }

    public function GeneratePayroll($entityId = 1, $paySbuId, $payTahun,$payBulan,$userId){
	    $sql = "Select fcGeneratePayroll($entityId,$paySbuId,$payTahun,$payBulan,$userId) As DataCount";
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null){
            return 0;
        }else{
            $row = $rs->FetchAssoc();
            return $row["DataCount"];
        }
    }
}
