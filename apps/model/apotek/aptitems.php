<?php
class AptItems extends EntityBase {
    public $Id;
    public $IsDeleted = false;
    public $EntityId;
    public $SbuId;
    public $ItemCode;
    public $ItemName;
    public $ItemDescs;
    public $ItemTypeCode;
    public $ItemGroupCode;
    public $SupplierCode;
    public $ItemBarCode;
    public $ItemUnit;
    public $ItemStockQty = 0;
    public $MinStockQty = 0;
    public $StockInOrderQty = 0;
    public $ReorderQty = 0;
    public $CogsValue = 0;
    public $PurchasePrice = 0;
    public $SalePrice1 = 0;
    public $SalePrice2 = 0;
    public $SalePrice3 = 0;
    public $SalePrice4 = 0;
    public $SalePrice5 = 0;
    public $SalePrice6 = 0;
    public $SpMarkup1 = 0;
    public $SpMarkup2 = 0;
    public $SpMarkup3 = 0;
    public $SpMarkup4 = 0;
    public $SpMarkup5 = 0;
    public $SpMarkup6 = 0;
    public $TaxPct = 0;
    public $CommisionPct = 0;
    public $DiscountPct = 0;
    public $IsAktif = 1;
    public $NeedPassword = 0;
    public $HideOnStruk = 0;
    public $AllowOutOfStock = 0;
    public $LastPurchaseDate;
    public $IsObsolete = 0;
    public $IsBpjs = 0;
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
        $this->SbuId = $row["sbu_id"];
        $this->ItemCode = $row["item_code"];
        $this->ItemName = $row["item_name"];
        $this->ItemDescs = $row["item_descs"];
        $this->ItemTypeCode = $row["item_type_code"];
        $this->ItemGroupCode = $row["item_group_code"];
        $this->SupplierCode = $row["supplier_code"];
        $this->ItemBarCode = $row["item_barcode"];
        $this->ItemUnit = $row["item_unit"];
        $this->ItemStockQty = $row["item_stock_qty"];
        $this->MinStockQty = $row["min_stock_qty"];
        $this->StockInOrderQty = $row["stock_inorder_qty"];
        $this->ReorderQty = $row["reorder_qty"];
        $this->CogsValue = $row["cogs_value"];
        $this->PurchasePrice = $row["purchase_price"];
        $this->SalePrice1 = $row["sale_price1"];
        $this->SalePrice2 = $row["sale_price2"];
        $this->SalePrice3 = $row["sale_price3"];
        $this->SalePrice4 = $row["sale_price4"];
        $this->SalePrice5 = $row["sale_price5"];
        $this->SalePrice6 = $row["sale_price6"];
        $this->SpMarkup1 = $row["sp_markup1"];
        $this->SpMarkup2 = $row["sp_markup2"];
        $this->SpMarkup3 = $row["sp_markup3"];
        $this->SpMarkup4 = $row["sp_markup4"];
        $this->SpMarkup5 = $row["sp_markup5"];
        $this->SpMarkup6 = $row["sp_markup6"];
        $this->TaxPct = $row["tax_pct"];
        $this->CommisionPct = $row["commision_pct"];
        $this->DiscountPct = $row["discount_pct"];
        $this->IsAktif = $row["is_aktif"];
        $this->NeedPassword = $row["need_password"];
        $this->HideOnStruk = $row["hideon_struk"];
        $this->AllowOutOfStock = $row["allow_outofstock"];
        $this->LastPurchaseDate = $row["last_purchase_date"];
        $this->IsObsolete = $row["is_obsolete"];
        $this->IsBpjs = $row["is_bpjs"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
    }

    /**
     * @param string $orderBy
     * @param bool $includeDeleted
     * @return AptItems[]
     */
    public function LoadAll($orderBy = "a.item_name", $activeOnly = true) {
        if ($activeOnly) {
            $this->connector->CommandText = "SELECT a.* FROM m_apt_items AS a Where a.is_deleted = 0 And a.is_aktif <> 0 ORDER BY $orderBy";
        } else {
            $this->connector->CommandText = "SELECT a.* FROM m_apt_items AS a Where a.is_deleted = 0 ORDER BY $orderBy";
        }
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs != null) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new AptItems();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

    /**
     * @param int $id
     * @return AptItems
     */
    public function FindById($id) {
        $this->connector->CommandText = "SELECT a.* FROM m_apt_items AS a WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $row = $rs->FetchAssoc();
        $this->FillProperties($row);
        return $this;
    }

    public function FindByKode($kode) {
        $this->connector->CommandText = "SELECT a.* FROM m_apt_items AS a WHERE a.item_code = ?kode";
        $this->connector->AddParameter("?kode", $kode);
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
     * @return AptItems
     */
    public function LoadById($id) {
        return $this->FindById($id);
    }

    /**
     * @param int $eti
     * @param string $orderBy
     * @param bool $includeDeleted
     * @return AptItems[]
     */

    public function Insert() {
        $sql = "Insert Into m_apt_items (entity_id,sbu_id,item_code,item_name,item_descs,item_type_code,item_group_code,supplier_code,item_barcode,item_unit,item_stock_qty,min_stock_qty,";
        $sql.= "stock_inorder_qty,reorder_qty,cogs_value,purchase_price,sale_price1,sale_price2,sale_price3,sale_price4,sale_price5,sale_price6,sp_markup1,sp_markup2,sp_markup3,";
        $sql.= "sp_markup4,sp_markup5,sp_markup6,tax_pct,commision_pct,discount_pct,is_aktif,need_password,hideon_struk,allow_outofstock,last_purchase_date,is_obsolete,is_bpjs,createby_id,create_time)";
        $sql.= " Values (?entity_id,?sbu_id,?item_code,?item_name,?item_descs,?item_type_code,?item_group_code,?supplier_code,?item_barcode,?item_unit,?item_stock_qty,?min_stock_qty,";
        $sql.= "?stock_inorder_qty,?reorder_qty,?cogs_value,?purchase_price,?sale_price1,?sale_price2,?sale_price3,?sale_price4,?sale_price5,?sale_price6,?sp_markup1,?sp_markup2,?sp_markup3,";
        $sql.= "?sp_markup4,?sp_markup5,?sp_markup6,?tax_pct,?commision_pct,?discount_pct,?is_aktif,?need_password,?hideon_struk,?allow_outofstock,?last_purchase_date,?is_obsolete,?is_bpjs,?createby_id,now())";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?sbu_id", $this->SbuId);
        $this->connector->AddParameter("?item_code", $this->ItemCode);
        $this->connector->AddParameter("?item_name", $this->ItemName);
        $this->connector->AddParameter("?item_descs", $this->ItemDescs);
        $this->connector->AddParameter("?item_type_code", $this->ItemTypeCode);
        $this->connector->AddParameter("?item_group_code", $this->ItemGroupCode);
        $this->connector->AddParameter("?supplier_code", $this->SupplierCode);
        $this->connector->AddParameter("?item_barcode", $this->ItemBarCode);
        $this->connector->AddParameter("?item_unit", $this->ItemUnit);
        $this->connector->AddParameter("?item_stock_qty", $this->ItemStockQty);
        $this->connector->AddParameter("?min_stock_qty", $this->MinStockQty);
        $this->connector->AddParameter("?stock_inorder_qty", $this->StockInOrderQty);
        $this->connector->AddParameter("?reorder_qty", $this->ReorderQty);
        $this->connector->AddParameter("?cogs_value", $this->CogsValue);
        $this->connector->AddParameter("?purchase_price", $this->PurchasePrice);
        $this->connector->AddParameter("?sale_price1", $this->SalePrice1);
        $this->connector->AddParameter("?sale_price2", $this->SalePrice2);
        $this->connector->AddParameter("?sale_price3", $this->SalePrice3);
        $this->connector->AddParameter("?sale_price4", $this->SalePrice4);
        $this->connector->AddParameter("?sale_price5", $this->SalePrice5);
        $this->connector->AddParameter("?sale_price6", $this->SalePrice6);
        $this->connector->AddParameter("?sp_markup1", $this->SpMarkup1);
        $this->connector->AddParameter("?sp_markup2", $this->SpMarkup2);
        $this->connector->AddParameter("?sp_markup3", $this->SpMarkup3);
        $this->connector->AddParameter("?sp_markup4", $this->SpMarkup4);
        $this->connector->AddParameter("?sp_markup5", $this->SpMarkup5);
        $this->connector->AddParameter("?sp_markup6", $this->SpMarkup6);
        $this->connector->AddParameter("?tax_pct", $this->TaxPct);
        $this->connector->AddParameter("?commision_pct", $this->CommisionPct);
        $this->connector->AddParameter("?discount_pct", $this->DiscountPct);
        $this->connector->AddParameter("?is_aktif", $this->IsAktif);
        $this->connector->AddParameter("?need_password", $this->NeedPassword);
        $this->connector->AddParameter("?hideon_struk", $this->HideOnStruk);
        $this->connector->AddParameter("?allow_outofstock", $this->AllowOutOfStock);
        $this->connector->AddParameter("?last_purchase_date", $this->LastPurchaseDate);
        $this->connector->AddParameter("?is_obsolete", $this->IsObsolete);
        $this->connector->AddParameter("?is_bpjs", $this->IsBpjs);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
        return $this->connector->ExecuteNonQuery();
    }

    public function Update($id) {
        $this->connector->CommandText = 'UPDATE m_apt_items 
SET entity_id = ?entity_id
,sbu_id = ?sbu_id
,item_code = ?item_code
,item_name = ?item_name
,item_descs = ?item_descs
,item_type_code = ?item_type_code
,item_group_code = ?item_group_code
,supplier_code = ?supplier_code
,item_barcode = ?item_barcode
,item_unit = ?item_unit
,item_stock_qty = ?item_stock_qty
,min_stock_qty = ?min_stock_qty
,stock_inorder_qty = ?stock_inorder_qty
,reorder_qty = ?reorder_qty
,cogs_value = ?cogs_value
,purchase_price = ?purchase_price
,sale_price1 = ?sale_price1
,sale_price2 = ?sale_price2
,sale_price3 = ?sale_price3
,sale_price4 = ?sale_price4
,sale_price5 = ?sale_price5
,sale_price6 = ?sale_price6
,sp_markup1 = ?sp_markup1
,sp_markup2 = ?sp_markup2
,sp_markup3 = ?sp_markup3
,sp_markup4 = ?sp_markup4
,sp_markup5 = ?sp_markup5
,sp_markup6 = ?sp_markup6
,tax_pct = ?tax_pct
,commision_pct = ?commision_pct
,discount_pct = ?discount_pct
,is_aktif = ?is_aktif
,need_password = ?need_password
,hideon_struk = ?hideon_struk
,allow_outofstock = ?allow_outofstock
,last_purchase_date = ?last_purchase_date
,is_obsolete = ?is_obsolete
,is_bpjs = ?is_bpjs
,updateby_id = ?updateby_id
,update_time = now() WHERE id = ?id';
        $this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?sbu_id", $this->SbuId);
        $this->connector->AddParameter("?item_code", $this->ItemCode);
        $this->connector->AddParameter("?item_name", $this->ItemName);
        $this->connector->AddParameter("?item_descs", $this->ItemDescs);
        $this->connector->AddParameter("?item_type_code", $this->ItemTypeCode);
        $this->connector->AddParameter("?item_group_code", $this->ItemGroupCode);
        $this->connector->AddParameter("?supplier_code", $this->SupplierCode);
        $this->connector->AddParameter("?item_barcode", $this->ItemBarCode);
        $this->connector->AddParameter("?item_unit", $this->ItemUnit);
        $this->connector->AddParameter("?item_stock_qty", $this->ItemStockQty);
        $this->connector->AddParameter("?min_stock_qty", $this->MinStockQty);
        $this->connector->AddParameter("?stock_inorder_qty", $this->StockInOrderQty);
        $this->connector->AddParameter("?reorder_qty", $this->ReorderQty);
        $this->connector->AddParameter("?cogs_value", $this->CogsValue);
        $this->connector->AddParameter("?purchase_price", $this->PurchasePrice);
        $this->connector->AddParameter("?sale_price1", $this->SalePrice1);
        $this->connector->AddParameter("?sale_price2", $this->SalePrice2);
        $this->connector->AddParameter("?sale_price3", $this->SalePrice3);
        $this->connector->AddParameter("?sale_price4", $this->SalePrice4);
        $this->connector->AddParameter("?sale_price5", $this->SalePrice5);
        $this->connector->AddParameter("?sale_price6", $this->SalePrice6);
        $this->connector->AddParameter("?sp_markup1", $this->SpMarkup1);
        $this->connector->AddParameter("?sp_markup2", $this->SpMarkup2);
        $this->connector->AddParameter("?sp_markup3", $this->SpMarkup3);
        $this->connector->AddParameter("?sp_markup4", $this->SpMarkup4);
        $this->connector->AddParameter("?sp_markup5", $this->SpMarkup5);
        $this->connector->AddParameter("?sp_markup6", $this->SpMarkup6);
        $this->connector->AddParameter("?tax_pct", $this->TaxPct);
        $this->connector->AddParameter("?commision_pct", $this->CommisionPct);
        $this->connector->AddParameter("?discount_pct", $this->DiscountPct);
        $this->connector->AddParameter("?is_aktif", $this->IsAktif);
        $this->connector->AddParameter("?need_password", $this->NeedPassword);
        $this->connector->AddParameter("?hideon_struk", $this->HideOnStruk);
        $this->connector->AddParameter("?allow_outofstock", $this->AllowOutOfStock);
        $this->connector->AddParameter("?last_purchase_date", $this->LastPurchaseDate);
        $this->connector->AddParameter("?is_obsolete", $this->IsObsolete);
        $this->connector->AddParameter("?is_bpjs", $this->IsBpjs);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
        $this->connector->AddParameter("?id", $id);
        return $this->connector->ExecuteNonQuery();
    }

    public function Delete($id) {
        //$this->connector->CommandText = "Delete From m_apt_items WHERE id = ?id";
        $this->connector->CommandText = "Update m_apt_items AS a Set a.is_deleted = 1 WHERE id = ?id";
        $this->connector->AddParameter("?id", $id);
        return $this->connector->ExecuteNonQuery();
    }

    public function GetAutoItemCode($entityId = 0) {
        // function untuk menggenerate kode sku
        $kode = $entityId."0001";
        $sqx = "SELECT max(a.item_code) AS pKode FROM m_apt_items a WHERE a.entity_id = $entityId";
        $this->connector->CommandText = $sqx;
        $rs = $this->connector->ExecuteQuery();
        if ($rs != null) {
            $row = $rs->FetchAssoc();
            $kode = (int) $row["pKode"];
            $kode++;
        }
        return $kode;
    }

    public function GetJSonStock($entityId = 0, $filter = null,$sort = 'a.item_name',$order = 'ASC') {
        $sql = "SELECT a.id,a.item_code,a.item_name,a.item_unit,format(a.item_stock_qty,0) as qty_stock,format(a.sale_price1,0) as harga_jual,a.purchase_price,a.cogs_value FROM m_apt_items as a Where a.is_deleted = 0 And a.is_aktif = 1 And a.item_stock_qty > 0";
        if ($entityId > 0) {
            $sql.= " And a.entity_id = " . $entityId;
        }
        if ($filter != null){
            $sql.= " And (a.item_code Like '%$filter%' Or a.item_name Like '%$filter%')";
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
}
