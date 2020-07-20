<?php
class PenerimaanController extends AppController {
	private $userCompanyId;
	private $userCabangId;
	private $userUid;

	protected function Initialize() {
		require_once(MODEL . "cashbook/piutang.php");
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
        $settings["columns"][] = array("name" => "a.tgl_piutang", "display" => "Tanggal", "width" => 70);
        $settings["columns"][] = array("name" => "a.keterangan", "display" => "Keterangan", "width" => 200);
        $settings["columns"][] = array("name" => "b.nm_pasien", "display" => "Nama Pasien", "width" => 120);
        $settings["columns"][] = array("name" => "a.no_reg", "display" => "Refferensi", "width" => 80);
        $settings["columns"][] = array("name" => "format(a.jum_piutang,0)", "display" => "Piutang", "width" => 80, "align" => 'right');
        $settings["columns"][] = array("name" => "format(a.jum_terbayar,0)", "display" => "Terbayar", "width" => 80, "align" => 'right');
        $settings["columns"][] = array("name" => "format(a.jum_piutang - a.jum_terbayar,0)", "display" => "Outstanding", "width" => 80, "align" => 'right');
        $settings["columns"][] = array("name" => "a.tgl_terbayar", "display" => "Tgl Bayar", "width" => 70);
        $settings["columns"][] = array("name" => "a.no_bukti_bayar", "display" => "Bukti Penerimaan", "width" => 90);
        $settings["columns"][] = array("name" => "a.bank_id", "display" => "Debet Akun", "width" => 80);
        $settings["columns"][] = array("name" => "if(a.sts_piutang = 1,'Posted',if(a.sts_piutang = 2,'Paid','Draft'))", "display" => "Status", "width" => 50);
        $settings["columns"][] = array("name" => "if(a.create_mode = 0,'Manual','Auto')", "display" => "Source", "width" => 50);

		$settings["filters"][] = array("name" => "a.no_bukti", "display" => "No. Bukti Piutang");
		$settings["filters"][] = array("name" => "a.no_bukti_bayar", "display" => "No.Bukti Penerimaan");
        $settings["filters"][] = array("name" => "a.tgl_piutang", "display" => "Tanggal Piutang");
        $settings["filters"][] = array("name" => "a.tgl_terbayar", "display" => "Tanggal Penerimaan");
        $settings["filters"][] = array("name" => "a.keterangan", "display" => "Keterangan");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Piutang Pasien";
            //button action
            if ($acl->CheckUserAccess("cashbook.penerimaan", "add")) {
                $settings["actions"][] = array("Text" => "Add", "Url" => "cashbook.penerimaan/add", "Class" => "bt_add", "ReqId" => 0);
            }
            if ($acl->CheckUserAccess("cashbook.penerimaan", "edit")) {
                $settings["actions"][] = array("Text" => "Edit", "Url" => "cashbook.penerimaan/edit/%s", "Class" => "bt_edit", "ReqId" => 1);
            }
            if ($acl->CheckUserAccess("cashbook.penerimaan", "delete")) {
                $settings["actions"][] = array("Text" => "Delete", "Url" => "cashbook.penerimaan/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
                    "Error" => "Mohon memilih Data Transaksi terlebih dahulu sebelum proses penghapusan.\nPERHATIAN: Mohon memilih tepat satu data.",
                    "Confirm" => "Apakah anda mau menghapus data transaksi yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
            }
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("cashbook.penerimaan", "approve")) {
                $settings["actions"][] = array("Text" => "Posting", "Url" => "cashbook.penerimaan/posting/%s", "Class" => "bt_approve", "ReqId" => 1,
                    "Error" => "Mohon memilih Data Transaksi terlebih dahulu sebelum proses posting.",
                    "Confirm" => "Posting data transaksi yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
                $settings["actions"][] = array("Text" => "Unposting", "Url" => "cashbook.penerimaan/unposting/%s", "Class" => "bt_reject", "ReqId" => 1,
                    "Error" => "Mohon memilih Data Transaksi terlebih dahulu sebelum proses pembatalan.",
                    "Confirm" => "Batalkan posting data transaksi yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
            }
            
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("cashbook.penerimaan", "edit")) {
                $settings["actions"][] = array("Text" => "Proses Penerimaan", "Url" => "cashbook.penerimaan/proses/%s", "Class" => "bt_create_new", "ReqId" => 1);
                $settings["actions"][] = array("Text" => "Batal Penerimaan", "Url" => "cashbook.penerimaan/batal/%s", "Class" => "bt_delete", "ReqId" => 1);
            }

            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("cashbook.penerimaan", "view")) {
                $settings["actions"][] = array("Text" => "Laporan", "Url" => "cashbook.penerimaan/report", "Class" => "bt_report", "ReqId" => 0);
            }

			$settings["def_filter"] = 0;
			$settings["def_order"] = 1;
			$settings["singleSelect"] = true;
		} else {
			$settings["from"] = "t_piutang AS a Left Join m_pasien AS b On a.no_rm_pasien = b.no_rm";
            if ($_GET["query"] == "") {
                $_GET["query"] = null;
                $settings["where"] = "a.sts_piutang < 2 And a.jns_piutang = 1 And a.entity_id = " . $this->userCompanyId;
            } else {
                $settings["where"] = "a.jns_piutang = 1 And a.entity_id = " . $this->userCompanyId;
            }
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

    public function add() {
        require_once(MODEL . "accounting/jurnal.php");
        $loader = null;
        $log = new UserAdmin();
        $penerimaan = new Piutang();
        if (count($this->postData) > 0) {
            $penerimaan->EntityId = $this->userCompanyId;
            $penerimaan->SbuId = 2;
            $penerimaan->NoReg = $this->GetPostValue("NoReg");
            $penerimaan->NoRmPasien = $this->GetPostValue("NoRmPasien");
            $penerimaan->Keterangan = $this->GetPostValue("Keterangan");
            $penerimaan->JnsPiutang = 1;
            $penerimaan->TglPiutang = strtotime($this->GetPostValue("TglPiutang"));
            $penerimaan->JumPiutang = $this->GetPostValue("JumPiutang");
            $penerimaan->JumTerbayar = 0;
            $penerimaan->StsPiutang = 0;
            $penerimaan->CreateMode = 0;
            if ($this->ValidateData($penerimaan)) {
                $penerimaan->CreatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
                $jurnal = new Jurnal();
                $jurnal->KdVoucher = 'BPT';
                $jurnal->EntityId = $this->userCompanyId;
                $jurnal->TglVoucher = date('Y-m-d', $penerimaan->TglPiutang);
                $penerimaan->NoBukti = $jurnal->GetJurnalDocNo($jurnal->KdVoucher);
                $rs = $penerimaan->Insert();
                if ($rs == 1) {
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbook.penerimaan','Add New Trx',$penerimaan->NoBukti,'Success');
                    $this->persistence->SaveState("info", sprintf("Data Data Transaksi: %s (%s) sudah berhasil disimpan", $penerimaan->NoBukti, 'Piutang BPJS'));
                    redirect_url("cashbook.penerimaan");
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbook.penerimaan','Add New CashBook Trx',$penerimaan->DocNo,'Failed');
                    $this->Set("error", "Gagal pada saat menyimpan data transaksi. Message: " . $this->connector->GetErrorMessage());
                }
            }
        }
        // load data for combo box
        $this->Set("penerimaan", $penerimaan);
    }

    public function edit($id) {
	    $loader = null;
        $log = new UserAdmin();
        $penerimaan = new Piutang();
        if (count($this->postData) > 0) {
            $penerimaan->Id = $id;
            $penerimaan->NoBukti = $this->GetPostValue("NoBukti");
            $penerimaan->EntityId = $this->userCompanyId;
            $penerimaan->SbuId = 2;
            $penerimaan->NoReg = $this->GetPostValue("NoReg");
            $penerimaan->NoRmPasien = $this->GetPostValue("NoRmPasien");
            $penerimaan->Keterangan = $this->GetPostValue("Keterangan");
            $penerimaan->JnsPiutang = 1;
            $penerimaan->TglPiutang = strtotime($this->GetPostValue("TglPiutang"));
            $penerimaan->JumPiutang = $this->GetPostValue("JumPiutang");
            $penerimaan->JumTerbayar = 0;
            $penerimaan->StsPiutang = 0;
            $penerimaan->CreateMode = 0;
            $penerimaan->UpdatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            if ($this->ValidateData($penerimaan)) {
                if ($penerimaan->Update($id)) {
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbook.penerimaan','Update Trx',$penerimaan->NoBukti,'Success');
                    $this->persistence->SaveState("info", sprintf("Data Data Transaksi: %s (%s) sudah berhasil diupdate", $penerimaan->NoBukti, 'Piutang BPJS'));
                    redirect_url("cashbook.penerimaan");
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbook.penerimaan','Add New CashBook Trx',$penerimaan->DocNo,'Failed');
                    $this->Set("error", "Gagal pada saat menyimpan data transaksi. Message: " . $this->connector->GetErrorMessage());
                }
            }
        }else{
            $penerimaan = $penerimaan->LoadById($id);
            if ($penerimaan == null){
                $this->persistence->SaveState("error", "Data Piutang tidak ditemukan (Mungkin sudah dihapus)!");
                redirect_url("cashbook.penerimaan");
            }
            if ($penerimaan->StsPiutang == 1){
                $this->persistence->SaveState("error", "Data Piutang tidak boleh diubah, karena sudah berstatus -Posted-");
                redirect_url("cashbook.penerimaan");
            }
            if ($penerimaan->StsPiutang == 2){
                $this->persistence->SaveState("error", "Data Piutang tidak boleh diubah, karena sudah berstatus -Paid-");
                redirect_url("cashbook.penerimaan");
            }
            if ($penerimaan->StsPiutang == 3){
                $this->persistence->SaveState("error", "Data Piutang tidak boleh diubah, karena sudah berstatus -Void-");
                redirect_url("cashbook.penerimaan");
            }
        }
        // load data for combo box
        $this->Set("penerimaan", $penerimaan);
    }

    private function ValidateData(Piutang $penerimaan) {
        if (strlen($penerimaan->Keterangan) < 5){
            $this->Set("error", "Keterangan Piutang harus diisi dengan jelas!");
            return false;
        }
        if ($penerimaan->JumPiutang == 0){
            $this->Set("error", "Jumlah Piutang belum diisi!");
            return false;
        }
        return true;
    }

    public function proses($id) {
        require_once(MODEL . "accounting/jurnal.php");
        require_once(MODEL . "master/bank.php");
        $log = new UserAdmin();
        $loader = null;
        $penerimaan = new Piutang();
        if (count($this->postData) > 0) {
            $penerimaan->Id = $id;
            $penerimaan->TglTerbayar = strtotime($this->GetPostValue("TglTerbayar"));
            $penerimaan->JumTerbayar = $this->GetPostValue("JumTerbayar");
            $penerimaan->BankId = $this->GetPostValue("BankId");
            $penerimaan->UpdatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            if ($this->IsValid($penerimaan)) {
                $jurnal = new Jurnal();
                $jurnal->KdVoucher = 'BKM';
                $jurnal->EntityId = $this->userCompanyId;
                $jurnal->TglVoucher = date('Y-m-d', $penerimaan->TglTerbayar);
                $penerimaan->NoBuktiBayar = $jurnal->GetJurnalDocNo($jurnal->KdVoucher);
                if ($penerimaan->ProsesBayar($id)) {
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbook.penerimaan','Update Trx',$penerimaan->NoBukti,'Success');
                    $this->persistence->SaveState("info", sprintf("Data Data Transaksi: %s (%s) sudah berhasil diupdate", $penerimaan->NoBukti, 'Piutang BPJS'));
                    redirect_url("cashbook.penerimaan");
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbook.penerimaan','Add New CashBook Trx',$penerimaan->DocNo,'Failed');
                    $this->Set("error", "Gagal pada saat menyimpan data transaksi. Message: " . $this->connector->GetErrorMessage());
                }
            }
        }else{
            $penerimaan = $penerimaan->LoadById($id);
            if ($penerimaan == null){
                $this->persistence->SaveState("error", "Data Piutang tidak ditemukan (Mungkin sudah dihapus)!");
                redirect_url("cashbook.penerimaan");
            }
            if ($penerimaan->StsPiutang == 0){
                $this->persistence->SaveState("error", "Data Piutang tidak boleh diproses, karena sudah masih berstatus -Draft-");
                redirect_url("cashbook.penerimaan");
            }
            if ($penerimaan->StsPiutang == 2){
                $this->persistence->SaveState("error", "Data Piutang tidak boleh diproses, karena sudah berstatus -Paid-");
                redirect_url("cashbook.penerimaan");
            }
            if ($penerimaan->StsPiutang == 3){
                $this->persistence->SaveState("error", "Data Piutang tidak boleh diproses, karena sudah berstatus -Void-");
                redirect_url("cashbook.penerimaan");
            }
            $penerimaan->JumTerbayar = $penerimaan->JumPiutang;
        }
        // load data for combo box
        $loader = new Bank();
        $banks = $loader->LoadByEntityId($this->userCompanyId);
        $this->Set("penerimaan", $penerimaan);
        $this->Set("banks", $banks);
    }

    private function IsValid(Piutang $penerimaan){
        #validasi pembayaran piutang
	    return true;
    }

    public function batal($id) {
        $penerimaan = new Piutang();
        $penerimaan = $penerimaan->LoadById($id);
        if ($penerimaan == null){
            $this->persistence->SaveState("error", "Data Piutang tidak ditemukan (Mungkin sudah dihapus)!");
            redirect_url("cashbook.penerimaan");
        }
        if ($penerimaan->StsPiutang == 1){
            $this->persistence->SaveState("error", "Data Piutang tidak bisa diproses, karena masih berstatus -Posted-");
            redirect_url("cashbook.penerimaan");
        }
        if ($penerimaan->StsPiutang == 2){
            $this->persistence->SaveState("error", "Data Piutang tidak bisa diproses, karena sudah berstatus -Paid-");
            redirect_url("cashbook.penerimaan");
        }
        if ($penerimaan->StsPiutang == 3){
            $this->persistence->SaveState("error", "Data Piutang tidak bisa diproses, karena sudah berstatus -Void-");
            redirect_url("cashbook.penerimaan");
        }
        if ($penerimaan->Unposting($penerimaan->NoBuktiBayar,$this->userUid)){
            $this->persistence->SaveState("info", "Data Piutang No: ".$penerimaan->NoBukti." berhasil dibatalkan penerimaannya");
            redirect_url("cashbook.penerimaan");
        }
    }

    public function delete($id) {
        $penerimaan = new Piutang();
        $penerimaan = $penerimaan->LoadById($id);
        if ($penerimaan == null){
            $this->persistence->SaveState("error", "Data Piutang tidak ditemukan (Mungkin sudah dihapus)!");
            redirect_url("cashbook.penerimaan");
        }
        if ($penerimaan->StsPiutang == 1){
            $this->persistence->SaveState("error", "Data Piutang tidak boleh dihapus, karena sudah berstatus -Posted-");
            redirect_url("cashbook.penerimaan");
        }
        if ($penerimaan->StsPiutang == 2){
            $this->persistence->SaveState("error", "Data Piutang tidak boleh dihapus, karena sudah berstatus -Paid-");
            redirect_url("cashbook.penerimaan");
        }
        if ($penerimaan->StsPiutang == 3){
            $this->persistence->SaveState("error", "Data Piutang tidak boleh dihapus, karena sudah berstatus -Void-");
            redirect_url("cashbook.penerimaan");
        }
        if ($penerimaan->Delete($id)){
            $this->persistence->SaveState("info", "Data Piutang No: ".$penerimaan->NoBukti." berhasil dihapus");
            redirect_url("cashbook.penerimaan");
        }
    }

    public function posting($id) {
        $penerimaan = new Piutang();
        $penerimaan = $penerimaan->LoadById($id);
        if ($penerimaan == null){
            $this->persistence->SaveState("error", "Data Piutang tidak ditemukan (Mungkin sudah dihapus)!");
            redirect_url("cashbook.penerimaan");
        }
        if ($penerimaan->StsPiutang == 1){
            $this->persistence->SaveState("error", "Data Piutang tidak boleh diposting, karena sudah berstatus -Posted-");
            redirect_url("cashbook.penerimaan");
        }
        if ($penerimaan->StsPiutang == 2){
            $this->persistence->SaveState("error", "Data Piutang tidak boleh diposting, karena sudah berstatus -Paid-");
            redirect_url("cashbook.penerimaan");
        }
        if ($penerimaan->StsPiutang == 3){
            $this->persistence->SaveState("error", "Data Piutang tidak boleh diposting, karena sudah berstatus -Void-");
            redirect_url("cashbook.penerimaan");
        }
        if ($penerimaan->Posting($penerimaan->NoBukti,$this->userUid)){
            $this->persistence->SaveState("info", "Data Piutang No: ".$penerimaan->NoBukti." berhasil diposting");
            redirect_url("cashbook.penerimaan");
        }
    }

    public function unposting($id) {
        $penerimaan = new Piutang();
        $penerimaan = $penerimaan->LoadById($id);
        if ($penerimaan == null){
            $this->persistence->SaveState("error", "Data Piutang tidak ditemukan (Mungkin sudah dihapus)!");
            redirect_url("cashbook.penerimaan");
        }
        if ($penerimaan->CreateMode == 1){
            $this->persistence->SaveState("error", "Data Piutang tidak boleh diunposting, karena hasil proses otomatis");
            redirect_url("cashbook.penerimaan");
        }
        if ($penerimaan->StsPiutang == 0){
            $this->persistence->SaveState("error", "Data Piutang tidak boleh diunposting, karena masih berstatus -Draft-");
            redirect_url("cashbook.penerimaan");
        }
        if ($penerimaan->StsPiutang == 2){
            $this->persistence->SaveState("error", "Data Piutang tidak boleh diunposting, karena sudah berstatus -Paid-");
            redirect_url("cashbook.penerimaan");
        }
        if ($penerimaan->StsPiutang == 3){
            $this->persistence->SaveState("error", "Data Piutang tidak boleh diunposting, karena sudah berstatus -Void-");
            redirect_url("cashbook.penerimaan");
        }
        if ($penerimaan->Unposting($penerimaan->NoBukti,$this->userUid)){
            $this->persistence->SaveState("info", "Data Piutang No: ".$penerimaan->NoBukti." berhasil di-unposting");
            redirect_url("cashbook.penerimaan");
        }
    }
}
