<?php
class KlpTransaksiController extends AppController {
	private $userCompanyId;
	private $userCabangId;
	private $userUid;

	protected function Initialize() {
		require_once(MODEL . "master/klptransaksi.php");
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
		$settings["columns"][] = array("name" => "a.kd_klptransaksi", "display" => "Kode", "width" => 150);
		$settings["columns"][] = array("name" => "a.klp_transaksi", "display" => "Kelompok Transaksi", "width" => 350);
        //filtering
		$settings["filters"][] = array("name" => "a.klp_transaksi", "display" => "Kelompok");
		$settings["filters"][] = array("name" => "a.kd_klptransaksi", "display" => "Kode");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Data Kelompok Transaksi";

			if ($acl->CheckUserAccess("klptransaksi", "add", "master")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.klptransaksi/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("klptransaksi", "edit", "master")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.klptransaksi/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
											   "Error" => "Mohon memilih data terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu data",
											   "Info" => "Apakah anda yakin mau merubah data yang dipilih ?");
			}
			if ($acl->CheckUserAccess("klptransaksi", "delete", "master")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.klptransaksi/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
											   "Error" => "Mohon memilih data terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu data",
											   "Info" => "Apakah anda yakin mau menghapus data yang dipilih ?");
			}
            //sort and method
			$settings["def_filter"] = 0;
			$settings["def_order"] = 2;
			$settings["singleSelect"] = true;
		} else {
		    //database
			$settings["from"] = "m_klptransaksi AS a";
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
		$loader = null;
		$klptransaksi = new KlpTransaksi();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$klptransaksi->EntityId = $this->userCompanyId;
			$klptransaksi->KdKlpTransaksi = $klptransaksi->GetAutoKode($this->userCompanyId);
			$klptransaksi->KlpTransaksi = $this->GetPostValue("KlpTransaksi");
            $klptransaksi->CreatebyId = $this->userUid;
			if ($this->DoInsert($klptransaksi)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.klptransaksi','Add New KlpTransaksi -> Kode: '.$klptransaksi->KdKlpTransaksi.' - '.$klptransaksi->KlpTransaksi,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data KlpTransaksi: '%s' Dengan Kode: %s telah berhasil disimpan.", $klptransaksi->KlpTransaksi, $klptransaksi->KdKlpTransaksi));
				redirect_url("master.klptransaksi");
			} else {
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $klptransaksi->EntityCd));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		}
		// untuk kirim variable ke view
		$this->Set("klptransaksi", $klptransaksi);
	}

	private function DoInsert(KlpTransaksi $klptransaksi) {

		if ($klptransaksi->KdKlpTransaksi == "") {
			$this->Set("error", "Kode belum diisi");
			return false;
		}
		if ($klptransaksi->KlpTransaksi == "") {
			$this->Set("error", "Kelompok Transaksi belum diisi");
			return false;
		}
		if ($klptransaksi->Insert() == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function edit($id = null) {
		$loader = null;
		$klptransaksi = new KlpTransaksi();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$klptransaksi->Id = $id;
			$klptransaksi->EntityId = $this->userCompanyId;
			$klptransaksi->KdKlpTransaksi = $this->GetPostValue("KdKlpTransaksi");
			$klptransaksi->KlpTransaksi = $this->GetPostValue("KlpTransaksi");
            $klptransaksi->UpdatebyId = $this->userUid;
			if ($this->DoUpdate($klptransaksi)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.klptransaksi','Update KlpTransaksi -> Kode: '.$klptransaksi->KdKlpTransaksi.' - '.$klptransaksi->KlpTransaksi,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data KlpTransaksi: '%s' Dengan Kode: %s telah berhasil diupdate.", $klptransaksi->KlpTransaksi, $klptransaksi->KdKlpTransaksi));
				redirect_url("master.klptransaksi");
			} else {
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $klptransaksi->EntityCd));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		} else {
			if ($id == null) {
				$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan edit data !");
				redirect_url("master.klptransaksi");
			}
			$klptransaksi = $klptransaksi->FindById($id);
			if ($klptransaksi == null) {
				$this->persistence->SaveState("error", "Data KlpTransaksi yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
				redirect_url("master.klptransaksi");
			}
		}
        $this->Set("klptransaksi", $klptransaksi);
	}

	private function DoUpdate(KlpTransaksi $klptransaksi) {
		if ($klptransaksi->KdKlpTransaksi == "") {
			$this->Set("error", "Kode  belum diisi");
			return false;
		}
		if ($klptransaksi->KlpTransaksi == "") {
			$this->Set("error", "Nama KlpTransaksi belum diisi");
			return false;
		}
		if ($klptransaksi->Update($klptransaksi->Id) == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function delete($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan hapus data !");
			redirect_url("master.klptransaksi");
		}
		$log = new UserAdmin();
		$klptransaksi = new KlpTransaksi();
		$klptransaksi = $klptransaksi->FindById($id);
		if ($klptransaksi == null) {
			$this->persistence->SaveState("error", "Data yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
			redirect_url("master.klptransaksi");
		}

		if ($klptransaksi->Delete($klptransaksi->Id) == 1) {
			$log = $log->UserActivityWriter($this->userCabangId,'master.klptransaksi','Delete KlpTransaksi -> Kode: '.$klptransaksi->KdKlpTransaksi.' - '.$klptransaksi->KlpTransaksi,'-','Success');
			$this->persistence->SaveState("info", sprintf("Data KlpTransaksi: '%s' Dengan Kode: %s telah berhasil dihapus.", $klptransaksi->KlpTransaksi, $klptransaksi->KdKlpTransaksi));
			redirect_url("master.klptransaksi");
		} else {
			$this->persistence->SaveState("error", sprintf("Gagal menghapus data KlpTransaksi: '%s'. Message: %s", $klptransaksi->KlpTransaksi, $this->connector->GetErrorMessage()));
		}
		redirect_url("master.klptransaksi");
	}

	public function optlistbyentity($EntityId = null, $sKlpTransaksiId = null) {
		$buff = '<option value="">-- PILIH KELOMPOK --</option>';
		if ($EntityId == null) {
			print($buff);
			return;
		}
		$klptransaksi = new KlpTransaksi();
		$klptransaksis = $klptransaksi->LoadByEntityId($EntityId);
		foreach ($klptransaksis as $klptransaksi) {
			if ($klptransaksi->Id == $sKlpTransaksiId) {
				$buff .= sprintf('<option value="%d" selected="selected">%s - %s</option>', $klptransaksi->Id, $klptransaksi->KdKlpTransaksi, $klptransaksi->KlpTransaksi);
			} else {
				$buff .= sprintf('<option value="%d">%s - %s</option>', $klptransaksi->Id, $klptransaksi->KdKlpTransaksi, $klptransaksi->KlpTransaksi);
			}
		}
		print($buff);
	}
}
