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
			select ID, Recipe_ID, Resource_ID, Amount, From_nature from Recipe_input
			where Recipe_ID = ?
			', array($id));

		$recipe_outputs = $db->Execute('
			select ID, Recipe_ID, Resource_ID, Amount from Recipe_output
			where Recipe_ID = ?
			', array($id));
		
		$result['recipe'] = $recipe->fields;
		$result['recipe_inputs'] = $recipe_inputs->GetArray();
		$result['recipe_outputs'] = $recipe_outputs->GetArray();

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
		
		if(!$recipe) {
			return false;
		}
		return $result_id;
	}
}
?>
