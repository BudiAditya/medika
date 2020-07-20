<?php
class Transaksi extends EntityBase {
	public $Id;
	public $IsDeleted = false;
    public $KdJnsTransaksi;
    public $TglTransaksi;
    public $NoBukti;
    public $Keterangan;
    public $Jumlah = 0;
    public $BebanBagian;
    public $TrxType = 0;
    public $TrxSource;
    public $StsTransaksi;
    public $CreatebyId;
    public $UpdatebyId;
    public $ApprovebyId;

	public function __construct($id = null) {
		parent::__construct();
		if (is_numeric($id)) {
			$this->FindById($id);
		}
	}

	public function FillProperties(array $row) {
		$this->Id = $row["id"];
		$this->IsDeleted = $row["is_deleted"] == 1;
		$this->KdJnsTransaksi = $row["kd_jnstransaksi"];
        $this->TglTransaksi = strtotime($row["tgl_transaksi"]);
        $this->NoBukti = $row["no_bukti"];
        $this->Keterangan = $row["keterangan"];
        $this->Jumlah = $row["jumlah"];
        $this->BebanBagian = $row["beban_bagian"];
        $this->TrxType = $row["trx_type"];
        $this->TrxSource = $row["trx_source"];
        $this->StsTransaksi = $row["sts_transaksi"];
        $this->ApprovebyId = $row["approveby_id"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
	}

    public function FormatTglTransaksi($format = HUMAN_DATE) {
        return is_int($this->TglTransaksi) ? date($format, $this->TglTransaksi) : date($format);
    }

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Transaksi[]
	 */
	public function LoadAll($orderBy = "a.id") {
		$this->connector->CommandText = "SELECT a.* FROM t_cb_transaction AS a Where a.is_deleted = 0 ORDER BY $orderBy";
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Transaksi();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	/**
	 * @param int $id
	 * @return Transaksi
	 */
	public function FindById($id) {
		$this->connector->CommandText = "SELECT a.* FROM t_cb_transaction AS a WHERE a.id = ?id And a.is_deleted = 0";
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
		$this->connector->CommandText = 'INSERT INTO t_cb_transaction (kd_jnstransaksi,tgl_transaksi,no_bukti,keterangan,jumlah,beban_bagian,trx_type,trx_source,sts_transaksi,createby_id,create_time) VALUES(?kd_jnstransaksi,?tgl_transaksi,?no_bukti,?keterangan,?jumlah,?beban_bagian,?trx_type,?trx_source,?sts_transaksi,?createby_id,now())';
        $this->connector->AddParameter("?kd_jnstransaksi", $this->KdJnsTransaksi);
        $this->connector->AddParameter("?tgl_transaksi", date('Y-m-d',$this->TglTransaksi));
        $this->connector->AddParameter("?no_bukti", $this->NoBukti);
        $this->connector->AddParameter("?keterangan", $this->Keterangan);
        $this->connector->AddParameter("?jumlah", $this->Jumlah);
        $this->connector->AddParameter("?beban_bagian", $this->BebanBagian);
        $this->connector->AddParameter("?trx_type", $this->TrxType);
        $this->connector->AddParameter("?trx_source", $this->TrxSource);
        $this->connector->AddParameter("?sts_transaksi", $this->StsTransaksi);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
        return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText = 'UPDATE t_cb_transaction SET
 kd_jnstransaksi = ?kd_jnstransaksi,
tgl_transaksi = ?tgl_transaksi,
no_bukti = ?no_bukti,
keterangan = ?keterangan,
jumlah = ?jumlah,
beban_bagian = ?beban_bagian, 
trx_type = ?trx_type,  
trx_source = ?trx_source,
sts_transaksi = ?sts_transaksi, 
updateby_id = ?updateby_id, 
update_time = now()
WHERE id = ?id';
        $this->connector->AddParameter("?kd_jnstransaksi", $this->KdJnsTransaksi);
        $this->connector->AddParameter("?tgl_transaksi", date('Y-m-d',$this->TglTransaksi));
        $this->connector->AddParameter("?no_bukti", $this->NoBukti);
        $this->connector->AddParameter("?keterangan", $this->Keterangan);
        $this->connector->AddParameter("?jumlah", $this->Jumlah);
        $this->connector->AddParameter("?beban_bagian", $this->BebanBagian);
        $this->connector->AddParameter("?trx_type", $this->TrxType);
        $this->connector->AddParameter("?trx_source", $this->TrxSource);
        $this->connector->AddParameter("?sts_transaksi", $this->StsTransaksi);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
        $this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = "Update t_cb_transaction a Set a.is_deleted = 1, a.sts_transaksi = 2 WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

    public function DeletePermanent($id) {
        $this->connector->CommandText = "Delete a From t_cb_transaction AS a WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        return $this->connector->ExecuteNonQuery();
    }

    public function DeleteByNoBukti($noBukti) {
        $this->connector->CommandText = "Delete a From t_cb_transaction AS a WHERE a.no_bukti = ?no_bukti";
        $this->connector->AddParameter("?no_bukti", $noBukti);
        return $this->connector->ExecuteNonQuery();
    }

    public function GetAutoNoBukti($entityId = 0, $kdJtrx = null, $tglTrx = null) {
        // function untuk menggenerate no bukti kas
        $posKas = 0;
        $sqx = "Select a.posisi_kas From m_jnstransaksi a Where a.kd_jnstransaksi = '".$kdJtrx."'";
        $this->connector->CommandText = $sqx;
        $rs = $this->connector->ExecuteQuery();
        if ($rs != null) {
            $row = $rs->FetchAssoc();
            $posKas = $row["posisi_kas"];
        }
        if ($posKas == 1){
            $kode = "BM".$entityId.right(date('Y',$tglTrx),2)."0001";
        }else{
            $kode = "BK".$entityId.right(date('Y',$tglTrx),2)."0001";
        }
        $sqx = "SELECT max(a.no_bukti) AS pKode FROM vw_t_cb_transaction a WHERE a.entity_id = $entityId And left(a.no_bukti,5) = '".left($kode,5)."'";
        $this->connector->CommandText = $sqx;
        $rs = $this->connector->ExecuteQuery();
        if ($rs != null) {
            $row = $rs->FetchAssoc();
            $pkode = $row["pKode"];
            if (strlen($pkode) == 9){
                $counter = (int)(right($pkode,4))+1;
                $kode = left($kode,5).str_pad($counter,4,'0',STR_PAD_LEFT);
            }
        }
        return $kode;
    }

    public function Approve($id = null, $uid = null){
        $this->connector->CommandText = "Update t_cb_transaction a Set a.sts_transaksi = 1, a.approveby_id = ?uid, a.approve_time = now() Where a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        $this->connector->AddParameter("?uid", $uid);
        return $this->connector->ExecuteNonQuery();
    }

    public function Unapprove($id = null, $uid = null){
        $this->connector->CommandText = "Update t_cb_transaction a Set a.sts_transaksi = 0, a.approveby_id = 0, a.approve_time = null Where a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        return $this->connector->ExecuteNonQuery();
    }

    public function Load4DetailReports($entityId,$kdKlpTrx = null,$kdJnsTrx = null,$kdDept = null,$posKas = 0, $startDate = null, $endDate = null) {
        $sql = "SELECT a.* FROM vw_t_cb_transaction AS a";
        $sql.= " WHERE a.entity_id = $entityId And a.is_deleted = 0 and a.sts_transaksi < 2 and a.tgl_transaksi BETWEEN ?startdate and ?enddate";
        if ($kdKlpTrx <> ''){
            $sql.= " and a.kd_klptransaksi = '".$kdKlpTrx."'";
        }
        if ($kdJnsTrx <> ''){
            $sql.= " and a.kd_jnstransaksi = '".$kdJnsTrx."'";
        }
        if ($kdDept <> ''){
            $sql.= " and a.beban_bagian = '".$kdDept."'";
        }
        if ($posKas > 0){
            $sql.= " and a.posisi_kas = $posKas";
        }
        $sql.= " Order By a.tgl_transaksi, a.no_bukti";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function Load4RekapReports($entityId,$kdKlpTrx = null,$kdJnsTrx = null,$kdDept = null,$posKas = 0, $startDate = null, $endDate = null) {
        $sql = "SELECT a.jns_transaksi,sum(a.debet) as sum_debet,sum(a.kredit) as sum_kredit,sum(a.jumlah) as sum_jumlah FROM vw_t_cb_transaction AS a";
        $sql.= " WHERE a.entity_id = $entityId And a.is_deleted = 0 and a.sts_transaksi < 2 and a.tgl_transaksi BETWEEN ?startdate and ?enddate";
        if ($kdKlpTrx <> ''){
            $sql.= " and a.kd_klptransaksi = '".$kdKlpTrx."'";
        }
        if ($kdJnsTrx <> ''){
            $sql.= " and a.kd_jnstransaksi = '".$kdJnsTrx."'";
        }
        if ($kdDept <> ''){
            $sql.= " and a.beban_bagian = '".$kdDept."'";
        }
        if ($posKas > 0){
            $sql.= " and a.posisi_kas = $posKas";
        }
        $sql.= " Group By a.jns_transaksi";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function GetSaldoLalu($entityId = 0,$b4Date = null){
        $sql = "Select Coalesce(a.saldo_awal,0) As Sawal From sys_company AS a Where a.entity_id = $entityId";
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteQuery();
        $sawal = 0;
        if ($rs != null) {
            $row = $rs->FetchAssoc();
            $sawal = $row["Sawal"];
        }
        $sql = "Select Coalesce(Sum(a.debet - a.kredit),0) As Saldo From vw_t_cb_transaction AS a Where a.entity_id = $entityId And a.tgl_transaksi < $b4Date";
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteQuery();
        $saldo = 0;
        if ($rs != null) {
            $row = $rs->FetchAssoc();
            $saldo = $row["Saldo"];
        }
        return $sawal + $saldo;
    }
}
