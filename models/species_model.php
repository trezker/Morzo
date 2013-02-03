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

	public function Get_specie($species_id, $location_id) {
		$db = Load_database();
		
		$rs = $db->Execute('
			select S.ID, S.Name, S.Max_population, LS.Population
			from Species S
			left join Location_species LS on LS.Species_ID = S.ID and LS.Location_ID = ?
			where S.ID = ?
			', array($location_id, $species_id));

		if(!$rs || $rs->RecordCount() == 0) {
			return false;
		}

		$r = $rs->fields;		
		return $r;
	}

	public function Save_species($name, $id, $max_population) {
		$db = Load_database();
		
		if($id == -1) {
			$args = array(	$name,
							$max_population
						);

			$rs = $db->Execute('
				insert into Species (Name, Max_population) values (?, ?)
				', $args);
		} else {
			$args = array(	$name,
							$max_population,
							$id
						);

			$rs = $db->Execute('
				update Species set Name = ?, Max_population = ? where ID = ?
				', $args);
		}

		if(!$rs) {
			echo $db->ErrorMsg();
			return false;
		}
		if($id == -1) {
			return $db->Insert_id();
		}
		return $id;
	}
	
	public function Get_location_species($location_id) {
		$db = Load_database();

		$args = array($location_id);

		$rs = $db->Execute('
			select S.ID, S.Name from Species S
			join Location_species LS on LS.Species_ID = S.ID
			where LS.Location_ID = ?
			', $args);

		if($rs == false) {
			return array();
		}
		
		return $rs->GetArray();
	}

	public function Save_location_species($species_id, $location_id, $on_location, $population) {
		$db = Load_database();

		if($on_location == "true"){
			$args = array($location_id, $species_id, $population, $population);
			$rs = $db->Execute('
				insert into Location_species(Location_ID, Species_ID, Population)
				values(?, ?, ?)
				on duplicate key update Population = ?
				', $args);
		}
		else {
			$args = array($location_id, $species_id);
			$rs = $db->Execute('
				delete from Location_species where Location_ID = ? and Species_ID = ?
				', $args);
		}
		if($rs == false) {
			echo "Error: " . $db->ErrorMsg();
			return false;
		}
		return $rs;
	}
	
	public function Start_hunt($actor_id, $hours, $species) {
		$db = Load_database();

		$db->StartTrans();

		$query = '
					insert into Hunt(Duration, Hours_left, Location_ID)
					select ?, ?, a.Location_ID from Actor a where a.ID = ?
				';
		$args = array($hours, $hours, $actor_id);
		$rs = $db->Execute($query, $args);
		if(!$rs) {
			$errormsg = $db->ErrorMsg();
			$db->FailTrans();
		} else {
			$hunt_id = $db->Insert_id();
		}
		
		if(!$db->HasFailedTrans()) {
			foreach($species as $species_id => $amount) {
				if($amount < 1)
					continue;
				$query = 'insert into Hunt_species(Hunt_ID, Species_ID, Amount) values(?, ?, ?)';
				$args = array($hunt_id, $species_id, $amount);
				$rs = $db->Execute($query, $args);
				if(!$rs) {
					$errormsg = $db->ErrorMsg();
					$db->FailTrans();
					break;
				}
			}
		}

		$failed = $db->HasFailedTrans();
		$db->CompleteTrans();
		
		if($failed) {
			return array('success' => false, 'data' => $errormsg);
		}
		
		return array('success' => true);
	}
	
	public function Get_hunts($actor_id) {
		$db = Load_database();
		/*TODO:
			Joined			Are you part of this hunt?
			Active			Is this hunt ongoing? Only needs one participant
			Description		Combine names of species being hunted.
		*/
		$query = "	select
						h.ID,
						hs.Name AS Stage_name,
						h.Prey_ID,
						h.Duration,
						h.Hours_left,
						false AS Joined,
						false AS Active,
						'TODO' AS Description
					from Hunt h
					join Huntstage hs on hs.ID = h.Stage_ID
					join Actor a on a.Location_ID = h.Location_ID
					where a.ID = ?
				";
		$args = array($actor_id);
		$rs = $db->Execute($query, $args);
		
		if(!$rs) {
			return false;
		}
		
		return $rs->GetArray();
	}
}
?>
