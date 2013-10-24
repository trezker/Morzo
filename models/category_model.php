<?php

require_once '../models/database.php';

class Category_model
{
	public function Get_categories() {
		$db = Load_database();
		
		$query = 'select ID, Name from Category';
		$array = array();
		
		$rs = $db->Execute($query, $array);
		if(!$rs) {
			return false;
		}
		
		$categories = array();
		foreach ($rs as $row) {
    		$categories[] = $row;
		}
		return $categories;
	}

	public function Get_category($id) {
		if($id == -1) {
			return false;
		}
		$db = Load_database();
		
		$query = 'select ID, Name from Category where ID = ?';
		$array = array($id);
		
		$rs = $db->Execute($query, $array);

		if(!$rs || $rs->RecordCount() == 0) {
			return false;
		}
		
		return $rs->fields;
	}

	public function Save_category($category) {
		$db = Load_database();
		
		$db->StartTrans();
		if($category['id'] == -1) {
			$query = 'insert into Category(Name) values(?)';
			$array = array($category['name']);
			$rs = $db->Execute($query, $array);
			$category['id'] = $db->Insert_ID();
		} else {
			$query = 'update Category set Name = ? where ID = ? ';
			$array = array($category['name'], $category['id']);
			$rs = $db->Execute($query, $array);
		}
		
		if($category['is_container'] == "true") {
			if(!is_numeric($category['container']['mass_limit']))
				$category['container']['mass_limit'] = NULL;
			if(!is_numeric($category['container']['volume_limit']))
				$category['container']['volume_limit'] = NULL;
			$query = 'insert into Category_container(Category_ID, Mass_limit, Volume_limit) values(?, ?, ?)
						on duplicate key update Mass_limit = ?, Volume_limit = ?';
			$array = array(
							$category['id'], 
							$category['container']['mass_limit'], 
							$category['container']['volume_limit'], 
							$category['container']['mass_limit'], 
							$category['container']['volume_limit']);
			$rs = $db->Execute($query, $array);
		} else {
			$query = 'delete from Category_container where Category_ID = ?';
			$array = array($category['id']);
			$rs = $db->Execute($query, $array);
		}
		$success = true;
		if($db->HasFailedTrans()) {
			$success = false;
		}
		$db->CompleteTrans();
		return $success;
	}
	
	function Get_container_properties($category_id) {
		$db = Load_database();
		$query = "select 'checked' as is_container_checked, Mass_limit, Volume_limit from Category_container where Category_ID = ? ";
		$array = array($category_id);
		$rs = $db->Execute($query, $array);
		if($rs != false && $rs->RecordCount() > 0) {
			return $rs->fields;
		}
		return array('is_container_checked' => '', 'Mass_limit' => null, 'Volume_limit' => null);
	}
}
?>
