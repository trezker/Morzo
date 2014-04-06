<?php

require_once '../models/model.php';

class Category_model extends Model
{
	public function Get_categories() {
		$query = 'select ID, Name from Category';
		$array = array();
		
		$rs = $this->db->Execute($query, $array);
		if(!$rs) {
			return false;
		}
		
		$categories = array();
		foreach ($rs as $row) {
    		$categories[] = $row;
		}
		return $categories;
	}

	public function Get_tool_categories() {
		$query = 'select ID, Name from Category where Is_tool = 1';
		$array = array();
		
		$rs = $this->db->Execute($query, $array);
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
		$query = 'select ID, Name, Is_tool from Category where ID = ?';
		$array = array($id);
		
		$rs = $this->db->Execute($query, $array);

		if(!$rs || $rs->RecordCount() == 0) {
			return false;
		}
		
		return $rs->fields;
	}

	public function Save_category($category) {
		if($category['is_tool'] == 'true')
			$category['is_tool'] = 1;
		else
			$category['is_tool'] = 0;

		$this->db->StartTrans();
		if($category['id'] == -1) {
			$query = 'insert into Category(Name, Is_tool) values(?, ?)';
			$array = array($category['name'], $category['is_tool']);
			$rs = $this->db->Execute($query, $array);
			$category['id'] = $this->db->Insert_ID();
		} else {
			$query = 'update Category set Name = ?, Is_tool = ? where ID = ? ';
			$array = array($category['name'], $category['is_tool'], $category['id']);
			$rs = $this->db->Execute($query, $array);
		}
		
		$success = true;
		if($this->db->HasFailedTrans()) {
			$success = false;
		}
		$this->db->CompleteTrans();
		return $success;
	}
}
?>
