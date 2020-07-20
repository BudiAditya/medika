<?php
class GajiController extends AppController {
	private $userCompanyId;
	private $userLevel;
	private $userCabangId;
	private $userUid;

	protected function Initialize() {
		require_once(MODEL . "master/karyawan.php");
        require_once(MODEL . "master/gaji.php");
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
        $settings["columns"][] = array("name" => "a.nik", "display" => "NIK", "width" => 60);
        $settings["columns"][] = array("name" => "a.nama", "display" => "Nama Karyawan", "width" => 150);
        //$settings["columns"][] = array("name" => "a.nm_panggilan", "display" => "Nama Panggilan", "width" => 100);
        $settings["columns"][] = array("name" => "a.alamat", "display" => "Alamat", "width" => 400);
        $settings["columns"][] = array("name" => "c.dept_cd", "display" => "Bagian", "width" => 50);
        $settings["columns"][] = array("name" => "a.jabatan", "display" => "Jabatan", "width" => 50);
        $settings["columns"][] = array("name" => "a.handphone", "display" => "Handphone", "width" => 100);
        $settings["columns"][] = array("name" => "if(a.jkelamin = 'L','Laki-laki',if(a.jkelamin='P','Perempuan','-'))", "display" => "Gender", "width" => 100);

        $settings["filters"][] = array("name" => "a.nik", "display" => "Nik");
        $settings["filters"][] = array("name" => "a.nama", "display" => "Nama Karyawan");

        if (!$router->IsAjaxRequest) {
            $acl = AclManager::GetInstance();
            $settings["title"] = "Data Gaji Karyawan";

            if ($acl->CheckUserAccess("gaji", "edit", "master")) {
                $settings["actions"][] = array("Text" => "Edit", "Url" => "master.gaji/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
                    "Error" => "Mohon memilih data karyawan terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu karyawan",
                    "Confirm" => "");
            }

            $settings["def_filter"] = 0;
            $settings["def_order"] = 1;
            $settings["singleSelect"] = true;
        } else {
            $settings["from"] =
                "m_karyawan AS a JOIN sys_company AS b ON a.entity_id = b.entity_id LEFT JOIN sys_dept As c on a.dept_id = c.id";
            if ($this->userLevel > 3) {
                $settings["where"] = "a.is_deleted = 0";
            } else {
                $settings["where"] = "a.is_deleted = 0 AND a.entity_id = " . $this->userCompanyId;
            }
        }

        $dispatcher = Dispatcher::CreateInstance();
        $dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
    }

	public function edit($id = null) {
		require_once(MODEL . "master/department.php");
        require_once(MODEL . "master/dokter.php");
		$loader = null;
		$log = new UserAdmin();
		$gaji = new Gaji();
		$karyawan = new Karyawan();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$gaji->Id = $id;
            $gaji->KdDokter = $this->GetPostValue("KdDokter");
            $gaji->Gapok = $this->GetPostValue("Gapok");
            $gaji->TjJabatan = $this->GetPostValue("TjJabatan");
            $gaji->TjProfesi = $this->GetPostValue("TjProfesi");
            $gaji->BpjsKes = $this->GetPostValue("BpjsKes");
            $gaji->BpjsTk = $this->GetPostValue("BpjsTk");
            $gaji->IsFeeProfit = $this->GetPostValue("IsFeeProfit");
            $gaji->IsFeeJasmed = $this->GetPostValue("IsFeeJasmed");
            $gaji->IsFeeTikhus = $this->GetPostValue("IsFeeTikhus");
            $gaji->UpdatebyId = $this->userUid;
			if ($gaji->Update($id)) {
			    $karyawan = $karyawan->FindById($id);
				$log = $log->UserActivityWriter($this->userCabangId,'master.karyawan','Update Gaji Karyawan -> NIK: '.$gaji->Nik.' - '.$karyawan->Nama,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data Gaji Karyawan: %s NIK: %s telah berhasil diupdate.", $karyawan->Nama, $karyawan->Nik));
				redirect_url("master.gaji");
			} else {
				$log = $log->UserActivityWriter($this->userCabangId,'master.karyawan','Update Gaji -> NIK: '.$karyawan->Nik.' - '.$karyawan->Nama,'-','Failed');
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Nik: '%s' telah ada pada database !", $karyawan->Nik));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		} else {
			if ($id == null) {
				$this->persistence->SaveState("error", "Anda harus memilih data karyawan sebelum melakukan edit data !");
				redirect_url("master.gaji");
			}
			$karyawan = $karyawan->FindById($id);
			if ($karyawan == null) {
				$this->persistence->SaveState("error", "Data Nama yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
				redirect_url("master.gaji");
			}
			$gaji = $gaji->FindByNik($this->userCompanyId,$karyawan->Nik);
		}

		// load data for combo box
		$loader = new Department();
        $depts = $loader->LoadAll();
        $loader = new Dokter();
        $dokters = $loader->LoadByEntityId($this->userCompanyId);
        // untuk kirim variable ke view
        $this->Set("karyawan", $karyawan);
        $this->Set("gaji", $gaji);
        $this->Set("depts", $depts);
        $this->Set("dokters", $dokters);
	}
}
