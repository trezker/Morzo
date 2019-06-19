<?php

require_once '../framework/model.php';

class Resource_model extends Model
{
	public function Get_resources()
	{
		$rs = $this->db->Execute('
			select ID, Name, Measure, Mass, Volume from Resource
			', array());

		if(!$rs)
		{
			return false;
		}
		
		$resources = array();
		foreach ($rs as $row) {
    		$resources[] = $row;
		}
		return $resources;
	}

	public function Get_resource($resource_id) {
		if($resource_id == -1) {
			return false;
		}
		
		$rs = $this->db->Execute('
			select ID, Name, Is_natural, Measure, Mass, Volume from Resource where ID = ?
			', array($resource_id));

		if(!$rs || $rs->RecordCount() == 0) {
			return false;
		}

		$rs2 = $this->db->Execute('
			select C.ID, C.Name, RC.Food_nutrition from Resource_category RC
			join Category C on C.ID = RC.Category_ID
			 where Resource_ID = ?
			', array($resource_id));
		
		$r = array('resource' => $rs->fields, 'categories' => $rs2->GetArray());
		
		return $r;
	}

	public function Save_resource($resource) {
		if($resource['is_natural'] == 'true')
			$resource['is_natural'] = 1;
		else
			$resource['is_natural'] = 0;

		$resource_id = $resource['id'];
		
		$this->db->StartTrans();

		if($resource_id == -1) {
			$args = array(	$resource['name'], 
							$resource['is_natural'],
							$resource['measure'],
							$resource['mass'],
							$resource['volume']
						);

			$rs = $this->db->Execute('
				insert into Resource (Name, Is_natural, Measure, Mass, Volume) values (?, ?, ?, ?, ?)
				', $args);

			$resource_id = $this->db->Insert_ID();
		} else {
			$args = array(	$resource['name'], 
							$resource['is_natural'],
							$resource['measure'],
							$resource['mass'],
							$resource['volume'],
							$resource_id
						);

			$rs = $this->db->Execute('
				update Resource set Name = ?, Is_natural = ?, Measure = ?, Mass = ?, Volume = ? where ID = ?
				', $args);
		}

		if(isset($resource['categories'])) {
			foreach($resource['categories'] as $category) {
				if(isset($category['state']) && $category['state'] == 'remove')
					$this->Remove_resource_category($resource_id, $category['id']);
				else
					if(!$this->Add_resource_category($resource_id, $category))
						$this->db->FailTrans();
			}
		}

		$success = !$this->db->HasFailedTrans();
		$this->db->CompleteTrans();
		if($success != true)
			return false;

		return true;
	}
	
	public function Get_measures() {
		$rs = $this->db->Execute('
			select ID, Name from Measure
			', array());

		if(!$rs) {
			return false;
		}
		
		return $rs->GetArray();
	}

	public function Add_resource_category($resource_id, $category) {
		if(!isset($category['nutrition']) || !is_numeric($category['nutrition']))
			$category['nutrition'] = NULL;
		$query = 'insert into Resource_category(Resource_id, Category_ID, Food_nutrition)
		values(?, ?, ?) on duplicate key update Food_nutrition = ?';
		$array = array($resource_id, $category['id'], $category['nutrition'], $category['nutrition']);
		$rs = $this->db->Execute($query, $array);
		return $rs;
	}

	public function Remove_resource_category($resource_id, $category_id) {
		$query = 'delete from Resource_category where Resource_ID = ? and Category_ID = ?';
		$array = array($resource_id, $category_id);
		$rs = $this->db->Execute($query, $array);
		return $rs;
	}
}
?>
