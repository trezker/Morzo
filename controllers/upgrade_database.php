<?php
require_once "../framework/controller.php";
require_once '../models/database.php';

class Upgrade_database extends Controller
{
	public function Index()
	{
		
		$db = Load_database();

		$r = $db->Execute('
			select count(*) as C from Actor where Inventory_ID is NULL', array());
			
		$n = $r->fields['C'];

		for($i = 0; $i<$n; $i++) {
			$r = $db->Execute('
				insert into Inventory values()', array());
			
			if($r) {
				$project_inventory_id = $db->Insert_id();
				$r = $db->Execute('
					update Actor set Inventory_ID = ?
					where Inventory_ID is NULL
					limit 1'
					, array($project_inventory_id));
			} else {
				echo "Fail";
			}
		}

		$r = $db->Execute('
			select count(*) as C from Location where Inventory_ID is NULL', array());
			
		$n = $r->fields['C'];

		for($i = 0; $i<$n; $i++) {
			$r = $db->Execute('
				insert into Inventory values()', array());
			
			if($r) {
				$project_inventory_id = $db->Insert_id();
				$r = $db->Execute('
					update Location set Inventory_ID = ?
					where Inventory_ID is NULL
					limit 1'
					, array($project_inventory_id));
			} else {
				echo "Fail";
			}
		}
	}
}
