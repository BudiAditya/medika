<?php

class ReportController extends AppController {
	private $userCompanyId;
    private $userCabangId;
    private $userLevel;
    private $trxMonth;
    private $trxYear;

	protected function Initialize() {
		require_once(MODEL . "cashbook/cbtrx.php");
        require_once(MODEL . "master/user_admin.php");
		$this->userCompanyId = $this->persistence->LoadState("entity_id");
        $this->userCabangId = $this->persistence->LoadState("cabang_id");
        $this->userLevel = $this->persistence->LoadState("user_lvl");
        $this->trxMonth = $this->persistence->LoadState("acc_month");
        $this->trxYear = $this->persistence->LoadState("acc_year");
	}

	public function index() {
		// reporting process
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/trxtype.php");
        require_once(MODEL . "master/bank.php");
        // Intelligent time detection...
        $month = (int)date("n");
        $year = (int)date("Y");
        $loader = null;
        if (count($this->postData) > 0) {
            // proses rekap disini
            $sCabangId = $this->GetPostValue("CabangId");
            $sTrxMode = $this->GetPostValue("TrxMode");
            $sTrxTypeCode = $this->GetPostValue("TrxTypeCode");
            $sCoaBankId= $this->GetPostValue("CoaBankId");
            $sTrxStatus = $this->GetPostValue("TrxStatus");
            $sStartDate = strtotime($this->GetPostValue("StartDate"));
            $sEndDate = strtotime($this->GetPostValue("EndDate"));
            $sOutput = $this->GetPostValue("Output");
            // tahun transaksi harus sama
            if (date("Y",$sStartDate) == date("Y",$sEndDate)){
                // ambil data yang diperlukan
                $cbtrx = new cbtrx();
                $reports = $cbtrx->Load4Reports($this->userCompanyId,$sCabangId,$sTrxTypeCode,$sTrxMode,$sCoaBankId,$sTrxStatus,$sStartDate,$sEndDate);
            }else{
                $reports = null;
                $this->persistence->SaveState("error", "Maaf Data Transaksi yang diminta harus dari tahun yang sama.");
                redirect_url("cashbook.cbtrx/report");
            }
        }else{
            $sCabangId = 0;
            $sTrxMode = 0;
            $sCoaBankId = 0;
            $sTrxStatus = -1;
            $sTrxTypeCode = null;
            $sStartDate = mktime(0, 0, 0, $month, 1, $year);
            $sEndDate = time();
            $sOutput = 0;
            $reports = null;
        }
        $loader = new Company($this->userCompanyId);
        $this->Set("company_name", $loader->CompanyName);
        $loader = new TrxType();
        $trxTypes = $loader->LoadAll($this->userCompanyId);
        $loader = new Bank();
        $coaBanks = $loader->LoadByEntityId($this->userCompanyId);
        //load data cabang
        $loader = new Cabang();
        $cabangs = $loader->LoadByEntityId($this->userCompanyId);
        // kirim ke view
        $this->Set("Cabangs",$cabangs);
        $this->Set("CoaBanks",$coaBanks);
        $this->Set("TrxTypes",$trxTypes);
        $this->Set("CabangId",$sCabangId);
        $this->Set("TrxTypeCode",$sTrxTypeCode);
        $this->Set("TrxMode",$sTrxMode);
        $this->Set("CoaBankId",$sCoaBankId);
        $this->Set("StartDate",$sStartDate);
        $this->Set("EndDate",$sEndDate);
        $this->Set("TrxStatus",$sTrxStatus);
        $this->Set("Output",$sOutput);
        $this->Set("Reports",$reports);
    }

    public function rekoran(){
        // report rekonsil process
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/coadetail.php");
        // Intelligent time detection...
        $month = (int)date("n");
        $year = (int)date("Y");
        $loader = null;
        if (count($this->postData) > 0) {
            // proses rekap disini
            $sCabangId = $this->GetPostValue("CabangId");
            $sCoaBankId= $this->GetPostValue("CoaBankId");
            $sStartDate = strtotime($this->GetPostValue("StartDate"));
            $sEndDate = strtotime($this->GetPostValue("EndDate"));
            $sOutput = $this->GetPostValue("Output");
            // tahun transaksi harus sama
            if (date("Y",$sStartDate) == date("Y",$sEndDate)){
                // ambil data yang diperlukan
                $cbtrx = new cbtrx();
                $sSaldoAwal = $cbtrx->GetSaldoAwal($sCabangId,$sCoaBankId,$sStartDate);
                $reports = $cbtrx->LoadRekoran($sCabangId,$sCoaBankId,$sStartDate,$sEndDate);
            }else{
                $reports = null;
                $this->persistence->SaveState("error", "Maaf Data Transaksi yang diminta harus dari tahun yang sama.");
                redirect_url("cashbook.cbtrx/rekoran");
            }
        }else{
            $sCabangId = 0;
            $sCoaBankId = 1;
            $sSaldoAwal = 0;
            $sStartDate = mktime(0, 0, 0, $month, 1, $year);
            $sEndDate = time();
            $sOutput = 0;
            $reports = null;
        }
        $loader = new Company($this->userCompanyId);
        $this->Set("company_name", $loader->CompanyName);
        $loader = new CoaDetail();
        $coaBanks = $loader->LoadCashBookAccount();
        //load data cabang
        $loader = new Cabang();
        $cabangs = $loader->LoadByEntityId($this->userCompanyId);
        //load data kas/bank
        $loader = new CoaDetail($sCoaBankId);
        $sBankName = $loader->Perkiraan;
        $sBankKode = $loader->Kode;
        // kirim ke view
        $this->Set("Cabangs",$cabangs);
        $this->Set("CoaBanks",$coaBanks);
        $this->Set("CabangId",$sCabangId);
        $this->Set("CoaBankId",$sCoaBankId);
        $this->Set("StartDate",$sStartDate);
        $this->Set("EndDate",$sEndDate);
        $this->Set("Output",$sOutput);
        $this->Set("BankName",$sBankName);
        $this->Set("BankKode",$sBankKode);
        $this->Set("SaldoAwal",$sSaldoAwal);
        $this->Set("Reports",$reports);
    }


}

// End of file: cbtrx_controller.php
