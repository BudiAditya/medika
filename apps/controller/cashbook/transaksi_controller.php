<?php
class TransaksiController extends AppController {
	private $userCompanyId;
	private $userCabangId;
	private $userUid;

	protected function Initialize() {
		require_once(MODEL . "cashbook/transaksi.php");
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
        $settings["columns"][] = array("name" => "a.tgl_transaksi", "display" => "Tanggal", "width" => 60);
        $settings["columns"][] = array("name" => "a.jns_transaksi", "display" => "Jns Transaksi", "width" => 150);
        $settings["columns"][] = array("name" => "a.keterangan", "display" => "Uraian Transaksi", "width" => 300);
        $settings["columns"][] = array("name" => "format(a.debet,0)", "display" => "Debet", "width" => 100, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.kredit,0)", "display" => "Kredit", "width" => 100, "align" => "right");
        $settings["columns"][] = array("name" => "a.beban_bagian", "display" => "Sumber/Beban", "width" => 100);
        $settings["columns"][] = array("name" => "if(a.sts_transaksi = 0,'Draft',if(a.sts_transaksi = 1,'Approved','Void'))", "display" => "Status", "width" => 50);
        $settings["columns"][] = array("name" => "if(a.trx_type = 1,'Auto','Manual')", "display" => "Type", "width" => 50);

        $settings["filters"][] = array("name" => "a.no_bukti", "display" => "No.Bukti");
		$settings["filters"][] = array("name" => "a.jns_transaksi", "display" => "Jenis Transaksi");
        $settings["filters"][] = array("name" => "a.keterangan", "display" => "Uraian Transaksi");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Daftar Transaksi Kas";
            //button action
            if ($acl->CheckUserAccess("transaksi", "add", "cashbook")) {
                $settings["actions"][] = array("Text" => "Add", "Url" => "cashbook.transaksi/add", "Class" => "bt_add", "ReqId" => 0);
            }
            if ($acl->CheckUserAccess("transaksi", "edit", "cashbook")) {
                $settings["actions"][] = array("Text" => "Edit", "Url" => "cashbook.transaksi/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
                    "Error" => "Mohon memilih data transaksi terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu transaksi",
                    "Info" => "Apakah anda yakin mau merubah data transaksi yang dipilih ?");
            }
            if ($acl->CheckUserAccess("transaksi", "view", "cashbook")) {
                $settings["actions"][] = array("Text" => "View", "Url" => "cashbook.transaksi/view/%s", "Class" => "bt_view", "ReqId" => 1,
                    "Error" => "Mohon memilih data transaksi terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu transaksi");
            }
            if ($acl->CheckUserAccess("transaksi", "delete", "cashbook")) {
                $settings["actions"][] = array("Text" => "Delete", "Url" => "cashbook.transaksi/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
                    "Error" => "Mohon memilih data transaksi terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu transaksi",
                    "Info" => "Apakah anda yakin mau menghapus data transaksi yang dipilih ?");
            }

            if ($acl->CheckUserAccess("transaksi", "approve", "cashbook")) {
                $settings["actions"][] = array("Text" => "separator", "Url" => null);
                $settings["actions"][] = array("Text" => "Approve", "Url" => "cashbook.transaksi/approve", "Class" => "bt_approve", "ReqId" => 2,
                    "Error" => "Mohon memilih Data Transaksi terlebih dahulu sebelum proses approval.",
                    "Confirm" => "Apakah anda menyetujui data transaksi kas yang dipilih ?\nKlik OK untuk melanjutkan prosedur");

                $settings["actions"][] = array("Text" => "Un-Approve", "Url" => "cashbook.transaksi/unapprove", "Class" => "bt_reject", "ReqId" => 2,
                    "Error" => "Mohon memilih Data Transaksi terlebih dahulu sebelum proses pembatalan.",
                    "Confirm" => "Apakah anda mau membatalkan approval data transaksi yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
            }
            if ($acl->CheckUserAccess("transaksi", "view", "cashbook")) {
                $settings["actions"][] = array("Text" => "separator", "Url" => null);
                $settings["actions"][] = array("Text" => "Laporan Kas", "Url" => "cashbook.transaksi/report", "Class" => "bt_report", "ReqId" => 0);
            }

			$settings["def_filter"] = 0;
			$settings["def_order"] = 1;
			$settings["singleSelect"] = false;
		} else {
			$settings["from"] = "vw_t_transaksikas AS a";
            if ($_GET["query"] == "") {
                $_GET["query"] = null;
                $settings["where"] = "a.sts_transaksi < 2 And a.is_deleted = 0 And a.entity_id = " . $this->userCompanyId;
            } else {
                $settings["where"] = "a.entity_id = " . $this->userCompanyId;
            }
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

    public function add() {
        require_once(MODEL . "master/jnstransaksi.php");
        require_once(MODEL . "master/department.php");
        $loader = null;
        $log = new UserAdmin();
        $transaksi = new Transaksi();
        $loader = null;
        if (count($this->postData) > 0) {
            // OK user ada kirim data kita proses
            $transaksi->KdJnsTransaksi = $this->GetPostValue("KdJnsTransaksi");
            $transaksi->TglTransaksi = strtotime($this->GetPostValue("TglTransaksi"));
            $transaksi->NoBukti = $transaksi->GetAutoNoBukti($this->userCompanyId,$transaksi->KdJnsTransaksi,$transaksi->TglTransaksi);
            $transaksi->Keterangan = $this->GetPostValue("Keterangan");
            $transaksi->Jumlah = $this->GetPostValue("Jumlah");
            $transaksi->BebanBagian = $this->GetPostValue("BebanBagian");
            $transaksi->TrxType = 2;
            $transaksi->TrxSource = 'Cash Book';
            $transaksi->StsTransaksi = 0;
            $transaksi->CreatebyId = $this->userUid;
            if ($this->DoInsert($transaksi)) {
                $log = $log->UserActivityWriter($this->userCabangId,'cashbook.transaksi','Add New Transaksi -> No: '.$transaksi->NoBukti.' - '.$transaksi->Keterangan,'-','Success');
                $this->persistence->SaveState("info", sprintf("Data Transaksi Kas No: %s telah berhasil disimpan.", $transaksi->NoBukti));
                redirect_url("cashbook.transaksi");
            } else {
                if ($this->connector->GetHasError()) {
                    if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
                        $this->Set("error", sprintf("No Bukti: %s telah ada pada database !", $transaksi->NoBukti));
                    } else {
                        $this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
                    }
                }
            }
        }
        //load data yg diperlukan
        $loader = new JnsTransaksi();
        $jnstrx = $loader->LoadByEntityId($this->userCompanyId,'a.jns_transaksi');
        $this->Set("jnstransaksi", $jnstrx);
        $loader = new Department();
        $deptcode = $loader->LoadByEntityId($this->userCompanyId);
        $this->Set("deptcode", $deptcode);
        // untuk kirim variable ke view
        $this->Set("transaksi", $transaksi);
    }

    private function DoInsert(Transaksi $transaksi) {

        if ($transaksi->NoBukti == "") {
            $this->Set("error", "Nomor Bukri transaksi masih kosong");
            return false;
        }

        if ($transaksi->KdJnsTransaksi == "") {
            $this->Set("error", "Jenis Transaksi transaksi masih kosong");
            return false;
        }

        if ($transaksi->Insert() == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function edit($id = null) {
        require_once(MODEL . "master/jnstransaksi.php");
        require_once(MODEL . "master/department.php");
        $loader = null;
        $log = new UserAdmin();
        $transaksi = new Transaksi();
        $loader = null;
        if (count($this->postData) > 0) {
            // OK user ada kirim data kita proses
            $transaksi->KdJnsTransaksi = $id;
            $transaksi->KdJnsTransaksi = $this->GetPostValue("KdJnsTransaksi");
            $transaksi->TglTransaksi = strtotime($this->GetPostValue("TglTransaksi"));
            $transaksi->NoBukti =$this->GetPostValue("NoBukti");
            $transaksi->Keterangan = $this->GetPostValue("Keterangan");
            $transaksi->Jumlah = $this->GetPostValue("Jumlah");
            $transaksi->BebanBagian = $this->GetPostValue("BebanBagian");
            $transaksi->TrxType = 2;
            $transaksi->TrxSource = 'Cash Book';
            $transaksi->StsTransaksi = 0;
            $transaksi->UpdatebyId = $this->userUid;
            //if ($this->DoUpdate($transaksi)) {
            if ($transaksi->Update($id)){
                $log = $log->UserActivityWriter($this->userCabangId,'cashbook.transaksi','Update Transaksi -> No: '.$transaksi->NoBukti.' - '.$transaksi->Keterangan,'-','Success');
                $this->persistence->SaveState("info", sprintf("Data Transaksi Kas No: %s telah berhasil diupdate..", $transaksi->NoBukti));
                redirect_url("cashbook.transaksi");
            } else {
                if ($this->connector->GetHasError()) {
                    if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
                        $this->Set("error", sprintf("No Bukti: %s telah ada pada database !", $transaksi->NoBukti));
                    } else {
                        $this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
                    }
                }
            }
        }else{
            if ($id == null) {
                $this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan edit data !");
                redirect_url("cashbook.transaksi");
            }
            $transaksi = $transaksi->FindById($id);
            if ($transaksi == null) {
                $this->persistence->SaveState("error", "Data Transaksi yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
                redirect_url("cashbook.transaksi");
            }
            if ($transaksi->StsTransaksi == 1){
                $this->persistence->SaveState("error", sprintf("Transaksi Kas No: %s sudah berstatus -APPROVED-, tidak boleh diubah.",$transaksi->NoBukti));
                redirect_url("cashbook.transaksi");
            }
        }
        //load data yg diperlukan
        $loader = new JnsTransaksi();
        $jnstrx = $loader->LoadByEntityId($this->userCompanyId,'a.jns_transaksi');
        $this->Set("jnstransaksi", $jnstrx);
        $loader = new Department();
        $deptcode = $loader->LoadByEntityId($this->userCompanyId);
        $this->Set("deptcode", $deptcode);
        // untuk kirim variable ke view
        $this->Set("transaksi", $transaksi);
    }

    private function DoUpdate(Transaksi $transaksi) {

        if ($transaksi->NoBukti == "") {
            $this->Set("error", "Nomor Bukri transaksi masih kosong");
            return false;
        }

        if ($transaksi->KdJnsTransaksi == "") {
            $this->Set("error", "Jenis Transaksi transaksi masih kosong");
            return false;
        }

        if ($transaksi->Update($transaksi->Id)) {
            return true;
        } else {
            return false;
        }
    }

    public function view($id = null) {
        require_once(MODEL . "master/jnstransaksi.php");
        require_once(MODEL . "master/department.php");
        $loader = null;
        $log = new UserAdmin();
        $transaksi = new Transaksi();

        if ($id == null) {
            $this->persistence->SaveState("error", "Anda harus memilih data transaksi untuk direview !");
            redirect_url("cashbook.transaksi");
        }
        $transaksi = $transaksi->FindById($id);
        if ($transaksi == null) {
            $this->persistence->SaveState("error", "Data Transaksi yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
            redirect_url("cashbook.transaksi");
        }
        //load data yg diperlukan
        $loader = new JnsTransaksi();
        $jnstrx = $loader->LoadByEntityId($this->userCompanyId,'a.jns_transaksi');
        $this->Set("jnstransaksi", $jnstrx);
        $loader = new Department();
        $deptcode = $loader->LoadByEntityId($this->userCompanyId);
        $this->Set("deptcode", $deptcode);
        // untuk kirim variable ke view
        $this->Set("transaksi", $transaksi);
    }

    public function delete($id = null) {
        if ($id == null) {
            $this->persistence->SaveState("error", "Anda harus memilih data transaksi sebelum melakukan hapus data !");
            redirect_url("cashbook.transaksi");
        }
        $log = new UserAdmin();
        $transaksi = new Transaksi();
        $transaksi = $transaksi->FindById($id);
        if ($transaksi == null) {
            $this->persistence->SaveState("error", "Data transaksi yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
            redirect_url("cashbook.transaksi");
        }
        if ($transaksi->StsTransaksi == 1){
            $this->persistence->SaveState("error", sprintf("Transaksi Kas No: %s sudah berstatus -APPROVED-, tidak boleh dihapus.",$transaksi->NoBukti));
            redirect_url("cashbook.transaksi");
        }
        if ($transaksi->StsTransaksi == 0) {
            if ($transaksi->Delete($transaksi->Id) == 1) {
                $log = $log->UserActivityWriter($this->userCabangId, 'cashbook.transaksi', 'Delete Transaksi No: ' . $transaksi->NoBukti . ' - ' . $transaksi->Keterangan, '-', 'Success');
                $this->persistence->SaveState("info", sprintf("Data Transaksi No: %s berhasil dihapus.", $transaksi->NoBukti));
                redirect_url("cashbook.transaksi");
            } else {
                $this->persistence->SaveState("error", sprintf("Gagal menghapus Transaksi Kas No: %s. Message: %s", $transaksi->NoBukti, $this->connector->GetErrorMessage()));
            }
        }else{
            if ($transaksi->DeletePermanent($transaksi->Id) == 1) {
                $log = $log->UserActivityWriter($this->userCabangId, 'cashbook.transaksi', 'Delete Transaksi No: ' . $transaksi->NoBukti . ' - ' . $transaksi->Keterangan, '-', 'Success');
                $this->persistence->SaveState("info", sprintf("Data Transaksi No: %s berhasil dihapus.", $transaksi->NoBukti));
                redirect_url("cashbook.transaksi");
            } else {
                $this->persistence->SaveState("error", sprintf("Gagal menghapus Transaksi Kas No: %s. Message: %s", $transaksi->NoBukti, $this->connector->GetErrorMessage()));
            }
        }
        redirect_url("cashbook.transaksi");
    }

    public function approve() {
        $ids = $this->GetGetValue("id", array());
        if (count($ids) == 0) {
            $this->persistence->SaveState("error", "Maaf anda belum memilih data yang akan di approve !");
            redirect_url("cashbook.transaksi");
            return;
        }
        $infos = array();
        $errors = array();
        foreach ($ids as $id) {
            $transaksi = new Transaksi();
            $log = new UserAdmin();
            $transaksi = $transaksi->FindById($id);
            /** @var $transaksi Transaksi */
            // process transaksi
            if($transaksi->StsTransaksi == 0){
                $rs = $transaksi->Approve($transaksi->Id,$this->userUid);
                if ($rs) {
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbook.transaksi','Approve Transaksi',$transaksi->NoBukti,'Success');
                    $infos[] = sprintf("Data Transaksi No: %s telah berhasil di-approve.", $transaksi->NoBukti);
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbook.transaksi','Approve Transaksi',$transaksi->NoBukti,'Failed');
                    $errors[] = sprintf("Maaf, Gagal proses approve Data Transaksi No: %s. Message: %s", $transaksi->NoBukti, $this->connector->GetErrorMessage());
                }
            }else{
                $errors[] = sprintf("Data Transaksi No: %s sudah berstatus -Approved- !",$transaksi->NoBukti);
            }
        }
        if (count($infos) > 0) {
            $this->persistence->SaveState("info", "<ul><li>" . implode("</li><li>", $infos) . "</li></ul>");
        }
        if (count($errors) > 0) {
            $this->persistence->SaveState("error", "<ul><li>" . implode("</li><li>", $errors) . "</li></ul>");
        }
        redirect_url("cashbook.transaksi");
    }

    public function unapprove() {
        $ids = $this->GetGetValue("id", array());
        if (count($ids) == 0) {
            $this->persistence->SaveState("error", "Maaf anda belum memilih data yang akan di Unapprove !");
            redirect_url("cashbook.transaksi");
            return;
        }
        $infos = array();
        $errors = array();
        foreach ($ids as $id) {
            $transaksi = new Transaksi();
            $log = new UserAdmin();
            $transaksi = $transaksi->FindById($id);
            /** @var $transaksi Transaksi */
            // process transaksi
            if($transaksi->StsTransaksi == 1){
                $rs = $transaksi->Unapprove($transaksi->Id,$this->userUid);
                if ($rs) {
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbook.transaksi','Un-Approve Transaksi',$transaksi->NoBukti,'Success');
                    $infos[] = sprintf("Data Transaksi No: %s telah berhasil di-unapprove.", $transaksi->NoBukti);
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbook.transaksi','Un-Approve Transaksi',$transaksi->NoBukti,'Failed');
                    $errors[] = sprintf("Maaf, Gagal proses unapprove Data Transaksi No: %s. Message: %s", $transaksi->NoBukti, $this->connector->GetErrorMessage());
                }
            }else{
                $errors[] = sprintf("Data Transaksi No: %s sudah berstatus -Draft- !",$transaksi->NoBukti);
            }
        }
        if (count($infos) > 0) {
            $this->persistence->SaveState("info", "<ul><li>" . implode("</li><li>", $infos) . "</li></ul>");
        }
        if (count($errors) > 0) {
            $this->persistence->SaveState("error", "<ul><li>" . implode("</li><li>", $errors) . "</li></ul>");
        }
        redirect_url("cashbook.transaksi");
    }

    public function report(){
        // report transaksi
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/klptransaksi.php");
        require_once(MODEL . "master/jnstransaksi.php");
        require_once(MODEL . "master/department.php");
        // Intelligent time detection...
        $month = (int)date("n");
        $year = (int)date("Y");
        $loader = null;
        if (count($this->postData) > 0) {
            // proses rekap disini
            $sKdKlpTrx = $this->GetPostValue("KdKlpTransaksi");
            $sKdJnsTrx = $this->GetPostValue("KdJnsTransaksi");
            $sKdDept = $this->GetPostValue("BebanBagian");
            $sStartDate = strtotime($this->GetPostValue("StartDate"));
            $sEndDate = strtotime($this->GetPostValue("EndDate"));
            $sOutput = $this->GetPostValue("Output");
            $sPosKas = $this->GetPostValue("PosisiKas");
            $sJnsRpt = $this->GetPostValue("JnsLaporan");
            // tahun transaksi harus sama
            if (date("Y",$sStartDate) == date("Y",$sEndDate)){
                // ambil data yang diperlukan
                $transaksi = new Transaksi();
                $sSldLalu = $transaksi->GetSaldoLalu($this->userCompanyId,$sStartDate);
                if ($sJnsRpt == 1) {
                    $reports = $transaksi->Load4DetailReports($this->userCompanyId, $sKdKlpTrx, $sKdJnsTrx, $sKdDept, $sPosKas, $sStartDate, $sEndDate);
                }else{
                    $reports = $transaksi->Load4RekapReports($this->userCompanyId, $sKdKlpTrx, $sKdJnsTrx, $sKdDept, $sPosKas, $sStartDate, $sEndDate);
                }
            }else{
                $reports = null;
                $this->persistence->SaveState("error", "Maaf Data Transaksi yang diminta harus dari tahun yang sama.");
                redirect_url("cashbook.transaksi/report");
            }
        }else{
            $sKdKlpTrx = null;
            $sKdJnsTrx = null;
            $sKdDept = null;
            $sPosKas = 0;
            $sStartDate = mktime(0, 0, 0, $month, 1, $year);
            $sEndDate = time();
            $sOutput = 0;
            $sJnsRpt = 1;
            $sSldLalu = 0;
            $reports = null;
        }
        $loader = new Company($this->userCompanyId);
        $this->Set("company_name", $loader->CompanyName);
        //load data yg diperlukan
        $loader = new Klptransaksi();
        $klptrx = $loader->LoadByEntityId($this->userCompanyId,'a.klp_transaksi');
        $this->Set("klptransaksi", $klptrx);
        $loader = new JnsTransaksi();
        $jnstrx = $loader->LoadByEntityId($this->userCompanyId,'a.jns_transaksi');
        $this->Set("jnstransaksi", $jnstrx);
        $loader = new Department();
        $deptcode = $loader->LoadByEntityId($this->userCompanyId);
        $this->Set("deptcode", $deptcode);
        // kirim ke view
        $this->Set("StartDate",$sStartDate);
        $this->Set("EndDate",$sEndDate);
        $this->Set("KdKlpTrx",$sKdKlpTrx);
        $this->Set("KdJnsTrx",$sKdJnsTrx);
        $this->Set("KdDept",$sKdDept);
        $this->Set("PosKas",$sPosKas);
        $this->Set("JnsRpt",$sJnsRpt);
        $this->Set("Output",$sOutput);
        $this->Set("Sawal",$sSldLalu);
        $this->Set("reports",$reports);
    }

}
