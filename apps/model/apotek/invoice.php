<?php

require_once("invoice_detail.php");

class Invoice extends EntityBase {
	private $editableDocId = array(1, 2, 3, 4);

	public static $InvoiceStatusCodes = array(
		0 => "DRAFT",
		1 => "POSTED",
        2 => "PENDING",
		3 => "VOID"
	);

    public static $CollectStatusCodes = array(
        0 => "ON HOLD",
        1 => "ON PROCESS",
        2 => "PAID",
        3 => "VOID"
    );

	public $Id;
    public $EntityId;
    public $SbuId;
    public $TrxNo;
	public $Tanggal;
    public $Kontak;
	public $Uraian;
	public $Karyawan;
	public $SubTotal = 0;
    public $DiskonPersen = 0;
    public $DiskonNilai = 0;
    public $PajakPersen = 0;
	public $PajakNilai = 0;
    public $BiayaNilai = 0;
    public $TotalTransaksi = 0;
    public $CaraBayar = 0;
    public $JumlahBayar = 0;
    public $TrxStatus = 0;
    public $JumlahRetur = 0;
    public $JenisPasien;
    public $NoResep;
    public $NmDokter;
    public $NmPasien;
    public $UmrPasien = 0;
    public $JnsBeli;
    public $NoHp;
    public $NoRm;
    public $KdDokter;
    public $TotalHpp = 0;
    public $CreatebyId = 0;
    public $UpdatebyId = 0;
    public $RelasiId;
    public $NoTerminal;

	/** @var InvoiceDetail[] */
	public $Details = array();

	public function __construct($id = null) {
		parent::__construct();
		if (is_numeric($id)) {
			$this->LoadById($id);
		}
	}

	public function FillProperties(array $row) {
        $this->Id = $row["id"];
        $this->EntityId = $row["entity_id"];
        $this->SbuId = $row["sbu_id"];
        $this->TrxNo = $row["trx_no"];
        $this->Tanggal = $row["tanggal"];
        $this->Kontak = $row["kontak"];
        $this->Uraian = $row["uraian"];
        $this->Karyawan = $row["karyawan"];
        $this->SubTotal = $row["sub_total"];
        $this->DiskonPersen = $row["diskon_persen"];
        $this->DiskonNilai = $row["diskon_nilai"];
        $this->PajakPersen = $row["pajak_persen"];
        $this->PajakNilai = $row["pajak_nilai"];
        $this->BiayaNilai = $row["biaya_nilai"];
        $this->TotalTransaksi = $row["total_transaksi"];
        $this->CaraBayar = $row["cara_bayar"];
        $this->JumlahBayar = $row["jumlah_bayar"];
        $this->TrxStatus = $row["trx_status"];
        $this->JumlahRetur = $row["jumlah_retur"];
        $this->JenisPasien = $row["jenis_pasien"];
        $this->NoResep = $row["no_resep"];
        $this->NmDokter = $row["nm_dokter"];
        $this->NmPasien = $row["nm_pasien"];
        $this->UmrPasien = $row["umr_pasien"];
        $this->JnsBeli = $row["jns_beli"];
        $this->NoHp = $row["no_hp"];
        $this->NoRm = $row["no_rm"];
        $this->KdDokter = $row["kd_dokter"];
        $this->TotalHpp = $row["total_hpp"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
        $this->RelasiId = $row["relasi_id"];
	}

	public function FormatTanggal($format = HUMAN_DATE) {
		return is_int($this->Tanggal) ? date($format, $this->Tanggal) : date($format, strtotime(date('Y-m-d')));
	}

	/**
	 * @return InvoiceDetail[]
	 */
	public function LoadDetails() {
		if ($this->TrxNo == null) {
			return $this->Details;
		}
		$detail = new InvoiceDetail();
		$this->Details = $detail->LoadByTrxNo($this->TrxNo);
		return $this->Details;
	}

	/**
	 * @param int $id
	 * @return Invoice
	 */
	public function LoadById($id) {
		$this->connector->CommandText = "SELECT a.* FROM t_aptjual_master AS a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

    public function FindById($id) {
        $this->connector->CommandText = "SELECT a.* FROM t_aptjual_master AS a WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $this->FillProperties($rs->FetchAssoc());
        return $this;
    }

	public function LoadByTrxNo($trxNo) {
		$this->connector->CommandText = "SELECT a.* FROM t_aptjual_master AS a WHERE a.trx_no = ?trxNo";
		$this->connector->AddParameter("?trxNo", $trxNo);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

    public function LoadByEntityId($entityId) {
        $this->connector->CommandText = "SELECT a.* FROM t_aptjual_master AS a WHERE a.entity_id = ?entityId";
        $this->connector->AddParameter("?entityId", $entityId);
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs != null) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new Invoice();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

	public function Insert() {
        $sql = "INSERT INTO t_aptjual_master (no_terminal,relasi_id,entity_id, sbu_id, trx_no, tanggal, kontak, uraian, karyawan, sub_total, diskon_persen, diskon_nilai, pajak_persen, pajak_nilai, biaya_nilai, total_transaksi, cara_bayar, jumlah_bayar, trx_status, jumlah_retur, jenis_pasien, no_resep, nm_dokter, nm_pasien, umr_pasien, jns_beli, no_hp, no_rm, kd_dokter, total_hpp, createby_id, create_time)";
        $sql.= "VALUES(?no_terminal,?relasi_id,?entity_id, ?sbu_id, ?trx_no, ?tanggal, ?kontak, ?uraian, ?karyawan, ?sub_total, ?diskon_persen, ?diskon_nilai, ?pajak_persen, ?pajak_nilai, ?biaya_nilai, ?total_transaksi, ?cara_bayar, ?jumlah_bayar, ?trx_status, ?jumlah_retur, ?jenis_pasien, ?no_resep, ?nm_dokter, ?nm_pasien, ?umr_pasien, ?jns_beli, ?no_hp, ?no_rm, ?kd_dokter, ?total_hpp, ?createby_id, now())";
		$this->connector->CommandText = $sql;
        $this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?sbu_id", $this->SbuId);
        $this->connector->AddParameter("?trx_no", $this->TrxNo);
        $this->connector->AddParameter("?tanggal", $this->Tanggal);
        $this->connector->AddParameter("?kontak", $this->Kontak);
        $this->connector->AddParameter("?uraian", $this->Uraian);
        $this->connector->AddParameter("?karyawan", $this->Karyawan);
        $this->connector->AddParameter("?sub_total", $this->SubTotal);
        $this->connector->AddParameter("?diskon_persen", $this->DiskonPersen);
        $this->connector->AddParameter("?diskon_nilai", $this->DiskonNilai);
        $this->connector->AddParameter("?pajak_persen", $this->PajakPersen);
        $this->connector->AddParameter("?pajak_nilai", $this->PajakNilai);
        $this->connector->AddParameter("?biaya_nilai", $this->BiayaNilai);
        $this->connector->AddParameter("?total_transaksi", $this->TotalTransaksi);
        $this->connector->AddParameter("?cara_bayar", $this->CaraBayar);
        $this->connector->AddParameter("?jumlah_bayar", $this->JumlahBayar);
        $this->connector->AddParameter("?trx_status", $this->TrxStatus);
        $this->connector->AddParameter("?jumlah_retur", $this->JumlahRetur);
        $this->connector->AddParameter("?jenis_pasien", $this->JenisPasien);
        $this->connector->AddParameter("?no_resep", $this->NoResep);
        $this->connector->AddParameter("?nm_dokter", $this->NmDokter);
        $this->connector->AddParameter("?nm_pasien", $this->NmPasien);
        $this->connector->AddParameter("?umr_pasien", $this->UmrPasien);
        $this->connector->AddParameter("?jns_beli", $this->JnsBeli);
        $this->connector->AddParameter("?no_hp", $this->NoHp,"char");
        $this->connector->AddParameter("?no_rm", $this->NoRm);
        $this->connector->AddParameter("?kd_dokter", $this->KdDokter);
        $this->connector->AddParameter("?total_hpp", $this->TotalHpp);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
        $this->connector->AddParameter("?relasi_id", $this->RelasiId);
        $this->connector->AddParameter("?no_terminal", $this->NoTerminal);
		$rs = $this->connector->ExecuteNonQuery();
		if ($rs == 1) {
			$this->connector->CommandText = "SELECT LAST_INSERT_ID();";
			$this->Id = (int)$this->connector->ExecuteScalar();
		}
		return $rs;
	}

	public function Update($id) {
		$this->connector->CommandText =
"UPDATE t_aptjual_master SET
	 entity_id = ?entity_id
	,sbu_id = ?sbu_id
	,trx_no = ?trx_no
	,tanggal = ?tanggal
	,kontak = ?kontak
	,uraian = ?uraian
	,karyawan = ?karyawan
	,sub_total = ?sub_total
	,diskon_persen = ?diskon_persen
	,diskon_nilai = ?diskon_nilai
	,pajak_persen = ?pajak_persen
	,pajak_nilai = ?pajak_nilai
	,biaya_nilai = ?biaya_nilai
	,total_transaksi = ?total_transaksi
	,cara_bayar = ?cara_bayar
	,jumlah_bayar = ?jumlah_bayar
	,trx_status = ?trx_status
	,jumlah_retur = ?jumlah_retur
	,jenis_pasien = ?jenis_pasien
	,no_resep = ?no_resep
	,nm_dokter = ?nm_dokter
	,nm_pasien = ?nm_pasien
	,umr_pasien = ?umr_pasien
	,jns_beli = ?jns_beli
	,no_hp = ?no_hp
	,no_rm = ?no_rm
	,kd_dokter = ?kd_dokter
	,total_hpp = ?total_hpp
	,updateby_id = ?updateby_id
	,update_time = now()
	,relasi_id = ?relasi_id
	,no_terminal = ?no_terminal
WHERE id = ?id";
        $this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?sbu_id", $this->SbuId);
        $this->connector->AddParameter("?trx_no", $this->TrxNo);
        $this->connector->AddParameter("?tanggal", $this->Tanggal);
        $this->connector->AddParameter("?kontak", $this->Kontak);
        $this->connector->AddParameter("?uraian", $this->Uraian);
        $this->connector->AddParameter("?karyawan", $this->Karyawan);
        $this->connector->AddParameter("?sub_total", $this->SubTotal);
        $this->connector->AddParameter("?diskon_persen", $this->DiskonPersen);
        $this->connector->AddParameter("?diskon_nilai", $this->DiskonNilai);
        $this->connector->AddParameter("?pajak_persen", $this->PajakPersen);
        $this->connector->AddParameter("?pajak_nilai", $this->PajakNilai);
        $this->connector->AddParameter("?biaya_nilai", $this->BiayaNilai);
        $this->connector->AddParameter("?total_transaksi", $this->TotalTransaksi);
        $this->connector->AddParameter("?cara_bayar", $this->CaraBayar);
        $this->connector->AddParameter("?jumlah_bayar", $this->JumlahBayar);
        $this->connector->AddParameter("?trx_status", $this->TrxStatus);
        $this->connector->AddParameter("?jumlah_retur", $this->JumlahRetur);
        $this->connector->AddParameter("?jenis_pasien", $this->JenisPasien);
        $this->connector->AddParameter("?no_resep", $this->NoResep);
        $this->connector->AddParameter("?nm_dokter", $this->NmDokter);
        $this->connector->AddParameter("?nm_pasien", $this->NmPasien);
        $this->connector->AddParameter("?umr_pasien", $this->UmrPasien);
        $this->connector->AddParameter("?jns_beli", $this->JnsBeli);
        $this->connector->AddParameter("?no_hp", $this->NoHp,"char");
        $this->connector->AddParameter("?no_rm", $this->NoRm);
        $this->connector->AddParameter("?kd_dokter", $this->KdDokter);
        $this->connector->AddParameter("?total_hpp", $this->TotalHpp);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
        $this->connector->AddParameter("?relasi_id", $this->RelasiId);
        $this->connector->AddParameter("?no_terminal", $this->NoTerminal);
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1){
            $this->RecalculateInvoiceMaster($this->TrxNo);
        }
        return $rs;
	}

	public function ProsesBayar($id){
	    $sql = "Update t_aptjual_master a Set a.cara_bayar = ?cara_bayar, a.jumlah_bayar = ?jumlah_bayar, a.bayar_tunai = ?jumlah_bayar, a.trx_status = ?trx_status, updateby_id = ?updateby_id, update_time = now() WHERE a.id = ?id";
	    $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?id", $id);
        $this->connector->AddParameter("?cara_bayar", $this->CaraBayar);
        $this->connector->AddParameter("?jumlah_bayar", $this->JumlahBayar);
        $this->connector->AddParameter("?trx_status", $this->TrxStatus);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
        return  $this->connector->ExecuteNonQuery();
    }

	public function Delete($id) {
        $rsx = null;
        $this->connector->CommandText = "SELECT fc_ar_invoicemaster_unpost($id) As valresult;";
        $rsx = $this->connector->ExecuteQuery();
        //baru hapus invoicenya
		$this->connector->CommandText = "Delete From t_aptjual_master WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

    public function Void($id) {
        //fc_ar_invoicemaster_unpost
        $rsx = null;
        $rsz = null;
        //unpost stock dulu
        $this->connector->CommandText = "SELECT fc_ar_invoicemaster_unpost($id) As valresult;";
        $rsx = $this->connector->ExecuteQuery();
        //baru hapus invoicenya
        $this->connector->CommandText = "Update t_aptjual_master a Set a.trx_status = 3 WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        $rsz =  $this->connector->ExecuteNonQuery();
        return $rsz;
    }

    public function GetInvoiceDocNo(){
        $sql = 'Select fc_sys_getdocno(?cbi,?txc,?txd) As valout;';
        $txc = 'INV';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?cbi", $this->SbuId);
        $this->connector->AddParameter("?txc", $txc);
        $this->connector->AddParameter("?txd", $this->Tanggal);
        $rs = $this->connector->ExecuteQuery();
        $val = null;
        if($rs){
            $row = $rs->FetchAssoc();
            $val = $row["valout"];
        }
        return $val;
    }

    public function RecalculateInvoiceMaster($trxNo){
        $sql = 'Update t_aptjual_master a Set a.sub_total = 0, a.pajak_nilai = 0, a.diskon_nilai = 0, a.total_transaksi = 0 Where a.trx_no = ?trxNo;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?trxNo", $trxNo);
        $rs = $this->connector->ExecuteNonQuery();
        $sql = 'Update t_aptjual_master a
Join (Select c.trx_no, sum(c.sub_total) As sumPrice, sum(c.qty_keluar * c.hpp_nilai) as sumHpp From t_aptjual_detail c Group By c.trx_no) b
On a.trx_no = b.trx_no Set a.sub_total = b.sumPrice, a.diskon_nilai = if(a.diskon_persen > 0,round(b.sumPrice * (a.diskon_persen/100),0),0), a.total_hpp = b.sumHpp Where a.trx_no = ?trxNo;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?trxNo", $trxNo);
        $rs = $this->connector->ExecuteNonQuery();
        $sql = 'Update t_aptjual_master a Set a.pajak_nilai = if(a.pajak_persen > 0 And (a.sub_total - a.diskon_nilai) > 0,round((a.sub_total - a.diskon_nilai)  * (a.pajak_persen/100),0),0) Where a.trx_no = ?trxNo;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?trxNo", $trxNo);
        $rs = $this->connector->ExecuteNonQuery();
        $sql = 'Update t_aptjual_master a Set a.total_transaksi = (a.sub_total - a.diskon_nilai) + a.pajak_nilai + a.biaya_nilai Where a.trx_no = ?trxNo';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?trxNo", $trxNo);
        $rs = $this->connector->ExecuteNonQuery();
        return $rs;
    }

    public function GetInvoiceItemRow($trxNo){
        $this->connector->CommandText = "Select count(*) As valresult From t_aptjual_detail as a Where a.trx_no = ?trxNo;";
        $this->connector->AddParameter("?trxNo", $trxNo);
        $rs = $this->connector->ExecuteQuery();
        $row = $rs->FetchAssoc();
        return strval($row["valresult"]);
    }
}


// End of File: estimasi.php
