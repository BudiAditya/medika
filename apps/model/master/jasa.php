<?php
class Jasa extends EntityBase {
	public $Id;
	public $IsDeleted = false;
    public $EntityId;
	public $EntityCd;
    public $KdKlpJasa;
    public $KdKlpBilling;
    public $KlpJasa;
    public $KdJasa;
    public $NmJasa;
    public $UraianJasa;
    public $Satuan;
    public $TPoli = 0;
    public $TIgd = 0;
    public $TK3 = 0;
    public $TK2 = 0;
    public $TK1 = 0;
    public $TVip = 0;
    public $TPolSp = 0;
    public $TPolRd = 0;
    public $TPolKb = 0;
    public $TPersalinan = 0;
    public $CreatebyId;
    public $UpdatebyId;
    public $IsAuto = 0;
    public $KpBaru = 0;
    public $KpLama = 0;
    public $IsFeeDokter = 0;
    public $IsBpjs = 0;
    public $BpjsLimit = 0;
    public $BpjsLimitMode = 0;

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
		$this->EntityCd = $row["entity_cd"];
        $this->KdKlpJasa = $row["kd_klpjasa"];
        $this->KdKlpBilling = $row["kd_klpbilling"];
        $this->KlpJasa = $row["klp_jasa"];
        $this->KdJasa = $row["kd_jasa"];
        $this->NmJasa = $row["nm_jasa"];
        $this->UraianJasa = $row["uraian_jasa"];
        $this->Satuan = $row["satuan"];
        $this->TPoli = $row["t_poli"];
        $this->TIgd = $row["t_igd"];
        $this->TK3 = $row["t_k3"];
        $this->TK2 = $row["t_k2"];
        $this->TK1 = $row["t_k1"];
        $this->TVip = $row["t_vip"];
        $this->TPolSp = $row["t_polsp"];
        $this->TPolRd = $row["t_polrd"];
        $this->TPolKb = $row["t_polkb"];
        $this->TPersalinan = $row["t_persalinan"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
        $this->IsAuto = $row["is_auto"];
        $this->KpBaru = $row["kp_baru"];
        $this->KpLama = $row["kp_lama"];
        $this->IsFeeDokter = $row["is_feedokter"];
        $this->IsBpjs = $row["is_bpjs"];
        $this->BpjsLimit = $row["bpjs_limit"];
        $this->BpjsLimitMode = $row["bpjs_limit_mode"];
	}

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Jasa[]
	 */
	public function LoadAll($orderBy = "a.id", $includeDeleted = false) {
		if ($includeDeleted) {
			$this->connector->CommandText = "SELECT a.*, b.entity_cd, c.klp_jasa FROM m_jasa AS a JOIN sys_company AS b ON a.entity_id = b.entity_id Join m_klpjasa AS c On a.kd_klpjasa = c.kd_klpjasa ORDER BY $orderBy";
		} else {
			$this->connector->CommandText = "SELECT a.*, b.entity_cd, c.klp_jasa FROM m_jasa AS a JOIN sys_company AS b ON a.entity_id = b.entity_id Join m_klpjasa AS c On a.kd_klpjasa = c.kd_klpjasa WHERE a.is_deleted = 0 ORDER BY $orderBy";
		}
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Jasa();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	/**
	 * @param int $id
	 * @return Jasa
	 */
	public function FindById($id) {
		$this->connector->CommandText = "SELECT a.*, b.entity_cd, c.klp_jasa FROM m_jasa AS a JOIN sys_company AS b ON a.entity_id = b.entity_id Join m_klpjasa AS c On a.kd_klpjasa = c.kd_klpjasa WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$row = $rs->FetchAssoc();
		$this->FillProperties($row);
		return $this;
	}

    public function FindAutoCharge() {
        $this->connector->CommandText = "SELECT a.*, b.entity_cd, c.klp_jasa FROM m_jasa AS a JOIN sys_company AS b ON a.entity_id = b.entity_id Join m_klpjasa AS c On a.kd_klpjasa = c.kd_klpjasa WHERE a.is_auto = 1";
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
	 * @return Jasa
	 */
	public function LoadById($id) {
		return $this->FindById($id);
	}

	/**
	 * @param int $eti
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Jasa[]
	 */
	public function LoadByEntityId($eti, $orderBy = "a.id", $includeDeleted = false) {
		if ($includeDeleted) {
			$this->connector->CommandText = "SELECT a.*, b.entity_cd, c.klp_jasa FROM m_jasa AS a JOIN sys_company AS b ON a.entity_id = b.entity_id Join m_klpjasa AS c On a.kd_klpjasa = c.kd_klpjasa WHERE a.entity_id = ?eti ORDER BY $orderBy";
		} else {
			$this->connector->CommandText ="SELECT a.*, b.entity_cd, c.klp_jasa FROM m_jasa AS a JOIN sys_company AS b ON a.entity_id = b.entity_id Join m_klpjasa AS c On a.kd_klpjasa = c.kd_klpjasa WHERE a.entity_id = ?eti And a.is_deleted = 0 ORDER BY $orderBy";
		}
		$this->connector->AddParameter("?eti", $eti);
		$rs = $this->connector->ExecuteQuery();
        $result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Jasa();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	public function Insert() {
		$this->connector->CommandText = 'INSERT INTO m_jasa(t_persalinan,t_polsp,t_polrd,t_polkb,bpjs_limit,bpjs_limit_mode,is_bpjs,is_feedokter,is_auto,kp_baru,kp_lama,kd_klpbilling,t_poli,t_igd,t_k3,t_k2,t_k1,t_vip,entity_id,kd_jasa,nm_jasa,uraian_jasa,kd_klpjasa,satuan,createby_id,create_time) 
        VALUES(?t_persalinan,?t_polsp,?t_polrd,?t_polkb,?bpjs_limit,?bpjs_limit_mode,?is_bpjs,?is_feedokter,?is_auto,?kp_baru,?kp_lama,?kd_klpbilling,?t_poli,?t_igd,?t_k3,?t_k2,?t_k1,?t_vip,?entity_id,?kd_jasa,?nm_jasa,?uraian_jasa,?kd_klpjasa,?satuan,?createby_id,now())';
		$this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?kd_jasa", $this->KdJasa);
        $this->connector->AddParameter("?nm_jasa", $this->NmJasa);
        $this->connector->AddParameter("?uraian_jasa", $this->UraianJasa);
        $this->connector->AddParameter("?kd_klpjasa", $this->KdKlpJasa);
        $this->connector->AddParameter("?kd_klpbilling", $this->KdKlpBilling);
        $this->connector->AddParameter("?satuan", $this->Satuan);
        $this->connector->AddParameter("?t_poli", $this->TPoli == null ? 0 : $this->TPoli);
        $this->connector->AddParameter("?t_igd", $this->TIgd == null ? 0 : $this->TIgd);
        $this->connector->AddParameter("?t_k3", $this->TK3 == null ? 0 : $this->TK3);
        $this->connector->AddParameter("?t_k2", $this->TK2 == null ? 0 : $this->TK2);
        $this->connector->AddParameter("?t_k1", $this->TK1 == null ? 0 : $this->TK1);
        $this->connector->AddParameter("?t_vip", $this->TVip == null ? 0 : $this->TVip);
        $this->connector->AddParameter("?t_polsp", $this->TPolSp == null ? 0 : $this->TPolSp);
        $this->connector->AddParameter("?t_polrd", $this->TPolRd == null ? 0 : $this->TPolRd);
        $this->connector->AddParameter("?t_polkb", $this->TPolKb == null ? 0 : $this->TPolKb);
        $this->connector->AddParameter("?t_persalinan", $this->TPersalinan == null ? 0 : $this->TPersalinan);
        $this->connector->AddParameter("?is_auto", $this->IsAuto == null ? 0 : $this->IsAuto);
        $this->connector->AddParameter("?is_feedokter", $this->IsFeeDokter == null ? 0 : $this->IsFeeDokter);
        $this->connector->AddParameter("?kp_baru", $this->KpBaru == null ? 0 : $this->KpBaru);
        $this->connector->AddParameter("?kp_lama", $this->KpLama == null ? 0 : $this->KpLama);
        $this->connector->AddParameter("?is_bpjs", $this->IsBpjs);
        $this->connector->AddParameter("?bpjs_limit", $this->BpjsLimit);
        $this->connector->AddParameter("?bpjs_limit_mode", $this->BpjsLimitMode);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
		return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText = 'UPDATE m_jasa 
SET kd_klpbilling = ?kd_klpbilling, 
t_poli = ?t_poli, 
t_igd = ?t_igd, 
t_k3 = ?t_k3, 
t_k2 = ?t_k2, 
t_k1 = ?t_k1, 
t_vip = ?t_vip, 
t_polsp = ?t_polsp,
t_polrd = ?t_polrd,
t_polkb = ?t_polkb,
t_persalinan = ?t_persalinan,
uraian_jasa = ?uraian_jasa, 
entity_id = ?entity_id,
kd_jasa = ?kd_jasa,
nm_jasa = ?nm_jasa,
kd_klpjasa = ?kd_klpjasa,
satuan = ?satuan,
is_auto = ?is_auto,
kp_baru = ?kp_baru,
kp_lama = ?kp_lama,
is_feedokter = ?is_feedokter,
is_bpjs = ?is_bpjs,
updateby_id = ?updateby_id, 
bpjs_limit = ?bpjs_limit,
bpjs_limit_mode = ?bpjs_limit_mode,
update_time = now() WHERE id = ?id';
        $this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?kd_jasa", $this->KdJasa);
        $this->connector->AddParameter("?nm_jasa", $this->NmJasa);
        $this->connector->AddParameter("?uraian_jasa", $this->UraianJasa);
        $this->connector->AddParameter("?kd_klpjasa", $this->KdKlpJasa);
        $this->connector->AddParameter("?kd_klpbilling", $this->KdKlpBilling);
        $this->connector->AddParameter("?satuan", $this->Satuan);
        $this->connector->AddParameter("?t_poli", $this->TPoli == null ? 0 : $this->TPoli);
        $this->connector->AddParameter("?t_igd", $this->TIgd == null ? 0 : $this->TIgd);
        $this->connector->AddParameter("?t_k3", $this->TK3 == null ? 0 : $this->TK3);
        $this->connector->AddParameter("?t_k2", $this->TK2 == null ? 0 : $this->TK2);
        $this->connector->AddParameter("?t_k1", $this->TK1 == null ? 0 : $this->TK1);
        $this->connector->AddParameter("?t_vip", $this->TVip == null ? 0 : $this->TVip);
        $this->connector->AddParameter("?t_polsp", $this->TPolSp == null ? 0 : $this->TPolSp);
        $this->connector->AddParameter("?t_polrd", $this->TPolRd == null ? 0 : $this->TPolRd);
        $this->connector->AddParameter("?t_polkb", $this->TPolKb == null ? 0 : $this->TPolKb);
        $this->connector->AddParameter("?t_persalinan", $this->TPersalinan == null ? 0 : $this->TPersalinan);
        $this->connector->AddParameter("?is_auto", $this->IsAuto == null ? 0 : $this->IsAuto);
        $this->connector->AddParameter("?is_feedokter", $this->IsFeeDokter == null ? 0 : $this->IsFeeDokter);
        $this->connector->AddParameter("?kp_baru", $this->KpBaru == null ? 0 : $this->KpBaru);
        $this->connector->AddParameter("?kp_lama", $this->KpLama == null ? 0 : $this->KpLama);
        $this->connector->AddParameter("?is_bpjs", $this->IsBpjs);
        $this->connector->AddParameter("?bpjs_limit", $this->BpjsLimit);
        $this->connector->AddParameter("?bpjs_limit_mode", $this->BpjsLimitMode);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = "Delete a From m_jasa a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

    public function GetJSonJasa($entityId = 0, $jnsRawat = 0,  $filter = null,$sort = 'a.kd_jasa',$order = 'ASC') {
        $sql = "SELECT a.id,a.kd_jasa,a.nm_jasa,a.uraian_jasa,a.satuan,a.is_bpjs,if(a.is_bpjs = 1,'BPJS','Tidak') as ket_bpjs,a.bpjs_limit,a.bpjs_limit_mode,";
        if ($jnsRawat == 1){
            $sql.= "a.t_poli";
        }elseif ($jnsRawat == 2){
            $sql.= "a.t_igd";
        }elseif ($jnsRawat == 3){
            $sql.= "a.t_k3";
        }elseif ($jnsRawat == 4){
            $sql.= "a.t_k2";
        }elseif ($jnsRawat == 5){
            $sql.= "a.t_k1";
        }elseif ($jnsRawat == 6){
            $sql.= "a.t_vip";
        }elseif ($jnsRawat == 7){
            $sql.= "a.t_polsp";
        }elseif ($jnsRawat == 8){
            $sql.= "a.t_polrd";
        }elseif ($jnsRawat == 9){
            $sql.= "a.t_polkb";
        }elseif ($jnsRawat == 10){
            $sql.= "a.t_persalinan";
        }else{
            $sql.= "0";
        }
        $sql.= " AS tarif FROM m_jasa as a Where a.is_auto = 0 And a.is_deleted = 0";
        if ($entityId > 0){
            $sql.= " and a.entity_id = $entityId";
        }
        if ($filter != null){
            $sql.= " And (a.kd_jasa Like '%$filter%' Or a.nm_jasa Like '%$filter%')";
        }
        $this->connector->CommandText = $sql;
        $data['count'] = $this->connector->ExecuteQuery()->GetNumRows();
        $sql.= " Order By $sort $order";
        $this->connector->CommandText = $sql;
        $rows = array();
        $rs = $this->connector->ExecuteQuery();
        while ($row = $rs->FetchAssoc()){
            $rows[] = $row;
        }
        $result = array('total'=>$data['count'],'rows'=>$rows);
        return $result;
    }

    public function GetAutoKode($entityId = 0) {
        // function untuk menggenerate kode sku
        $kode = "JK".$entityId."001";
        $sqx = "SELECT max(a.kd_jasa) AS pKode FROM m_jasa a WHERE a.entity_id = $entityId And left(a.kd_jasa,3) = 'JK".$entityId."'";
        $this->connector->CommandText = $sqx;
        $rs = $this->connector->ExecuteQuery();
        if ($rs != null) {
            $row = $rs->FetchAssoc();
            $pkode = $row["pKode"];
            if (strlen($pkode) == 6){
                $counter = (int)(right($pkode,3))+1;
                $kode = "JK".$entityId.str_pad($counter,3,'0',STR_PAD_LEFT);
            }
        }
        return $kode;
    }
}
