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
            if ($acl->CheckUserAccess("cashbook.pembayaran", "view")) {
                $settings["actions"][] = array("Text" => "View", "Url" => "cashbook.pembayaran/view/%s", "Class" => "bt_edit", "ReqId" => 1);
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

     public function view($id) {
        require_once(MODEL . "master/bank.php");
	    $loader = null;
        $log = new UserAdmin();
        $pembayaran = new Hutang();
        $pembayaran = $pembayaran->LoadById($id);
        if ($pembayaran == null){
            $this->Set("error", "Data Hutang tidak ditemukan (Mungkin sudah dihapus)!");
            redirect_url("cashbook.pembayaran");
        }
        // load data for combo box
         $loader = new Bank();
         $banks = $loader->LoadByEntityId($this->userCompanyId);
         $this->Set("pembayaran", $pembayaran);
         $this->Set("banks", $banks);
    }

    public function proses($id) {
        require_once(MODEL . "accounting/jurnal.php");
        require_once(MODEL . "master/bank.php");
        $log = new UserAdmin();
        $loader = null;
        $pembayaran = new Hutang();
        if (count($this->postData) > 0) {
            $pembayaran->Id = $id;
            $pembayaran->JumHutang = $this->GetPostValue("JumHutang");
            $pembayaran->TglTerbayar = strtotime($this->GetPostValue("TglTerbayar"));
            $pembayaran->JumTerbayar = $this->GetPostValue("JumTerbayar");
            $pembayaran->BankId = $this->GetPostValue("BankId");
            $pembayaran->UpdatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            if ($this->IsValid($pembayaran)) {
                $jurnal = new Jurnal();
                $jurnal->KdVoucher = 'BKK';
                $jurnal->EntityId = $this->userCompanyId;
                $jurnal->TglVoucher = date('Y-m-d', $pembayaran->TglTerbayar);
                $pembayaran->NoBuktiBayar = $jurnal->GetJurnalDocNo($jurnal->KdVoucher);
                if ($pembayaran->ProsesBayar($id)) {
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbook.pembayaran','Proses Bayar No:',$pembayaran->NoBukti,'Success');
                    $this->persistence->SaveState("info", sprintf("Data Hutang: %s (%s) sudah berhasil diproses bayar", $pembayaran->NoBukti, 'Hutang Supplier'));
                    redirect_url("cashbook.pembayaran");
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbook.pembayaran','Add New CashBook Trx',$pembayaran->DocNo,'Failed');
                    $this->Set("error", "Gagal pada saat menyimpan data transaksi. Message: " . $this->connector->GetErrorMessage());
                }
            }
        }else{
            $pembayaran = $pembayaran->LoadById($id);
            if ($pembayaran == null){
                $this->persistence->SaveState("error", "Data Hutang tidak ditemukan (Mungkin sudah dihapus)!");
                redirect_url("cashbook.pembayaran");
            }
            if ($pembayaran->StsHutang == 0){
                $this->persistence->SaveState("error", "Data Hutang tidak boleh diproses, karena sudah masih berstatus -Draft-");
                redirect_url("cashbook.pembayaran");
            }
            if ($pembayaran->StsHutang == 2){
                $this->persistence->SaveState("error", "Data Hutang tidak boleh diproses, karena sudah berstatus -Paid-");
                redirect_url("cashbook.pembayaran");
            }
            if ($pembayaran->StsHutang == 3){
                $this->persistence->SaveState("error", "Data Hutang tidak boleh diproses, karena sudah berstatus -Void-");
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
        if ($pembayaran->JumTerbayar <> $pembayaran->JumHutang){
            $this->Set("error", "Jumlah Pembayaran Hutang tidak sesuai!");
            return false;
        }
        if ($pembayaran->BankId == null || $pembayaran->BankId == "" || $pembayaran->BankId == 0){
            $this->Set("error", "Kas/Bank Pembayaran belum dipilih!");
            return false;
        }
	    return true;
    }

    public function batal($id) {
        $pembayaran = new Hutang();
        $pembayaran = $pembayaran->LoadById($id);
        if ($pembayaran == null){
            $this->persistence->SaveState("error", "Data Hutang tidak ditemukan (Mungkin sudah dihapus)!");
            redirect_url("cashbook.pembayaran");
        }
        if ($pembayaran->StsHutang == 0){
            $this->persistence->SaveState("error", "Pembatalan tidak bisa diproses, karena masih berstatus -Draft-");
            redirect_url("cashbook.pembayaran");
        }
        if ($pembayaran->StsHutang == 1){
            $this->persistence->SaveState("error", "Pembatalan tidak bisa diproses, karena sudah berstatus -Posted-");
            redirect_url("cashbook.pembayaran");
        }
        if ($pembayaran->StsHutang == 3){
            $this->persistence->SaveState("error", "Pembatalan tidak bisa diproses, karena sudah berstatus -Void-");
            redirect_url("cashbook.pembayaran");
        }
        if ($pembayaran->Unpaid($pembayaran->NoBuktiBayar,$this->userUid)){
            $this->persistence->SaveState("info", "Data Hutang No: ".$pembayaran->NoBukti." berhasil dibatalkan pembayarannya");
            redirect_url("cashbook.pembayaran");
        }
    }
}
