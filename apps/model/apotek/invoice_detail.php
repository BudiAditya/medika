<?php

class InvoiceDetail extends EntityBase {
	public $Id;
    public $TrxNo;
	public $NamaBarang;
    public $KodeBarang;
    public $IsFree = 0;
    public $QtyKeluar = 0;
    public $QtyRetur = 0;
    public $Satuan;
	public $Harga = 0;
    public $DiskonPersen = 0;
    public $DiskonNilai = 0;
    public $SubTotal = 0;
    public $HppNilai = 0;
	// Helper Variable;
	public $MarkedForDeletion = false;


	public function FillProperties(array $row) {
		$this->Id = $row["id"];
        $this->TrxNo = $row["trx_no"];
        $this->IsFree = $row["is_free"];
        $this->KodeBarang = $row["kode_barang"];
		$this->NamaBarang = $row["nama_barang"];                
        $this->QtyKeluar = $row["qty_keluar"];
        $this->QtyRetur = $row["qty_retur"];
        $this->Satuan = $row["satuan"];
		$this->Harga = $row["harga"];
        $this->DiskonPersen = $row["diskon_persen"];
        $this->DiskonNilai = $row["diskon_nilai"];
        $this->SubTotal = $row["sub_total"];
        $this->HppNilai = $row["hpp_nilai"];
    }

	public function LoadById($id) {
		$this->connector->CommandText = "SELECT a.* FROM t_aptjual_detail AS a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

    public function FindById($id) {
        $this->connector->CommandText = "SELECT a.* FROM t_aptjual_detail AS a WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $this->FillProperties($rs->FetchAssoc());
        return $this;
    }

    public function FindDuplicate($trxNo,$kodeBarang,$hargaBarang,$disPersen,$disNilai,$isFree = 0) {
        $sql = "SELECT a.* FROM t_aptjual_detail AS a ";
        $sql.= " WHERE a.trx_no = '".$trxNo."' And a.kode_barang = '".$kodeBarang."' And a.harga = $hargaBarang And a.diskon_persen = $disPersen And a.diskon_nilai = $disNilai And a.is_free = $isFree";
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $this->FillProperties($rs->FetchAssoc());
        return $this;
    }
    
    public function LoadByTrxNo($trxNo, $orderBy = "a.id") {
        $this->connector->CommandText = "SELECT a.* FROM t_aptjual_detail AS a WHERE a.trx_no = ?trxNo ORDER BY $orderBy";
        $this->connector->AddParameter("?trxNo", $trxNo);
        $result = array();
        $rs = $this->connector->ExecuteQuery();
        if ($rs) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new InvoiceDetail();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

	public function Insert() {
	    $sql = "INSERT INTO t_aptjual_detail(trx_no, kode_barang, nama_barang, qty_keluar, qty_retur, satuan, harga, diskon_persen, diskon_nilai, sub_total,hpp_nilai,is_free)";
	    $sql.= " VALUES(?trx_no, ?kode_barang, ?nama_barang, ?qty_keluar, ?qty_retur, ?satuan, ?harga, ?diskon_persen, ?diskon_nilai, ?sub_total,?hpp_nilai,?is_free)";
		$this->connector->CommandText = $sql;
		$this->connector->AddParameter("?trx_no", $this->TrxNo);
        $this->connector->AddParameter("?item_id", $this->IsFree);
		$this->connector->AddParameter("?kode_barang", $this->KodeBarang, "char");
        $this->connector->AddParameter("?nama_barang", $this->NamaBarang);
        $this->connector->AddParameter("?qty_keluar", $this->QtyKeluar);
        $this->connector->AddParameter("?qty_retur", $this->QtyRetur);
		$this->connector->AddParameter("?satuan", $this->Satuan);
		$this->connector->AddParameter("?harga", $this->Harga);
        $this->connector->AddParameter("?diskon_persen", $this->DiskonPersen);
        $this->connector->AddParameter("?diskon_nilai", $this->DiskonNilai);
        $this->connector->AddParameter("?sub_total", $this->SubTotal);
        $this->connector->AddParameter("?hpp_nilai", $this->HppNilai);
        $this->connector->AddParameter("?is_free", $this->IsFree);
		$rs = $this->connector->ExecuteNonQuery();
        $rsx = null;
        $did = 0;
		if ($rs == 1) {
			$this->connector->CommandText = "SELECT LAST_INSERT_ID();";
			$this->Id = (int)$this->connector->ExecuteScalar();
            $did = $this->Id;
            //potong stock
            $sql = "Update m_apt_items a Set a.item_stock_qty = a.item_stock_qty - ?qty_keluar Where a.item_code = ?kode_barang";
            $this->connector->AddParameter("?kode_barang", $this->KodeBarang, "char");
            $this->connector->AddParameter("?qty_keluar", $this->QtyKeluar);
            $this->connector->CommandText = $sql;
            $rsx = $this->connector->ExecuteQuery();
            //update invoice master amount
            $this->UpdateInvoiceMaster($this->TrxNo);
		}
		return $rs;
	}

	public function Update($id) {
        //unpost stock dulu
        $rsx = null;
        $sql = "Update m_apt_items a Set a.item_stock_qty = a.item_stock_qty + ?qty_keluar Where a.item_code = ?kode_barang";
        $this->connector->AddParameter("?kode_barang", $this->KodeBarang, "char");
        $this->connector->AddParameter("?qty_keluar", $this->QtyKeluar);
        $this->connector->CommandText = $sql;
        $rsx = $this->connector->ExecuteQuery();
		$this->connector->CommandText =
"UPDATE t_aptjual_detail SET
	  trx_no = ?trx_no
	, nama_barang = ?nama_barang
	, satuan = ?satuan
	, harga = ?harga
	, sub_total = ?sub_total
	, kode_barang = ?kode_barang
	, qty_keluar = ?qty_keluar
	, qty_retur = ?qty_retur
	, diskon_persen = ?diskon_persen
	, diskon_nilai = ?diskon_nilai
	, hpp_nilai = ?hpp_nilai
	, is_free = ?is_free
WHERE id = ?id";
        $this->connector->AddParameter("?trx_no", $this->TrxNo);
        $this->connector->AddParameter("?item_id", $this->IsFree);
        $this->connector->AddParameter("?kode_barang", $this->KodeBarang, "char");
        $this->connector->AddParameter("?nama_barang", $this->NamaBarang);
        $this->connector->AddParameter("?qty_keluar", $this->QtyKeluar);
        $this->connector->AddParameter("?qty_retur", $this->QtyRetur);
        $this->connector->AddParameter("?satuan", $this->Satuan);
        $this->connector->AddParameter("?harga", $this->Harga);
        $this->connector->AddParameter("?diskon_persen", $this->DiskonPersen);
        $this->connector->AddParameter("?diskon_nilai", $this->DiskonNilai);
        $this->connector->AddParameter("?sub_total", $this->SubTotal);
        $this->connector->AddParameter("?hpp_nilai", $this->HppNilai);
        $this->connector->AddParameter("?is_free", $this->IsFree);
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1) {
            //potong stock lagi
            $sql = "Update m_apt_items a Set a.item_stock_qty = a.item_stock_qty - ?qty_keluar Where a.item_code = ?kode_barang";
            $this->connector->AddParameter("?kode_barang", $this->KodeBarang, "char");
            $this->connector->AddParameter("?qty_keluar", $this->QtyKeluar);
            $this->connector->CommandText = $sql;
            $rsx = $this->connector->ExecuteQuery();
            //update invoice master amount
            $this->UpdateInvoiceMaster($this->TrxNo);
        }
        return $rs;
	}

	public function Delete($id,$txn) {
        //unpost stock dulu
        $rsx = null;
        $this->FindById($id);
        $sql = "Update m_apt_items a Set a.item_stock_qty = a.item_stock_qty + ?qty_keluar Where a.item_code = ?kode_barang";
        $this->connector->AddParameter("?kode_barang", $this->KodeBarang, "char");
        $this->connector->AddParameter("?qty_keluar", $this->QtyKeluar);
        $this->connector->CommandText = $sql;
        $rsx = $this->connector->ExecuteQuery();
        //baru hapus detail
		$this->connector->CommandText = "DELETE FROM t_aptjual_detail WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1) {
            //update invoice master amount
            $this->UpdateInvoiceMaster($txn);
        }else{
            //ngotot...wkwkwkwk
            $this->UpdateInvoiceMaster($txn);
        }
        return $rs;
	}

    public function UpdateInvoiceMaster($trxNo){
        $sql = 'Update t_aptjual_master a Set a.sub_total = 0, a.pajak_nilai = 0, a.diskon_nilai = 0, a.total_hpp = 0, a.total_transaksi = 0 Where a.trx_no = ?trxNo;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?trxNo", $trxNo);
        $rs = $this->connector->ExecuteNonQuery();
        $sql = 'Update t_aptjual_master a
Join (Select c.trx_no, sum(c.sub_total) As sumHarga, sum(c.qty_keluar * c.hpp_nilai) as sumHpp From t_aptjual_detail c Group By c.trx_no) b
On a.trx_no = b.trx_no Set a.sub_total = b.sumHarga, a.diskon_nilai = if(a.diskon_persen > 0,round(b.sumHarga * (a.diskon_persen/100),0),0), a.total_hpp = b.sumHpp Where a.trx_no = ?trxNo;';
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
}
// End of File: estimasi_detail.php
