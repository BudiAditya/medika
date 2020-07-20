<?php
class JasaController extends AppController {
	private $userCompanyId;
	private $userCabangId;
	private $userUid;

	protected function Initialize() {
		require_once(MODEL . "master/jasa.php");
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
        $settings["columns"][] = array("name" => "a.kd_jasa", "display" => "Kode", "width" => 60);
        $settings["columns"][] = array("name" => "b.klp_jasa", "display" => "Kelompok", "width" => 150);
		$settings["columns"][] = array("name" => "a.nm_jasa", "display" => "Nama Jasa/Tindakan", "width" => 200);
        $settings["columns"][] = array("name" => "a.uraian_jasa", "display" => "Uraian Jasa/Tindakan", "width" => 350);
        $settings["columns"][] = array("name" => "a.satuan", "display" => "Satuan", "width" => 50);
        $settings["columns"][] = array("name" => "format(a.t_poli,0)", "display" => "Poliklinik", "width" => 70, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.t_igd,0)", "display" => "I G D", "width" => 60, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.t_k3,0)", "display" => "Kelas 3", "width" => 60, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.t_k2,0)", "display" => "Kelas 2", "width" => 60, "align" => "right");
        $settings["columns"][] = array("name" => "if(a.is_bpjs = 1,'Ya','Tidak')", "display" => "BPJS?", "width" => 60, "align" => "left");
        //filtering
		$settings["filters"][] = array("name" => "b.klp_jasa", "display" => "Kelompok");
		$settings["filters"][] = array("name" => "a.kd_jasa", "display" => "Kode");
        $settings["filters"][] = array("name" => "a.nm_jasa", "display" => "Nama Jasa/Tindakan");
        $settings["filters"][] = array("name" => "a.uraian_jasa", "display" => "Uraian Jasa/Tindakan");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Daftar Jasa/Tindakan/Layanan & Tarif";

			if ($acl->CheckUserAccess("jasa", "add", "master")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.jasa/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("jasa", "edit", "master")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.jasa/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
											   "Error" => "Mohon memilih data terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu data",
											   "Info" => "Apakah anda yakin mau merubah data yang dipilih ?");
			}
			if ($acl->CheckUserAccess("jasa", "delete", "master")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.jasa/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
											   "Error" => "Mohon memilih data terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu data",
											   "Info" => "Apakah anda yakin mau menghapus data yang dipilih ?");
			}
            //sort and method
			$settings["def_filter"] = 0;
			$settings["def_order"] = 2;
			$settings["singleSelect"] = true;
		} else {
		    //database
			$settings["from"] = "m_jasa as a Join m_klpjasa as b On a.kd_klpjasa = b.kd_klpjasa";
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
        require_once(MODEL . "master/klpjasa.php");
        require_once(MODEL . "master/klpbilling.php");
		$loader = null;
		$jasa = new Jasa();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$jasa->EntityId = $this->userCompanyId;
			$jasa->KdJasa = $this->GetPostValue("KdJasa");
            $jasa->NmJasa = $this->GetPostValue("NmJasa");
			$jasa->KdKlpJasa = $this->GetPostValue("KdKlpJasa");
            $jasa->KdKlpBilling = $this->GetPostValue("KdKlpBilling");
            $jasa->UraianJasa = $this->GetPostValue("UraianJasa");
            $jasa->Satuan = $this->GetPostValue("Satuan");
            $jasa->TPoli = $this->GetPostValue("TPoli");
            $jasa->TPolSp = $this->GetPostValue("TPolSp");
            $jasa->TPolRd = $this->GetPostValue("TPolRd");
            $jasa->TPolKb = $this->GetPostValue("TPolKb");
            $jasa->TPersalinan = $this->GetPostValue("TPersalinan");
            $jasa->TIgd = $this->GetPostValue("TIgd");
            $jasa->TK3 = $this->GetPostValue("TK3");
            $jasa->TK2 = $this->GetPostValue("TK2");
            $jasa->TK1 = $this->GetPostValue("TK1");
            $jasa->TVip = $this->GetPostValue("TVip");
            $jasa->IsAuto = $this->GetPostValue("IsAuto");
            //$jasa->KpBaru = $this->GetPostValue("KpBaru");
            //$jasa->KpLama = $this->GetPostValue("KpLama");
            $jasa->IsFeeDokter = $this->GetPostValue("IsFeeDokter");
            $jasa->IsBpjs = $this->GetPostValue("IsBpjs");
            $jasa->BpjsLimit = $this->GetPostValue("BpjsLimit");
            $jasa->BpjsLimitMode = $this->GetPostValue("BpjsLimitMode");
            $jasa->CreatebyId = $this->userUid;
			if ($this->DoInsert($jasa)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.jasa','Add New Jasa -> Kode: '.$jasa->KdJasa.' - '.$jasa->NmJasa,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data Jasa: '%s' Dengan Kode: %s telah berhasil disimpan.", $jasa->NmJasa, $jasa->KdJasa));
				redirect_url("master.jasa");
			} else {
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $jasa->EntityCd));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		}
		// untuk kirim variable ke view
        $loader = new Klpjasa();
		$klpjasa = $loader->LoadByEntityId($this->userCompanyId);
		$this->Set("klpjasa", $klpjasa);
        $loader = new KlpBilling();
        $klpbilling = $loader->LoadByEntityId($this->userCompanyId,"a.klp_billing");
        $this->Set("klpbilling", $klpbilling);
        $this->Set("jasa", $jasa);
	}

	private function DoInsert(Jasa $jasa) {
		if ($jasa->KdJasa == "") {
			$this->Set("error", "Kode belum diisi");
			return false;
		}
		if ($jasa->NmJasa == "") {
			$this->Set("error", "Nama Jasa belum diisi");
			return false;
		}
		if ($jasa->Insert() == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function edit($id = null) {
        require_once(MODEL . "master/klpjasa.php");
        require_once(MODEL . "master/klpbilling.php");
		$loader = null;
		$jasa = new Jasa();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$jasa->Id = $id;
			$jasa->EntityId = $this->userCompanyId;
            $jasa->KdJasa = $this->GetPostValue("KdJasa");
            $jasa->NmJasa = $this->GetPostValue("NmJasa");
            $jasa->UraianJasa = $this->GetPostValue("UraianJasa");
            $jasa->KdKlpJasa = $this->GetPostValue("KdKlpJasa");
            $jasa->KdKlpBilling = $this->GetPostValue("KdKlpBilling");
            $jasa->Satuan = $this->GetPostValue("Satuan");
            $jasa->TPoli = $this->GetPostValue("TPoli");
            $jasa->TPolSp = $this->GetPostValue("TPolSp");
            $jasa->TPolRd = $this->GetPostValue("TPolRd");
            $jasa->TPolKb = $this->GetPostValue("TPolKb");
            $jasa->TPersalinan = $this->GetPostValue("TPersalinan");
            $jasa->TIgd = $this->GetPostValue("TIgd");
            $jasa->TK3 = $this->GetPostValue("TK3");
            $jasa->TK2 = $this->GetPostValue("TK2");
            $jasa->TK1 = $this->GetPostValue("TK1");
            $jasa->TVip = $this->GetPostValue("TVip");
            $jasa->IsAuto = $this->GetPostValue("IsAuto");
            //$jasa->KpBaru = $this->GetPostValue("KpBaru");
            //$jasa->KpLama = $this->GetPostValue("KpLama");
            $jasa->IsFeeDokter = $this->GetPostValue("IsFeeDokter");
            $jasa->IsBpjs = $this->GetPostValue("IsBpjs");
            $jasa->BpjsLimit = $this->GetPostValue("BpjsLimit");
            $jasa->BpjsLimitMode = $this->GetPostValue("BpjsLimitMode");
            $jasa->UpdatebyId = $this->userUid;
			if ($this->DoUpdate($jasa)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.jasa','Update Jasa -> Kode: '.$jasa->KdJasa.' - '.$jasa->NmJasa,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data Jasa: '%s' Dengan Kode: %s telah berhasil diupdate.", $jasa->NmJasa, $jasa->KdJasa));
				redirect_url("master.jasa");
			} else {
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $jasa->EntityCd));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		} else {
			if ($id == null) {
				$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan edit data !");
				redirect_url("master.jasa");
			}
			$jasa = $jasa->FindById($id);
			if ($jasa == null) {
				$this->persistence->SaveState("error", "Data Jasa yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
				redirect_url("master.jasa");
			}
		}
        $loader = new Klpjasa();
        $klpjasa = $loader->LoadByEntityId($this->userCompanyId);
        $this->Set("klpjasa", $klpjasa);
        $loader = new KlpBilling();
        $klpbilling = $loader->LoadByEntityId($this->userCompanyId,"a.klp_billing");
        $this->Set("klpbilling", $klpbilling);
        $this->Set("jasa", $jasa);
	}

	private function DoUpdate(Jasa $jasa) {
		if ($jasa->KdJasa == "") {
			$this->Set("error", "Kode  belum diisi");
			return false;
		}
		if ($jasa->NmJasa == "") {
			$this->Set("error", "Nama Jasa belum diisi");
			return false;
		}
		if ($jasa->Update($jasa->Id) == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function delete($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan hapus data !");
			redirect_url("master.jasa");
		}
		$log = new UserAdmin();
		$jasa = new Jasa();
		$jasa = $jasa->FindById($id);
		if ($jasa == null) {
			$this->persistence->SaveState("error", "Data yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
			redirect_url("master.jasa");
		}

		if ($jasa->Delete($jasa->Id) == 1) {
			$log = $log->UserActivityWriter($this->userCabangId,'master.jasa','Delete Jasa -> Kode: '.$jasa->KdJasa.' - '.$jasa->NmJasa,'-','Success');
			$this->persistence->SaveState("info", sprintf("Data Jasa: '%s' Dengan Kode: %s telah berhasil dihapus.", $jasa->NmJasa, $jasa->KdJasa));
			redirect_url("master.jasa");
		} else {
			$this->persistence->SaveState("error", sprintf("Gagal menghapus data Jasa: '%s'. Message: %s", $jasa->NmJasa, $this->connector->GetErrorMessage()));
		}
		redirect_url("master.jasa");
	}

	public function optlistbyentity($EntityId = null, $sJasaId = null) {
		$buff = '<option value="">-- PILIH KELOMPOK --</option>';
		if ($EntityId == null) {
			print($buff);
			return;
		}
		$jasa = new Jasa();
		$jasas = $jasa->LoadByEntityId($EntityId);
		foreach ($jasas as $jasa) {
			if ($jasa->Id == $sJasaId) {
				$buff .= sprintf('<option value="%d" selected="selected">%s - %s</option>', $jasa->Id, $jasa->KdJasa, $jasa->NmJasa);
			} else {
				$buff .= sprintf('<option value="%d">%s - %s</option>', $jasa->Id, $jasa->KdJasa, $jasa->NmJasa);
			}
		}
		print($buff);
	}

    public function AutoKode(){
        $kode = new Jasa();
        $kode = $kode->GetAutoKode($this->userCompanyId);
        print $kode;
    }
}
