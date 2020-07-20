<?php
class JnsTransaksiController extends AppController {
	private $userCompanyId;
	private $userCabangId;
	private $userUid;

	protected function Initialize() {
		require_once(MODEL . "master/jnstransaksi.php");
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
        $settings["columns"][] = array("name" => "a.kd_jnstransaksi", "display" => "Kode", "width" => 60);
        $settings["columns"][] = array("name" => "b.klp_transaksi", "display" => "Kelompok", "width" => 150);
		$settings["columns"][] = array("name" => "a.jns_transaksi", "display" => "Jenis Transaksi", "width" => 350);
        $settings["columns"][] = array("name" => " if (a.posisi_kas = 1, 'Masuk','Keluar')", "display" => "Posisi Kas", "width" => 50);

        //filtering
		$settings["filters"][] = array("name" => "b.klp_transaksi", "display" => "Kelompok");
		$settings["filters"][] = array("name" => "a.kd_jnstransaksi", "display" => "Kode");
        $settings["filters"][] = array("name" => "a.jns_transaksi", "display" => "Jenis Transaksi");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Daftar Jenis Transaksi";

			if ($acl->CheckUserAccess("jnstransaksi", "add", "master")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.jnstransaksi/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("jnstransaksi", "edit", "master")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.jnstransaksi/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
											   "Error" => "Mohon memilih data terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu data",
											   "Info" => "Apakah anda yakin mau merubah data yang dipilih ?");
			}
			if ($acl->CheckUserAccess("jnstransaksi", "delete", "master")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.jnstransaksi/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
											   "Error" => "Mohon memilih data terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu data",
											   "Info" => "Apakah anda yakin mau menghapus data yang dipilih ?");
			}
            //sort and method
			$settings["def_filter"] = 0;
			$settings["def_order"] = 2;
			$settings["singleSelect"] = true;
		} else {
		    //database
			$settings["from"] = "m_jnstransaksi as a Join m_klptransaksi as b On a.kd_klptransaksi = b.kd_klptransaksi";
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
        require_once(MODEL . "master/klptransaksi.php");
		$loader = null;
		$jnstransaksi = new JnsTransaksi();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$jnstransaksi->EntityId = $this->userCompanyId;
			$jnstransaksi->KdJnsTransaksi = $jnstransaksi->GetAutoKode($this->userCompanyId);
            $jnstransaksi->JnsTransaksi = $this->GetPostValue("JnsTransaksi");
			$jnstransaksi->KdKlpTransaksi = $this->GetPostValue("KdKlpTransaksi");
            $jnstransaksi->PosisiKas = $this->GetPostValue("PosisiKas");
            $jnstransaksi->CreatebyId = $this->userUid;
			if ($this->DoInsert($jnstransaksi)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.jnstransaksi','Add New JnsTransaksi -> Kode: '.$jnstransaksi->KdJnsTransaksi.' - '.$jnstransaksi->JnsTransaksi,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data JnsTransaksi: '%s' Dengan Kode: %s telah berhasil disimpan.", $jnstransaksi->JnsTransaksi, $jnstransaksi->KdJnsTransaksi));
				redirect_url("master.jnstransaksi");
			} else {
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $jnstransaksi->EntityCd));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		}
		// untuk kirim variable ke view
        $loader = new Klptransaksi();
		$klptransaksi = $loader->LoadByEntityId($this->userCompanyId);
		$this->Set("klptransaksi", $klptransaksi);
        $this->Set("jnstransaksi", $jnstransaksi);
	}

	private function DoInsert(JnsTransaksi $jnstransaksi) {
		if ($jnstransaksi->KdJnsTransaksi == "") {
			$this->Set("error", "Kode belum diisi");
			return false;
		}
		if ($jnstransaksi->JnsTransaksi == "") {
			$this->Set("error", "Jenis Transaksi belum diisi");
			return false;
		}
		if ($jnstransaksi->Insert() == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function edit($id = null) {
        require_once(MODEL . "master/klptransaksi.php");
		$loader = null;
		$jnstransaksi = new JnsTransaksi();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$jnstransaksi->Id = $id;
			$jnstransaksi->EntityId = $this->userCompanyId;
            $jnstransaksi->KdJnsTransaksi = $this->GetPostValue("KdJnsTransaksi");
            $jnstransaksi->JnsTransaksi = $this->GetPostValue("JnsTransaksi");
            $jnstransaksi->KdKlpTransaksi = $this->GetPostValue("KdKlpTransaksi");
            $jnstransaksi->PosisiKas = $this->GetPostValue("PosisiKas");
            $jnstransaksi->UpdatebyId = $this->userUid;
			if ($this->DoUpdate($jnstransaksi)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.jnstransaksi','Update JnsTransaksi -> Kode: '.$jnstransaksi->KdJnsTransaksi.' - '.$jnstransaksi->JnsTransaksi,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data JnsTransaksi: '%s' Dengan Kode: %s telah berhasil diupdate.", $jnstransaksi->JnsTransaksi, $jnstransaksi->KdJnsTransaksi));
				redirect_url("master.jnstransaksi");
			} else {
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $jnstransaksi->EntityCd));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		} else {
			if ($id == null) {
				$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan edit data !");
				redirect_url("master.jnstransaksi");
			}
			$jnstransaksi = $jnstransaksi->FindById($id);
			if ($jnstransaksi == null) {
				$this->persistence->SaveState("error", "Data JnsTransaksi yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
				redirect_url("master.jnstransaksi");
			}
		}
        $loader = new Klptransaksi();
        $klptransaksi = $loader->LoadByEntityId($this->userCompanyId);
        $this->Set("klptransaksi", $klptransaksi);
        $this->Set("jnstransaksi", $jnstransaksi);
	}

	private function DoUpdate(JnsTransaksi $jnstransaksi) {
		if ($jnstransaksi->KdJnsTransaksi == "") {
			$this->Set("error", "Kode  belum diisi");
			return false;
		}
		if ($jnstransaksi->JnsTransaksi == "") {
			$this->Set("error", "Nama JnsTransaksi belum diisi");
			return false;
		}
		if ($jnstransaksi->Update($jnstransaksi->Id) == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function delete($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan hapus data !");
			redirect_url("master.jnstransaksi");
		}
		$log = new UserAdmin();
		$jnstransaksi = new JnsTransaksi();
		$jnstransaksi = $jnstransaksi->FindById($id);
		if ($jnstransaksi == null) {
			$this->persistence->SaveState("error", "Data yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
			redirect_url("master.jnstransaksi");
		}

		if ($jnstransaksi->Delete($jnstransaksi->Id) == 1) {
			$log = $log->UserActivityWriter($this->userCabangId,'master.jnstransaksi','Delete JnsTransaksi -> Kode: '.$jnstransaksi->KdJnsTransaksi.' - '.$jnstransaksi->JnsTransaksi,'-','Success');
			$this->persistence->SaveState("info", sprintf("Data JnsTransaksi: '%s' Dengan Kode: %s telah berhasil dihapus.", $jnstransaksi->JnsTransaksi, $jnstransaksi->KdJnsTransaksi));
			redirect_url("master.jnstransaksi");
		} else {
			$this->persistence->SaveState("error", sprintf("Gagal menghapus data JnsTransaksi: '%s'. Message: %s", $jnstransaksi->JnsTransaksi, $this->connector->GetErrorMessage()));
		}
		redirect_url("master.jnstransaksi");
	}

	public function optlistbyentity($EntityId = null, $sJnsTransaksiId = null) {
		$buff = '<option value="">-- PILIH KELOMPOK --</option>';
		if ($EntityId == null) {
			print($buff);
			return;
		}
		$jnstransaksi = new JnsTransaksi();
		$jnstransaksis = $jnstransaksi->LoadByEntityId($EntityId);
		foreach ($jnstransaksis as $jnstransaksi) {
			if ($jnstransaksi->Id == $sJnsTransaksiId) {
				$buff .= sprintf('<option value="%d" selected="selected">%s - %s</option>', $jnstransaksi->Id, $jnstransaksi->KdJnsTransaksi, $jnstransaksi->JnsTransaksi);
			} else {
				$buff .= sprintf('<option value="%d">%s - %s</option>', $jnstransaksi->Id, $jnstransaksi->KdJnsTransaksi, $jnstransaksi->JnsTransaksi);
			}
		}
		print($buff);
	}
}
