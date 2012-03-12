<?php

require_once '../models/database.php';

class Project_model
{
	public function Get_recipes()
	{
		$db = Load_database();

		$rs = $db->Execute('
			select ID, Name from Recipe
			', array());
		
		if(!$rs)
		{
			return false;
		}
		return $rs->GetArray();
	}

	public function Get_recipe($id)
	{
		$db = Load_database();

		$result = array();

		$recipe = $db->Execute('
			select ID, Name, Cycle_time, Allow_fraction_output, Require_full_cycle from Recipe
			where ID = ?
			', array($id));

		if(!$recipe) {
			return false;
		}

		$recipe_inputs = $db->Execute('
			select RI.ID, RI.Recipe_ID, RI.Resource_ID, R.Name AS Resource_Name, RI.Amount, From_nature
			from Recipe_input RI
			join Resource R on R.ID = RI.Resource_ID
			where RI.Recipe_ID = ?
			', array($id));

		$recipe_outputs = $db->Execute('
			select RO.ID, RO.Resource_ID, R.Name AS Resource_Name, RO.Amount
			from Recipe_output RO
			join Resource R on R.ID = RO.Resource_ID
			where RO.Recipe_ID = ?
			', array($id));

		$new_output = $db->Execute('
			select -1 AS ID, R.ID AS Resource_ID, R.Name AS Resource_Name, 1 AS Amount
			from Resource R
			limit 1
			', array());

		$new_input = $db->Execute('
			select -1 AS ID, R.ID AS Resource_ID, R.Name AS Resource_Name, 1 AS Amount, 1 AS From_nature
			from Resource R
			limit 1
			', array());

		$result['recipe'] = $recipe->fields;
		$result['recipe_inputs'] = $recipe_inputs->GetArray();
		$result['recipe_outputs'] = $recipe_outputs->GetArray();
		$result['new_output'] = $new_output->fields;
		$result['new_input'] = $new_input->fields;

		return $result;
	}

	public function Save_recipe($data)
	{
		$db = Load_database();

		$db->StartTrans();

		$result = array();
		$result_id = -1;
		
		if($data['recipe']['id'] != -1) {
			$args = array(
						$data['recipe']['name'], 
						$data['recipe']['cycle_time'], 
						($data['recipe']['allow_fraction_output'] == 'true')?1:0, 
						($data['recipe']['require_full_cycle'] == 'true')?1:0,
						$data['recipe']['id']);
			$recipe = $db->Execute('
				update Recipe set 
					Name = ?, 
					Cycle_time = ?, 
					Allow_fraction_output = ?, 
					Require_full_cycle = ?
				where ID = ?
				', $args);
			$result_id = $data['recipe']['id'];
		} else {
			$args = array(
						$data['recipe']['name'], 
						$data['recipe']['cycle_time'], 
						($data['recipe']['allow_fraction_output'] == 'true')?1:0, 
						($data['recipe']['require_full_cycle'] == 'true')?1:0
					);
			$recipe = $db->Execute('
				insert into Recipe (Name, Cycle_time, Allow_fraction_output, Require_full_cycle)
				values (?, ?, ?, ?)
				', $args);
			$result_id = $db->Insert_ID();
		}

		if($result_id != -1) {
			foreach($data['outputs'] as $o) {
				if($o['id'] != "-1") {
					$args = array(
								$o['amount'], 
								$o['resource_id'],
								$o['id']
							);
					$r = $db->Execute('
						update Recipe_output set 
							Amount = ?,
							Resource_ID = ?
						where ID = ?
						', $args);
				} else {
					$args = array(
								$o['amount'], 
								$o['resource_id'],
								$result_id
							);
					$r = $db->Execute('
						insert into Recipe_output (Amount, Resource_ID, Recipe_ID) values (?, ?, ?)
						', $args);
				}
			}

			foreach($data['inputs'] as $i) {
				if($i['id'] != "-1") {
					$args = array(
								$i['amount'], 
								$i['resource_id'],
								($i['from_nature'] == 'true')?1:0,
								$i['id']
							);
					$r = $db->Execute('
						update Recipe_input set 
							Amount = ?,
							Resource_ID = ?,
							From_nature = ?
						where ID = ?
						', $args);
				} else {
					$args = array(
								$i['amount'], 
								$i['resource_id'],
								$result_id,
								($i['from_nature'] == 'true')?1:0
							);
					$r = $db->Execute('
						insert into Recipe_input (Amount, Resource_ID, Recipe_ID, From_nature) values (?, ?, ?, ?)
						', $args);
				}
			}
		}

		$success = !$db->HasFailedTrans();
		$db->CompleteTrans();
		if($success != true)
			return false;

		return $result_id;
	}
	
	public function Remove_recipe_output($recipe_id, $output_id)
	{
		$db = Load_database();
		
		$args = array($recipe_id, $output_id);

		$r = $db->Execute('
			delete from Recipe_output 
			where Recipe_ID = ? and ID = ?
			', $args);
		
		if($db->Affected_rows() > 0)
			return true;
		return false;
	}

	public function Remove_recipe_input($recipe_id, $input_id)
	{
		$db = Load_database();
		
		$args = array($recipe_id, $input_id);

		$r = $db->Execute('
			delete from Recipe_input 
			where Recipe_ID = ? and ID = ?
			', $args);
		
		if($db->Affected_rows() > 0)
			return true;
		return false;
	}
	
	public function Get_recipes_with_nature_resource($actor_id, $resource_id) {
		$db = Load_database();
		
		$args = array($resource_id, $actor_id);

		$r = $db->Execute('
			select R.ID, R.Name from Recipe R
			join Recipe_input RI on R.ID = RI.Recipe_ID and RI.Resource_ID = ? and RI.From_nature = 1
			join Location_resource LR on LR.Resource_ID = RI.Resource_ID
			join Actor A on A.Location_ID = LR.Location_ID
			where A.ID = ?
			', $args);
		
		if(!$r) {
			return false;
		}

		return $r->GetArray();
	}
	
	public function Start_project($actor_id, $recipe_id)
	{
		$db = Load_database();
		
		$args = array($recipe_id, $actor_id);

		//TODO: Figure out if you're allowed to create the project
		
		//Create the project
		$r = $db->Execute('
			insert into Project (Creator_actor_ID, Location_ID, Recipe_ID, Cycles_left, Created_time)
			select A.ID, A.Location_ID, ?, 1, C.Value
			from Count C, Actor A where 
			C.Name = \'Update\' and A.ID = ?
			', $args);
		
		if(!$r) {
			return false;
		}

		//Add neccessary resources from actor inventory

		return true;
	}

	public function Join_project($actor_id, $project_id)
	{
		$this->Leave_project($actor_id);

		$db = Load_database();

		$args = array($project_id, $actor_id);

		//TODO: Figure out if you're allowed to join the project
		
		$r = $db->Execute('
			update Actor A set A.Project_ID = ?
			where A.ID = ?
			', $args);
		
		if(!$r) {
			return false;
		}

		$this->Update_project_active_state($project_id);

		return true;
	}

	public function Leave_project($actor_id)
	{
		$db = Load_database();
		
		$args = array($actor_id);

		//Create the project
		$r = $db->Execute('
			select A.Project_ID from Actor A
			where A.ID = ?
			', $args);

		if(!$r) {
			return false;
		}
		
		$project_id = $r->fields['Project_ID'];
		if($project_id != NULL) {
			$args = array($actor_id);

			$r = $db->Execute('
				update Actor A set A.Project_ID = NULL
				where A.ID = ?
				', $args);
			
			if(!$r) {
				return false;
			}

			$this->Update_project_active_state($project_id);
		}

		return true;
	}
	
	private function Update_project_active_state($project_id) {
		//TODO: Check prerequisites
		//Set active accordingly
		$db = Load_database();

		$args = array($project_id);

		$r = $db->Execute('
			select P.Active from Project P
			join Actor A on A.Project_ID = P.ID
			where P.ID = ?
			', $args);
		
		if(!$r) {
			return false;
		}

		if($r->RecordCount()>0) {
			$active = 1;
		} else {
			$active = 0;
		}
		
		$args = array($active, $project_id);

		//Create the project
		$r = $db->Execute('
			update Project P set P.Active = ?
			where P.ID = ?
			', $args);
		
		if(!$r) {
			return false;
		}

		return true;
	}
	
	public function Get_projects($actor_id)
	{
		$db = Load_database();
		
		$args = array($actor_id, $actor_id);

		$r = $db->Execute('
			select
				P.ID,
				P.Creator_actor_ID,
				P.Recipe_ID,
				R.Name as Recipe_Name,
				R.Cycle_time,
				P.Cycles_left,
				P.Created_time,
				P.Progress,
				P.Active,
				IF(AP.ID, true, false) AS Joined
			from Project P
			join Recipe R on R.ID = P.Recipe_ID
			join Location L on L.ID = P.Location_ID
			join Actor A on L.ID = A.Location_ID
			left join Actor AP on AP.Project_ID = P.ID AND AP.ID = ?
			where A.ID = ?
			', $args);
		
		if(!$r) {
			return false;
		}

		return $r->GetArray();;
	}
}
?>
