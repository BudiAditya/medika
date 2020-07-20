<?php
class Gaji extends EntityBase {
	public $Id;
	public $EntityId = 1;
	public $SbuId;
	public $Nik;
	public $KdDokter;
	public $Gapok = 0;
	public $TjJabatan = 0;
	public $TjProfesi = 0;
	public $BpjsKes = 0;
	public $BpjsTk = 0;
	public $Thr = 0;
	public $IsFeeProfit = 0;
	public $IsFeeTikhus = 0;
	public $IsFeeJasmed = 0;
    public $UpdatebyId = 0;
    public $CreatebyId = 0;

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
		$this->Nik = $row["nik"];
        $this->KdDokter = $row["kd_dokter"];
        $this->Gapok = $row["gapok"];
        $this->TjJabatan = $row["tj_jabatan"];
        $this->TjProfesi = $row["tj_profesi"];
        $this->BpjsKes = $row["bpjs_kes"];
        $this->BpjsTk = $row["bpjs_tk"];
        $this->Thr = $row["thr"];
        $this->IsFeeProfit = $row["is_feeprofit"];
        $this->IsFeeTikhus = $row["is_feetikhus"];
        $this->IsFeeJasmed = $row["is_feejasmed"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
	}

	public function FindByNik($entityId,$sbuId,$nik) {
		$this->connector->CommandText = "SELECT a.* FROM m_gaji AS a WHERE a.entity_id = ?entity_id And a.sbu_id = ?sbu_id And a.nik = ?nik";
		$this->connector->AddParameter("?entity_id", $entityId);
        $this->connector->AddParameter("?sbu_id", $sbuId);
        $this->connector->AddParameter("?nik", $nik);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$row = $rs->FetchAssoc();
		$this->FillProperties($row);
		return $this;
	}

    public function FindById($id) {
        $this->connector->CommandText = "SELECT a.* FROM m_gaji AS a WHERE a.id = ?id";
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
        $this->connector->CommandText = 'Insert Into m_gaji (entity_id, sbu_id, nik, kd_dokter, gapok, tj_jabatan, tj_profesi, bpjs_kes, bpjs_tk, thr, is_feeprofit, is_feetikhus, is_feejasmed, createby_id, create_time) Values (?entity_id, ?sbu_id, ?nik, ?kd_dokter, ?gapok, ?tj_jabatan, ?tj_profesi, ?bpjs_kes, ?bpjs_tk, ?thr, ?is_feeprofit, ?is_feetikhus, ?is_feejasmed, ?createby_id, now())';
        $this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?sbu_id", $this->SbuId);
        $this->connector->AddParameter("?nik", $this->Nik);
        $this->connector->AddParameter("?gapok", $this->Gapok);
        $this->connector->AddParameter("?tj_jabatan", $this->TjJabatan);
        $this->connector->AddParameter("?tj_profesi", $this->TjProfesi);
        $this->connector->AddParameter("?bpjs_kes", $this->BpjsKes);
        $this->connector->AddParameter("?bpjs_tk", $this->BpjsTk);
        $this->connector->AddParameter("?thr", $this->Thr);
        $this->connector->AddParameter("?kd_dokter", $this->KdDokter);
        $this->connector->AddParameter("?is_feeprofit", $this->IsFeeProfit);
        $this->connector->AddParameter("?is_feetikhus", $this->IsFeeTikhus);
        $this->connector->AddParameter("?is_feejasmed", $this->IsFeeJasmed);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
        return $this->connector->ExecuteNonQuery();
    }

	public function Update($id) {
		$this->connector->CommandText = 'UPDATE m_gaji AS a 
        SET
        a.entity_id = ?entity_id,
        a.sbu_id = ?sbu_id, 
        a.nik = ?nik,
        a.gapok = ?gapok,
        a.tj_jabatan = ?tj_jabatan,
        a.tj_profesi = ?tj_profesi,
        a.bpjs_kes = ?bpjs_kes,
        a.bpjs_tk = ?bpjs_tk,
        a.thr = ?thr,
        a.is_feeprofit = ?is_feeprofit,
        a.is_feetikhus = ?is_feetikhus,
        a.is_feejasmed = ?is_feejasmed,
        a.kd_dokter = ?kd_dokter,                    
        a.updateby_id = ?updateby_id,
        a.update_time = now()
        WHERE id = ?id';
        $this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?sbu_id", $this->SbuId);
        $this->connector->AddParameter("?nik", $this->Nik);
        $this->connector->AddParameter("?gapok", $this->Gapok);
        $this->connector->AddParameter("?tj_jabatan", $this->TjJabatan);
        $this->connector->AddParameter("?tj_profesi", $this->TjProfesi);
        $this->connector->AddParameter("?bpjs_kes", $this->BpjsKes);
        $this->connector->AddParameter("?bpjs_tk", $this->BpjsTk);
        $this->connector->AddParameter("?thr", $this->Thr);
        $this->connector->AddParameter("?kd_dokter", $this->KdDokter);
        $this->connector->AddParameter("?is_feeprofit", $this->IsFeeProfit);
        $this->connector->AddParameter("?is_feetikhus", $this->IsFeeTikhus);
        $this->connector->AddParameter("?is_feejasmed", $this->IsFeeJasmed);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
        $this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}
}
