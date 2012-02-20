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
		
		$result['recipe'] = $recipe->fields;
		$result['recipe_inputs'] = $recipe_inputs->GetArray();
		$result['recipe_outputs'] = $recipe_outputs->GetArray();
		$result['new_output'] = $new_output->fields;

		return $result;
	}

	public function Save_recipe($data)
	{
		$db = Load_database();

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
								$data['recipe']['id']
							);
					$r = $db->Execute('
						insert into Recipe_output (Amount, Resource_ID, Recipe_ID) values (?, ?, ?)
						', $args);
//				var_dump($r);
				}
			}
		}
		
		if(!$recipe) {
			return false;
		}
		return $result_id;
	}
}
?>
