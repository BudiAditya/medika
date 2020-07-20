<?php
class KlpJasaController extends AppController {
	private $userCompanyId;
	private $userCabangId;
	private $userUid;

	protected function Initialize() {
		require_once(MODEL . "master/klpjasa.php");
		require_once(MODEL . "master/user_admin.php");
		$this->userCompanyId = $this->persistence->LoadState("entity_id");
		$this->userCabangId = $this->persistence->LoadState("cabang_id");
        $this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();
		//contents
		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 40);
		$settings["columns"][] = array("name" => "a.kd_klpjasa", "display" => "Kode", "width" => 50);
		$settings["columns"][] = array("name" => "a.klp_jasa", "display" => "Kelompok Jasa/Tindakan/Layanan", "width" => 250);
        $settings["columns"][] = array("name" => "a.trxtype_code", "display" => "Kode Transaksi", "width" => 100);
        $settings["columns"][] = array("name" => "a.pjm_klinik", "display" => "Klinik (%)", "width" => 60, "align" => "center");
        $settings["columns"][] = array("name" => "a.pjm_operator", "display" => "Operator (%)", "width" => 60, "align" => "center");
        $settings["columns"][] = array("name" => "a.pjm_pelaksana", "display" => "Pelaksana (%)", "width" => 60, "align" => "center");
        //filtering
		$settings["filters"][] = array("name" => "a.klp_jasa", "display" => "Kelompok");
		$settings["filters"][] = array("name" => "a.kd_klpjasa", "display" => "Kode");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Data Kelompok Jasa/Tindakan";

			if ($acl->CheckUserAccess("klpjasa", "add", "master")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.klpjasa/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("klpjasa", "edit", "master")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.klpjasa/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
											   "Error" => "Mohon memilih data terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu data",
											   "Info" => "Apakah anda yakin mau merubah data yang dipilih ?");
			}
			if ($acl->CheckUserAccess("klpjasa", "delete", "master")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.klpjasa/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
											   "Error" => "Mohon memilih data terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu data",
											   "Info" => "Apakah anda yakin mau menghapus data yang dipilih ?");
			}
            //sort and method
			$settings["def_filter"] = 0;
			$settings["def_order"] = 2;
			$settings["singleSelect"] = true;
		} else {
		    //database
			$settings["from"] = "m_klpjasa AS a";
			if ($this->userCompanyId == 1 || $this->userCompanyId == null) {
				$settings["where"] = "a.is_deleted = 0";
			} else {
				$settings["where"] = "a.is_deleted = 0 AND a.entity_id = " . $this->userCompanyId;
			}
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

	public function add() {
	    require_once (MODEL . "master/trxtype.php");
		$loader = null;
		$klpjasa = new KlpJasa();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$klpjasa->EntityId = $this->userCompanyId;
			$klpjasa->KdKlpJasa = $this->GetPostValue("KdKlpJasa");
			$klpjasa->KlpJasa = $this->GetPostValue("KlpJasa");
            $klpjasa->TrxTypeCode = $this->GetPostValue("TrxTypeCode");
            $klpjasa->PjmKlinik = $this->GetPostValue("PjmKlinik");
            $klpjasa->PjmOperator = $this->GetPostValue("PjmOperator");
            $klpjasa->PjmPelaksana = $this->GetPostValue("PjmPelaksana");
            $klpjasa->CreatebyId = $this->userUid;
			if ($this->DoInsert($klpjasa)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.klpjasa','Add New KlpJasa -> Kode: '.$klpjasa->KdKlpJasa.' - '.$klpjasa->KlpJasa,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data KlpJasa: '%s' Dengan Kode: %s telah berhasil disimpan.", $klpjasa->KlpJasa, $klpjasa->KdKlpJasa));
				redirect_url("master.klpjasa");
			} else {
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $klpjasa->EntityCd));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		}
		//load data
		$loader = new TrxType();
		$trxtype = $loader->LoadByTrxMode($this->userCompanyId,1);
		// untuk kirim variable ke view
		$this->Set("trxtype", $trxtype);
        $this->Set("klpjasa", $klpjasa);
	}

	private function DoInsert(KlpJasa $klpjasa) {
		if ($klpjasa->KdKlpJasa == "") {
			$this->Set("error", "Kode belum diisi!");
			return false;
		}
		if ($klpjasa->KlpJasa == "") {
			$this->Set("error", "Kelompok Jasa belum diisi!");
			return false;
		}
		//if ($klpjasa->PjmKlinik + $klpjasa->PjmOperator + $klpjasa->PjmPelaksana == 0){
        //    $this->Set("error", "Persentase Jasa Medis belum diisi!");
        //    return false;
        // }
        if ($klpjasa->PjmKlinik + $klpjasa->PjmOperator + $klpjasa->PjmPelaksana > 100){
            $this->Set("error", "Persentase Jasa Medis melebihi 100%!");
            return false;
        }
		if ($klpjasa->Insert() == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function edit($id = null) {
        require_once (MODEL . "master/trxtype.php");
		$loader = null;
		$klpjasa = new KlpJasa();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$klpjasa->Id = $id;
			$klpjasa->EntityId = $this->userCompanyId;
			$klpjasa->KdKlpJasa = $this->GetPostValue("KdKlpJasa");
			$klpjasa->KlpJasa = $this->GetPostValue("KlpJasa");
            $klpjasa->TrxTypeCode = $this->GetPostValue("TrxTypeCode");
            $klpjasa->PjmKlinik = $this->GetPostValue("PjmKlinik");
            $klpjasa->PjmOperator = $this->GetPostValue("PjmOperator");
            $klpjasa->PjmPelaksana = $this->GetPostValue("PjmPelaksana");
            $klpjasa->UpdatebyId = $this->userUid;
			if ($this->DoUpdate($klpjasa)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.klpjasa','Update KlpJasa -> Kode: '.$klpjasa->KdKlpJasa.' - '.$klpjasa->KlpJasa,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data KlpJasa: '%s' Dengan Kode: %s telah berhasil diupdate.", $klpjasa->KlpJasa, $klpjasa->KdKlpJasa));
				redirect_url("master.klpjasa");
			} else {
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $klpjasa->EntityCd));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		} else {
			if ($id == null) {
				$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan edit data !");
				redirect_url("master.klpjasa");
			}
			$klpjasa = $klpjasa->FindById($id);
			if ($klpjasa == null) {
				$this->persistence->SaveState("error", "Data KlpJasa yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
				redirect_url("master.klpjasa");
			}
		}
        //load data
        $loader = new TrxType();
        $trxtype = $loader->LoadByTrxMode($this->userCompanyId,1);
        // untuk kirim variable ke view
        $this->Set("trxtype", $trxtype);
        $this->Set("klpjasa", $klpjasa);
	}

	private function DoUpdate(KlpJasa $klpjasa) {
		if ($klpjasa->KdKlpJasa == "") {
            $this->Set("error", "Kode belum diisi!");
            return false;
        }
        if ($klpjasa->KlpJasa == "") {
            $this->Set("error", "Kelompok Jasa belum diisi!");
            return false;
        }
        //if ($klpjasa->PjmKlinik + $klpjasa->PjmOperator + $klpjasa->PjmPelaksana == 0){
        //    $this->Set("error", "Persentase Jasa Medis belum diisi!");
        //    return false;
        //}
        if ($klpjasa->PjmKlinik + $klpjasa->PjmOperator + $klpjasa->PjmPelaksana > 100){
            $this->Set("error", "Persentase Jasa Medis melebihi 100%!");
            return false;
        }
		if ($klpjasa->Update($klpjasa->Id) == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function delete($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan hapus data !");
			redirect_url("master.klpjasa");
		}
		$log = new UserAdmin();
		$klpjasa = new KlpJasa();
		$klpjasa = $klpjasa->FindById($id);
		if ($klpjasa == null) {
			$this->persistence->SaveState("error", "Data yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
			redirect_url("master.klpjasa");
		}

		if ($klpjasa->Delete($klpjasa->Id) == 1) {
			$log = $log->UserActivityWriter($this->userCabangId,'master.klpjasa','Delete KlpJasa -> Kode: '.$klpjasa->KdKlpJasa.' - '.$klpjasa->KlpJasa,'-','Success');
			$this->persistence->SaveState("info", sprintf("Data KlpJasa: '%s' Dengan Kode: %s telah berhasil dihapus.", $klpjasa->KlpJasa, $klpjasa->KdKlpJasa));
			redirect_url("master.klpjasa");
		} else {
			$this->persistence->SaveState("error", sprintf("Gagal menghapus data KlpJasa: '%s'. Message: %s", $klpjasa->KlpJasa, $this->connector->GetErrorMessage()));
		}
		redirect_url("master.klpjasa");
	}

	public function optlistbyentity($EntityId = null, $sKlpJasaId = null) {
		$buff = '<option value="">-- PILIH KELOMPOK --</option>';
		if ($EntityId == null) {
			print($buff);
			return;
		}
		$klpjasa = new KlpJasa();
		$klpjasas = $klpjasa->LoadByEntityId($EntityId);
		foreach ($klpjasas as $klpjasa) {
			if ($klpjasa->Id == $sKlpJasaId) {
				$buff .= sprintf('<option value="%d" selected="selected">%s - %s</option>', $klpjasa->Id, $klpjasa->KdKlpJasa, $klpjasa->KlpJasa);
			} else {
				$buff .= sprintf('<option value="%d">%s - %s</option>', $klpjasa->Id, $klpjasa->KdKlpJasa, $klpjasa->KlpJasa);
			}
		}
		print($buff);
	}

    public function AutoKode(){
        $kode = new KlpJasa();
        $kode = $kode->GetAutoKode($this->userCompanyId);
        print $kode;
    }
}
