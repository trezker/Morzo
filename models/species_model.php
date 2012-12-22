<?php

require_once '../models/database.php';

class Species_model{
	public function Get_species(){
		$db = Load_database();
		
		$rs = $db->Execute('
			select ID, Name from Species
			', array());

		if(!$rs){
			return false;
		}
		
		$species = $rs->GetArray();
		return $species;
	}

	public function Get_specie($species_id) {
		$db = Load_database();
		
		$rs = $db->Execute('
			select ID, Name from Species where ID = ?
			', array($species_id));

		if(!$rs || $rs->RecordCount() == 0) {
			return false;
		}

		$r = $rs->fields;		
		return $r;
	}

	public function Save_species($species) {
		$db = Load_database();
		
		if($species['id'] == -1) {
			$args = array(	$species['name'],
							$species['max_population']
						);

			$rs = $db->Execute('
				insert into Species (Name, Max_population) values (?, ?)
				', $args);
		} else {
			$args = array(	$species['name'], 
							$species['max_population'],
							$species['id']
						);

			$rs = $db->Execute('
				update Species set Name = ?, Max_population = ? where ID = ?
				', $args);
		}

		if(!$rs) {
			echo $db->ErrorMsg();
			return false;
		}

		return true;
	}
	
	public function Get_location_species($location_id) {
		$db = Load_database();

		$args = array($location_id);

		$rs = $db->Execute('
			select ID, Name from Species where Location_species = ?
			', $args);
		
		if($rs == false) {
			return array();
		}
		
		return $rs->GetArray();
	}
}
?>
