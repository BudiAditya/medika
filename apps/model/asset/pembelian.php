<?php
class Pembelian extends EntityBase {
	public $Id;
	public $EntityId = 1;
	public $SbuId = 0;
    public $JnsPembelian = 0;
    public $BankId = 0;
    public $NoBukti;
    public $TglPembelian;
    public $KdRelasi;
    public $KdKlpAsset;
    public $KdAsset;
    public $NmAsset;
    public $NoReff;
    public $Qty = 0;
    public $Harga = 0;
    public $Jumlah = 0;
    public $ApreYear = 0;
    public $DeprYear = 0;
    public $MasaManfaat = 0;
    public $StsPembelian = 0;
    public $CreatebyId = 0;
    public $UpdatebyId = 0;
    

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
        $this->JnsPembelian = $row["jns_pembelian"];
        $this->BankId = $row["bank_id"];
        $this->NoBukti = $row["no_bukti"];
        $this->TglPembelian = strtotime($row["tgl_pembelian"]);
        $this->KdRelasi = $row["kd_relasi"];
        $this->KdKlpAsset = $row["kd_klpasset"];
        $this->KdAsset = $row["kd_asset"];
        $this->NmAsset = $row["nm_asset"];
        $this->NoReff = $row["no_reff"];
        $this->Qty = $row["qty"];
        $this->Harga = $row["harga"];
        $this->Jumlah = $row["jumlah"];
        $this->ApreYear = $row["apre_year"];
        $this->DeprYear = $row["depr_year"];
        $this->MasaManfaat = $row["masa_manfaat"];
        $this->StsPembelian = $row["sts_pembelian"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
	}

    public function FormatTglPembelian($format = HUMAN_DATE) {
        return is_int($this->TglPembelian) ? date($format, $this->TglPembelian) : date($format);
    }

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Pembelian[]
	 */
	public function LoadAll($orderBy = "a.id") {
		$this->connector->CommandText = "SELECT a.* FROM t_pembelian AS a Where a.is_deleted = 0 ORDER BY $orderBy";
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Pembelian();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	/**
	 * @param int $id
	 * @return Pembelian
	 */
	public function FindById($id) {
		$this->connector->CommandText = "SELECT a.* FROM t_pembelian AS a WHERE a.id = ?id";
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
	    $sql = "INSERT INTO t_pembelian (bank_id,entity_id, sbu_id, jns_pembelian, no_bukti, tgl_pembelian, kd_relasi, kd_klpasset, kd_asset, nm_asset, no_reff, qty, harga, jumlah, apre_year, depr_year, masa_manfaat, sts_pembelian, createby_id, create_time)";
        $sql.= "Values(?bank_id,?entity_id, ?sbu_id, ?jns_pembelian, ?no_bukti, ?tgl_pembelian, ?kd_relasi, ?kd_klpasset, ?kd_asset, ?nm_asset, ?no_reff, ?qty, ?harga, ?jumlah, ?apre_year, ?depr_year, ?masa_manfaat, ?sts_pembelian, ?createby_id, now())";
		$this->connector->CommandText = $sql;
        $this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?sbu_id", $this->SbuId);
        $this->connector->AddParameter("?jns_pembelian", $this->JnsPembelian);
        $this->connector->AddParameter("?bank_id", $this->BankId);
        $this->connector->AddParameter("?no_bukti", $this->NoBukti);
        $this->connector->AddParameter("?tgl_pembelian", $this->TglPembelian != null ? date('Y-m-d',$this->TglPembelian) : null);
        $this->connector->AddParameter("?kd_relasi", $this->KdRelasi);
        $this->connector->AddParameter("?kd_klpasset", $this->KdKlpAsset);
        $this->connector->AddParameter("?kd_asset", $this->KdAsset);
        $this->connector->AddParameter("?nm_asset", $this->NmAsset);
        $this->connector->AddParameter("?no_reff", $this->NoReff);
        $this->connector->AddParameter("?qty", $this->Qty);
        $this->connector->AddParameter("?harga", $this->Harga);
        $this->connector->AddParameter("?jumlah", $this->Jumlah);
        $this->connector->AddParameter("?apre_year", $this->ApreYear);
        $this->connector->AddParameter("?depr_year", $this->DeprYear);
        $this->connector->AddParameter("?masa_manfaat", $this->MasaManfaat);
        $this->connector->AddParameter("?sts_pembelian", $this->StsPembelian);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1){
            $rs = $this->Posting($this->NoBukti,$this->CreatebyId);
        }
        return $rs;
	}

	public function Update($id) {
	    $sql =  'UPDATE t_pembelian a 
SET a.kd_klpasset = ?kd_klpasset
,a.entity_id = ?entity_id
,a.sbu_id = ?sbu_id
,a.no_reff = ?no_reff
,a.jns_pembelian = ?jns_pembelian
,a.kd_relasi = ?kd_relasi
,a.tgl_pembelian = ?tgl_pembelian
,a.no_bukti = ?no_bukti
,a.kd_asset = ?kd_asset
,a.nm_asset = ?nm_asset
,a.qty = ?qty
,a.harga = ?harga
,a.jumlah = ?jumlah
,a.depr_year = ?depr_year
,a.apre_year = ?apre_year
,a.masa_manfaat = ?masa_manfaat
,a.sts_pembelian = ?sts_pembelian
,a.updateby_id = ?updateby_id
,a.update_time = now()
,a.bank_id = ?bank_id
 WHERE id = ?id';
		$this->connector->CommandText = $sql;
        $this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?sbu_id", $this->SbuId);
        $this->connector->AddParameter("?jns_pembelian", $this->JnsPembelian);
        $this->connector->AddParameter("?bank_id", $this->BankId);
        $this->connector->AddParameter("?no_bukti", $this->NoBukti);
        $this->connector->AddParameter("?tgl_pembelian", $this->TglPembelian != null ? date('Y-m-d',$this->TglPembelian) : null);
        $this->connector->AddParameter("?kd_relasi", $this->KdRelasi);
        $this->connector->AddParameter("?kd_klpasset", $this->KdKlpAsset);
        $this->connector->AddParameter("?kd_asset", $this->KdAsset);
        $this->connector->AddParameter("?nm_asset", $this->NmAsset);
        $this->connector->AddParameter("?no_reff", $this->NoReff);
        $this->connector->AddParameter("?qty", $this->Qty);
        $this->connector->AddParameter("?harga", $this->Harga);
        $this->connector->AddParameter("?jumlah", $this->Jumlah);
        $this->connector->AddParameter("?apre_year", $this->ApreYear);
        $this->connector->AddParameter("?depr_year", $this->DeprYear);
        $this->connector->AddParameter("?masa_manfaat", $this->MasaManfaat);
        $this->connector->AddParameter("?sts_pembelian", $this->StsPembelian);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
        $this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = "Delete a From t_pembelian a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

    public function Posting($nobukti,$uId){
	    $sqx = "Select fcPostingPembelian(?id,?user_id) AS valresult";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?id", $nobukti);
        $this->connector->AddParameter("?user_id", $uId);
        $rs = $this->connector->ExecuteQuery();
        $row = $rs->FetchAssoc();
        return strval($row["valresult"]);
    }

    public function Unposting($nobukti,$uId){
        $sqx = "Select fcUnpostingPembelian(?id,?user_id) AS valresult";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?id", $nobukti);
        $this->connector->AddParameter("?user_id", $uId);
        $rs = $this->connector->ExecuteQuery();
        $row = $rs->FetchAssoc();
        return strval($row["valresult"]);
    }
}
