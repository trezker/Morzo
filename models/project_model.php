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

	public function Get_recipes_without_nature_resource() {
		$db = Load_database();

		$rs = $db->Execute('
			select R.ID, R.Name from Recipe R
			left join Recipe_input RI on R.ID = RI.Recipe_ID and RI.From_nature = 1
			where RI.ID is null
			', array());
		
		if(!$rs)
		{
			return false;
		}
		return $rs->GetArray();
	}
	
	public function Start_project($actor_id, $recipe_id, $supply)
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

		$project_id = $db->Insert_id();
		if($supply == true) {
			$supply_result = $this->Supply_project($project_id, $actor_id);
		}

		return true;
	}

	public function Join_project($actor_id, $project_id)
	{
		$this->Leave_project($actor_id);

		$db = Load_database();

		$args = array($project_id, $actor_id);

		//TODO: Figure out if you're allowed to join the project
		//At same location
		//Not travelling
		$r = $db->Execute('
			select 
				T.ID as Travelling,
				P.ID as Location
			from Actor A
			left join Project P on P.Location_ID = A.Location_ID and P.ID = ?
			left join Travel T on T.ActorID = A.ID
			where A.ID = ?
			', $args);

		if(!$r) {
			return false;
		}
		
		if($r->fields['Travelling'] !== NULL || $r->fields['Location'] == NULL) {
			return false;
		}

		$args = array($project_id, $actor_id);
		
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
		
		if($active == 1) {
			$rs = $db->Execute("
				select Value from Count where Name = 'Update'
				");
			$update = $rs->fields['Value'];

			$args = array($update, $project_id);

			$r = $db->Execute('
				update Project P set 
					P.Active = 1,
					P.UpdateTick = ?
				where P.ID = ? and P.Active = 0
				', $args);
		} else {
			$args = array($project_id);

			$r = $db->Execute('
				update Project P set 
					P.Active = 0
				where P.ID = ?
				', $args);
		}

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

		return $r->GetArray();
	}
	
	public function Get_project($project_id, $actor_id) {
		$db = Load_database();
		
		$args = array($actor_id, $actor_id, $project_id);

		$info = $db->Execute('
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
			where A.ID = ? and P.ID = ?
			', $args);
		
		if(!$info) {
			return false;
		}

		$recipe_inputs = $db->Execute('
			select 
				R.ID, 
				R.Name, 
				RI.Amount, 
				RI.From_nature,
				IFNULL(PI.Amount, 0) as Project_amount
			from Project P
			join Recipe_input RI on RI.Recipe_ID = P.Recipe_ID
			join Resource R on R.ID = RI.Resource_ID
			left join Project_input PI on PI.Project_ID = P.ID and PI.Resource_ID = RI.Resource_ID
			where P.ID = ?
			', array($project_id));

		if(!$recipe_inputs) {
			return false;
		}

		$recipe_outputs = $db->Execute('
			select R.ID, R.Name, RO.Amount
			from Project P
			join Recipe_output RO on RO.Recipe_ID = P.Recipe_ID
			join Resource R on R.ID = RO.Resource_ID
			where P.ID = ?
			', array($project_id));

		if(!$recipe_outputs) {
			return false;
		}

		$project = array();
		$project['info'] = $info->fields;
		$project['recipe_inputs'] = $recipe_inputs->getArray();
		$project['recipe_outputs'] = $recipe_outputs->getArray();

		return $project;
	}
	
	public function Update_projects($time) {
		$db = Load_database();
		
		$args = array($time, $time, $time);

		$r = $db->Execute('
			update Project P
			set P.Progress = P.Progress + ? - P.UpdateTick,
			P.UpdateTick = ?
			where P.UpdateTick < ? and P.Active = 1
			', $args);
		
		if(!$r) {
			return false;
		}

		return true;
	}

	public function Get_output_from_finished_cycles() {
		$db = Load_database();
		
		$args = array();

		$r = $db->Execute('
			select
				P.ID as Project_ID,
				P.Cycles_left,
				P.Creator_actor_ID,
				O.Resource_ID,
				O.Amount
			from Project P
			join Recipe R on R.ID = P.Recipe_ID
			join Recipe_output O on R.ID = O.Recipe_ID
			where P.Progress >= R.Cycle_time
			', $args);
		
		if(!$r) {
			return false;
		}

		return $r->getArray();
	}
	
	public function Process_finished_projects($projects) {
		$db = Load_database();

		$success = true;

		foreach($projects as $project) {
			$db->StartTrans();

			//Put output into inventory
			foreach($project['outputs'] as $output) {
				$query = '
					insert into Actor_inventory (Actor_ID, Resource_ID, Amount)
					values(?,?,?)
					on duplicate key update Amount = Amount + ?
				';
				$args = array($output['Creator_actor_ID'], $output['Resource_ID'], $output['Amount'], $output['Amount']);
				$rs = $db->Execute($query, $args);
				
				if(!$rs) {
					$db->FailTrans();
					break;
				}
			}

			//Update/delete project
			if(!$db->HasFailedTrans()) {
				if($project['Cycles_left'] > 1) {
					$query = '
						update Project set Cycles_left = Cycles_left - 1
						where ID = ?
					';
				} else {
					$query = '
						Update Actor set Project_ID = NULL where Project_ID = ?
					';
					$args = array($project['Project_ID']);
					$rs = $db->Execute($query, $args);

					$query = '
						delete from Project where ID = ?
					';
				}
				$args = array($project['Project_ID']);
				$rs = $db->Execute($query, $args);
			}

			if($db->HasFailedTrans()) {
				echo $db->ErrorMsg();
			}
			$db->CompleteTrans();
		}
		return $success;
	}
	
	public function Supply_project($project_id, $actor_id) {
		$db = Load_database();
		$db->StartTrans();
		
		$args = array($project_id, $actor_id);

		$r = $db->Execute('
			select 	RI.Resource_ID, 
					RI.Amount AS Needed_amount, 
					PI.Amount AS Project_amount,
					AI.Amount AS Actor_amount
			from Project P
			join Actor A on P.Location_ID = A.Location_ID
			join Recipe_input RI on RI.Recipe_ID = P.Recipe_ID
			join Actor_inventory AI on RI.Resource_ID = AI.Resource_ID and AI.Actor_ID = A.ID
			left join Project_input PI on PI.Project_ID = P.ID and PI.Resource_ID = RI.Resource_ID and PI.Amount < RI.Amount
			where P.ID = ? and A.ID = ?
			', $args);
		
		$inputs = $r->getArray();
		
		foreach($inputs as $input) {
			if($input['Project_amount'] == NULL) {
				$supply_amount = min($input['Needed_amount'], $input['Actor_amount']);

				$args = array($project_id, $input['Resource_ID'], $supply_amount);

				$r = $db->Execute('
					insert into Project_input (Project_ID, Resource_ID, Amount)
					values (?,?,?)
					', $args);
				if(!$r)
					break;
			} else {
				$supply_amount = min($input['Needed_amount']-$input['Project_amount'], $input['Actor_amount']);

				$args = array($input['Project_amount']+$supply_amount, $project_id, $input['Resource_ID']);

				$r = $db->Execute('
					update Project_input set Amount = ?
					where Project_ID = ? and Resource_ID = ?
					', $args);
				if(!$r)
					break;
			}

			$args = array($actor_id, $input['Resource_ID'], $input['Actor_amount']-$supply_amount);

			$r = $db->Execute('
				update Actor_inventory set Amount = ?
				where Actor_ID = ? and Resource_ID = ?
				', $args);
			if(!$r)
				break;
		}

		if($db->HasFailedTrans()) {
			echo $db->ErrorMsg();
		}
		$db->CompleteTrans();

		return true;
	}
	public function Cancel_project($project_id, $actor_id) {
		$db = Load_database();
		$db->StartTrans();
		
		$args = array($project_id, $actor_id);

		$r = $db->Execute('
			delete P from Project P
			join Actor A on A.Location_ID = P.Location_ID
			where P.ID = ? and A.ID = ?
			', $args);
		
		$success = true;
		if($db->HasFailedTrans()) {
			$success = false;
			echo $db->ErrorMsg();
		}
		$db->CompleteTrans();

		return $success;
	}
}
?>
