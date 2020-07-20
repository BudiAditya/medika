<?php
class AssetList extends EntityBase {
	public $Id;
	public $IsDeleted = false;
    public $EntityId;
	public $SbuId = 0;
    public $KdKlpAsset;
    public $KlpAsset;
    public $KdAsset;
    public $NmAsset;
    public $Keterangan;
    public $ThnPerolehan;
    public $ReffNo;
    public $NilaiPerolehan = 0;
    public $NilaiBuku = 0;
    public $Qty = 0;
    public $ApreYear = 0;
    public $DeprYear = 0;
    public $MasaManfaat = 0;
    public $LastDepr = 201712;
    public $IsAktif = 1;
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
		$this->IsDeleted = $row["is_deleted"] == 1;
		$this->EntityId = $row["entity_id"];
		$this->KdKlpAsset = $row["kd_klpasset"];
        $this->KlpAsset = $row["klp_asset"];
        $this->KdAsset = $row["kd_asset"];
        $this->NmAsset = $row["nm_asset"];
        $this->Keterangan = $row["keterangan"];
        $this->ThnPerolehan = $row["thn_perolehan"];
        $this->ReffNo = $row["reff_no"];
        $this->NilaiPerolehan = $row["nilai_perolehan"];
        $this->NilaiBuku = $row["nilai_buku"];
        $this->IsAktif = $row["is_aktif"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
        $this->Qty = $row["qty"];
        $this->ApreYear = $row["apre_year"];
        $this->DeprYear = $row["depr_year"];
        $this->MasaManfaat = $row["masa_manfaat"];
        $this->LastDepr = $row["last_depr"];
	}

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Asset[]
	 */
	public function LoadAll($orderBy = "a.id", $includeDeleted = false) {
		$this->connector->CommandText = "SELECT a.*, b.klp_asset FROM m_asset AS a Join m_klpasset AS b On a.kd_klpasset = b.kd_klpasset WHERE a.is_deleted = 0 ORDER BY $orderBy";
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new AssetList();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	/**
	 * @param int $id
	 * @return Asset
	 */
	public function FindById($id) {
		$this->connector->CommandText = "SELECT a.*, b.klp_asset FROM m_asset AS a Join m_klpasset AS b On a.kd_klpasset = b.kd_klpasset WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$row = $rs->FetchAssoc();
		$this->FillProperties($row);
		return $this;
	}

	/**
	 * @param int $id
	 * @return Asset
	 */
	public function LoadById($id) {
		return $this->FindById($id);
	}

	/**
	 * @param int $eti
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Asset[]
	 */
	public function LoadByEntityId($eti, $orderBy = "a.id", $includeDeleted = false) {
		$this->connector->CommandText ="SELECT a.*, b.klp_asset FROM m_asset AS a Join m_klpasset AS b On a.kd_klpasset = b.kd_klpasset WHERE a.entity_id = ?eti And a.is_deleted = 0 ORDER BY $orderBy";
		$this->connector->AddParameter("?eti", $eti);
		$rs = $this->connector->ExecuteQuery();
        $result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new AssetList();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	public function Insert() {
		$this->connector->CommandText = 'INSERT INTO m_asset(last_depr,qty,masa_manfaat,apre_year,depr_year,is_aktif,reff_no,nilai_perolehan,nilai_buku,entity_id,kd_asset,nm_asset,keterangan,kd_klpasset,thn_perolehan,createby_id,create_time) 
        VALUES(?last_depr,?qty,?masa_manfaat,?apre_year,?depr_year,?is_aktif,?reff_no,?nilai_perolehan,?nilai_buku,?entity_id,?kd_asset,?nm_asset,?keterangan,?kd_klpasset,?thn_perolehan,?createby_id,now())';
		$this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?kd_asset", $this->KdAsset);
        $this->connector->AddParameter("?nm_asset", $this->NmAsset);
        $this->connector->AddParameter("?keterangan", $this->Keterangan);
        $this->connector->AddParameter("?kd_klpasset", $this->KdKlpAsset);
        $this->connector->AddParameter("?thn_perolehan", $this->ThnPerolehan);
        $this->connector->AddParameter("?reff_no", $this->ReffNo);
        $this->connector->AddParameter("?nilai_perolehan", $this->NilaiPerolehan == null ? 0 : $this->NilaiPerolehan);
        $this->connector->AddParameter("?nilai_buku", $this->NilaiBuku == null ? 0 : $this->NilaiBuku);
        $this->connector->AddParameter("?is_aktif", $this->IsAktif == null ? 0 : $this->IsAktif);
        $this->connector->AddParameter("?qty", $this->Qty == null ? 1 : $this->Qty);
        $this->connector->AddParameter("?last_depr", $this->LastDepr == null ? 201712 : $this->LastDepr);
        $this->connector->AddParameter("?masa_manfaat", $this->MasaManfaat == null ? 0 : $this->MasaManfaat);
        $this->connector->AddParameter("?apre_year", $this->ApreYear == null ? 0 : $this->ApreYear);
        $this->connector->AddParameter("?depr_year", $this->DeprYear == null ? 0 : $this->DeprYear);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
		return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText = 'UPDATE m_asset 
SET reff_no = ?reff_no, 
nilai_perolehan = ?nilai_perolehan, 
nilai_buku = ?nilai_buku, 
keterangan = ?keterangan, 
entity_id = ?entity_id,
kd_asset = ?kd_asset,
nm_asset = ?nm_asset,
kd_klpasset = ?kd_klpasset,
thn_perolehan = ?thn_perolehan,
is_aktif = ?is_aktif,
qty = ?qty,
apre_year = ?apre_year,
depr_year = ?depr_year,
last_depr = ?last_dpr,
masa_manfaat = ?masa_manfaat,
updateby_id = ?updateby_id, 
update_time = now() WHERE id = ?id';
        $this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?kd_asset", $this->KdAsset);
        $this->connector->AddParameter("?nm_asset", $this->NmAsset);
        $this->connector->AddParameter("?keterangan", $this->Keterangan);
        $this->connector->AddParameter("?kd_klpasset", $this->KdKlpAsset);
        $this->connector->AddParameter("?thn_perolehan", $this->ThnPerolehan);
        $this->connector->AddParameter("?reff_no", $this->ReffNo);
        $this->connector->AddParameter("?nilai_perolehan", $this->NilaiPerolehan == null ? 0 : $this->NilaiPerolehan);
        $this->connector->AddParameter("?nilai_buku", $this->NilaiBuku == null ? 0 : $this->NilaiBuku);
        $this->connector->AddParameter("?is_aktif", $this->IsAktif == null ? 0 : $this->IsAktif);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		$this->connector->AddParameter("?id", $id);
        $this->connector->AddParameter("?last_depr", $this->LastDepr == null ? 201712 : $this->LastDepr);
        $this->connector->AddParameter("?qty", $this->Qty == null ? 1 : $this->Qty);
        $this->connector->AddParameter("?masa_manfaat", $this->MasaManfaat == null ? 0 : $this->MasaManfaat);
        $this->connector->AddParameter("?apre_year", $this->ApreYear == null ? 0 : $this->ApreYear);
        $this->connector->AddParameter("?depr_year", $this->DeprYear == null ? 0 : $this->DeprYear);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = "Delete a From m_asset a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

    public function GetAutoKode($entityId = 0) {
        // function untuk menggenerate kode sku
        $kode = "AS".$entityId."001";
        $sqx = "SELECT max(a.kd_asset) AS pKode FROM m_asset a WHERE a.entity_id = $entityId And left(a.kd_asset,3) = 'AS".$entityId."'";
        $this->connector->CommandText = $sqx;
        $rs = $this->connector->ExecuteQuery();
        if ($rs != null) {
            $row = $rs->FetchAssoc();
            $pkode = $row["pKode"];
            if (strlen($pkode) == 6){
                $counter = (int)(right($pkode,3))+1;
                $kode = "AS".$entityId.str_pad($counter,3,'0',STR_PAD_LEFT);
            }
        }
        return $kode;
    }

    public function Load4Report($entityId, $sbuId = 0, $kdKlpAsset = '0'){
	    $sqx = "Select a.thn_perolehan,a.masa_manfaat,a.kd_klpasset,b.klp_asset,a.kd_asset,a.nm_asset,a.keterangan,a.masa_manfaat,a.thn_perolehan,a.qty,a.nilai_buku,a.depr_year,a.last_depr";
        $sqx.= " From m_asset AS a Join m_klpasset AS b On a.kd_klpasset = b.kd_klpasset Where a.entity_id = $entityId";
	    if ($sbuId > 0){
	        $sqx.= " And a.sbu_id = ".$sbuId;
        }
        if ($kdKlpAsset != '0'){
            $sqx.= " And a.kd_klpasset = '".$kdKlpAsset."'";
        }
        $sqx.= " Order By a.kd_klpasset, a.kd_asset";
        $this->connector->CommandText = $sqx;
        return $this->connector->ExecuteQuery();
    }
}
