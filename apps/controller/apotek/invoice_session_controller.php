<?php
class InvoiceController extends AppController {
	private $userCompanyId;
	private $userCabangId;
    private $userUid;
    private $trxMonth;
    private $trxYear;

	protected function Initialize() {
		require_once(MODEL . "apotek/invoice.php");
		require_once(MODEL . "master/user_admin.php");
		$this->userCompanyId = $this->persistence->LoadState("entity_id");
		$this->userCabangId = $this->persistence->LoadState("cabang_id");
        $this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
        $this->trxMonth = $this->persistence->LoadState("acc_month");
        $this->trxYear = $this->persistence->LoadState("acc_year");
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();

		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 40);
		$settings["columns"][] = array("name" => "a.tanggal", "display" => "Tanggal", "width" => 60);
		$settings["columns"][] = array("name" => "a.trx_no", "display" => "No. Transaksi", "width" => 80);
        $settings["columns"][] = array("name" => "a.kontak", "display" => "Atas Nama", "width" => 150);
        $settings["columns"][] = array("name" => "a.jenis_pasien", "display" => "Jns Pasien", "width" => 70);
        $settings["columns"][] = array("name" => "a.nm_pasien", "display" => "Nama Pasien", "width" => 150);
        $settings["columns"][] = array("name" => "a.jns_beli", "display" => "Jns Beli", "width" => 70);
        $settings["columns"][] = array("name" => "a.nm_dokter", "display" => "Nama Dokter", "width" => 150);
        $settings["columns"][] = array("name" => "format(a.total_transaksi,0)", "display" => "Jumlah", "width" => 70, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.jumlah_retur,0)", "display" => "Retur", "width" => 70, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.total_transaksi - a.jumlah_retur,0)", "display" => "Penjualan", "width" => 70, "align" => "right");
        $settings["columns"][] = array("name" => "b.short_desc", "display" => "Status", "width" => 50);
        $settings["columns"][] = array("name" => "a.karyawan", "display" => "Kasir", "width" => 70);
        $settings["columns"][] = array("name" => "a.waktu", "display" => "Waktu", "width" => 50);

		$settings["filters"][] = array("name" => "a.tanggal", "display" => "Tanggal");
		$settings["filters"][] = array("name" => "a.trx_no", "display" => "No. Transaksi");
        $settings["filters"][] = array("name" => "a.kontak", "display" => "Atas Nama");
        $settings["filters"][] = array("name" => "a.jenis_pasien", "display" => "Jenis Pasien");
        $settings["filters"][] = array("name" => "a.jns_beli", "display" => "Jenis Pembelian");
        $settings["filters"][] = array("name" => "a.nm_dokter", "display" => "Nama Dokter");
        $settings["filters"][] = array("name" => "b.short_desc", "display" => "Status Transaksi");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Daftar Penjualan";

			if ($acl->CheckUserAccess("invoice", "add", "apotek")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "apotek.invoice/add/0", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("invoice", "edit", "apotek")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "apotek.invoice/add/%s", "Class" => "bt_edit", "ReqId" => 1,
											   "Error" => "Mohon memilih data penjualan terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu data",
											   "Info" => "Apakah anda yakin mau merubah data penjualan yang dipilih ?");
			}
			if ($acl->CheckUserAccess("invoice", "delete", "apotek")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "apotek.invoice/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
											   "Error" => "Mohon memilih data penjualan terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu data",
											   "Info" => "Apakah anda yakin mau menghapus data penjualan yang dipilih ?");
			}

            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("invoice", "view","apotek")) {
                $settings["actions"][] = array("Text" => "Laporan Penjualan", "Url" => "apotek.invoice/report","Target"=>"_blank","Class" => "bt_report", "ReqId" => 0);
            }

			$settings["def_filter"] = 0;
			$settings["def_order"] = 2;
			$settings["singleSelect"] = true;
		} else {
            $settings["from"] = "t_aptjual_master AS a Left Join sys_status_code AS b On a.trx_status = b.code And b.key = 'invoice_status'";
            if ($_GET["query"] == "") {
                $_GET["query"] = null;
                $settings["where"] = "a.trx_status < 3 And a.entity_id = ".$this->userCompanyId . " And year(a.tanggal) = " . $this->trxYear . " And month(a.tanggal) = " . $this->trxMonth;
            } else {
                $settings["where"] = "a.entity_id = ".$this->userCompanyId;
            }
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

    /* entry data penjualan*/
    public function add($invoiceId = 0) {
        //require_once (MODEL . "pelayanan/pasien.php");
        $acl = AclManager::GetInstance();
        $loader = null;
        $invoice = new Invoice();
        if ($invoiceId > 0 ) {
            $invoice = $invoice->LoadById($invoiceId);
            if ($invoice == null) {
                $this->persistence->SaveState("error", "Maaf Data Invoice dimaksud tidak ada pada database. Mungkin sudah dihapus!");
                redirect_url("apotek.invoice");
            }
            if ($invoice->TrxStatus == 3) {
                $this->persistence->SaveState("error", sprintf("Maaf Invoice No. %s sudah di-Void- Tidak boleh diubah lagi..", $invoice->InvoiceNo));
                redirect_url("apotek.invoice");
            }
            if ($invoice->RelasiId == 0){
                $invoice->RelasiId = 3;
            }
            $this->persistence->SaveState("invDate",$invoice->FormatTanggal(JS_DATE));
            $this->persistence->SaveState("relasiId",$invoice->RelasiId);
            $this->persistence->SaveState("nmPasien",$invoice->NmPasien." (session)");
        }else{
            $invoice->Tanggal = date('Y-m-d');
            $invoice->RelasiId = 3;
        }
        // load details
        $invoice->LoadDetails();
        //load data medrek
        //$loader = new Pasien();
        //$pasien = $loader->LoadByEntityId($this->userCompanyId);
        //kirim ke view
        //$this->Set("pasien", $pasien);
        $this->Set("invoice", $invoice);
        $this->Set("acl", $acl);
        $this->Set("itemsCount", $this->InvoiceItemsCount($invoiceId));
    }

    public function proses_master($invoiceId = 0) {
        $invoice = new Invoice();
        $log = new UserAdmin();
        if (count($this->postData) > 0) {
            $invoice->Id = $invoiceId;
            $invoice->CabangId = $this->GetPostValue("CabangId");
            $invoice->GudangId = $this->GetPostValue("GudangId");
            $invoice->InvoiceDate = date('Y-m-d',strtotime($this->GetPostValue("InvoiceDate")));
            $invoice->InvoiceNo = $this->GetPostValue("InvoiceNo");
            $invoice->InvoiceDescs = $this->GetPostValue("InvoiceDescs");
            $invoice->CustomerId = $this->GetPostValue("CustomerId");
            $invoice->CustLevel = $this->GetPostValue("CustLevel") == null ? 0 : $this->GetPostValue("CustLevel");
            $invoice->SalesId = $this->GetPostValue("SalesId");
            $invoice->ExSoNo = $this->GetPostValue("ExSoNo");
            if ($this->GetPostValue("InvoiceStatus") == null || $this->GetPostValue("InvoiceStatus") == 0){
                $invoice->InvoiceStatus = 1;
            }else{
                $invoice->InvoiceStatus = $this->GetPostValue("InvoiceStatus");
            }
            $invoice->UpdatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            $invoice->CreatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            if($this->GetPostValue("PaymentType") == null){
                $invoice->PaymentType = 0;
                $invoice->InvoiceStatus = 1;
            }else{
                $invoice->PaymentType = $this->GetPostValue("PaymentType");
                if ($invoice->PaymentType == 0){
                    $invoice->InvoiceStatus = 1;
                }
            }
            if($this->GetPostValue("CreditTerms") == null){
                $invoice->CreditTerms = 0;
            }else{
                $invoice->CreditTerms = $this->GetPostValue("CreditTerms");
            }
            $invoice->BaseAmount = $this->GetPostValue("BaseAmount") == null ? 0 : $this->GetPostValue("BaseAmount");
            $invoice->Disc1Pct = $this->GetPostValue("Disc1Pct") == null ? 0 : $this->GetPostValue("Disc1Pct");
            $invoice->Disc1Amount = $this->GetPostValue("Disc1Amount") == null ? 0 : $this->GetPostValue("Disc1Amount");
            $invoice->Disc2Pct = 0;
            $invoice->Disc2Amount = 0;
            $invoice->PaidAmount = 0;
            $invoice->TaxPct = $this->GetPostValue("TaxPct") == null ? 0 : $this->GetPostValue("TaxPct");
            $invoice->TaxAmount = $this->GetPostValue("TaxAmount") == null ? 0 : $this->GetPostValue("TaxAmount");
            $invoice->OtherCosts = $this->GetPostValue("OtherCosts") == null ? 0 : $this->GetPostValue("OtherCosts");
            $invoice->OtherCostsAmount = str_replace(",","",$this->GetPostValue("OtherCostsAmount") == null ? 0 : $this->GetPostValue("OtherCostsAmount"));
            $invoice->InvoiceType = $this->GetPostValue("InvoiceType");
            if ($invoice->Id == 0) {
                $invoice->InvoiceNo = $invoice->GetInvoiceDocNo();
                $rs = $invoice->Insert();
                if ($rs == 1) {
                    $log = $log->UserActivityWriter($this->userCabangId,'apotek.invoice','Add New Invoice',$invoice->InvoiceNo,'Success');
                    printf("OK|A|%d|%s",$invoice->Id,$invoice->InvoiceNo);
                }else{
                    $log = $log->UserActivityWriter($this->userCabangId,'apotek.invoice','Add New Invoice',$invoice->InvoiceNo,'Failed');
                    printf("ER|A|%d",$invoice->Id);
                }
            }else{
                $rs = $invoice->Update($invoice->Id);
                if ($rs == 1) {
                    $log = $log->UserActivityWriter($this->userCabangId,'apotek.invoice','Update Invoice',$invoice->InvoiceNo,'Success');
                    printf("OK|U|%d|%s",$invoice->Id,$invoice->InvoiceNo);
                }else{
                    $log = $log->UserActivityWriter($this->userCabangId,'apotek.invoice','Update Invoice',$invoice->InvoiceNo,'Failed');
                    printf("ER|U|%d",$invoice->Id);
                }
            }
        }else{
            printf("ER|X|%d",$invoiceId);
        }
    }

    private function ValidateMaster(Invoice $invoice) {
        if ($invoice->CustomerId == 0 || $invoice->CustomerId == null || $invoice->CustomerId == ''){
            $this->Set("error", "Customer tidak boleh kosong!");
            return false;
        }
        if ($invoice->SalesId == 0 || $invoice->SalesId == null || $invoice->SalesId == ''){
            $this->Set("error", "Salesman tidak boleh kosong!");
            return false;
        }
        if ($invoice->PaymentType == 1 && $invoice->CreditTerms == 0){
            $this->Set("error", "Lama kredit belum diisi!");
            return false;
        }
        return true;
    }

    public function getInvoiceItemRows($id){
        $invoice = new Invoice();
        $rows = $invoice->GetInvoiceItemRow($id);
        print($rows);
    }

    public function InvoiceItemsCount($id){
        $invoice = new Invoice();
        $rows = $invoice->GetInvoiceItemRow($id);
        return $rows;
    }

    public function getPlainPasien($noRM){
        require_once (MODEL . "pelayanan/pasien.php");
        $pasien = new Pasien();
        $pasien = $pasien->FindByNoRm($noRM);
        $data = null;
        if ($pasien == null){
            $data = "ER|0";
        }else{
            $data = "OK|".$pasien->NmPasien."|".$pasien->Usia."|".$pasien->NoHp;
        }
        print($data);
    }

    public function getStockJson(){
        require_once (MODEL . "apotek/aptitems.php");
        $filter = isset($_POST['q']) ? strval($_POST['q']) : '';
        $stock = new AptItems();
        $stock = $stock->GetJSonStock($this->userCompanyId,$filter);
        echo json_encode($stock);
    }

    public function getStockPlain($itc){
        require_once (MODEL . "apotek/aptitems.php");
        $stock = new AptItems();
        $stock = $stock->FindByKode($itc);
        $data = null;
        if ($stock == null){
            $data = "ER|0";
        }else{
            $data = "OK|".$stock->ItemName."|".$stock->ItemStockQty."|".$stock->ItemUnit."|".$stock->SalePrice1."|".$stock->PurchasePrice."|".$stock->CogsValue;
        }
        print($data);
    }

}
