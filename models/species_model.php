<?php

require_once '../models/database.php';
require_once '../models/model.php';

class Species_model extends Model {
	public function Get_species() {
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
						IF(a.Hunt_ID = h.ID,true,false) AS Joined,
						(	SELECT COUNT(1) FROM Actor 
							where Hunt_ID=h.ID
						) AS Participants,
						(	SELECT GROUP_CONCAT(Name SEPARATOR ', ') 
							FROM Hunt_species hsp
							JOIN Species s on s.ID = hsp.Species_ID
							WHERE hsp.Hunt_ID = h.ID
						) AS Description
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
	
	public function Join_hunt($actor_id, $hunt_id) {
		$db = Load_database();

		//Check if allowed to join
		$query = " select H.ID from Hunt H
					join Actor A on A.Location_ID = H.Location_ID
					where H.ID = ? and A.ID = ?
				";
		$args = array($hunt_id, $actor_id);
		$rs = $db->Execute($query, $args);
		if(!$rs || $rs->RecordCount() < 1) {
			$errormsg = $db->ErrorMsg();
			return array('success' => false, 'data' => $errormsg);
		}

		//Make sure actor leaves any participation in a project
		$this->Load_model('Project_model');
		$left = $this->Project_model->Leave_project($actor_id);
		if($left != true) {
			return array('success' => false, 'data' => 'Failed to leave project');
		}
		
		//Join
		$query = "update Actor set Hunt_ID = ? where ID = ?";
		$args = array($hunt_id, $actor_id);
		$rs = $db->Execute($query, $args);
		if(!$rs) {
			$errormsg = $db->ErrorMsg();
			return array('success' => false, 'data' => $errormsg);
		}
		
		return array('success' => true);
	}

	public function Leave_hunt($actor_id) {
		$db = Load_database();

		$query = "update Actor set Hunt_ID = NULL where ID = ?";
		$args = array($actor_id);
		$rs = $db->Execute($query, $args);
		if(!$rs) {
			$errormsg = $db->ErrorMsg();
			return array('success' => false, 'data' => $errormsg);
		}
		
		return array('success' => true);
	}
	
	public function Update_hunts($time) {
		$db = Load_database();
		$errormsg = "";

		$query = "
				select
					ID,
					Stage_ID,
					Prey_ID,
					Hours_left,
					Location_ID,
					UpdateTick
				from Hunt
				where UpdateTick < ?
				";
		$args = array($time);
		$rs = $db->Execute($query, $args);
		if(!$rs) {
			$errormsg = $db->ErrorMsg();
			return array('success' => false, 'data' => $errormsg);
		}
		
		$db->StartTrans();

		foreach($rs->GetArray() as $hunt) {
			echo '<pre>';
			var_dump($hunt);
			echo '</pre>';
			$hunt['UpdateTick'] += 1;
			$hunt['Hours_left'] -= 1;
			
			//Update hunt stage, create corpse...
			if($hunt['Stage_ID'] == 1) { //Searching
				//10% chance we run into animal directly
				$animal = rand (0, 9);
				//50% chance to find tracks
				$tracks = rand (0, 1);
				if($animal == 0) {
					$hunt['Stage_ID'] = 3;
				} else if($tracks == 0) {
					$hunt['Stage_ID'] = 2;
				}
				//equal split which species we find
				if($hunt['Stage_ID'] > 1) {
					$query = "
							select ID from Hunt_species
							where Hunt_ID = ? and Amount > 0
							";
					$args = array($hunt['ID']);
					$rs = $db->Execute($query, $args);
					if(!$rs) {
						$errormsg = $db->ErrorMsg();
						break;
					}
					$species = $rs->GetArray();
					$num_species = count($species);
					$i = rand (0, $num_species-1);
					$hunt['Prey_ID'] = $species[$i]['ID'];
				}
			}
			if($hunt['Stage_ID'] == 2) { //Tracking
				//50% chance to catch up to the animal
			}
			if($hunt['Stage_ID'] == 3) { //Chasing
				//50% chance to kill
			}
			
			if($hunt['Hours_left'] > 0) {
				$query = "
						update Hunt set
							UpdateTick = ?,
							Hours_left = ?,
							Stage_ID = ?,
							Prey_ID = ?
						where ID = ?
						";
				$args = array(
							$hunt['UpdateTick'],
							$hunt['Hours_left'],
							$hunt['Prey_ID'],
							$hunt['Stage_ID'],
							$hunt['ID']);
				$rs = $db->Execute($query, $args);
				if($db->Affected_rows() == 0) {
					$errormsg = "No affected rows from update";
					$db->FailTrans();
					break;
				}
				if(!$rs) {
					$errormsg = $db->ErrorMsg();
					break;
				}
				if($db->HasFailedTrans()) {
					$errormsg = $db->ErrorMsg();
					break;
				}
			} else {
				$this->Load_model('Event_model');
				$this->Event_model->Save_hunt_event("{LNG_Hunt_ended}", $hunt['ID']);
				if($db->HasFailedTrans()) {
					$errormsg = "Saving event failed";
					break;
				}
				$query = "
						delete from Hunt where ID = ?
						";
				$args = array($hunt['ID']);
				$rs = $db->Execute($query, $args);
				if(!$rs) {
					$errormsg = $db->ErrorMsg();
					break;
				}
			}
		}

		$success = !$db->HasFailedTrans();
		$db->CompleteTrans();
		return array('success' => $success, 'data' => $errormsg);
	}
}
?>
