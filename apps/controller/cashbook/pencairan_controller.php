<?php
class PencairanController extends AppController {
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
        $settings["columns"][] = array("name" => "a.tgl_terbayar", "display" => "Tgl Cair", "width" => 70);
        $settings["columns"][] = array("name" => "a.no_bukti_bayar", "display" => "Bukti Pencairan", "width" => 90);
        $settings["columns"][] = array("name" => "a.bank_id", "display" => "Debet Akun", "width" => 80);
        $settings["columns"][] = array("name" => "if(a.sts_piutang = 1,'Posted',if(a.sts_piutang = 2,'Paid','Draft'))", "display" => "Status", "width" => 50);
        $settings["columns"][] = array("name" => "if(a.create_mode = 0,'Manual','Auto')", "display" => "Source", "width" => 50);

		$settings["filters"][] = array("name" => "a.no_bukti", "display" => "No. Bukti Piutang");
		$settings["filters"][] = array("name" => "a.no_bukti_bayar", "display" => "No.Bukti Pencairan");
        $settings["filters"][] = array("name" => "a.tgl_piutang", "display" => "Tanggal Piutang");
        $settings["filters"][] = array("name" => "a.tgl_terbayar", "display" => "Tanggal Pencairan");
        $settings["filters"][] = array("name" => "a.keterangan", "display" => "Keterangan");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Klaim BPJS";
            //button action
            if ($acl->CheckUserAccess("cashbook.pencairan", "add")) {
                $settings["actions"][] = array("Text" => "Add", "Url" => "cashbook.pencairan/add", "Class" => "bt_add", "ReqId" => 0);
            }
            if ($acl->CheckUserAccess("cashbook.pencairan", "edit")) {
                $settings["actions"][] = array("Text" => "Edit", "Url" => "cashbook.pencairan/edit/%s", "Class" => "bt_edit", "ReqId" => 1);
            }
            if ($acl->CheckUserAccess("cashbook.pencairan", "delete")) {
                $settings["actions"][] = array("Text" => "Delete", "Url" => "cashbook.pencairan/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
                    "Error" => "Mohon memilih Data Transaksi terlebih dahulu sebelum proses penghapusan.\nPERHATIAN: Mohon memilih tepat satu data.",
                    "Confirm" => "Apakah anda mau menghapus data transaksi yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
            }
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("cashbook.pencairan", "approve")) {
                $settings["actions"][] = array("Text" => "Posting", "Url" => "cashbook.pencairan/posting/%s", "Class" => "bt_approve", "ReqId" => 1,
                    "Error" => "Mohon memilih Data Transaksi terlebih dahulu sebelum proses posting.",
                    "Confirm" => "Posting data transaksi yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
                $settings["actions"][] = array("Text" => "Unposting", "Url" => "cashbook.pencairan/unposting/%s", "Class" => "bt_reject", "ReqId" => 1,
                    "Error" => "Mohon memilih Data Transaksi terlebih dahulu sebelum proses pembatalan.",
                    "Confirm" => "Batalkan posting data transaksi yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
            }
            
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("cashbook.pencairan", "edit")) {
                $settings["actions"][] = array("Text" => "Proses Pencairan", "Url" => "cashbook.pencairan/proses/%s", "Class" => "bt_create_new", "ReqId" => 1);
                $settings["actions"][] = array("Text" => "Batal Pencairan", "Url" => "cashbook.pencairan/batal/%s", "Class" => "bt_delete", "ReqId" => 1);
            }

            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("cashbook.pencairan", "view")) {
                $settings["actions"][] = array("Text" => "Laporan", "Url" => "cashbook.pencairan/report", "Class" => "bt_report", "ReqId" => 0);
            }

			$settings["def_filter"] = 0;
			$settings["def_order"] = 1;
			$settings["singleSelect"] = true;
		} else {
			$settings["from"] = "t_piutang AS a Left Join m_pasien AS b On a.no_rm_pasien = b.no_rm";
            if ($_GET["query"] == "") {
                $_GET["query"] = null;
                $settings["where"] = "a.sts_piutang < 2 And a.jns_piutang = 2 And a.entity_id = " . $this->userCompanyId;
            } else {
                $settings["where"] = "a.jns_piutang = 2 And a.entity_id = " . $this->userCompanyId;
            }
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

    public function add() {
        require_once(MODEL . "accounting/jurnal.php");
        $loader = null;
        $log = new UserAdmin();
        $pencairan = new Piutang();
        if (count($this->postData) > 0) {
            $pencairan->EntityId = $this->userCompanyId;
            $pencairan->SbuId = 2;
            $pencairan->NoReg = $this->GetPostValue("NoReg");
            $pencairan->NoRmPasien = $this->GetPostValue("NoRmPasien");
            $pencairan->Keterangan = $this->GetPostValue("Keterangan");
            $pencairan->JnsPiutang = 2;
            $pencairan->TglPiutang = strtotime($this->GetPostValue("TglPiutang"));
            $pencairan->JumPiutang = $this->GetPostValue("JumPiutang");
            $pencairan->JumTerbayar = 0;
            $pencairan->StsPiutang = 0;
            $pencairan->CreateMode = 0;
            if ($this->ValidateData($pencairan)) {
                $pencairan->CreatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
                $jurnal = new Jurnal();
                $jurnal->KdVoucher = 'BPT';
                $jurnal->EntityId = $this->userCompanyId;
                $jurnal->TglVoucher = date('Y-m-d', $pencairan->TglPiutang);
                $pencairan->NoBukti = $jurnal->GetJurnalDocNo($jurnal->KdVoucher);
                $rs = $pencairan->Insert();
                if ($rs == 1) {
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbook.pencairan','Add New Trx',$pencairan->NoBukti,'Success');
                    $this->persistence->SaveState("info", sprintf("Data Data Transaksi: %s (%s) sudah berhasil disimpan", $pencairan->NoBukti, 'Klaim BPJS'));
                    redirect_url("cashbook.pencairan");
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbook.pencairan','Add New CashBook Trx',$pencairan->DocNo,'Failed');
                    $this->Set("error", "Gagal pada saat menyimpan data transaksi. Message: " . $this->connector->GetErrorMessage());
                }
            }
        }
        // load data for combo box
        $this->Set("pencairan", $pencairan);
    }

    public function edit($id) {
	    $loader = null;
        $log = new UserAdmin();
        $pencairan = new Piutang();
        if (count($this->postData) > 0) {
            $pencairan->Id = $id;
            $pencairan->NoBukti = $this->GetPostValue("NoBukti");
            $pencairan->EntityId = $this->userCompanyId;
            $pencairan->SbuId = 2;
            $pencairan->NoReg = $this->GetPostValue("NoReg");
            $pencairan->NoRmPasien = $this->GetPostValue("NoRmPasien");
            $pencairan->Keterangan = $this->GetPostValue("Keterangan");
            $pencairan->JnsPiutang = 2;
            $pencairan->TglPiutang = strtotime($this->GetPostValue("TglPiutang"));
            $pencairan->JumPiutang = $this->GetPostValue("JumPiutang");
            $pencairan->JumTerbayar = 0;
            $pencairan->StsPiutang = 0;
            $pencairan->CreateMode = 0;
            $pencairan->UpdatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            if ($this->ValidateData($pencairan)) {
                if ($pencairan->Update($id)) {
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbook.pencairan','Update Trx',$pencairan->NoBukti,'Success');
                    $this->persistence->SaveState("info", sprintf("Data Data Transaksi: %s (%s) sudah berhasil diupdate", $pencairan->NoBukti, 'Klaim BPJS'));
                    redirect_url("cashbook.pencairan");
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbook.pencairan','Add New CashBook Trx',$pencairan->DocNo,'Failed');
                    $this->Set("error", "Gagal pada saat menyimpan data transaksi. Message: " . $this->connector->GetErrorMessage());
                }
            }
        }else{
            $pencairan = $pencairan->LoadById($id);
            if ($pencairan == null){
                $this->Set("error", "Data Klaim tidak ditemukan (Mungkin sudah dihapus)!");
                redirect_url("cashbook.pencairan");
            }
            if ($pencairan->StsPiutang == 1){
                $this->Set("error", "Data Klaim tidak boleh diubah, karena sudah berstatus -Posted-");
                redirect_url("cashbook.pencairan");
            }
            if ($pencairan->StsPiutang == 2){
                $this->Set("error", "Data Klaim tidak boleh diubah, karena sudah berstatus -Paid-");
                redirect_url("cashbook.pencairan");
            }
            if ($pencairan->StsPiutang == 3){
                $this->Set("error", "Data Klaim tidak boleh diubah, karena sudah berstatus -Void-");
                redirect_url("cashbook.pencairan");
            }
        }
        // load data for combo box
        $this->Set("pencairan", $pencairan);
    }

    private function ValidateData(Piutang $pencairan) {
        if (strlen($pencairan->Keterangan) < 5){
            $this->Set("error", "Keterangan Klaim harus diisi dengan jelas!");
            return false;
        }
        if ($pencairan->JumPiutang == 0){
            $this->Set("error", "Jumlah Klaim belum diisi!");
            return false;
        }
        return true;
    }

    public function proses($id) {
        require_once(MODEL . "accounting/jurnal.php");
        require_once(MODEL . "master/bank.php");
        $log = new UserAdmin();
        $loader = null;
        $pencairan = new Piutang();
        if (count($this->postData) > 0) {
            $pencairan->Id = $id;
            $pencairan->TglTerbayar = strtotime($this->GetPostValue("TglTerbayar"));
            $pencairan->JumTerbayar = $this->GetPostValue("JumTerbayar");
            $pencairan->BankId = $this->GetPostValue("BankId");
            $pencairan->UpdatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            if ($this->IsValid($pencairan)) {
                $jurnal = new Jurnal();
                $jurnal->KdVoucher = 'BKM';
                $jurnal->EntityId = $this->userCompanyId;
                $jurnal->TglVoucher = date('Y-m-d', $pencairan->TglTerbayar);
                $pencairan->NoBuktiBayar = $jurnal->GetJurnalDocNo($jurnal->KdVoucher);
                if ($pencairan->ProsesBayar($id)) {
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbook.pencairan','Update Trx',$pencairan->NoBukti,'Success');
                    $this->persistence->SaveState("info", sprintf("Data Data Transaksi: %s (%s) sudah berhasil diupdate", $pencairan->NoBukti, 'Klaim BPJS'));
                    redirect_url("cashbook.pencairan");
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbook.pencairan','Add New CashBook Trx',$pencairan->DocNo,'Failed');
                    $this->Set("error", "Gagal pada saat menyimpan data transaksi. Message: " . $this->connector->GetErrorMessage());
                }
            }
        }else{
            $pencairan = $pencairan->LoadById($id);
            if ($pencairan == null){
                $this->Set("error", "Data Klaim tidak ditemukan (Mungkin sudah dihapus)!");
                redirect_url("cashbook.pencairan");
            }
            if ($pencairan->StsPiutang == 0){
                $this->Set("error", "Data Klaim tidak boleh diproses, karena sudah masih berstatus -Draft-");
                redirect_url("cashbook.pencairan");
            }
            if ($pencairan->StsPiutang == 2){
                $this->Set("error", "Data Klaim tidak boleh diproses, karena sudah berstatus -Paid-");
                redirect_url("cashbook.pencairan");
            }
            if ($pencairan->StsPiutang == 3){
                $this->Set("error", "Data Klaim tidak boleh diproses, karena sudah berstatus -Void-");
                redirect_url("cashbook.pencairan");
            }
            $pencairan->JumTerbayar = $pencairan->JumPiutang;
        }
        // load data for combo box
        $loader = new Bank();
        $banks = $loader->LoadByEntityId($this->userCompanyId);
        $this->Set("pencairan", $pencairan);
        $this->Set("banks", $banks);
    }

    private function IsValid(Piutang $pencairan){
        #validasi pembayaran piutang
	    return true;
    }

    public function batal($id) {
        $pencairan = new Piutang();
        $pencairan = $pencairan->LoadById($id);
        if ($pencairan == null){
            $this->Set("error", "Data Klaim tidak ditemukan (Mungkin sudah dihapus)!");
            redirect_url("cashbook.pencairan");
        }
        if ($pencairan->StsPiutang == 1){
            $this->Set("error", "Data Klaim tidak bisa diproses, karena masih berstatus -Posted-");
            redirect_url("cashbook.pencairan");
        }
        if ($pencairan->StsPiutang == 2){
            $this->Set("error", "Data Klaim tidak bisa diproses, karena sudah berstatus -Paid-");
            redirect_url("cashbook.pencairan");
        }
        if ($pencairan->StsPiutang == 3){
            $this->Set("error", "Data Klaim tidak bisa diproses, karena sudah berstatus -Void-");
            redirect_url("cashbook.pencairan");
        }
        if ($pencairan->Unposting($pencairan->NoBuktiBayar,$this->userUid)){
            $this->Set("info", "Data Klaim No: ".$pencairan->NoBukti." berhasil dibatalkan pencairannya");
            redirect_url("cashbook.pencairan");
        }
    }

    public function delete($id) {
        $pencairan = new Piutang();
        $pencairan = $pencairan->LoadById($id);
        if ($pencairan == null){
            $this->Set("error", "Data Klaim tidak ditemukan (Mungkin sudah dihapus)!");
            redirect_url("cashbook.pencairan");
        }
        if ($pencairan->StsPiutang == 1){
            $this->Set("error", "Data Klaim tidak boleh dihapus, karena sudah berstatus -Posted-");
            redirect_url("cashbook.pencairan");
        }
        if ($pencairan->StsPiutang == 2){
            $this->Set("error", "Data Klaim tidak boleh dihapus, karena sudah berstatus -Paid-");
            redirect_url("cashbook.pencairan");
        }
        if ($pencairan->StsPiutang == 3){
            $this->Set("error", "Data Klaim tidak boleh dihapus, karena sudah berstatus -Void-");
            redirect_url("cashbook.pencairan");
        }
        if ($pencairan->Delete($id)){
            $this->Set("info", "Data Klaim No: ".$pencairan->NoBukti." berhasil dihapus");
            redirect_url("cashbook.pencairan");
        }
    }

    public function posting($id) {
        $pencairan = new Piutang();
        $pencairan = $pencairan->LoadById($id);
        if ($pencairan == null){
            $this->Set("error", "Data Klaim tidak ditemukan (Mungkin sudah dihapus)!");
            redirect_url("cashbook.pencairan");
        }
        if ($pencairan->StsPiutang == 1){
            $this->Set("error", "Data Klaim tidak boleh diposting, karena sudah berstatus -Posted-");
            redirect_url("cashbook.pencairan");
        }
        if ($pencairan->StsPiutang == 2){
            $this->Set("error", "Data Klaim tidak boleh diposting, karena sudah berstatus -Paid-");
            redirect_url("cashbook.pencairan");
        }
        if ($pencairan->StsPiutang == 3){
            $this->Set("error", "Data Klaim tidak boleh diposting, karena sudah berstatus -Void-");
            redirect_url("cashbook.pencairan");
        }
        if ($pencairan->Posting($pencairan->NoBukti,$this->userUid)){
            $this->Set("info", "Data Klaim No: ".$pencairan->NoBukti." berhasil diposting");
            redirect_url("cashbook.pencairan");
        }
    }

    public function unposting($id) {
        $pencairan = new Piutang();
        $pencairan = $pencairan->LoadById($id);
        if ($pencairan == null){
            $this->Set("error", "Data Klaim tidak ditemukan (Mungkin sudah dihapus)!");
            redirect_url("cashbook.pencairan");
        }
        if ($pencairan->CreateMode == 1){
            $this->Set("error", "Data Klaim tidak boleh diunposting, karena hasil proses otomatis");
            redirect_url("cashbook.pencairan");
        }
        if ($pencairan->StsPiutang == 0){
            $this->Set("error", "Data Klaim tidak boleh diunposting, karena masih berstatus -Draft-");
            redirect_url("cashbook.pencairan");
        }
        if ($pencairan->StsPiutang == 2){
            $this->Set("error", "Data Klaim tidak boleh diunposting, karena sudah berstatus -Paid-");
            redirect_url("cashbook.pencairan");
        }
        if ($pencairan->StsPiutang == 3){
            $this->Set("error", "Data Klaim tidak boleh diunposting, karena sudah berstatus -Void-");
            redirect_url("cashbook.pencairan");
        }
        if ($pencairan->Unposting($pencairan->NoBukti,$this->userUid)){
            $this->Set("info", "Data Klaim No: ".$pencairan->NoBukti." berhasil di-unposting");
            redirect_url("cashbook.pencairan");
        }
    }
}
