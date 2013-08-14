<?php

require_once '../models/database.php';
require_once '../models/model.php';

class Project_model extends Model
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
			select
				RI.ID,
				RI.Resource_ID,
				R.Name AS Resource_Name,
				R.Measure AS Measure_ID,
				M.Name AS Measure_name,
				RI.Amount,
				(RI.Amount * R.Mass) AS Mass,
				(RI.Amount * R.Volume) AS Volume,
				From_nature
			from Recipe_input RI
			join Resource R on R.ID = RI.Resource_ID
			join Measure M on R.Measure = M.ID
			where RI.Recipe_ID = ?
			', array($id));

		$recipe_outputs = $db->Execute('
			select
				RO.ID,
				RO.Resource_ID,
				R.Name AS Resource_Name,
				R.Measure AS Measure_ID,
				M.Name AS Measure_name,
				RO.Amount,
				(RO.Amount * R.Mass) AS Mass,
				(RO.Amount * R.Volume) AS Volume
			from Recipe_output RO
			join Resource R on R.ID = RO.Resource_ID
			join Measure M on R.Measure = M.ID
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

		$recipe_product_inputs = $db->Execute('
			select
				RPI.ID,
				RPI.Product_ID,
				P.Name AS Product_Name,
				RPI.Amount
			from Recipe_product_input RPI
			join Product P on P.ID = RPI.Product_ID
			where RPI.Recipe_ID = ?
			', array($id));

		$recipe_product_outputs = $db->Execute('
			select
				RPO.ID,
				RPO.Product_ID,
				P.Name AS Product_Name,
				RPO.Amount
			from Recipe_product_output RPO
			join Product P on P.ID = RPO.Product_ID
			where RPO.Recipe_ID = ?
			', array($id));

		$new_product_component = $db->Execute('
			select -1 AS ID, P.ID AS Product_ID, P.Name AS Product_Name, 1 AS Amount
			from Product P
			limit 1
			', array());

		$result['recipe'] = $recipe->fields;
		$result['recipe_inputs'] = $recipe_inputs->GetArray();
		$result['recipe_outputs'] = $recipe_outputs->GetArray();
		$result['recipe_product_inputs'] = $recipe_product_inputs->GetArray();
		$result['recipe_product_outputs'] = $recipe_product_outputs->GetArray();
		$result['new_product_component'] = $new_product_component->fields;
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
			$recipe_id = $data['recipe']['id'];
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
			$recipe_id = $db->Insert_ID();
		}

		$args = array();
		$r = $db->Execute('
			select ID, Mass, Volume, 1 as Object from Resource
			', $args);
			
		$amount_factors = array();
		foreach($r->GetArray() as $resource) {
			$amount_factors[$resource['ID']] = $resource;
		}

		$args = array();
		$r = $db->Execute('
			select ID, Name from Measure
			', $args);
			
		$measures = array();
		foreach($r->GetArray() as $measure) {
			$measures[$measure['ID']] = $measure['Name'];
		}

		if($recipe_id != -1) {
			foreach($data['outputs'] as $o) {
				if($o['remove']) {
					$this->Remove_recipe_output($recipe_id, $o['id']);
				}
			}
			foreach($data['outputs'] as $o) {
				if($o['remove']) {
					continue;
				}
				if($o['id'] < 0) {
					$this->Add_recipe_output($recipe_id, $o['resource_id'], $o['measure'], $o['amount']);
				} else {
					$o['amount'] /= $amount_factors[$o['resource_id']][$measures[$o['measure']]];
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
				}
			}

			foreach($data['inputs'] as $i) {
				if($i['remove']) {
					$this->Remove_recipe_input($recipe_id, $i['id']);
				}
			}
			foreach($data['inputs'] as $i) {
				if($i['remove']) {
					continue;
				}
				if($i['id'] < 0) {
					$this->Add_recipe_input($recipe_id, $i['resource_id'], $i['measure'], $i['amount']);
				} else {
					$i['amount'] /= $amount_factors[$i['resource_id']][$measures[$i['measure']]];
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
				}
			}

			foreach($data['product_outputs'] as $o) {
				if($o['remove']) {
					$this->Remove_recipe_product_output($recipe_id, $o['id']);
				}
			}
			foreach($data['product_outputs'] as $o) {
				if($o['remove']) {
					continue;
				}
				if($o['id'] < 0) {
					$this->Add_recipe_product_output($recipe_id, $o['product_id'], $o['amount']);
				} else {
					$args = array(
								$o['amount'], 
								$o['product_id'],
								$o['id']
							);
					$r = $db->Execute('
						update Recipe_product_output set 
							Amount = ?,
							Product_ID = ?
						where ID = ?
						', $args);
				}
			}

			foreach($data['product_inputs'] as $i) {
				if($i['remove']) {
					$this->Remove_recipe_product_input($recipe_id, $i['id']);
				}
			}
			foreach($data['product_inputs'] as $i) {
				if($i['remove']) {
					continue;
				}
				if($i['id'] < 0) {
					$this->Add_recipe_product_input($recipe_id, $i['product_id'], $i['amount']);
				} else {
					$args = array(
								$i['amount'], 
								$i['product_id'],
								$i['id']
							);
					$r = $db->Execute('
						update Recipe_product_input set 
							Amount = ?,
							Product_ID = ?
						where ID = ?
						', $args);
				}
			}
		}

		$success = !$db->HasFailedTrans();
		$db->CompleteTrans();
		if($success != true)
			return false;

		return $recipe_id;
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
	
	public function Get_measure_conversion_data() {
		$db = Load_database();

		$args = array();
		$r = $db->Execute('
			select ID, Mass, Volume, 1 as Object from Resource
			', $args);

		$amount_factors = array();
		foreach($r->GetArray() as $resource) {
			$amount_factors[$resource['ID']] = $resource;
		}

		$args = array();
		$r = $db->Execute('
			select ID, Name from Measure
			', $args);
			
		$measures = array();
		foreach($r->GetArray() as $measure) {
			$measures[$measure['ID']] = $measure['Name'];
		}
		return array($measures, $amount_factors);
	}

	public function Add_recipe_output($recipe_id, $resource_id, $measure_id, $amount) {
		$db = Load_database();

		$mcd = $this->Get_measure_conversion_data();
		$measures = $mcd[0];
		$amount_factors = $mcd[1];

		$amount /= $amount_factors[$resource_id][$measures[$measure_id]];
		$args = array(
					$amount,
					$resource_id,
					$recipe_id
				);
		$r = $db->Execute('
			insert into Recipe_output (Amount, Resource_ID, Recipe_ID) values (?, ?, ?)
			', $args);
		
		if(!$r)
			return array('success' => false);
		return array('success' => true, 'id' => $db->Insert_id());
	}

	public function Add_recipe_input($recipe_id, $resource_id, $measure_id, $amount) {
		$db = Load_database();

		$mcd = $this->Get_measure_conversion_data();
		$measures = $mcd[0];
		$amount_factors = $mcd[1];

		$amount /= $amount_factors[$resource_id][$measures[$measure_id]];
		$args = array(
					$amount,
					$resource_id,
					$recipe_id
				);
		$r = $db->Execute('
			insert into Recipe_input (Amount, Resource_ID, Recipe_ID) values (?, ?, ?)
			', $args);
		
		if(!$r)
			return array('success' => false);
		return array('success' => true, 'id' => $db->Insert_id());
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

	public function Remove_recipe_product_output($recipe_id, $output_id)
	{
		$db = Load_database();
		
		$args = array($recipe_id, $output_id);

		$r = $db->Execute('
			delete from Recipe_product_output 
			where Recipe_ID = ? and ID = ?
			', $args);
		
		if($db->Affected_rows() > 0)
			return true;
		return false;
	}
	public function Add_recipe_product_output($recipe_id, $product_id, $amount) {
		$db = Load_database();

		$args = array(
					$amount,
					$product_id,
					$recipe_id
				);
		$r = $db->Execute('
			insert into Recipe_product_output (Amount, Product_ID, Recipe_ID) values (?, ?, ?)
			', $args);
		
		if(!$r)
			return array('success' => false);
		return array('success' => true, 'id' => $db->Insert_id());
	}
	
	public function Add_recipe_product_input($recipe_id, $product_id, $amount) {
		$db = Load_database();

		$args = array(
					$amount,
					$product_id,
					$recipe_id
				);
		$r = $db->Execute('
			insert into Recipe_product_input (Amount, Product_ID, Recipe_ID) values (?, ?, ?)
			', $args);
		
		if(!$r)
			return array('success' => false);
		return array('success' => true, 'id' => $db->Insert_id());
	}

	public function Remove_recipe_product_input($recipe_id, $input_id)
	{
		$db = Load_database();
		
		$args = array($recipe_id, $input_id);

		$r = $db->Execute('
			delete from Recipe_product_input 
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
			group by R.ID
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
	
	public function Start_project($actor_id, $recipe_id, $supply, $cycles)
	{
		$db = Load_database();
		
		$db->StartTrans();
		
		//Create project inventory
		$r = $db->Execute('
			insert into Inventory values()', array());
		
		if(!$r) {
			$db->FailTrans();
			$db->CompleteTrans();
			return false;
		}
		$project_inventory_id = $db->Insert_id();
		
		$this->Load_model('Actor_model');
		$inventory_ids = $this->Actor_model->Get_actor_and_location_inventory($actor_id);
		//Create the project
		$args = array($inventory_ids['Location_inventory'], $recipe_id, $cycles, $project_inventory_id, $actor_id);

		$r = $db->Execute('
			insert into Project (Creator_actor_ID, Location_inventory_ID, Recipe_ID, Cycles_left, Created_time, Inventory_ID)
			select A.ID, ?, ?, ?, C.Value, ?
			from Count C, Actor A
			where C.Name = \'Update\' and A.ID = ?
			', $args);
			
		if(!$r) {
			$db->FailTrans();
			$db->CompleteTrans();
			return false;
		}
		$project_id = $db->Insert_id();

		if($supply == true) {
			$supply_result = $this->Supply_project($project_id, $actor_id);
		}
		
		$success = !($db->HasFailedTrans());
		$db->CompleteTrans();

		return $success;
	}

	public function Join_project($actor_id, $project_id)
	{
		$this->Leave_project($actor_id);

		$db = Load_database();

		$this->Load_model('Actor_model');
		$inventory_ids = $this->Actor_model->Get_actor_and_location_inventory($actor_id);
		$args = array($inventory_ids['Location_inventory'], $project_id, $actor_id);

		//Figure out if you're allowed to join the project
		//At same location
		//Not travelling
		$r = $db->Execute('
			select 
				T.ID as Travelling,
				P.ID as Location
			from Actor A
			left join Project P on P.Location_inventory_ID = ? and P.ID = ?
			left join Travel T on T.ActorID = A.ID
			where A.ID = ?
			', $args);

		if(!$r) {
			return false;
		}
		
		if($r->fields['Travelling'] !== NULL || $r->fields['Location'] == NULL) {
			return false;
		}

		//Make sure actor leaves any participation in a hunt
		$this->Load_model('Species_model');
		$left = $this->Species_model->Leave_hunt($actor_id);
		if($left != true) {
			return array('success' => false, 'data' => 'Failed to leave hunt');
		}

		//Join the project
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
		$db = Load_database();

		$args = array($project_id);

		//Check that enough workers are on the project.
		$workers = $db->Execute('
			select P.Active from Project P
			join Actor A on A.Project_ID = P.ID
			where P.ID = ?
			', $args);
		
		if(!$workers) {
			return false;
		}

		$missing_input = $db->Execute('
			select 	RI.Resource_ID, 
					RI.Amount AS Needed_amount, 
					PI.Amount AS Project_amount
			from Project P
			join Recipe_input RI on RI.Recipe_ID = P.Recipe_ID and RI.From_nature = 0
			left join Inventory_resource PI on PI.Inventory_ID = P.Inventory_ID and PI.Resource_ID = RI.Resource_ID
			where P.ID = ? and (PI.Amount < RI.Amount or PI.Amount is NULL)
			', $args);

		if(!$missing_input) {
			return false;
		}

		$missing_products = $db->Execute('
			select 	RI.Product_ID, 
					RI.Amount AS Needed_amount, 
					count(PO.ID) AS Project_amount
			from Project P
			join Recipe_product_input RI on RI.Recipe_ID = P.Recipe_ID
			left join Object PO on RI.Product_ID = PO.Product_ID and PO.Inventory_ID = P.Inventory_ID
			where P.ID = ?
			group by RI.ID
			having(count(PO.ID) < RI.Amount)
			', $args);

		if(!$missing_products) {
			return false;
		}

		if($workers->RecordCount()>0 && $missing_input->RecordCount() == 0 && $missing_products->RecordCount() == 0) {
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
		
		$this->Load_model('Actor_model');
		$inventory_ids = $this->Actor_model->Get_actor_and_location_inventory($actor_id);
		$args = array($actor_id, $inventory_ids['Location_inventory']);

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
			left join Actor AP on AP.Project_ID = P.ID AND AP.ID = ?
			where P.Location_inventory_ID = ?
			', $args);
		
		if(!$r) {
			return false;
		}

		return $r->GetArray();
	}
	
	public function Get_project($project_id, $actor_id) {
		$db = Load_database();
		
		$this->Load_model('Actor_model');
		$inventory_ids = $this->Actor_model->Get_actor_and_location_inventory($actor_id);
		$args = array($actor_id, $inventory_ids['Location_inventory'], $project_id);

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
			left join Actor AP on AP.Project_ID = P.ID AND AP.ID = ?
			where P.Location_inventory_ID = ? and P.ID = ?
			', $args);
		
		if(!$info) {
			return false;
		}

		$recipe_inputs = $db->Execute('
			select 
				R.ID, 
				R.Name, 
				R.Measure AS Measure_ID,
				M.Name AS Measure_name,
				RI.Amount,
				R.Mass AS Mass_factor,
				R.Volume AS Volume_factor,
				RI.From_nature,
				IFNULL(PI.Amount, 0) as Project_amount
			from Project P
			join Recipe_input RI on RI.Recipe_ID = P.Recipe_ID
			join Resource R on R.ID = RI.Resource_ID
			join Measure M on R.Measure = M.ID
			left join Inventory_resource PI on PI.Inventory_ID = P.Inventory_ID and PI.Resource_ID = RI.Resource_ID
			where P.ID = ?
			', array($project_id));

		if(!$recipe_inputs) {
			return false;
		}

		$recipe_outputs = $db->Execute('
			select 
				R.ID, 
				R.Name, 
				R.Measure AS Measure_ID,
				M.Name AS Measure_name,
				RO.Amount,
				R.Mass AS Mass_factor,
				R.Volume AS Volume_factor
			from Project P
			join Recipe_output RO on RO.Recipe_ID = P.Recipe_ID
			join Resource R on R.ID = RO.Resource_ID
			join Measure M on R.Measure = M.ID
			where P.ID = ?
			', array($project_id));

		if(!$recipe_outputs) {
			return false;
		}

		$recipe_product_inputs = $db->Execute('
			select 
				R.ID, 
				R.Name, 
				RI.Amount,
				count(O.ID) as Project_amount
			from Project P
			join Recipe_product_input RI on RI.Recipe_ID = P.Recipe_ID
			join Product R on R.ID = RI.Product_ID
			left join Object O on O.Inventory_ID = P.Inventory_ID and O.Product_ID = R.ID
			where P.ID = ?
			group by R.ID
			', array($project_id));

		if(!$recipe_product_inputs) {
			return false;
		}

		$recipe_product_outputs = $db->Execute('
			select 
				R.ID, 
				R.Name, 
				RO.Amount
			from Project P
			join Recipe_product_output RO on RO.Recipe_ID = P.Recipe_ID
			join Product R on R.ID = RO.Product_ID
			where P.ID = ?
			', array($project_id));

		if(!$recipe_product_outputs) {
			return false;
		}

		$project = array();
		$project['info'] = $info->fields;
		$project['recipe_inputs'] = $recipe_inputs->getArray();
		$project['recipe_outputs'] = $recipe_outputs->getArray();
		$project['recipe_product_inputs'] = $recipe_product_inputs->getArray();
		$project['recipe_product_outputs'] = $recipe_product_outputs->getArray();

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
				R.Cycle_time,
				P.ID as Project_ID,
				P.Inventory_ID as Project_inventory,
				P.Cycles_left,
				P.Progress,
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

		$output = array();
		$output['resources'] = $r->GetArray();

		$r = $db->Execute('
			select
				P.ID as Project_ID,
				P.Inventory_ID as Project_inventory,
				P.Cycles_left,
				P.Progress,
				R.Cycle_time,
				P.Creator_actor_ID,
				A.Inventory_ID,
				O.Product_ID,
				O.Amount
			from Project P
			join Actor A on P.Creator_actor_ID = A.ID
			join Recipe R on R.ID = P.Recipe_ID
			join Recipe_product_output O on R.ID = O.Recipe_ID
			where P.Progress >= R.Cycle_time
			', $args);
		
		if(!$r) {
			return false;
		}

		$output['products'] = $r->GetArray();

		return $output;
	}
	
	public function Process_finished_projects($projects) {
		$db = Load_database();

		$success = true;

		foreach($projects as $project) {
			$db->StartTrans();

			//Put output into inventory
			if(isset($project['outputs'])) {
				foreach($project['outputs'] as $output) {
					$query = '
						insert into Inventory_resource (Inventory_ID, Resource_ID, Amount)
						select A.Inventory_ID, ?, ?
						from Actor A
						where A.ID = ? limit 1
						on duplicate key update Amount = Amount + ?
					';
					$args = array($output['Resource_ID'], $output['Amount'], $output['Creator_actor_ID'], $output['Amount']);
					$rs = $db->Execute($query, $args);
					
					if(!$rs) {
						$db->FailTrans();
						break;
					}
				}
			}

			if(isset($project['product_outputs'])) {
				$key_form_id = false;
				foreach($project['product_outputs'] as $output) {
					for($i = 0; $i < $output['Amount']; $i++) {
						$query = '
							insert into Object (Product_ID, Inventory_ID, Quality, Rot)
							values(?,?,1,0)
						';
						$args = array($output['Product_ID'], $output['Inventory_ID']);
						$rs = $db->Execute($query, $args);
						if(!$rs)
							break;
						
						$Object_ID = $db->Insert_id();
						
						$query = '
								select c.ID, c.Name
								from Product p 
								join Product_category pc on pc.Product_ID = p.ID
								join Category c on pc.Category_ID = c.ID
								where p.ID = ?
								';

						$args = array($output['Product_ID']);
						$rs = $db->Execute($query, $args);
						if(!$rs)
							break;
						foreach($rs->GetArray() as $category) {
							if($category['Name'] == 'Container') {
								$query = 'insert into Inventory () values()';

								$args = array();
								$rs = $db->Execute($query, $args);
								if(!$rs)
									break;
								$Inventory_ID = $db->Insert_id();
								
								$query = 'insert into Object_inventory (Object_ID, Inventory_ID) values(?, ?)';

								$args = array($Object_ID, $Inventory_ID);
								$rs = $db->Execute($query, $args);
								if(!$rs)
									break;
							} elseif($category['Name'] == 'Key') {
								if(!$key_form_id) {
									$key_form_id = $this->Create_key_form();
									if(!$key_form_id) {
										$db->FailTrans();
										$db->CompleteTrans();
										return false;
									}
								}
								$query = 'insert into Object_key (Object_ID, Key_form_ID) values(?, ?)';
								$args = array($Object_ID, $key_form_id);
								$rs = $db->Execute($query, $args);
								if(!$rs) {
									$db->FailTrans();
									$db->CompleteTrans();
									echo "Key fail";
									return false;
								}
							} elseif($category['Name'] == 'Lock') {
								if(!$key_form_id) {
									$key_form_id = $this->Create_key_form();
									if(!$key_form_id) {
										$db->FailTrans();
										$db->CompleteTrans();
										return false;
									}
								}
								$query = 'insert into Object_lock (Object_ID, Key_form_ID) values(?, ?)';
								$args = array($Object_ID, $key_form_id);
								$rs = $db->Execute($query, $args);
								if(!$rs) {
									$db->FailTrans();
									$db->CompleteTrans();
									echo "Lock fail";
									echo $Object_ID . " " . $key_form_id;
									return false;
								}
							}
						}
					}
					if(!$rs) {
						$db->FailTrans();
						break;
					}
				}
			}

			//Update/delete project
			if(!$db->HasFailedTrans()) {
				if($project['Cycles_left'] > 1) {
					$query = '
						update Project set Cycles_left = Cycles_left - 1, Progress = ?
						where ID = ?
					';
					$args = array($project['Progress'] - $project['Cycle_time'], $project['Project_ID']);
					$rs = $db->Execute($query, $args);
					
					//Remove one cycles worth of input resources from project
					$query = '
						select PI.ID, RI.Amount from Project P
						join Recipe_input RI on RI.Recipe_ID = P.Recipe_ID
						join Inventory_resource PI on PI.Resource_ID = RI.Resource_ID and PI.Inventory_ID = P.Inventory_ID
						where P.ID = ?
					';
					$args = array($project['Project_ID']);
					$rs = $db->Execute($query, $args);

					if(!$db->HasFailedTrans()) {
						foreach($rs as $r) {
							$query = '
								update Inventory_resource set Amount = Amount - ?
								where ID = ?
							';
							$args = array($r['Amount'], $r['ID']);
							$prs = $db->Execute($query, $args);
						}
					}

					//Remove one cycles worth of input objects from project
					$query = '
						select RPI.Product_ID, RPI.Amount, P.Inventory_ID from Project P
						join Recipe_product_input RPI on P.Recipe_ID = RPI.Recipe_ID
						where P.ID = ?
					';
					$args = array($project['Project_ID']);
					$rs = $db->Execute($query, $args);

					if(!$db->HasFailedTrans()) {
						foreach($rs as $r) {
							$query = '
								select ID from Object
								where Inventory_ID = ? and Product_ID = ?
								limit ' . $r['Amount'] . '
							';
							$args = array($r['Inventory_ID'], $r['Product_ID']);
							$srs = $db->Execute($query, $args);

							foreach($srs as $pr) {
								$query = '
									delete from Object
									where ID = ?
								';
								$args = array($pr['ID']);
								$prs = $db->Execute($query, $args);
							}
						}
					}
				} else {
					$query = '
						Update Actor set Project_ID = NULL where Project_ID = ?
					';
					$args = array($project['Project_ID']);
					$rs = $db->Execute($query, $args);

					$query = '
						delete from Inventory_resource where Inventory_ID = ?
					';
					$args = array($project['Project_inventory']);
					$rs = $db->Execute($query, $args);

					$query = '
						delete from Object where Inventory_ID = ?
					';
					$args = array($project['Project_inventory']);
					$rs = $db->Execute($query, $args);

					$query = '
						delete from Project where ID = ?
					';
					$args = array($project['Project_ID']);
					$rs = $db->Execute($query, $args);

					$query = '
						delete from Inventory where ID = ?
					';
					$args = array($project['Project_inventory']);
					$rs = $db->Execute($query, $args);
				}
			}

			if($db->HasFailedTrans()) {
				echo $db->ErrorMsg();
			}
			$db->CompleteTrans();
		}
		return $success;
	}
	
	private function Create_key_form() {
		$db = Load_database();
		$query = 'insert into Key_form () values()';
		$args = array();
		$rs = $db->Execute($query, $args);
		if(!$rs) {
			return false;
		}
		$key_form_id = $db->Insert_id();
		return $key_form_id;
	}
	
	public function Supply_project($project_id, $actor_id) {
		$db = Load_database();
		$db->StartTrans();
		
		$this->Load_model('Actor_model');
		$inventory_ids = $this->Actor_model->Get_actor_and_location_inventory($actor_id);
		$args = array($project_id, $actor_id, $inventory_ids['Location_inventory']);

		$r = $db->Execute('
			select 	RI.Resource_ID, 
					AI.Inventory_ID,
					RI.Amount * P.Cycles_left AS Needed_amount, 
					PI.Amount AS Project_amount,
					AI.Amount AS Actor_amount
			from Project P
			join Actor A
			join Recipe_input RI on RI.Recipe_ID = P.Recipe_ID and RI.From_nature = 0
			join Inventory_resource AI on RI.Resource_ID = AI.Resource_ID and AI.Inventory_ID = A.Inventory_ID
			left join Inventory_resource PI on PI.Inventory_ID = P.Inventory_ID and PI.Resource_ID = RI.Resource_ID
			where P.ID = ? and A.ID = ? and (PI.Amount < RI.Amount or PI.Amount is NULL) and P.Location_inventory_ID = ?
			', $args);
		
		$inputs = $r->getArray();

		$args = array($project_id, $actor_id);
		$r = $db->Execute('select P.Inventory_ID as Project_inventory from Project P where P.ID = ?', $args);
		$project_inventory_id = $r->fields['Project_inventory'];

		$this->Load_model('Inventory_model');
		foreach($inputs as $input) {
			if($input['Project_amount'] == NULL) {
				$supply_amount = min($input['Needed_amount'], $input['Actor_amount']);
			} else {
				$supply_amount = min($input['Needed_amount']-$input['Project_amount'], $input['Actor_amount']);
			}
			$this->Inventory_model->Transfer_resource($inventory_ids['Actor_inventory'], $project_inventory_id, $input['Resource_ID'], $supply_amount, true);
		}

		$args = array($project_id, $actor_id, $inventory_ids['Location_inventory']);
		$r = $db->Execute('
			select 	RI.Product_ID, 
					RI.Amount * P.Cycles_left AS Needed_amount, 
					count(PO.ID) AS Project_amount
			from Project P
			join Actor A
			join Recipe_product_input RI on RI.Recipe_ID = P.Recipe_ID
			left join Object PO on RI.Product_ID = PO.Product_ID and PO.Inventory_ID = P.Inventory_ID
			where P.ID = ? and A.ID = ? and P.Location_inventory_ID = ?
			group by RI.ID
			having(count(PO.ID) < Needed_amount)
			', $args);
		
		$inputs = $r->getArray();
		
		foreach($inputs as $input) {
			$this->Inventory_model->Transfer_product(
													$inventory_ids['Actor_inventory'], 
													$project_inventory_id, 
													$input['Product_ID'],
													$input['Needed_amount'] - $input['Project_amount']
												);
		}		

		if($db->HasFailedTrans()) {
			echo $db->ErrorMsg();
		}

		$db->CompleteTrans();

		$this->Update_project_active_state($project_id);

		return true;
	}

	public function Cancel_project($project_id, $actor_id) {
		$db = Load_database();
		$db->StartTrans();
		
		$this->Load_model('Actor_model');
		$inventory_ids = $this->Actor_model->Get_actor_and_location_inventory($actor_id);

		$args = array($project_id);
		$r = $db->Execute('
			select Inventory_ID from Project where ID = ?
			', $args);
		
			echo $db->ErrorMsg();
		$args = array($project_id);

		$args = array($project_id, $inventory_ids['Location_inventory']);
		$r2 = $db->Execute('
			delete P from Project P
			where P.ID = ? and P.Location_inventory_ID = ?
			', $args);

		$args = array($r->fields['Inventory_ID']);
		$r = $db->Execute('
			delete from Inventory where ID = ?
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
