<?php
class Tindakan extends EntityBase {
	public $Id;
	public $NoReg;
    public $TglLayanan;
    public $JamLayanan;
    public $JnsRawat;
    public $JnsRawatDesc;
    public $KdPetugas;
    public $KdDokter;
    public $KdKamar;
    public $KdJasa;
    public $NmJasa;
    public $UraianJasa;
    public $Qty = 1;
    public $TarifHarga = 0;
    public $Keterangan;
    public $TrxStatus = 0;
    public $CreatebyId;
    public $UpdatebyId;
    public $Diagnosa;
    public $Terapi;
    public $IsBpjs = 0;

	public function __construct($id = null) {
		parent::__construct();
		if (is_numeric($id)) {
			$this->FindById($id);
		}
	}

	public function FillProperties(array $row) {
		$this->Id = $row["id"];
		$this->NoReg = $row["no_reg"];
        $this->TglLayanan = strtotime($row["tgl_layanan"]);
        $this->JamLayanan = $row["jam_layanan"];
        $this->JnsRawat = $row["jns_rawat"];
        $this->JnsRawatDesc = $row["jns_rawat_desc"];
        $this->KdPetugas = $row["kd_petugas"];
        $this->KdDokter = $row["kd_dokter"];
        $this->KdKamar = $row["kd_kamar"];
        $this->KdJasa = $row["kd_jasa"];
        $this->NmJasa = $row["nm_jasa"];
        $this->UraianJasa = $row["uraian_jasa"];
        $this->Qty = $row["qty"];
        $this->TarifHarga = $row["tarif_harga"];
        $this->Keterangan = $row["keterangan"];
        $this->TrxStatus = $row["trx_status"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
        $this->Diagnosa = $row["diagnosa"];
        $this->Terapi = $row["terapi"];
        $this->IsBpjs = $row["is_bpjs"];
	}

    public function FormatTglLayanan($format = HUMAN_DATE) {
        return is_int($this->TglLayanan) ? date($format, $this->TglLayanan) : date($format);
    }

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Tindakan[]
	 */
	public function LoadAll($orderBy = "a.id") {
		$this->connector->CommandText = "SELECT a.* vw_t_tindakan AS a ORDER BY $orderBy";
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Tindakan();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

    public function LoadByNoRm($noRm = null,$orderBy = "a.id") {
        $this->connector->CommandText = "SELECT a.* From vw_t_tindakan AS a Join t_perawatan AS b On a.no_reg = b.no_reg Where b.no_rm = '".$noRm."' ORDER BY $orderBy";
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs != null) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new Tindakan();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

    public function LoadByNoReg($noReg = null,$orderBy = "a.id") {
        $this->connector->CommandText = "SELECT a.* From vw_t_tindakan AS a Where a.no_reg = '".$noReg."' ORDER BY $orderBy";
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs != null) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new Tindakan();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

    public function LoadBillingTindakanByNoReg($noReg = null,$orderBy = "a.klp_billing") {
        $this->connector->CommandText = "SELECT a.* From vw_t_billing_tindakan AS a Where a.no_reg = '".$noReg."' ORDER BY $orderBy";
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

	/**
	 * @param int $id
	 * @return Tindakan
	 */
	public function FindById($id) {
		$this->connector->CommandText = "SELECT a.* FROM vw_t_tindakan AS a WHERE a.id = ?id";
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
		$this->connector->CommandText = 'INSERT INTO t_tindakan(is_bpjs,diagnosa,terapi,jns_rawat,uraian_jasa,no_reg,tgl_layanan,jam_layanan,kd_petugas,kd_dokter,kd_kamar,kd_jasa,nm_jasa,qty,tarif_harga,keterangan,trx_status,createby_id,create_time) VALUES(?is_bpjs,?diagnosa,?terapi,?jns_rawat,?uraian_jasa,?no_reg,?tgl_layanan,?jam_layanan,?kd_petugas,?kd_dokter,?kd_kamar,?kd_jasa,?nm_jasa,?qty,?tarif_harga,?keterangan,?trx_status,?createby_id,now())';
		$this->connector->AddParameter("?no_reg", $this->NoReg);
        $this->connector->AddParameter("?tgl_layanan", date('Y-m-d',$this->TglLayanan));
        $this->connector->AddParameter("?jam_layanan", $this->JamLayanan);
        $this->connector->AddParameter("?jns_rawat", $this->JnsRawat);
        $this->connector->AddParameter("?kd_petugas", $this->KdPetugas);
        $this->connector->AddParameter("?kd_dokter", $this->KdDokter);
        $this->connector->AddParameter("?kd_kamar", $this->KdKamar);
        $this->connector->AddParameter("?kd_jasa", $this->KdJasa);
        $this->connector->AddParameter("?nm_jasa", $this->NmJasa);
        $this->connector->AddParameter("?uraian_jasa", $this->UraianJasa);
        $this->connector->AddParameter("?qty", $this->Qty);
        $this->connector->AddParameter("?tarif_harga", $this->TarifHarga);
        $this->connector->AddParameter("?keterangan", $this->Keterangan);
        $this->connector->AddParameter("?trx_status", $this->TrxStatus);
        $this->connector->AddParameter("?diagnosa", $this->Diagnosa);
        $this->connector->AddParameter("?terapi", $this->Terapi);
        $this->connector->AddParameter("?is_bpjs", $this->IsBpjs);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1) {
            $this->connector->CommandText = "SELECT LAST_INSERT_ID();";
            $this->Id = (int)$this->connector->ExecuteScalar();
            if (trim($this->KdJasa) != '' && $this->Qty > 0 && $this->TarifHarga > 0) {
                $this->PostTindakan($this->Id, $this->NoReg, $this->KdPetugas, $this->KdDokter, $this->KdJasa, $this->Qty, $this->TarifHarga);
            }
        }
        return $rs;
	}

	public function Update($id) {
		$this->connector->CommandText = 'UPDATE t_tindakan SET 
no_reg = ?no_reg,
tgl_layanan = ?tgl_layanan,
jam_layanan = ?jam_layanan, 
jns_rawat = ?jns_rawat,
kd_petugas = ?kd_petugas,  
kd_dokter = ?kd_dokter,
kd_kamar = ?kd_kamar, 
kd_jasa = ?kd_jasa, 
nm_jasa = ?nm_jasa,
uraian_jasa = ?uraian_jasa,
qty = ?qty,
tarif_harga = ?tarif_harga, 
keterangan = ?keterangan,
trx_status = ?trx_status,
updateby_id = ?updateby_id, 
diagnosa = ?diagnosa,
terapi = ?terapi,
is_bpjs = ?is_bpjs,
update_time = now() WHERE id = ?id';
        $this->connector->AddParameter("?no_reg", $this->NoReg);
        $this->connector->AddParameter("?tgl_layanan", date('Y-m-d',$this->TglLayanan));
        $this->connector->AddParameter("?jam_layanan", $this->JamLayanan);
        $this->connector->AddParameter("?jns_rawat", $this->JnsRawat);
        $this->connector->AddParameter("?kd_petugas", $this->KdPetugas);
        $this->connector->AddParameter("?kd_dokter", $this->KdDokter);
        $this->connector->AddParameter("?kd_kamar", $this->KdKamar);
        $this->connector->AddParameter("?kd_jasa", $this->KdJasa);
        $this->connector->AddParameter("?nm_jasa", $this->NmJasa);
        $this->connector->AddParameter("?uraian_jasa", $this->UraianJasa);
        $this->connector->AddParameter("?qty", $this->Qty);
        $this->connector->AddParameter("?tarif_harga", $this->TarifHarga);
        $this->connector->AddParameter("?keterangan", $this->Keterangan);
        $this->connector->AddParameter("?trx_status", $this->TrxStatus);
        $this->connector->AddParameter("?diagnosa", $this->Diagnosa);
        $this->connector->AddParameter("?terapi", $this->Terapi);
        $this->connector->AddParameter("?is_bpjs", $this->IsBpjs);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs){
            $sqx = "Delete a From t_tindakan_petugas a Where a.id_tindakan = $id";
            $this->connector->CommandText = $sqx;
            $this->connector->ExecuteNonQuery();
            $sqx = "Delete a From t_tindakan_dokter a Where a.id_tindakan = $id";
            $this->connector->CommandText = $sqx;
            $this->connector->ExecuteNonQuery();
            if (trim($this->KdJasa) != '' && $this->Qty > 0 && $this->TarifHarga > 0) {
                $this->PostTindakan($this->Id, $this->NoReg, $this->KdPetugas, $this->KdDokter, $this->KdJasa, $this->Qty, $this->TarifHarga);
            }
        }
		return $rs;
	}

	public function Delete($id) {
	    $sqx = "Delete a From t_tindakan_petugas a Where a.id_tindakan = $id";
        $this->connector->CommandText = $sqx;
        $this->connector->ExecuteNonQuery();
        $sqx = "Delete a From t_tindakan_dokter a Where a.id_tindakan = $id";
        $this->connector->CommandText = $sqx;
        $this->connector->ExecuteNonQuery();
		$this->connector->CommandText = "Delete a From t_tindakan a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

    public function DeleteByNoReg($noReg) {
        $this->connector->CommandText = "Delete a From t_tindakan a WHERE a.no_reg = ?no_reg";
        $this->connector->AddParameter("?no_reg", $noReg);
        return $this->connector->ExecuteNonQuery();
    }

    public function GetTindakanByNoReg($noReg){
	    //declaration
        $result = array();
	    //get total
        $sql = "Select count(*) From t_tindakan a Where a.no_reg = '".$noReg."'";
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteQuery();
        if ($rs != null) {
            $row = $rs->FetchRow();
            $result["total"] = $row[0];
        }
        //get rows
        $sql = "Select a.*,a.qty * a.tarif_harga as jumlah From vw_t_tindakan a Where a.no_reg = '".$noReg."' Order By a.tgl_layanan,a.jam_layanan";
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteQuery();
        $items = array();
        if ($rs != null) {
            while ($row = $rs->FetchObject()) {
                array_push($items, $row);
            }
        }
        $result["rows"] = $items;
        //get footer
        $sql = "Select sum(a.tarif_harga * a.qty) AS sumJumlah From t_tindakan a Where a.no_reg = '".$noReg."'";
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteQuery();
        if ($rs != null){
            while ($row = $rs->FetchArray()) {
                $entry = array('uraian_jasa' => 'Total','tarif_harga' => '','jumlah' => $row['sumJumlah']);
                $jsonData[] = $entry;
            }
        }
        $result["footer"] = $jsonData;
        return $result;
    }

    public function PostTindakan($id,$no_reg,$kd_petugas,$kd_dokter,$kd_jasa,$qty,$tarif){
        //posting pembagian jasa medis
        if (trim($kd_petugas) == '' && trim($kd_dokter) == ''){
            return 0;
        }else {
            // get kd_klpjasa
            $pjm_klinik = 0;
            $pjm_operator = 0;
            $pjm_pelaksana = 0;
            $jm_pelaksana = 0;
            $jm_operator = 0;
            $pembagi = 0;
            $sql = "Select b.pjm_klinik,b.pjm_pelaksana,b.pjm_operator From m_jasa a Join m_klpjasa b On a.kd_klpjasa = b.kd_klpjasa Where a.kd_jasa = '" . $kd_jasa . "'";
            $this->connector->CommandText = $sql;
            $rs = $this->connector->ExecuteQuery();
            if ($rs == null) {
                return 0;
            }else{
                $row = $rs->FetchRow();
                $pjm_klinik = $row[0];
                $pjm_pelaksana = $row[1];
                $pjm_operator = $row[2];
            }
            if (trim($kd_petugas) != '' && $pjm_pelaksana > 0){
                $apetugas = explode(',',$kd_petugas);
                $pembagi = count($apetugas);
                $jm_pelaksana = (($qty * $tarif) * ($pjm_pelaksana/100))/$pembagi;
                foreach ($apetugas as $pelaksana){
                    $sqx = "Insert Into t_tindakan_petugas (id_tindakan, no_reg, kd_petugas, kd_jasa, qty, tarif, pjm_pelaksana, pembagi, jm_pelaksana)";
                    $sqx.= " Values($id,'".$no_reg."','".$pelaksana."','".$kd_jasa."',$qty,$tarif,$pjm_pelaksana,$pembagi,$jm_pelaksana)";
                    $this->connector->CommandText = $sqx;
                    $this->connector->ExecuteNonQuery();
                }
            }
            if (trim($kd_dokter) != '' && $pjm_operator > 0){
                $adokter = explode(',',$kd_dokter);
                $pembagi = count($adokter);
                $jm_operator = (($qty * $tarif) * ($pjm_operator/100))/$pembagi;
                foreach ($adokter as $dokter){
                    $sqx = "Insert Into t_tindakan_dokter (id_tindakan, no_reg, kd_dokter, kd_jasa, qty, tarif, pjm_pelaksana, pembagi, jm_pelaksana)";
                    $sqx.= " Values($id,'".$no_reg."','".$dokter."','".$kd_jasa."',$qty,$tarif,$pjm_operator,$pembagi,$jm_operator)";
                    $this->connector->CommandText = $sqx;
                    $this->connector->ExecuteNonQuery();
                }
            }
            return 1;
        }
    }

    public function SetTindakanStatus($noReg = null,$trxStatus = 0){
        $this->connector->CommandText = "Update t_tindakan a Set a.trx_status = $trxStatus WHERE a.no_reg = ?no_reg And a.trx_status < 2";
        $this->connector->AddParameter("?no_reg", $noReg);
        return $this->connector->ExecuteNonQuery();
    }

    public function LoadMedrek4Report($startDate,$endDate,$jnsLaporan = 2){
        if ($jnsLaporan == 2) {
            $sql = "Select a.* From vw_rekap_medrek a Where a.tgl_layanan BETWEEN ?startdate and ?enddate";
        }else{
            $sql = "Select a.klp_jasa,a.kd_jasa,a.nm_jasa,a.jns_rawat,sum(a.sum_qty) as qty,a.tarif_harga,sum(a.sum_tarif) as jumlah From vw_rekap_medrek a Where a.tgl_layanan BETWEEN ?startdate and ?enddate Group By a.klp_jasa,a.kd_jasa,a.nm_jasa,a.jns_rawat,a.tarif_harga";
        }
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        return $this->connector->ExecuteQuery();
    }

    public function LoadTindakan4Report($jnsRawat = 0,$startDate,$endDate,$jnsLaporan = 2){
        if ($jnsLaporan == 2) {
            if ($jnsRawat > 0) {
                $sql = "Select a.* From vw_rekap_tindakan a Where a.jns_rawat = $jnsRawat And a.tgl_layanan BETWEEN ?startdate and ?enddate";
            }else{
                $sql = "Select a.* From vw_rekap_tindakan a Where a.tgl_layanan BETWEEN ?startdate and ?enddate";
            }
        }else{
            if ($jnsRawat > 0) {
                $sql = "Select a.klp_jasa,a.kd_jasa,a.nm_jasa,a.jenis_rawat,sum(a.sum_qty) as qty,a.tarif_harga,sum(a.sum_tarif) as jumlah From vw_rekap_tindakan a Where a.jns_rawat = $jnsRawat And a.tgl_layanan BETWEEN ?startdate and ?enddate Group By a.jns_rawat,a.klp_jasa,a.kd_jasa,a.nm_jasa,a.tarif_harga,a.jenis_rawat";
            }else{
                $sql = "Select a.klp_jasa,a.kd_jasa,a.nm_jasa,a.jenis_rawat,sum(a.sum_qty) as qty,a.tarif_harga,sum(a.sum_tarif) as jumlah From vw_rekap_tindakan a Where a.tgl_layanan BETWEEN ?startdate and ?enddate Group By a.klp_jasa,a.kd_jasa,a.nm_jasa,a.tarif_harga,a.jenis_rawat";
            }
        }
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        return $this->connector->ExecuteQuery();
    }

    public function LoadLab4Report($startDate,$endDate,$jnsLaporan = 2){
        if ($jnsLaporan == 2) {
            $sql = "Select a.* From vw_rekap_laboratorium a Where a.tgl_layanan BETWEEN ?startdate and ?enddate";
        }else{
            $sql = "Select a.klp_jasa,a.kd_jasa,a.nm_jasa,sum(a.sum_qty) as qty,a.tarif_harga,sum(a.sum_tarif) as jumlah From vw_rekap_laboratorium a Where a.tgl_layanan BETWEEN ?startdate and ?enddate Group By a.klp_jasa,a.kd_jasa,a.nm_jasa,a.tarif_harga";
        }
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        return $this->connector->ExecuteQuery();
    }

    public function LoadGizi4Report($startDate,$endDate,$jnsLaporan = 2){
        if ($jnsLaporan == 2) {
            $sql = "Select a.* From vw_rekap_gizi a Where a.tgl_layanan BETWEEN ?startdate and ?enddate";
        }else{
            $sql = "Select a.klp_jasa,a.kd_jasa,a.nm_jasa,sum(a.sum_qty) as qty,a.tarif_harga,sum(a.sum_tarif) as jumlah From vw_rekap_gizi a Where a.tgl_layanan BETWEEN ?startdate and ?enddate Group By a.klp_jasa,a.kd_jasa,a.nm_jasa,a.tarif_harga";
        }
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        return $this->connector->ExecuteQuery();
    }

    public function LoadAmbulance4Report($startDate,$endDate,$jnsLaporan = 2){
        if ($jnsLaporan == 2) {
            $sql = "Select a.* From vw_rekap_ambulance a Where a.tgl_layanan BETWEEN ?startdate and ?enddate";
        }else{
            $sql = "Select a.klp_jasa,a.kd_jasa,a.nm_jasa,sum(a.sum_qty) as qty,a.tarif_harga,sum(a.sum_tarif) as jumlah From vw_rekap_ambulance a Where a.tgl_layanan BETWEEN ?startdate and ?enddate Group By a.klp_jasa,a.kd_jasa,a.nm_jasa,a.tarif_harga";
        }
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        return $this->connector->ExecuteQuery();
    }

    public function LoadJmtikus4Report($jnsPetugas,$startDate,$endDate,$jnsLaporan = 2){
        if ($jnsLaporan == 2) {
            if ($jnsPetugas == 1) {
                $sql = "Select a.* From vw_rekap_jmtikus_petugas a Where a.tgl_layanan BETWEEN ?startdate and ?enddate";
            }else{
                $sql = "Select a.* From vw_rekap_jmtikus_dokter a Where a.tgl_layanan BETWEEN ?startdate and ?enddate";
            }
        }else{
            if ($jnsPetugas == 1) {
                $sql = "Select a.klp_jasa,a.kd_jasa,a.nm_jasa,a.kode,a.ats_nama,sum(a.qty) as sum_qty,sum(a.jm_pelaksana) as jumlah From vw_rekap_jmtikus_petugas a Where a.tgl_layanan BETWEEN ?startdate and ?enddate Group By a.klp_jasa,a.kd_jasa,a.nm_jasa,a.kode,a.ats_nama";
            }else{
                $sql = "Select a.klp_jasa,a.kd_jasa,a.nm_jasa,a.kode,a.ats_nama,sum(a.qty) as sum_qty,sum(a.jm_pelaksana) as jumlah From vw_rekap_jmtikus_dokter a Where a.tgl_layanan BETWEEN ?startdate and ?enddate Group By a.klp_jasa,a.kd_jasa,a.nm_jasa,a.kode,a.ats_nama";
            }
        }
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        return $this->connector->ExecuteQuery();
    }

    public function LoadJasmed4Report($startDate,$endDate,$jnsLaporan = 2){
        if ($jnsLaporan == 2) {
           $sql = "Select a.* From vw_rekap_jmv_dokter a Where a.tgl_layanan BETWEEN ?startdate and ?enddate";
        }else{
            $sql = "Select a.cara_bayar_desc,a.nm_jasa,a.kd_dokter,a.nm_dokter,sum(a.qty) as jum_visit,sum(a.jasa_visit) as jumlah From vw_rekap_jmv_dokter a Where a.tgl_layanan BETWEEN ?startdate and ?enddate Group By a.cara_bayar_desc,a.nm_jasa,a.kd_dokter,a.nm_dokter";
        }
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        return $this->connector->ExecuteQuery();
    }

    public function LoadRekapTindakan($jnsLaporan,$jnsPasien = 0,$jnsRawat = 0,$jnsJasa = null, $bulan = 0,$tahun = 0){
        if ($jnsLaporan == 1) {
            $sql = "Select a.* From vw_t_tindakan_detail a Where year(a.tgl_layanan) = ?tahun";
            if ($bulan > 0){
                $sql.= " And month(a.tgl_layanan) = ?bulan";
            }
            if ($jnsPasien > 0) {
                $sql.= " And a.cara_bayar = $jnsPasien";
            }
            if ($jnsRawat > 0) {
                $sql.= " And a.jns_rawat = $jnsRawat";
            }
            if ($jnsJasa != null && $jnsJasa != "") {
                $sql.= " And a.kd_klpjasa = '".$jnsJasa."'";
            }
            $sql.= " Order By a.tgl_layanan,a.jam_layanan,a.kd_klpjasa,a.kd_jasa";
        }else{
            $sql = "Select a.kd_klpjasa,a.klp_jasa,a.nm_jasa,a.uraian_jasa,a.nm_dokter,a.tarif_harga,a.is_feedokter,a.is_auto,sum(IF (a.is_bpjs = 1 And a.cara_bayar = 2,a.qty,0)) as sum_bqty,sum(IF (a.is_bpjs = 0 Or a.cara_bayar = 1,a.qty,0)) as sum_sqty";
            $sql.= " From vw_t_tindakan_detail a Where year(a.tgl_layanan) = ?tahun";
            if ($bulan > 0){
                $sql.= " And month(a.tgl_layanan) = ?bulan";
            }
            if ($jnsPasien > 0) {
                $sql.= " And a.cara_bayar = $jnsPasien";
            }
            if ($jnsRawat > 0) {
                $sql.= " And a.jns_rawat = $jnsRawat";
            }
            if ($jnsJasa != null && $jnsJasa != "") {
                $sql.= " And a.kd_klpjasa = '".$jnsJasa."'";
            }
            $sql.= " Group By a.kd_klpjasa,a.klp_jasa,a.nm_jasa,a.uraian_jasa,a.nm_dokter,a.tarif_harga,a.is_feedokter,a.is_auto";

        }
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?bulan", $bulan);
        $this->connector->AddParameter("?tahun", $tahun);
        return $this->connector->ExecuteQuery();
    }

    public function CheckLimitBpjs($NoReg,$TglTindakan,$KdJasa){
        $retval = null;
        $isbpjs = 0;
        $bpjslimit = 0;
        $bpjslimitmode = 0;
        $sql = "Select a.is_bpjs,a.bpjs_limit,a.bpjs_limit_mode From m_jasa AS a Where a.kd_jasa = '".$KdJasa."'";
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null) {
            $retval = "0|0";
        }else{
            $row = $rs->FetchRow();
            $isbpjs = $row[0];
            $bpjslimit = $row[1];
            $bpjslimitmode = $row[2];
            if ($isbpjs == 1 && $bpjslimit > 0 & $bpjslimitmode > 0){
                $retval = $bpjslimit.'|'.$bpjslimitmode;
            }else{
                $retval = "0|0";
            }
        }
        return $retval;
    }
}
