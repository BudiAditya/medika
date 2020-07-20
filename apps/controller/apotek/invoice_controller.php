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
            if ($acl->CheckUserAccess("invoice", "view", "apotek")) {
                $settings["actions"][] = array("Text" => "View", "Url" => "apotek.invoice/view/%s", "Class" => "bt_view", "ReqId" => 1,"Confirm" => "");
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
            if ($invoice->TrxStatus == 2) {
                //$this->persistence->SaveState("error", sprintf("Maaf Invoice No. %s sudah di-Posted- Tidak boleh diubah lagi..", $invoice->TrxNo));
                redirect_url("apotek.invoice/view/".$invoiceId);
            }
            if ($invoice->TrxStatus == 3) {
                $this->persistence->SaveState("error", sprintf("Maaf Invoice No. %s sudah di-Void- Tidak boleh diubah lagi..", $invoice->TrxNo));
                redirect_url("apotek.invoice");
            }
            if ($invoice->RelasiId == 0){
                $invoice->RelasiId = 3;
            }
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

    public function view($invoiceId = 0) {
        $acl = AclManager::GetInstance();
        $loader = null;
        $invoice = new Invoice();
        if ($invoiceId > 0 ) {
            $invoice = $invoice->LoadById($invoiceId);
            if ($invoice == null) {
                $this->persistence->SaveState("error", "Maaf Data Invoice dimaksud tidak ada pada database. Mungkin sudah dihapus!");
                redirect_url("apotek.invoice");
            }
        }else{
           $this->persistence->SaveState("error", "Maaf Data Invoice dimaksud tidak ditemukan!");
           redirect_url("apotek.invoice");
        }
        // load details
        $invoice->LoadDetails();
        $this->Set("acl", $acl);
        $this->Set("invoice", $invoice);
    }

    public function proses_master($invoiceId = 0) {
        require_once (MODEL . "master/relasi.php");
        require_once (MODEL . "master/dokter.php");
        $invoice = new Invoice();
        $log = new UserAdmin();
        if (count($this->postData) > 0) {
            $invoice->Id = $invoiceId;
            $invoice->EntityId = $this->userCompanyId;
            $invoice->SbuId = $this->userCabangId;
            $invoice->Tanggal = $this->GetPostValue("Tanggal");
            $invoice->TrxNo = $this->GetPostValue("TrxNo");
            $invoice->Uraian = $this->GetPostValue("Uraian");
            $invoice->RelasiId = $this->GetPostValue("RelasiId");
            if ($this->GetPostValue("Kontak") == null && $invoice->RelasiId > 0){
                $rls = new Relasi($invoice->RelasiId);
                if ($rls != null) {
                    $invoice->Kontak = $rls->NmRelasi;
                }else{
                    $invoice->Kontak = 'TUNAI';
                }
            }else {
                $invoice->Kontak = 'TUNAI';
            }
            $invoice->NoRm = $this->GetPostValue("NoRm");
            $invoice->KdDokter = $this->GetPostValue("KdDokter");
            if (strlen($invoice->KdDokter) > 2){
                $dok = new Dokter();
                $dok = $dok->FindByKode($invoice->KdDokter);
                if ($dok != null){
                    $invoice->NmDokter = $dok->NmDokter;
                }else{
                    $invoice->NmDokter = "-";
                }
            }else{
                $invoice->NmDokter = $this->GetPostValue("NmDokter");
            }
            $invoice->NmPasien = $this->GetPostValue("NmPasien");
            $invoice->UmrPasien = $this->GetPostValue("UmrPasien");
            $invoice->NoResep = $this->GetPostValue("NoResep");
            $invoice->NoHp = $this->GetPostValue("NoHp");
            $invoice->JnsBeli = $this->GetPostValue("JnsBeli");
            $invoice->JenisPasien = $this->GetPostValue("JenisPasien");
            $invoice->CaraBayar = $this->GetPostValue("CaraBayar");
            $invoice->TrxStatus = $this->GetPostValue("TrxStatus");
            $invoice->UpdatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            $invoice->CreatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            $invoice->NoTerminal = getenv("REMOTE_ADDR");
            $invoice->SubTotal = $this->GetPostValue("SubTotal") == null ? 0 : $this->GetPostValue("SubTotal");
            $invoice->DiskonPersen = $this->GetPostValue("DiskonPersen") == null ? 0 : $this->GetPostValue("DiskonPersen");
            $invoice->DiskonNilai = $this->GetPostValue("DiskonNilai") == null ? 0 : $this->GetPostValue("DiskonNilai");
            $invoice->PajakPersen = $this->GetPostValue("PajakPersen") == null ? 0 : $this->GetPostValue("PajakPersen");
            $invoice->PajakNilai = $this->GetPostValue("PajakNilai") == null ? 0 : $this->GetPostValue("PajakNilai");
            $invoice->BiayaNilai = str_replace(",","",$this->GetPostValue("BiayaNilai") == null ? 0 : $this->GetPostValue("BiayaNilai"));
            $invoice->TotalHpp = $this->GetPostValue("TotalHpp") == null ? 0 : $this->GetPostValue("TotalHpp");
            if ($invoice->Id == 0) {
                $invoice->TrxNo = $invoice->GetInvoiceDocNo();
                $rs = $invoice->Insert();
                if ($rs == 1) {
                    $log = $log->UserActivityWriter($this->userCabangId,'apotek.invoice','Add New Invoice',$invoice->TrxNo,'Success');
                    printf("OK|A|%d|%s",$invoice->Id,$invoice->TrxNo);
                }else{
                    $log = $log->UserActivityWriter($this->userCabangId,'apotek.invoice','Add New Invoice',$invoice->TrxNo,'Failed');
                    printf("ER|A|%d",$invoice->Id);
                }
            }else{
                $rs = $invoice->Update($invoice->Id);
                if ($rs == 1) {
                    $log = $log->UserActivityWriter($this->userCabangId,'apotek.invoice','Update Invoice',$invoice->TrxNo,'Success');
                    printf("OK|U|%d|%s",$invoice->Id,$invoice->TrxNo);
                }else{
                    $log = $log->UserActivityWriter($this->userCabangId,'apotek.invoice','Update Invoice',$invoice->TrxNo,'Failed');
                    printf("ER|U|%d",$invoice->Id);
                }
            }
        }else{
            printf("ER|X|%d",$invoiceId);
        }
    }

    private function ValidateMaster(Invoice $invoice) {
        if ($invoice->RelasiId == 0 || $invoice->RelasiId == null || $invoice->RelasiId == ''){
            $this->Set("error", "Customer tidak boleh kosong!");
            return false;
        }
        return true;
    }

    public function add_detail($invoiceId = null) {
        $log = new UserAdmin();
        $invoice = new Invoice($invoiceId);
        $invdetail = new InvoiceDetail();
        $invdetail->TrxNo = $invoice->TrxNo;
        $items = null;
        $is_item_exist = false;
        if (count($this->postData) > 0) {
            $invdetail->KodeBarang = $this->GetPostValue("aItemCode");
            $invdetail->NamaBarang = $this->GetPostValue("aItemName");
            $invdetail->QtyKeluar = $this->GetPostValue("aQty");
            $invdetail->Harga = $this->GetPostValue("aHarga");
            $invdetail->DiskonPersen = $this->GetPostValue("aDiskonPersen");
            $invdetail->DiskonNilai = $this->GetPostValue("aDiskonNilai");
            $invdetail->SubTotal = $this->GetPostValue("aSubTotal");
            $invdetail->HppNilai = $this->GetPostValue("aItemHpp");
            $invdetail->IsFree = $this->GetPostValue("aIsFree");
            $invdetail->Satuan = $this->GetPostValue("aSatuan");
            // periksa apa sudah ada item dengan harga yang sama, kalo ada gabungkan saja
            $invdetail_exists = new InvoiceDetail();
            $invdetail_exists = $invdetail_exists->FindDuplicate($invdetail->TrxNo,$invdetail->KodeBarang,$invdetail->Harga,$invdetail->DiskonPersen,$invdetail->DiskonNilai,$invdetail->IsFree);
            if ($invdetail_exists != null){
                // proses penggabungan disini
                /** @var $invdetail_exists InvoiceDetail */
                $is_item_exist = true;
                $invdetail->QtyKeluar+= $invdetail_exists->QtyKeluar;
                $invdetail->DiskonNilai+= $invdetail_exists->DiskonNilai;
                $invdetail->SubTotal+= $invdetail_exists->SubTotal;
            }
            // insert ke table
            if ($is_item_exist){
                // sudah ada item yg sama gabungkan..
                $rs = $invdetail->Update($invdetail_exists->Id);
                if ($rs > 0) {
                    $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Merge Invoice Detail-> Item Code: '.$invdetail->KodeBarang.' = '.$invdetail->QtyKeluar,$invoice->TrxNo,'Success');
                    print('OK|Proses simpan update berhasil!');
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Merge Invoice Detail-> Item Code: '.$invdetail->KodeBarang.' = '.$invdetail->QtyKeluar,$invoice->TrxNo,'Failed');
                    print('ER|Gagal proses update data!');
                }
            }else {
                // item baru simpan
                $rs = $invdetail->Insert() == 1;
                if ($rs > 0) {
                    $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Add Invoice Detail-> Item Code: '.$invdetail->KodeBarang.' = '.$invdetail->QtyKeluar,$invoice->TrxNo,'Success');
                    print('OK|Proses simpan data berhasil!');
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Add Invoice Detail-> Item Code: '.$invdetail->KodeBarang.' = '.$invdetail->QtyKeluar,$invoice->TrxNo,'Failed');
                    print('ER|Gagal proses simpan data!');
                }
            }
        }
    }

    public function edit_detail($invoiceId = null) {
        $log = new UserAdmin();
        $invoice = new Invoice($invoiceId);
        $invdetail = new InvoiceDetail();
        $invdetail->TrxNo = $invoice->TrxNo;
        $items = null;
        if (count($this->postData) > 0) {
            $invdetail->Id = $this->GetPostValue("aId");
            $invdetail->KodeBarang = $this->GetPostValue("aItemCode");
            $invdetail->NamaBarang = $this->GetPostValue("aItemName");
            $invdetail->QtyKeluar = $this->GetPostValue("aQty");
            $invdetail->Harga = $this->GetPostValue("aHarga");
            $invdetail->DiskonPersen = $this->GetPostValue("aDiskonPersen");
            $invdetail->DiskonNilai = $this->GetPostValue("aDiskonNilai");
            $invdetail->SubTotal = $this->GetPostValue("aSubTotal");
            $invdetail->HppNilai = $this->GetPostValue("aItemHpp");
            $invdetail->IsFree = $this->GetPostValue("aIsFree");
            $invdetail->Satuan = $this->GetPostValue("aSatuan");
            // insert ke table
            $rs = $invdetail->Update($invdetail->Id);
            if ($rs > 0) {
                $log = $log->UserActivityWriter($this->userCabangId, 'ar.invoice', 'Edit Invoice Detail-> Item Code: ' . $invdetail->KodeBarang . ' = ' . $invdetail->QtyKeluar, $invoice->TrxNo, 'Success');
                print('OK|Proses update data berhasil!');
            } else {
                $log = $log->UserActivityWriter($this->userCabangId, 'ar.invoice', 'Edit Invoice Detail-> Item Code: ' . $invdetail->KodeBarang . ' = ' . $invdetail->QtyKeluar, $invoice->TrxNo, 'Failed');
                print('ER|Gagal update data!');
            }
        }
    }


    public function delete_detail($id) {
        // Cek datanya
        $invdetail = new InvoiceDetail();
        $log = new UserAdmin();
        $invdetail = $invdetail->FindById($id);
        if ($invdetail == null) {
            print("Data tidak ditemukan..");
            return;
        }
        if ($invdetail->Delete($id,$invdetail->TrxNo) == 1) {
            $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Delete Invoice Detail-> Item Code: '.$invdetail->KodeBarang.' = '.$invdetail->QtyKeluar,$invdetail->TrxNo,'Success');
            printf("Data Detail Invoice ID: %d berhasil dihapus!",$id);
        }else{
            $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Delete Invoice Detail-> Item Code: '.$invdetail->KodeBarang.' = '.$invdetail->QtyKeluar,$invdetail->TrxNo,'Failed');
            printf("Maaf, Data Detail Invoice ID: %d gagal dihapus!",$id);
        }
    }

    //proses pembayaran
    public function prosesBayar($id){
        $data = null;
        if (count($this->postData) > 0) {
            $invoice = new Invoice();
            $invoice->TrxStatus = 2;
            $invoice->CaraBayar = $this->GetPostValue("caraBayar");
            $invoice->JumlahBayar = $this->GetPostValue("cashAmt");
            $invoice->UpdatebyId = $this->userUid;
            if ($invoice->ProsesBayar($id)) {
                $data = "OK|Payment Process Succeed!";
                $this->printStruk($id);
            }else{
                $data = "ER|Payment Process Fail!";
            }
        }else{
            $data = "ER|No data posted!";
        }
        print($data);
    }

    //proses print struk penjualan
    public function printStruk($id){
        //direct printing metode printer sharing
        $invoice = new Invoice($id);
        //data setup
        $prtName = "TMU220";
        $isCutter = 1;
        $isDrawer = 0;
        $printCounter = 1;
        $outletName = "APOTEK MEDIKA JAYA";
        $alamat1 = "MOPUYA UTARA";
        $alamat2 = "BOLAANG MONGONDOW - SULUT";
        $judul = "STRUK PENJUALAN";
        $kasir = AclManager::GetInstance()->GetCurrentUser()->RealName;
        if ($invoice != null) {
            $tmpdir = sys_get_temp_dir();   # ambil direktori temporary untuk simpan file.
            $file = tempnam($tmpdir, 'cetak');  # nama file temporary yang akan dicetak
            $handle = fopen($file, 'w');
            $condensed = Chr(27) . Chr(33) . Chr(4);
            $bold1 = Chr(27) . Chr(69);
            $bold0 = Chr(27) . Chr(70);
            $initialized = chr(27) . chr(64);
            $potong = chr(27) . chr(105);
            $bukalaci = chr(27) . chr(112) . chr(0) . chr(25) . chr(250);
            $condensed1 = chr(15);
            $condensed0 = chr(18);
            $data = $initialized;
            $data .= $condensed1;
            //$data .= "1234567890123456789012345678901234567890\n";
            $data .= $bold1 . str_repeat(' ', (int)20 - (strlen($outletName) / 2)) . $outletName . $bold0 . "\n";
            $data .= str_repeat(' ', (int)20 - (strlen($alamat1) / 2)) . $alamat1 . "\n";
            $data .= str_repeat(' ', (int)20 - (strlen($alamat2) / 2)) . $alamat2 . "\n";
            $data .= "----------------------------------------\n";
            $data .= str_repeat(' ', (int)20 - (strlen($judul) / 2)) . $judul . "\n";
            $txt = "Nomor : " . $invoice->TrxNo . " Tgl: " . $invoice->Tanggal."\n";
            //$data .= str_repeat(' ', (int)20 - (strlen($txt) / 2)) . $txt . "\n";
            $data .= $txt;
            $txt = "Cust. : " . strtoupper($invoice->Kontak)."\n";
            //$data .= str_repeat(' ', (int)20 - (strlen($txt) / 2)) . $txt . "\n";
            $data .= $txt;
            $txt = "Dokter: " . strtoupper($invoice->NmDokter)."\n";
            $data .= $txt;
            $txt = "Pasien: " . strtoupper($invoice->NmPasien)." (".$invoice->UmrPasien." Thn)\n";
            $data .= $txt;
            $data .= "----------------------------------------\n";
            $data .= "Kode Barang      QTY    Harga  Sub-Total\n";
            $data .= "----------------------------------------\n";
            $invoicedetails = $invoice->LoadDetails();
            $tx1 = null;
            $txt = null;
            foreach ($invoicedetails as $idx => $detail) {
                $txt = '';
                $tx1 = left(trim($detail->NamaBarang)." (".left($detail->Satuan,3).")",40);
                $txt.= $tx1 . "\n";
                $tx1 = trim($detail->KodeBarang);
                $txt.= $tx1.str_repeat(' ', 16 - strlen($tx1));
                $tx1 = number_format($detail->QtyKeluar,0);
                $tx1 = str_repeat(' ', 4 - strlen($tx1)) . $tx1 . ' ';
                $txt .= $tx1;
                $tx1 = number_format($detail->Harga,0);
                $tx1 = str_repeat(' ', 8 - strlen($tx1)) . $tx1 . ' ';
                $txt .= $tx1;
                $tx1 = number_format($detail->QtyKeluar * $detail->Harga, 0);
                $tx1 = str_repeat(' ', 10 - strlen($tx1)) . $tx1;
                $txt .= $tx1 . "\n";
                if ($detail->DiskonNilai > 0){
                    $tx1 = "- Discount: ".number_format($detail->DiskonPersen,0)."% = ".number_format($detail->DiskonNilai * $detail->QtyKeluar);
                    $tx1 = str_repeat(' ', 40 - strlen($tx1)) . $tx1;
                    $txt .= $tx1 . "\n";
                }
                $data .= $txt;
            }
            $data .= "----------------------------------------\n";
            if ($invoice->DiskonNilai + $invoice->PajakNilai > 0) {
                $txt = "Sub Total Rp. " . number_format($invoice->SubTotal);
                $data .= str_repeat(' ', 40 - strlen($txt)) . $txt . "\n";
                if ($invoice->DiskonNilai > 0) {
                    $txt = "Discount " . number_format($invoice->DiskonPersen) . "% Rp. " . number_format($invoice->DiskonNilai);
                    $data .= str_repeat(' ', 40 - strlen($txt)) . $txt . "\n";
                }
                if ($invoice->PajakNilai > 0) {
                    $txt = "Pajak " . number_format($invoice->PajakPersen) . "% Rp. " . number_format($invoice->PajakNilai);
                    $data .= str_repeat(' ', 40 - strlen($txt)) . $txt . "\n";
                }
                $data .= "----------------------------------------\n";
                $txt = "Total Rp. " . number_format($invoice->TotalTransaksi);
                $data .= str_repeat(' ', 40 - strlen($txt)) . $txt . "\n";
            } else {
                $txt = "Total Rp. " . number_format($invoice->SubTotal);
                $data .= str_repeat(' ', 40 - strlen($txt)) . $txt . "\n";
            }
            if ($invoice->JumlahBayar > $invoice->TotalTransaksi) {
                $txt = "Pembayaran Cash Rp. " . number_format($invoice->JumlahBayar);
                $data .= str_repeat(' ', 40 - strlen($txt)) . $txt . "\n";
                $txt = "Kembalian Rp. " . number_format($invoice->JumlahBayar - $invoice->TotalTransaksi);
                $data .= str_repeat(' ', 40 - strlen($txt)) . $txt . "\n";
            }
            $data .= "\n";
            $txt = "* TERIMA KASIH ATAS KUNJUNGAN ANDA *";
            $data .= str_repeat(' ', (int)20 - (strlen($txt) / 2)) . $txt . "\n";
            $data .= "Kasir: " . $kasir." T: ".date("Y-m-d H:i");
            $data .= "\n\n\n\n\n\n\n\n\n\n";
            if ($isDrawer == 1) {
                $data .= $bukalaci;
            }
            if ($isCutter == 1) {
                $data .= $potong;
            }
            fwrite($handle, $data);
            fclose($handle);
            copy($file, "//localhost/" . strtolower($prtName));  # Lakukan cetak
            unlink($file);
            print('OK|'.$id);
        }else{
            print('ER|Printing Fail!');
        }
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
