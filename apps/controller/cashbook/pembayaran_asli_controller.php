<?php
class PembayaranController extends AppController {
	private $userCompanyId;
	private $userCabangId;
	private $userUid;

	protected function Initialize() {
		require_once(MODEL . "cashbook/hutang.php");
        require_once(MODEL . "master/user_admin.php");
		$this->userCompanyId = $this->persistence->LoadState("entity_id");
		$this->userCabangId = $this->persistence->LoadState("cabang_id");
        $this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();
		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 40);
		$settings["columns"][] = array("name" => "a.no_bukti", "display" => "No. Bukti", "width" => 90);
        $settings["columns"][] = array("name" => "a.tgl_hutang", "display" => "Tanggal", "width" => 70);
        $settings["columns"][] = array("name" => "a.keterangan", "display" => "Keterangan", "width" => 200);
        $settings["columns"][] = array("name" => "b.nm_relasi", "display" => "Nama Relasi", "width" => 120);
        $settings["columns"][] = array("name" => "a.no_reff", "display" => "Refferensi", "width" => 80);
        $settings["columns"][] = array("name" => "format(a.jum_hutang,0)", "display" => "Hutang", "width" => 80, "align" => 'right');
        $settings["columns"][] = array("name" => "format(a.jum_terbayar,0)", "display" => "Terbayar", "width" => 80, "align" => 'right');
        $settings["columns"][] = array("name" => "format(a.jum_hutang - a.jum_terbayar,0)", "display" => "Outstanding", "width" => 80, "align" => 'right');
        $settings["columns"][] = array("name" => "a.tgl_terbayar", "display" => "Tgl Bayar", "width" => 70);
        $settings["columns"][] = array("name" => "a.no_bukti_bayar", "display" => "Bukti Pembayaran", "width" => 90);
        $settings["columns"][] = array("name" => "a.bank_id", "display" => "Debet Akun", "width" => 80);
        $settings["columns"][] = array("name" => "if(a.sts_hutang = 1,'Posted',if(a.sts_hutang = 2,'Paid','Draft'))", "display" => "Status", "width" => 50);
        $settings["columns"][] = array("name" => "if(a.create_mode = 0,'Manual','Auto')", "display" => "Source", "width" => 50);

		$settings["filters"][] = array("name" => "a.no_bukti", "display" => "No. Bukti Hutang");
		$settings["filters"][] = array("name" => "a.no_bukti_bayar", "display" => "No.Bukti Pembayaran");
        $settings["filters"][] = array("name" => "a.tgl_hutang", "display" => "Tanggal Hutang");
        $settings["filters"][] = array("name" => "a.tgl_terbayar", "display" => "Tanggal Pembayaran");
        $settings["filters"][] = array("name" => "a.keterangan", "display" => "Keterangan");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Hutang Supplier";
            //button action
            if ($acl->CheckUserAccess("cashbook.pembayaran", "add")) {
                $settings["actions"][] = array("Text" => "Add", "Url" => "cashbook.pembayaran/add", "Class" => "bt_add", "ReqId" => 0);
            }
            if ($acl->CheckUserAccess("cashbook.pembayaran", "edit")) {
                $settings["actions"][] = array("Text" => "Edit", "Url" => "cashbook.pembayaran/edit/%s", "Class" => "bt_edit", "ReqId" => 1);
            }
            if ($acl->CheckUserAccess("cashbook.pembayaran", "delete")) {
                $settings["actions"][] = array("Text" => "Delete", "Url" => "cashbook.pembayaran/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
                    "Error" => "Mohon memilih Data Transaksi terlebih dahulu sebelum proses penghapusan.\nPERHATIAN: Mohon memilih tepat satu data.",
                    "Confirm" => "Apakah anda mau menghapus data transaksi yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
            }
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("cashbook.pembayaran", "approve")) {
                $settings["actions"][] = array("Text" => "Posting", "Url" => "cashbook.pembayaran/posting/%s", "Class" => "bt_approve", "ReqId" => 1,
                    "Error" => "Mohon memilih Data Transaksi terlebih dahulu sebelum proses posting.",
                    "Confirm" => "Posting data transaksi yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
                $settings["actions"][] = array("Text" => "Unposting", "Url" => "cashbook.pembayaran/unposting/%s", "Class" => "bt_reject", "ReqId" => 1,
                    "Error" => "Mohon memilih Data Transaksi terlebih dahulu sebelum proses pembatalan.",
                    "Confirm" => "Batalkan posting data transaksi yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
            }
            
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("cashbook.pembayaran", "edit")) {
                $settings["actions"][] = array("Text" => "Proses Pembayaran", "Url" => "cashbook.pembayaran/proses/%s", "Class" => "bt_create_new", "ReqId" => 1);
                $settings["actions"][] = array("Text" => "Batal Pembayaran", "Url" => "cashbook.pembayaran/batal/%s", "Class" => "bt_delete", "ReqId" => 1);
            }

            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("cashbook.pembayaran", "view")) {
                $settings["actions"][] = array("Text" => "Laporan", "Url" => "cashbook.pembayaran/report", "Class" => "bt_report", "ReqId" => 0);
            }

			$settings["def_filter"] = 0;
			$settings["def_order"] = 1;
			$settings["singleSelect"] = true;
		} else {
			$settings["from"] = "t_hutang AS a Left Join m_relasi AS b On a.kd_relasi = b.kd_relasi";
            if ($_GET["query"] == "") {
                $_GET["query"] = null;
                $settings["where"] = "a.sts_hutang < 2 And a.entity_id = " . $this->userCompanyId;
            } else {
                $settings["where"] = "a.entity_id = " . $this->userCompanyId;
            }
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

    public function add() {
        require_once(MODEL . "accounting/jurnal.php");
        $loader = null;
        $log = new UserAdmin();
        $pembayaran = new Hutang();
        if (count($this->postData) > 0) {
            $pembayaran->EntityId = $this->userCompanyId;
            $pembayaran->SbuId = 2;
            $pembayaran->NoReff = $this->GetPostValue("NoReff");
            $pembayaran->KdRelasi = $this->GetPostValue("KdRelasi");
            $pembayaran->Keterangan = $this->GetPostValue("Keterangan");
            $pembayaran->JnsHutang = 1;
            $pembayaran->TglHutang = strtotime($this->GetPostValue("TglHutang"));
            $pembayaran->JumHutang = $this->GetPostValue("JumHutang");
            $pembayaran->JumTerbayar = 0;
            $pembayaran->StsHutang = 0;
            $pembayaran->CreateMode = 0;
            if ($this->ValidateData($pembayaran)) {
                $pembayaran->CreatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
                $jurnal = new Jurnal();
                $jurnal->KdVoucher = 'BPK';
                $jurnal->EntityId = $this->userCompanyId;
                $jurnal->TglVoucher = date('Y-m-d', $pembayaran->TglHutang);
                $pembayaran->NoBukti = $jurnal->GetJurnalDocNo($jurnal->KdVoucher);
                $rs = $pembayaran->Insert();
                if ($rs == 1) {
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbook.pembayaran','Add New Trx',$pembayaran->NoBukti,'Success');
                    $this->persistence->SaveState("info", sprintf("Data Data Transaksi: %s (%s) sudah berhasil disimpan", $pembayaran->NoBukti, 'Hutang BPJS'));
                    redirect_url("cashbook.pembayaran");
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbook.pembayaran','Add New CashBook Trx',$pembayaran->DocNo,'Failed');
                    $this->Set("error", "Gagal pada saat menyimpan data transaksi. Message: " . $this->connector->GetErrorMessage());
                }
            }
        }
        // load data for combo box
        $this->Set("pembayaran", $pembayaran);
    }

    public function edit($id) {
	    $loader = null;
        $log = new UserAdmin();
        $pembayaran = new Hutang();
        if (count($this->postData) > 0) {
            $pembayaran->Id = $id;
            $pembayaran->NoBukti = $this->GetPostValue("NoBukti");
            $pembayaran->EntityId = $this->userCompanyId;
            $pembayaran->SbuId = 2;
            $pembayaran->NoReff = $this->GetPostValue("NoReff");
            $pembayaran->KdRelasi = $this->GetPostValue("KdRelasi");
            $pembayaran->Keterangan = $this->GetPostValue("Keterangan");
            $pembayaran->JnsHutang = 1;
            $pembayaran->TglHutang = strtotime($this->GetPostValue("TglHutang"));
            $pembayaran->JumHutang = $this->GetPostValue("JumHutang");
            $pembayaran->JumTerbayar = 0;
            $pembayaran->StsHutang = 0;
            $pembayaran->CreateMode = 0;
            $pembayaran->UpdatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            if ($this->ValidateData($pembayaran)) {
                if ($pembayaran->Update($id)) {
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbook.pembayaran','Update Trx',$pembayaran->NoBukti,'Success');
                    $this->persistence->SaveState("info", sprintf("Data Data Transaksi: %s (%s) sudah berhasil diupdate", $pembayaran->NoBukti, 'Hutang BPJS'));
                    redirect_url("cashbook.pembayaran");
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbook.pembayaran','Add New CashBook Trx',$pembayaran->DocNo,'Failed');
                    $this->Set("error", "Gagal pada saat menyimpan data transaksi. Message: " . $this->connector->GetErrorMessage());
                }
            }
        }else{
            $pembayaran = $pembayaran->LoadById($id);
            if ($pembayaran == null){
                $this->Set("error", "Data Hutang tidak ditemukan (Mungkin sudah dihapus)!");
                redirect_url("cashbook.pembayaran");
            }
            if ($pembayaran->StsHutang == 1){
                $this->Set("error", "Data Hutang tidak boleh diubah, karena sudah berstatus -Posted-");
                redirect_url("cashbook.pembayaran");
            }
            if ($pembayaran->StsHutang == 2){
                $this->Set("error", "Data Hutang tidak boleh diubah, karena sudah berstatus -Paid-");
                redirect_url("cashbook.pembayaran");
            }
            if ($pembayaran->StsHutang == 3){
                $this->Set("error", "Data Hutang tidak boleh diubah, karena sudah berstatus -Void-");
                redirect_url("cashbook.pembayaran");
            }
        }
        // load data for combo box
        $this->Set("pembayaran", $pembayaran);
    }

    private function ValidateData(Hutang $pembayaran) {
        if (strlen($pembayaran->Keterangan) < 5){
            $this->Set("error", "Keterangan Hutang harus diisi dengan jelas!");
            return false;
        }
        if ($pembayaran->JumHutang == 0){
            $this->Set("error", "Jumlah Hutang belum diisi!");
            return false;
        }
        return true;
    }

    public function proses($id) {
        require_once(MODEL . "accounting/jurnal.php");
        require_once(MODEL . "master/bank.php");
        $log = new UserAdmin();
        $loader = null;
        $pembayaran = new Hutang();
        if (count($this->postData) > 0) {
            $pembayaran->Id = $id;
            $pembayaran->TglTerbayar = strtotime($this->GetPostValue("TglTerbayar"));
            $pembayaran->JumTerbayar = $this->GetPostValue("JumTerbayar");
            $pembayaran->BankId = $this->GetPostValue("BankId");
            $pembayaran->UpdatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            if ($this->IsValid($pembayaran)) {
                $jurnal = new Jurnal();
                $jurnal->KdVoucher = 'BKM';
                $jurnal->EntityId = $this->userCompanyId;
                $jurnal->TglVoucher = date('Y-m-d', $pembayaran->TglTerbayar);
                $pembayaran->NoBuktiBayar = $jurnal->GetJurnalDocNo($jurnal->KdVoucher);
                if ($pembayaran->ProsesBayar($id)) {
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbook.pembayaran','Update Trx',$pembayaran->NoBukti,'Success');
                    $this->persistence->SaveState("info", sprintf("Data Data Transaksi: %s (%s) sudah berhasil diupdate", $pembayaran->NoBukti, 'Hutang BPJS'));
                    redirect_url("cashbook.pembayaran");
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbook.pembayaran','Add New CashBook Trx',$pembayaran->DocNo,'Failed');
                    $this->Set("error", "Gagal pada saat menyimpan data transaksi. Message: " . $this->connector->GetErrorMessage());
                }
            }
        }else{
            $pembayaran = $pembayaran->LoadById($id);
            if ($pembayaran == null){
                $this->Set("error", "Data Hutang tidak ditemukan (Mungkin sudah dihapus)!");
                redirect_url("cashbook.pembayaran");
            }
            if ($pembayaran->StsHutang == 0){
                $this->Set("error", "Data Hutang tidak boleh diproses, karena sudah masih berstatus -Draft-");
                redirect_url("cashbook.pembayaran");
            }
            if ($pembayaran->StsHutang == 2){
                $this->Set("error", "Data Hutang tidak boleh diproses, karena sudah berstatus -Paid-");
                redirect_url("cashbook.pembayaran");
            }
            if ($pembayaran->StsHutang == 3){
                $this->Set("error", "Data Hutang tidak boleh diproses, karena sudah berstatus -Void-");
                redirect_url("cashbook.pembayaran");
            }
            $pembayaran->JumTerbayar = $pembayaran->JumHutang;
        }
        // load data for combo box
        $loader = new Bank();
        $banks = $loader->LoadByEntityId($this->userCompanyId);
        $this->Set("pembayaran", $pembayaran);
        $this->Set("banks", $banks);
    }

    private function IsValid(Hutang $pembayaran){
        #validasi pembayaran hutang
	    return true;
    }

    public function batal($id) {
        $pembayaran = new Hutang();
        $pembayaran = $pembayaran->LoadById($id);
        if ($pembayaran == null){
            $this->Set("error", "Data Hutang tidak ditemukan (Mungkin sudah dihapus)!");
            redirect_url("cashbook.pembayaran");
        }
        if ($pembayaran->StsHutang == 1){
            $this->Set("error", "Data Hutang tidak bisa diproses, karena masih berstatus -Posted-");
            redirect_url("cashbook.pembayaran");
        }
        if ($pembayaran->StsHutang == 2){
            $this->Set("error", "Data Hutang tidak bisa diproses, karena sudah berstatus -Paid-");
            redirect_url("cashbook.pembayaran");
        }
        if ($pembayaran->StsHutang == 3){
            $this->Set("error", "Data Hutang tidak bisa diproses, karena sudah berstatus -Void-");
            redirect_url("cashbook.pembayaran");
        }
        if ($pembayaran->Unposting($pembayaran->NoBuktiBayar,$this->userUid)){
            $this->Set("info", "Data Hutang No: ".$pembayaran->NoBukti." berhasil dibatalkan pembayarannya");
            redirect_url("cashbook.pembayaran");
        }
    }

    public function delete($id) {
        $pembayaran = new Hutang();
        $pembayaran = $pembayaran->LoadById($id);
        if ($pembayaran == null){
            $this->Set("error", "Data Hutang tidak ditemukan (Mungkin sudah dihapus)!");
            redirect_url("cashbook.pembayaran");
        }
        if ($pembayaran->StsHutang == 1){
            $this->Set("error", "Data Hutang tidak boleh dihapus, karena sudah berstatus -Posted-");
            redirect_url("cashbook.pembayaran");
        }
        if ($pembayaran->StsHutang == 2){
            $this->Set("error", "Data Hutang tidak boleh dihapus, karena sudah berstatus -Paid-");
            redirect_url("cashbook.pembayaran");
        }
        if ($pembayaran->StsHutang == 3){
            $this->Set("error", "Data Hutang tidak boleh dihapus, karena sudah berstatus -Void-");
            redirect_url("cashbook.pembayaran");
        }
        if ($pembayaran->Delete($id)){
            $this->Set("info", "Data Hutang No: ".$pembayaran->NoBukti." berhasil dihapus");
            redirect_url("cashbook.pembayaran");
        }
    }

    public function posting($id) {
        $pembayaran = new Hutang();
        $pembayaran = $pembayaran->LoadById($id);
        if ($pembayaran == null){
            $this->Set("error", "Data Hutang tidak ditemukan (Mungkin sudah dihapus)!");
            redirect_url("cashbook.pembayaran");
        }
        if ($pembayaran->StsHutang == 1){
            $this->Set("error", "Data Hutang tidak boleh diposting, karena sudah berstatus -Posted-");
            redirect_url("cashbook.pembayaran");
        }
        if ($pembayaran->StsHutang == 2){
            $this->Set("error", "Data Hutang tidak boleh diposting, karena sudah berstatus -Paid-");
            redirect_url("cashbook.pembayaran");
        }
        if ($pembayaran->StsHutang == 3){
            $this->Set("error", "Data Hutang tidak boleh diposting, karena sudah berstatus -Void-");
            redirect_url("cashbook.pembayaran");
        }
        if ($pembayaran->Posting($pembayaran->NoBukti,$this->userUid)){
            $this->Set("info", "Data Hutang No: ".$pembayaran->NoBukti." berhasil diposting");
            redirect_url("cashbook.pembayaran");
        }
    }

    public function unposting($id) {
        $pembayaran = new Hutang();
        $pembayaran = $pembayaran->LoadById($id);
        if ($pembayaran == null){
            $this->Set("error", "Data Hutang tidak ditemukan (Mungkin sudah dihapus)!");
            redirect_url("cashbook.pembayaran");
        }
        if ($pembayaran->CreateMode == 1){
            $this->Set("error", "Data Hutang tidak boleh diunposting, karena hasil proses otomatis");
            redirect_url("cashbook.pembayaran");
        }
        if ($pembayaran->StsHutang == 0){
            $this->Set("error", "Data Hutang tidak boleh diunposting, karena masih berstatus -Draft-");
            redirect_url("cashbook.pembayaran");
        }
        if ($pembayaran->StsHutang == 2){
            $this->Set("error", "Data Hutang tidak boleh diunposting, karena sudah berstatus -Paid-");
            redirect_url("cashbook.pembayaran");
        }
        if ($pembayaran->StsHutang == 3){
            $this->Set("error", "Data Hutang tidak boleh diunposting, karena sudah berstatus -Void-");
            redirect_url("cashbook.pembayaran");
        }
        if ($pembayaran->Unposting($pembayaran->NoBukti,$this->userUid)){
            $this->Set("info", "Data Hutang No: ".$pembayaran->NoBukti." berhasil di-unposting");
            redirect_url("cashbook.pembayaran");
        }
    }
}
