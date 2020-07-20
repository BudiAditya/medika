<?php
class SbuController extends AppController {
	private $userCompanyId;
	private $userCabangId;

	protected function Initialize() {
		require_once(MODEL . "master/sbu.php");
		$this->userCompanyId = $this->persistence->LoadState("entity_id");
		require_once(MODEL . "master/user_admin.php");
		$this->userCabangId = $this->persistence->LoadState("cabang_id");

		//TO-DO: Apakah controller ini hanya boleh diakses oleh Corporate Level ? Bila Diakses non-CORP datanya cuma ada 1 LOLZ
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();

		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 50);
		//$settings["columns"][] = array("name" => "b.entity_cd", "display" => "Kode", "width" => 50);
		//$settings["columns"][] = array("name" => "b.company_name", "display" => "Nama Perusahaan", "width" => 150);
		$settings["columns"][] = array("name" => "a.sbu_name", "display" => "Kode", "width" => 100);
        $settings["columns"][] = array("name" => "a.sbu_descs", "display" => "Nama Bisnis Unit", "width" => 250);
		$settings["columns"][] = array("name" => "a.pic", "display" => "PIC", "width" => 150);

		$settings["filters"][] = array("name" => "a.sbu_name", "display" => "Nama Bisnis Unit");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Bisnis Unit Perusahaan";

			//sort and method
            $settings["def_filter"] = 0;
            $settings["def_order"] = 0;
            $settings["singleSelect"] = true;
		} else {
			//$settings["from"] = "m_sbu AS a JOIN sys_company AS b On a.entity_id = b.entity_id";
            $settings["from"] = "m_sbu AS a";
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}
}
