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
		
		//Todo: Save various type data here, like food/weapon etc...
		$query = 'insert into Category_food(Category_ID, Nutrition) values(?, ?)
					on duplicate key update Nutrition = ?';
		$array = array($category['id'], $category['food']['nutrition'], $category['food']['nutrition']);
		$rs = $db->Execute($query, $array);
	}
	
	function Get_food_properties($category_id) {
		$db = Load_database();
		$query = 'select Nutrition from Category_food where Category_ID = ? ';
		$array = array($category_id);
		$rs = $db->Execute($query, $array);
		if($rs != false && $rs->RecordCount() > 0) {
			return $rs->fields;
		}
		return array('Nutrition' => null);
	}
}
?>
