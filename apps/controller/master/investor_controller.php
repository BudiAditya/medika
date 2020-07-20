<?php
class InvestorController extends AppController {
	private $userCompanyId;
	private $userLevel;
	private $userCabangId;
	private $userUid;

	protected function Initialize() {
		require_once(MODEL . "master/investor.php");
		require_once(MODEL . "master/user_admin.php");
		$this->userCompanyId = $this->persistence->LoadState("entity_id");
		$this->userCabangId = $this->persistence->LoadState("cabang_id");
		$this->userLevel = $this->persistence->LoadState("user_lvl");
        $this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();

		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 40);
		$settings["columns"][] = array("name" => "a.nama", "display" => "Nama", "width" => 200);
        $settings["columns"][] = array("name" => "a.nik_ktp", "display" => "No. KTP", "width" => 100);
        $settings["columns"][] = array("name" => "a.no_kk", "display" => "No. KK", "width" => 100);
        $settings["columns"][] = array("name" => "a.alamat", "display" => "Alamat", "width" => 400);

		$settings["filters"][] = array("name" => "a.nama", "display" => "Nama Investor");
        $settings["filters"][] = array("name" => "a.nik_ktp", "display" => "No. KTP");
        $settings["filters"][] = array("name" => "a.nik_kk", "display" => "No. KK");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Daftar Investor";

			if ($acl->CheckUserAccess("investor", "add", "master")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.investor/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("investor", "edit", "master")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.investor/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
											   "Error" => "Mohon memilih data investor terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu investor",
											   "Info" => "Apakah anda yakin mau merubah data investor yang dipilih ?");
			}
			/*
			if ($acl->CheckUserAccess("investor", "view", "master")) {
				$settings["actions"][] = array("Text" => "View", "Url" => "master.investor/view/%s", "Class" => "bt_view", "ReqId" => 1,
						"Error" => "Mohon memilih data investor terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu investor");
			}
			*/
			if ($acl->CheckUserAccess("investor", "delete", "master")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.investor/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
											   "Error" => "Mohon memilih data investor terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu investor",
											   "Info" => "Apakah anda yakin mau menghapus data investor yang dipilih ?");
			}

			$settings["def_filter"] = 0;
			$settings["def_order"] = 1;
			$settings["singleSelect"] = true;
		} else {
			$settings["from"] = "m_investor AS a";
			if ($this->userLevel > 3) {
				$settings["where"] = "a.is_deleted = 0 and a.is_aktif = 1";
			} else {
				$settings["where"] = "a.is_deleted = 0 and a.is_aktif = 1 AND a.entity_id = " . $this->userCompanyId;
			}
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

	public function add() {
		$loader = null;
		$log = new UserAdmin();
		$investor = new Investor();
		$fpath = null;
		$ftmp = null;
		$fname = null;
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$investor->Nama = $this->GetPostValue("Nama");
            $investor->NikKtp = $this->GetPostValue("NikKtp");
            $investor->NoKK = $this->GetPostValue("NoKK");
            $investor->Alamat = $this->GetPostValue("Alamat");
			$investor->CreatebyId = $this->userUid;
			if ($this->DoInsert($investor)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.investor','Add New Investor -> NIK: '.$investor->NikKtp.' - '.$investor->Nama,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data Nama: '%s' Dengan Nik: %s telah berhasil disimpan.", $investor->Nama, $investor->NikKtp));
				redirect_url("master.investor");
			} else {
				$log = $log->UserActivityWriter($this->userCabangId,'master.investor','Add New Investor -> NIK: '.$investor->NikKtp.' - '.$investor->Nama,'-','Failed');
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Nik: '%s' telah ada pada database !", $investor->NikKtp));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		}

		// load data for combo box
		// untuk kirim variable ke view
		$this->Set("investor", $investor);
	}

	private function DoInsert(Investor $investor) {

		if (strlen($investor->NikKtp) < 16) {
			$this->Set("error", "Nomor KTP tidak valid (Minimal 16 digit)");
			return false;
		}

        if (strlen($investor->NoKK) < 16) {
            $this->Set("error", "Nomor KK tidak valid (Minimal 16 digit)");
            return false;
        }

		if ($investor->Nama == "") {
			$this->Set("error", "Nama investor masih kosong");
			return false;
		}

		if ($investor->Insert() == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function edit($id = null) {
		$loader = null;
		$log = new UserAdmin();
		$investor = new Investor();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$investor->Id = $id;
            $investor->Nama = $this->GetPostValue("Nama");
            $investor->NikKtp = $this->GetPostValue("NikKtp");
            $investor->NoKK = $this->GetPostValue("NoKK");
            $investor->Alamat = $this->GetPostValue("Alamat");
            $investor->UpdatebyId = $this->userUid;
			if ($this->DoUpdate($investor)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.investor','Update Investor -> NIK: '.$investor->NikKtp.' - '.$investor->Nama,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data Nama: '%s' Dengan Nik: %s telah berhasil diupdate.", $investor->Nama, $investor->NikKtp));
				redirect_url("master.investor");
			} else {
				$log = $log->UserActivityWriter($this->userCabangId,'master.investor','Update Investor -> NIK: '.$investor->NikKtp.' - '.$investor->Nama,'-','Failed');
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Nik: '%s' telah ada pada database !", $investor->NikKtp));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		} else {
			if ($id == null) {
				$this->persistence->SaveState("error", "Anda harus memilih data investor sebelum melakukan edit data !");
				redirect_url("master.investor");
			}
			$investor = $investor->FindById($id);
			if ($investor == null) {
				$this->persistence->SaveState("error", "Data Nama yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
				redirect_url("master.investor");
			}
		}

		// load data for combo box
		// untuk kirim variable ke view
        $this->Set("investor", $investor);
	}

	public function view($id = null) {

		$loader = null;
		$log = new UserAdmin();
		$investor = new Investor();

		if ($id == null) {
			$this->persistence->SaveState("error", "Anda harus memilih data investor untuk direview !");
			redirect_url("master.investor");
		}
		$investor = $investor->FindById($id);
		if ($investor == null) {
			$this->persistence->SaveState("error", "Data Nama yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
			redirect_url("master.investor");
		}
        // untuk kirim variable ke view
        $this->Set("investor", $investor);
	}

	private function DoUpdate(Investor $investor) {
        if (strlen($investor->NikKtp) < 16) {
            $this->Set("error", "Nomor KTP tidak valid (Minimal 16 digit)");
            return false;
        }

        if (strlen($investor->NoKK) < 16) {
            $this->Set("error", "Nomor KK tidak valid (Minimal 16 digit)");
            return false;
        }

		if ($investor->Nama == "") {
			$this->Set("error", "Nama investor masih kosong");
			return false;
		}

		if ($investor->Update($investor->Id) == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function delete($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Anda harus memilih data investor sebelum melakukan hapus data !");
			redirect_url("master.investor");
		}
		$log = new UserAdmin();
		$investor = new Investor();
		$investor = $investor->FindById($id);
		if ($investor == null) {
			$this->persistence->SaveState("error", "Data investor yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
			redirect_url("master.investor");
		}

		if ($investor->Delete($investor->Id) == 1) {
			$log = $log->UserActivityWriter($this->userCabangId,'master.investor','Delete Investor -> NIK: '.$investor->Nik.' - '.$investor->Nama,'-','Success');
			$this->persistence->SaveState("info", sprintf("Data Nama: '%s' Dengan Nik: %s telah berhasil dihapus.", $investor->Nama, $investor->Nik));
			redirect_url("master.investor");
		} else {
			$log = $log->UserActivityWriter($this->userCabangId,'master.investor','Delete Investor -> NIK: '.$investor->Nik.' - '.$investor->Nama,'-','Success');
			$this->persistence->SaveState("error", sprintf("Gagal menghapus data investor: '%s'. Message: %s", $investor->Nama, $this->connector->GetErrorMessage()));
		}
		redirect_url("master.investor");
	}

    public function AutoKode(){
        $kode = new Investor();
        $kode = $kode->GetAutoKode($this->userCompanyId);
        print $kode;
    }
}
