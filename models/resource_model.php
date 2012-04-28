<?php

require_once '../models/database.php';

class Resource_model
{
	public function Get_resources()
	{
		$db = Load_database();
		
		$rs = $db->Execute('
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
		
		$db = Load_database();
		
		$rs = $db->Execute('
			select ID, Name, Is_natural, Measure, Mass, Volume from Resource where ID = ?
			', array($resource_id));

		if(!$rs || $rs->RecordCount() == 0) {
			return false;
		}
		
		return $rs->fields;
	}

	public function Save_resource($resource) {
		$db = Load_database();
		
		if($resource['is_natural'] == 'true')
			$resource['is_natural'] = 1;
		else
			$resource['is_natural'] = 0;

		if($resource['id'] == -1) {
			$args = array(	$resource['name'], 
							$resource['is_natural'],
							$resource['measure'],
							$resource['mass'],
							$resource['volume']
						);

			$rs = $db->Execute('
				insert into Resource (Name, Is_natural, Measure, Mass, Volume) values (?, ?, ?, ?, ?)
				', $args);
		} else {
			$args = array(	$resource['name'], 
							$resource['is_natural'],
							$resource['measure'],
							$resource['mass'],
							$resource['volume'],
							$resource['id']
						);

			$rs = $db->Execute('
				update Resource set Name = ?, Is_natural = ?, Measure = ?, Mass = ?, Volume = ? where ID = ?
				', $args);
		}

		if(!$rs) {
			echo $db->ErrorMsg();
			return false;
		}

		return true;
	}
	
	public function Get_measures() {
		$db = Load_database();
		
		$rs = $db->Execute('
			select ID, Name from Measure
			', array());

		if(!$rs) {
			return false;
		}
		
		return $rs->GetArray();
	}
}
?>
