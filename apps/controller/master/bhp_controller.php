<?php
class BhpController extends AppController {
	private $userCompanyId;
	private $userCabangId;
	private $userUid;

	protected function Initialize() {
		require_once(MODEL . "master/bhp.php");
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
        $settings["columns"][] = array("name" => "a.kd_bhp", "display" => "Kode", "width" => 100);
		$settings["columns"][] = array("name" => "a.nm_bhp", "display" => "Nama Bahan Habis Pakai", "width" => 350);
        $settings["columns"][] = array("name" => "a.satuan", "display" => "Satuan", "width" => 50);
        $settings["columns"][] = array("name" => "format(a.harga_dasar,0)", "display" => "Hrg Dasar", "width" => 70, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.harga_jual,0)", "display" => "Hrg Jual", "width" => 70, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.stock_awal,0)", "display" => "Stock Awal", "width" => 70, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.stock_qty,0)", "display" => "Qty Stock", "width" => 70, "align" => "right");
        //filtering
		$settings["filters"][] = array("name" => "a.kd_bhp", "display" => "Kode");
        $settings["filters"][] = array("name" => "a.nm_bhp", "display" => "Nama Bahan");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Daftar Bahan Habis Pakai";

			if ($acl->CheckUserAccess("bhp", "add", "master")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.bhp/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("bhp", "edit", "master")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.bhp/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
											   "Error" => "Mohon memilih data terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu data",
											   "Info" => "Apakah anda yakin mau merubah data yang dipilih ?");
			}
			if ($acl->CheckUserAccess("bhp", "delete", "master")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.bhp/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
											   "Error" => "Mohon memilih data terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu data",
											   "Info" => "Apakah anda yakin mau menghapus data yang dipilih ?");
			}
            //sort and method
			$settings["def_filter"] = 0;
			$settings["def_order"] = 2;
			$settings["singleSelect"] = true;
		} else {
		    //database
			$settings["from"] = "m_bhp as a";
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
		$bhp = new Bhp();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$bhp->EntityId = $this->userCompanyId;
			$bhp->KdBhp = $this->GetPostValue("KdBhp");
            $bhp->NmBhp = $this->GetPostValue("NmBhp");
			$bhp->HargaDasar = $this->GetPostValue("HargaDasar");
            $bhp->HargaJual = $this->GetPostValue("HargaJual");
            $bhp->Satuan = $this->GetPostValue("Satuan");
            $bhp->StockAwal = $this->GetPostValue("StockAwal");
            $bhp->StockQty = $this->GetPostValue("StockQty");
            $bhp->CreatebyId = $this->userUid;
			if ($this->DoInsert($bhp)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.bhp','Add New Bhp -> Kode: '.$bhp->KdBhp.' - '.$bhp->NmBhp,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data Bhp: '%s' Dengan Kode: %s telah berhasil disimpan.", $bhp->NmBhp, $bhp->KdBhp));
				redirect_url("master.bhp");
			} else {
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $bhp->EntityCd));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		}
		// untuk kirim variable ke view
        $this->Set("bhp", $bhp);
	}

	private function DoInsert(Bhp $bhp) {
		if ($bhp->KdBhp == "") {
			$this->Set("error", "Kode belum diisi");
			return false;
		}
		if ($bhp->NmBhp == "") {
			$this->Set("error", "Kelompok Bhp belum diisi");
			return false;
		}
		if ($bhp->Insert() == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function edit($id = null) {
        $loader = null;
		$bhp = new Bhp();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$bhp->Id = $id;
			$bhp->EntityId = $this->userCompanyId;
            $bhp->KdBhp = $this->GetPostValue("KdBhp");
            $bhp->NmBhp = $this->GetPostValue("NmBhp");
            $bhp->HargaDasar = $this->GetPostValue("HargaDasar");
            $bhp->HargaJual = $this->GetPostValue("HargaJual");
            $bhp->Satuan = $this->GetPostValue("Satuan");
            $bhp->StockAwal = $this->GetPostValue("StockAwal");
            $bhp->StockQty = $this->GetPostValue("StockQty");
            $bhp->UpdatebyId = $this->userUid;
			if ($this->DoUpdate($bhp)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.bhp','Update Bhp -> Kode: '.$bhp->KdBhp.' - '.$bhp->NmBhp,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data Bhp: '%s' Dengan Kode: %s telah berhasil diupdate.", $bhp->NmBhp, $bhp->KdBhp));
				redirect_url("master.bhp");
			} else {
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $bhp->EntityCd));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		} else {
			if ($id == null) {
				$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan edit data !");
				redirect_url("master.bhp");
			}
			$bhp = $bhp->FindById($id);
			if ($bhp == null) {
				$this->persistence->SaveState("error", "Data Bhp yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
				redirect_url("master.bhp");
			}
		}
        //send to view
        $this->Set("bhp", $bhp);
	}

	private function DoUpdate(Bhp $bhp) {
		if ($bhp->KdBhp == "") {
			$this->Set("error", "Kode  belum diisi");
			return false;
		}
		if ($bhp->NmBhp == "") {
			$this->Set("error", "Nama Bhp belum diisi");
			return false;
		}
		if ($bhp->Update($bhp->Id) == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function delete($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan hapus data !");
			redirect_url("master.bhp");
		}
		$log = new UserAdmin();
		$bhp = new Bhp();
		$bhp = $bhp->FindById($id);
		if ($bhp == null) {
			$this->persistence->SaveState("error", "Data yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
			redirect_url("master.bhp");
		}

		if ($bhp->Delete($bhp->Id) == 1) {
			$log = $log->UserActivityWriter($this->userCabangId,'master.bhp','Delete Bhp -> Kode: '.$bhp->KdBhp.' - '.$bhp->NmBhp,'-','Success');
			$this->persistence->SaveState("info", sprintf("Data Bhp: '%s' Dengan Kode: %s telah berhasil dihapus.", $bhp->NmBhp, $bhp->KdBhp));
			redirect_url("master.bhp");
		} else {
			$this->persistence->SaveState("error", sprintf("Gagal menghapus data Bhp: '%s'. Message: %s", $bhp->NmBhp, $this->connector->GetErrorMessage()));
		}
		redirect_url("master.bhp");
	}

	public function optlistbyentity($EntityId = null, $sBhpId = null) {
		$buff = '<option value="">-- PILIH BAHAN --</option>';
		if ($EntityId == null) {
			print($buff);
			return;
		}
		$bhp = new Bhp();
		$bhps = $bhp->LoadByEntityId($EntityId);
		foreach ($bhps as $bhp) {
			if ($bhp->Id == $sBhpId) {
				$buff .= sprintf('<option value="%d" selected="selected">%s - %s</option>', $bhp->Id, $bhp->KdBhp, $bhp->NmBhp);
			} else {
				$buff .= sprintf('<option value="%d">%s - %s</option>', $bhp->Id, $bhp->KdBhp, $bhp->NmBhp);
			}
		}
		print($buff);
	}
}
