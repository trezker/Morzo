<?php

require_once '../framework/model.php';

class Species_model extends Model {
	public function Get_species() {
		$rs = $this->db->Execute('
			select ID, Name from Species
			', array());

		if(!$rs){
			return false;
		}
		
		$species = $rs->GetArray();
		return $species;
	}

	public function Get_specie($species_id, $location_id) {
		$rs = $this->db->Execute('
			select S.ID, S.Name, S.Max_population, LS.Population, LS.Actor_spawn, S.Corpse_product_ID
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
		$this->Load_model("Product_model");
		if($id == -1) {
			$product_spec = array(
								'name' => "Dead " . $name, 
								'mass' => 1,
								'volume' => 1,
								'rot_rate' => 1,
								'id' => -1
							);
			if($this->Product_model->Save_product($product_spec)) {
				$corpse_product_id = $this->db->Insert_id();
			} else {
				return false;
			}

			$args = array(	$name,
							$max_population,
							$corpse_product_id
						);

			$rs = $this->db->Execute('
				insert into Species (Name, Max_population, Corpse_product_ID) values (?, ?, ?)
				', $args);
		} else {
			$args = array(	"Dead " . $name,
							$id
						);

			$rs = $this->db->Execute('
				update Product set Name = ?
				where ID in (select Corpse_product_ID from Species where ID = ?)
				', $args);
				
			if(!$rs) {
				echo $this->db->ErrorMsg();
				return false;
			}

			$args = array(	$name,
							$max_population,
							$id
						);

			$rs = $this->db->Execute('
				update Species set Name = ?, Max_population = ? where ID = ?
				', $args);
		}

		if(!$rs) {
			echo $this->db->ErrorMsg();
			return false;
		}
		if($id == -1) {
			return $this->db->Insert_id();
		}
		return $id;
	}
	
	public function Get_location_species($location_id) {
		$args = array($location_id);

		$rs = $this->db->Execute('
			select S.ID, S.Name from Species S
			join Location_species LS on LS.Species_ID = S.ID
			where LS.Location_ID = ?
			', $args);

		if($rs == false) {
			return array();
		}
		
		return $rs->GetArray();
	}

	public function Save_location_species($species_id, $location_id, $on_location, $population, $actor_spawn) {
		if($on_location == "true"){
			if(!filter_var($population, FILTER_VALIDATE_INT))
				$population = 0;
			if(!filter_var($actor_spawn, FILTER_VALIDATE_INT))
				$actor_spawn = 0;
			$args = array($location_id, $species_id, $population, $actor_spawn, $population, $actor_spawn);
			$rs = $this->db->Execute('
				insert into Location_species(Location_ID, Species_ID, Population, Actor_spawn)
				values(?, ?, ?, ?)
				on duplicate key update Population = ?, Actor_spawn = ?
				', $args);
		}
		else {
			$args = array($location_id, $species_id);
			$rs = $this->db->Execute('
				delete from Location_species where Location_ID = ? and Species_ID = ?
				', $args);
		}
		if($rs == false) {
			echo "Error: " . $this->db->ErrorMsg();
			return false;
		}
		return $rs;
	}
	
	public function Start_hunt($actor_id, $hours, $species) {
		$this->Load_model('Actor_model');
		if($this->Actor_model->Actor_is_alive($actor_id) == false)
			return false;

		$this->db->StartTrans();

		$query = '
					insert into Hunt(Duration, Hours_left, Location_ID)
					select ?, ?, a.Location_ID from Actor a where a.ID = ?
				';
		$args = array($hours, $hours, $actor_id);
		$rs = $this->db->Execute($query, $args);
		if(!$rs) {
			$errormsg = $this->db->ErrorMsg();
			$this->db->FailTrans();
		} else {
			$hunt_id = $this->db->Insert_id();
		}
		
		if(!$this->db->HasFailedTrans()) {
			foreach($species as $species_id => $amount) {
				if($amount < 1)
					continue;
				$query = 'insert into Hunt_species(Hunt_ID, Species_ID, Amount) values(?, ?, ?)';
				$args = array($hunt_id, $species_id, $amount);
				$rs = $this->db->Execute($query, $args);
				if(!$rs) {
					$errormsg = $this->db->ErrorMsg();
					$this->db->FailTrans();
					break;
				}
			}
		}

		$failed = $this->db->HasFailedTrans();
		$this->db->CompleteTrans();
		
		if($failed) {
			return array('success' => false, 'data' => $errormsg);
		}
		
		return array('success' => true);
	}
	
	public function Get_hunts($actor_id) {
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
		$rs = $this->db->Execute($query, $args);
		
		if(!$rs) {
			return false;
		}
		
		return $rs->GetArray();
	}

	public function Get_hunt($actor_id, $hunt_id) {
		$query = "
					select
						h.ID,
						hs.Name AS Stage_name,
						h.Prey_ID,
						h.Duration,
						h.Hours_left,
						s.Name as Prey_name
					from Hunt h
					join Huntstage hs on hs.ID = h.Stage_ID
					join Actor a on a.Location_ID = h.Location_ID
					left join Species s on s.ID = h.Prey_ID
					where a.ID = ? and h.ID = ?
				";
		$args = array($actor_id, $hunt_id);
		$rs = $this->db->Execute($query, $args);
		
		if(!$rs) {
			return false;
		}
		
		$r = array();
		$r['info'] = $rs->fields;
		
		return $r;
	}
	
	public function Join_hunt($actor_id, $hunt_id) {
		$this->Load_model('Actor_model');
		if($this->Actor_model->Actor_is_alive($actor_id) == false)
			return false;

		//Check if allowed to join
		$query = " select H.ID from Hunt H
					join Actor A on A.Location_ID = H.Location_ID
					where H.ID = ? and A.ID = ?
				";
		$args = array($hunt_id, $actor_id);
		$rs = $this->db->Execute($query, $args);
		if(!$rs || $rs->RecordCount() < 1) {
			$errormsg = $this->db->ErrorMsg();
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
		$rs = $this->db->Execute($query, $args);
		if(!$rs) {
			$errormsg = $this->db->ErrorMsg();
			return array('success' => false, 'data' => $errormsg);
		}
		
		return array('success' => true);
	}

	public function Leave_hunt($actor_id) {
		$this->Load_model('Actor_model');
		if($this->Actor_model->Actor_is_alive($actor_id) == false)
			return false;

		$query = "update Actor set Hunt_ID = NULL where ID = ?";
		$args = array($actor_id);
		$rs = $this->db->Execute($query, $args);
		if(!$rs) {
			$errormsg = $this->db->ErrorMsg();
			return array('success' => false, 'data' => $errormsg);
		}
		
		return array('success' => true);
	}
	
	public function Update_hunts($time) {
		$errormsg = "";

		$query = "
				select
					H.ID,
					H.Stage_ID,
					H.Prey_ID,
					H.Hours_left,
					H.Location_ID,
					H.UpdateTick,
					(select count(ID) from Actor A where A.Hunt_ID = H.ID) as Hunters
				from Hunt H
				where H.UpdateTick < ?
				";
		$args = array($time);
		$rs = $this->db->Execute($query, $args);
		if(!$rs) {
			$errormsg = $this->db->ErrorMsg();
			return array('success' => false, 'data' => $errormsg);
		}
		$this->db->StartTrans();

		foreach($rs->GetArray() as $hunt) {
			/*
			echo '<pre>';
			var_dump($hunt);
			echo '</pre>';
			*/
			$hunt['UpdateTick'] += 1;
			if($hunt['Hunters'] > 0) {
				$hunt['Hours_left'] -= 1;
				
				//Update hunt stage, create corpse...
				if($hunt['Stage_ID'] == 1) { //Searching
					//50% chance to find tracks
					$tracks = rand (0, 1);
					if($tracks == 0) {
						//equal split which species we find
						$query = "
								select Species_ID from Hunt_species
								where Hunt_ID = ? and Amount > 0
								";
						$args = array($hunt['ID']);
						$rs = $this->db->Execute($query, $args);
						if(!$rs) {
							$errormsg = $this->db->ErrorMsg();
							break;
						}
						$species = $rs->GetArray();
						$num_species = count($species);
						$i = rand (0, $num_species-1);
						$hunt['Prey_ID'] = $species[$i]['Species_ID'];
						$hunt['Stage_ID'] = 2;
					}
				}
				if($hunt['Stage_ID'] == 2) { //Tracking
					//50% chance to catch up to the animal
					$found = rand (0, 1);
					if($found == 0) {
						$hunt['Stage_ID'] = 3;
					}
				}
				if($hunt['Stage_ID'] == 3) { //Chasing
					//50% chance to kill
					$kill = rand (0, 1);
					if($kill == 0) {
						$query = '
							insert into Object (Product_ID, Inventory_ID, Quality, Rot)
							select s.Corpse_product_ID, l.Inventory_ID, 1, 0 from Hunt h
							join Species s on s.ID = h.Prey_ID
							join Location l on l.ID = h.Location_ID
							where h.ID = ?
						';
						$args = array($hunt['ID']);

						$rs = $this->db->Execute($query, $args);
						
						if(!$rs) {
							$errormsg = $this->db->ErrorMsg();
							break;
						}

						$query = '
							update Hunt_species set Amount = Amount - 1
							where Hunt_ID = ? and Species_ID = ?
						';
						$args = array($hunt['ID'], $hunt['Prey_ID']);

						$rs = $this->db->Execute($query, $args);
						
						if(!$rs) {
							$errormsg = $this->db->ErrorMsg();
							break;
						}

						$this->Load_model('Event_model');
						$this->Event_model->Save_hunt_event("{LNG_Hunt_killed_animal}", $hunt['ID']);
						if($this->db->HasFailedTrans()) {
							$errormsg = "Saving event failed";
							break;
						}

						//Check if there are any animals left to hunt, if not we end the hunt.
						$query = "
								select Species_ID from Hunt_species
								where Hunt_ID = ? and Amount > 0
								";
						$args = array($hunt['ID']);
						$rs = $this->db->Execute($query, $args);
						if(!$rs) {
							$errormsg = $this->db->ErrorMsg();
							break;
						}
						$species = $rs->GetArray();
						$num_species = count($species);
						if($num_species == 0) {
							$hunt['Hours_left'] = 0;
						}

						$hunt['Stage_ID'] = 1;
					}
				}
			}
			
			if($hunt['Hours_left'] > 0) {
				/*
				echo '<pre>';
				var_dump($hunt);
				echo '</pre>';
				*/
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
							$hunt['Stage_ID'],
							$hunt['Prey_ID'],
							$hunt['ID']);
				$rs = $this->db->Execute($query, $args);
				if($this->db->Affected_rows() == 0) {
					$errormsg = "No affected rows from update";
					$this->db->FailTrans();
					break;
				}
				if(!$rs) {
					$errormsg = $this->db->ErrorMsg();
					break;
				}
				if($this->db->HasFailedTrans()) {
					$errormsg = $this->db->ErrorMsg();
					break;
				}
			} else {
				$this->Load_model('Event_model');
				$this->Event_model->Save_hunt_event("{LNG_Hunt_ended}", $hunt['ID']);
				if($this->db->HasFailedTrans()) {
					$errormsg = "Saving event failed";
					break;
				}
				$query = "
						update Actor set Hunt_ID = NULL where Hunt_ID = ?
						";
				$args = array($hunt['ID']);
				$rs = $this->db->Execute($query, $args);
				if(!$rs) {
					$errormsg = $this->db->ErrorMsg();
					break;
				}
				$query = "
						delete from Hunt where ID = ?
						";
				$args = array($hunt['ID']);
				$rs = $this->db->Execute($query, $args);
				if(!$rs) {
					$errormsg = $this->db->ErrorMsg();
					break;
				}
			}
		}

		$success = !$this->db->HasFailedTrans();
		$this->db->CompleteTrans();
		return array('success' => $success, 'data' => $errormsg);
	}
}
?>
