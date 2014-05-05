<?php
require_once "../controllers/base.php";

class Actor extends Base {
	public function Request_actor() {
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			return $this->Json_response_not_logged_in();
		}
		$this->Load_model('Actor_model');
		$r = $this->Actor_model->Request_actor($this->Session_get('userid'));
		
		return array(
			'type' => 'json',
			'data' => $r
		);
	}
	
	public function Show_actor($actor_id, $tab = 'events') {
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			return array("type" => "redirect", "data" => "/");
		}
		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($this->Session_get('userid'), $actor_id)) {
			return array("type" => "redirect", "data" => "/");
		}

		$this->Load_model("Travel_model");

		$actor = $this->Actor_model->Get_actor($actor_id);
		if($actor['Name'] == NULL) {
			$actor['Name'] = 'Unnamed actor';
		}
		if($actor['Location'] == NULL) {
			$actor['Location'] = 'Unnamed location';
		}
		
		$this->Load_controller('Update');
		$time = $this->Update->Get_time_units($actor['Time']);
		$minutes_to_next_update = $this->Get_time_to_next_update();
		
		$tab_view = '';
		if($tab == 'locations') {
			$travel = $this->Travel_model->Get_travel_info($actor_id);
			if($travel) {
				if(!$travel['OriginName'])
					$travel['OriginName'] = 'Unnamed location';
				if(!$travel['DestinationName'])
					$travel['DestinationName'] = 'Unnamed location';
			}
			$locations = $this->Get_neighbouring_locations($actor_id);
			$containers = $this->Actor_model->Get_containers_on_location($actor_id);
			if($actor['Inside_object_name'] !== NULL) {
				$locations = NULL; //TODO: add check for vehicle so it can travel when implemented
			}
			$tab_view = array(
				'view' => 'locations_tab_view', 
				'data' => array(
					'locations' => $locations, 
					'travel' => $travel, 
					'actor' => $actor, 
					'actor_id' => $actor_id,
					'containers' => $containers
				)
			);
		} elseif ($tab == 'people') {
			$actors = $this->Actor_model->Get_visible_actors($actor_id);
			$tab_view = array(
				'view' => 'people_tab_view', 
				'data' => array(
					'actors' => $actors, 
					'actor_id' => $actor_id
				)
			);
		} elseif ($tab == 'events') {
			$this->Load_model("Event_model");
			$events = $this->Event_model->Get_events($actor_id);
			$this->Load_model("Language_model");
			foreach ($events as $key => $event) {
				$events[$key]['Time_values'] = $this->Update->Get_time_units($event['Ingame_time']);
				$events[$key]['Text'] = $this->Language_model->Translate_event($events[$key], $actor_id);
			}
			$tab_view = array(
				'view' => 'events_tab_view', 
				'data' => array(
					'events' => $events, 
					'actor_id' => $actor_id
				)
			);
		} elseif ($tab == 'resources') {
			if($actor['Inside_object_ID'] === NULL) {
				$this->Load_model("Location_model");
				$resources = $this->Location_model->Get_location_resources($actor['Location_ID']);
				$this->Load_model("Species_model");
				$species = $this->Species_model->Get_location_species($actor['Location_ID']);
				$tab_view = array(
					'view' => 'resources_tab_view', 
					'data' => array(
						'resources' => $resources, 
						'species' => $species, 
						'actor_id' => $actor_id
					)
				);
			}
		} elseif ($tab == 'projects') {
			$this->Load_model("Project_model");
			$this->Load_model("Species_model");
			$projects = $this->Project_model->Get_projects($actor_id);
			$hunts = $this->Species_model->Get_hunts($actor_id);
			$recipe_list = $this->Project_model->Get_recipes_without_nature_resource();
			$recipe_selection_view = $this->Load_view('recipe_selection_view', array('recipe_list' => $recipe_list, 'actor_id' => $actor_id), true);
			$tab_view = array(
				'view' => 'projects_tab_view', 
				'data' => array(
					'hunts' => $hunts, 
					'projects' => $projects, 
					'actor_id' => $actor_id, 
					'recipe_selection_view' => $recipe_selection_view
				)
			);
		} elseif ($tab == 'inventory') {
			$inventory_ids = $this->Actor_model->Get_actor_and_location_inventory($actor_id);
			$this->Load_model('Inventory_model');
			$actor_inventory = $this->Inventory_model->Get_inventory($inventory_ids['Actor_inventory']);
			$location_inventory = $this->Inventory_model->Get_inventory($inventory_ids['Location_inventory']);
			
			$actor_inventory_view = array(
				'view' => 'inventory_view', 
				'data' => array(
					'inventory_title' => 'Actor inventory',
					'inventory_id' => $inventory_ids['Actor_inventory'],
					'inventory' => $actor_inventory, 
					'actor_id' => $actor_id
				)
			);

			$location_inventory_view = array(
				'view' => 'inventory_view', 
				'data' => array(
					'inventory_title' => 'Location inventory',
					'inventory_id' => $inventory_ids['Location_inventory'],
					'inventory' => $location_inventory, 
					'actor_id' => $actor_id
				)
			);
			
			$tab_view = array(
				'view' => 'inventory_tab_view', 
				'data' => array(
					'inventory_ids' => $inventory_ids,
					'actor_inventory' => $actor_inventory, 
					'location_inventory' => $location_inventory, 
					'actor_inventory_view' => $actor_inventory_view,
					'location_inventory_view' => $location_inventory_view,
					'actor_id' => $actor_id
				)
			);
		}
		
		return array(
			'type' => 'view', 
			'view' => 'actor_view', 
			'data' => array(
				'tab' => $tab,
				'actor_id' => $actor_id,
				'tab_view' => $tab_view,
				'time' => $time,
				'actor' => $actor,
				'minutes_to_next_update' => $minutes_to_next_update
			)
		);
	}
	
	public function Change_actor_name() {
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			return $this->Json_response_not_logged_in();
		}
		$actor_id = $this->Input_post('actor');
		$named_actor_id = $this->Input_post('named_actor');
		$new_name = $this->Input_post('name');
		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($this->Session_get('userid'), $actor_id)) {
			return $this->Json_response_not_your_actor();
		}
		$r = $this->Actor_model->Change_actor_name($actor_id, $named_actor_id, $new_name);
		if($r == false) {
			return array(
				'type' => 'json',
				'data' => array(
					'success' => false, 
					'reason' => 'Could not change actor name'
				)
			);
		}
		else {
			if(strlen($new_name) == 0) {
				return array(
					'type' => 'json',
					'data' => array(
						'success' => true, 
						'data' => 'Unnamed actor'
					)
				);
			}
			else {
				return array(
					'type' => 'json',
					'data' => array(
						'success' => true, 
						'data' => $new_name
					)
				);
			}
		}
	}

	public function Speak() {
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			return $this->Json_response_not_logged_in();
		}
		$actor_id = $this->Input_post('actor');
		$message = $this->Input_post('message');
		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($this->Session_get('userid'), $actor_id)) {
			return $this->Json_response_not_your_actor();
		}
		
		$this->Load_model('Event_model');
		$r = $this->Event_model->Save_event('{LNG_Actor_said}', $actor_id, NULL, $message);
		if($r == false) {
			return array(
				'type' => 'json',
				'data' => array(
					'success' => false, 
					'reason' => 'Could not save your message'
				)
			);
		}
		else {
			return $this->Json_response_success();
		}
	}
	
	function Natural_resource_dialog() {
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			$this->Json_response_not_logged_in();
		}

		$this->Load_model('Project_model');
		
		$actor_id = $this->Input_post('actor_id');
		$resource_id = $this->Input_post('resource');

		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($this->Session_get('userid'), $actor_id)) {
			return $this->Json_response_not_your_actor();
		}

		$recipe_list = $this->Project_model->Get_recipes_with_nature_resource($actor_id, $resource_id);

		return array(
			'type' => 'json',
			'view' => 'single_view_json',
			'data' => array(
				'success' => true,
				'html' => array(
					'view' => 'recipe_selection_view',
					'data' => array(
						'recipe_list' => $recipe_list, 
						'actor_id' => $actor_id
					)
				)
			)
		);
	}
	
	function Start_project_form() {
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			return $this->Json_response_not_logged_in();
		}
		
		$recipe_id = $this->Input_post('recipe_id');
		$actor_id = $this->Input_post('actor_id');

		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($this->Session_get('userid'), $actor_id)) {
			return $this->Json_response_not_your_actor();
		}

		$this->Load_model('Project_model');
		$recipe = $this->Project_model->Get_recipe($recipe_id);
		
		return array(
			'type' => 'json',
			'view' => 'single_view_json',
			'data' => array(
				'success' => true,
				'html' => array(
					'view' => 'start_project_view',
					'data' => array(
						'recipe' => $recipe, 
						'actor_id' => $actor_id
					)
				)
			)
		);
	}

	function Start_project() {
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			return $this->Json_response_not_logged_in();
		}
		
		$recipe_id = $this->Input_post('recipe_id');
		$actor_id = $this->Input_post('actor_id');
		$supply = $this->Input_post('supply') == "true";
		$cycles = intval($this->Input_post('cycles'));
		if($cycles < 1) {
			$cycles = 1;
		}

		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($this->Session_get('userid'), $actor_id)) {
			return $this->Json_response_not_your_actor();
		}

		$this->Load_model('Project_model');
		$success = $this->Project_model->Start_project($actor_id, $recipe_id, $supply, $cycles);

		return array(
			'type' => 'json',
			'data' => array(
				'success' => $success
			)
		);
	}

	function Join_project() {
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			return $this->Json_response_not_logged_in();
		}
		
		$project_id = $this->Input_post('project_id');
		$actor_id = $this->Input_post('actor_id');

		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($this->Session_get('userid'), $actor_id)) {
			return $this->Json_response_not_your_actor();
		}

		$this->Load_model('Project_model');
		$success = $this->Project_model->Join_project($actor_id, $project_id);

		return array(
			'type' => 'json',
			'data' => array(
				'success' => $success
			)
		);
	}

	function Leave_project() {
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			return $this->Json_response_not_logged_in();
		}
		
		$actor_id = $this->Input_post('actor_id');

		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($this->Session_get('userid'), $actor_id)) {
			return $this->Json_response_not_your_actor();
		}

		$this->Load_model('Project_model');
		$success = $this->Project_model->Leave_project($actor_id);

		return array(
			'type' => 'json',
			'data' => array(
				'success' => $success
			)
		);
	}

	private function Get_neighbouring_locations($actor_id) {
		$this->Load_model("Location_model");
		$locations = $this->Location_model->Get_neighbouring_locations($actor_id);
		$east = false;
		$west = false;
		$north = false;
		$south = false;
		foreach ($locations as &$location) {
			if($location['x'] == 1 && $location['y'] == 0)
				$east = true;
			if($location['x'] == -1 && $location['y'] == 0)
				$west = true;
			if($location['x'] == 0 && $location['y'] == 1)
				$south = true;
			if($location['x'] == 0 && $location['y'] == -1)
				$north = true;
		}

		if(!$east) {
    		$locations[] = array(
    			'ID' => 'east',
    			'x' => 1,
    			'y' => 0,
    			'Name' => 'Unnamed location'
    		);
		}
		if(!$west) {
    		$locations[] = array(
    			'ID' => 'west',
    			'x' => -1,
    			'y' => 0,
    			'Name' => 'Unnamed location'
    		);
		}
		if(!$south) {
    		$locations[] = array(
    			'ID' => 'south',
    			'x' => 0,
    			'y' => 1,
    			'Name' => 'Unnamed location'
    		);
		}
		if(!$north) {
    		$locations[] = array(
    			'ID' => 'north',
    			'x' => 0,
    			'y' => -1,
    			'Name' => 'Unnamed location'
    		);
		}

		foreach ($locations as &$location) {
			if(!$location['Name'])
				$location['Name'] = 'Unnamed location';
			$location['Direction'] = 90+rad2deg(atan2($location['y'], $location['x']));
			if($location['Direction'] < 0) {
				$location['Direction'] += 360;
			}
			if($location['Direction'] > 360) {
				$location['Direction'] -= 360;
			}
			if($location['Direction'] < 22.5 || $location['Direction'] >= 337.5)
				$location['Compass'] = 'N';
			else if($location['Direction'] < 22.5+45)
				$location['Compass'] = 'NE';
			else if($location['Direction'] < 22.5+90)
				$location['Compass'] = 'E';
			else if($location['Direction'] < 22.5+135)
				$location['Compass'] = 'SE';
			else if($location['Direction'] < 22.5+180)
				$location['Compass'] = 'S';
			else if($location['Direction'] < 22.5+225)
				$location['Compass'] = 'SW';
			else if($location['Direction'] < 22.5+270)
				$location['Compass'] = 'W';
			else if($location['Direction'] < 22.5+315)
				$location['Compass'] = 'NW';
		}
		function compare_direction($a, $b) {
			return $a['Direction'] > $b['Direction'];
		}
		unset($location);
		usort($locations, 'compare_direction');
		return $locations;
	}
	
	public function Point_at_actor() {
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			return $this->Json_response_not_logged_in();
		}
		$actor_id = $this->Input_post('actor_id');
		$pointee_id = $this->Input_post('pointee_id');
		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($this->Session_get('userid'), $actor_id)) {
			return $this->Json_response_not_your_actor();
		}
		
		$this->Load_model('Event_model');
		$r = $this->Event_model->Save_event('{LNG_Actor_pointed}',$actor_id, $pointee_id);
		if($r == false) {
			return array(
				'type' => 'json',
				'data' => array(
					'success' => false, 
					'reason' => 'Could not save your message'
				)
			);
		}
		else {
			return $this->Json_response_success();
		}
	}

	public function Attack_actor() {
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			return $this->Json_response_not_logged_in();
		}
		$actor_id = $this->Input_post('actor_id');
		$attacked_actor_id = $this->Input_post('attacked_actor_id');
		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($this->Session_get('userid'), $actor_id)) {
			return $this->Json_response_not_your_actor();
		}
		
		$this->Load_model('Event_model');
		$r = $this->Event_model->Save_event('{LNG_Actor_attacked}',$actor_id, $attacked_actor_id);
		if($r == false) {
			return array(
				'type' => 'json',
				'data' => array(
					'success' => false, 
					'reason' => 'Could not save'
				)
			);
		}
		else {
			return $this->Json_response_success();
		}
	}

	public function Whisper() {
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			$this->Json_response_not_logged_in();
		}
		$actor_id = $this->Input_post('actor_id');
		$whispree_id = $this->Input_post('whispree_id');
		$message = $this->Input_post('message');
		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($this->Session_get('userid'), $actor_id)) {
			return $this->Json_response_not_your_actor();
		}
		
		$this->Load_model('Event_model');
		$r = $this->Event_model->Save_event('{LNG_Actor_whispered}', $actor_id, $whispree_id, $message, NULL, NULL, true);
		if($r == false) {
			return array(
				'type' => 'json',
				'data' => array(
					'success' => false, 
					'reason' => 'Could not save your message'
				)
			);
		}
		else {
			return $this->Json_response_success();
		}
	}
	
	public function Show_project_details() {
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			$this->Json_response_not_logged_in();
		}

		$actor_id = $this->Input_post('actor_id');
		$project_id = $this->Input_post('project_id');
		
		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($this->Session_get('userid'), $actor_id)) {
			return $this->Json_response_not_your_actor();
		}

		$this->Load_model('Project_model');
		$project = $this->Project_model->Get_project($project_id, $actor_id);

		return array(
			'type' => 'json',
			'view' => 'single_view_json',
			'data' => array(
				'success' => true,
				'html' => array(
					'view' => 'project_details_view',
					'data' => array(
						'actor_id' => $actor_id, 
						'project' => $project
					)
				)
			)
		);
	}

	public function Show_hunt_details() {
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			$this->Json_response_not_logged_in();
		}

		$actor_id = $this->Input_post('actor_id');
		$hunt_id = $this->Input_post('hunt_id');
		
		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($this->Session_get('userid'), $actor_id)) {
			return $this->Json_response_not_your_actor();
		}

		$this->Load_model('Species_model');
		$hunt = $this->Species_model->Get_hunt($actor_id, $hunt_id);

		return array(
			'type' => 'json',
			'view' => 'single_view_json',
			'data' => array(
				'success' => true,
				'html' => array(
					'view' => 'hunt_details_view',
					'data' => array(
						'actor_id' => $actor_id, 
						'hunt' => $hunt
					)
				)
			)
		);
	}
	
	public function Supply_project() {
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			$this->Json_response_not_logged_in();
		}

		$actor_id = $this->Input_post('actor_id');
		$project_id = $this->Input_post('project_id');
		
		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($this->Session_get('userid'), $actor_id)) {
			return $this->Json_response_not_your_actor();
		}

		$this->Load_model('Project_model');
		$success = $this->Project_model->Supply_project($project_id, $actor_id);
		
		return array(
			'type' => 'json',
			'data' => array(
				'success' => $success
			)
		);
	}

	public function Cancel_project() {
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			$this->Json_response_not_logged_in();
		}

		$actor_id = $this->Input_post('actor_id');
		$project_id = $this->Input_post('project_id');
		
		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($this->Session_get('userid'), $actor_id)) {
			return $this->Json_response_not_your_actor();
		}

		$this->Load_model('Project_model');
		$success = $this->Project_model->Cancel_project($project_id, $actor_id);
		
		return array(
			'type' => 'json',
			'data' => array(
				'success' => $success
			)
		);
	}

	public function Start_hunt() {
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			$this->Json_response_not_logged_in();
		}

		$actor_id = $this->Input_post('actor_id');
		$hours = $this->Input_post('hours');
		$species = $this->Input_post('species');
		
		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($this->Session_get('userid'), $actor_id)) {
			return $this->Json_response_not_your_actor();
		}

		$this->Load_model('species_model');
		$result = $this->species_model->Start_hunt($actor_id, $hours, $species);

		return array(
			'type' => 'json',
			'data' => $result
		);
	}

	public function Join_hunt() {
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			$this->Json_response_not_logged_in();
		}

		$actor_id = $this->Input_post('actor_id');
		$hunt_id = $this->Input_post('hunt_id');
		
		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($this->Session_get('userid'), $actor_id)) {
			return $this->Json_response_not_your_actor();
		}

		$this->Load_model('species_model');
		$result = $this->species_model->Join_hunt($actor_id, $hunt_id);

		return array(
			'type' => 'json',
			'data' => $result
		);
	}

	public function Leave_hunt() {
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			$this->Json_response_not_logged_in();
		}

		$actor_id = $this->Input_post('actor_id');
		
		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($this->Session_get('userid'), $actor_id)) {
			return $this->Json_response_not_your_actor();
		}

		$this->Load_model('species_model');
		$result = $this->species_model->Leave_hunt($actor_id);

		return array(
			'type' => 'json',
			'data' => $result
		);
	}

	public function Enter_object() {
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			$this->Json_response_not_logged_in();
		}

		$actor_id = $this->Input_post('actor_id');
		$object_id = $this->Input_post('object_id');
		
		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($this->Session_get('userid'), $actor_id)) {
			return $this->Json_response_not_your_actor();
		}

		$result = $this->Actor_model->Enter_object($actor_id, $object_id);

		return array(
			'type' => 'json',
			'data' => $result
		);
	}

	public function Leave_object() {
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			$this->Json_response_not_logged_in();
		}

		$actor_id = $this->Input_post('actor_id');
		
		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($this->Session_get('userid'), $actor_id)) {
			return $this->Json_response_not_your_actor();
		}

		$actor = $this->Actor_model->Get_actor($actor_id);
		$object_id = $actor['Inside_object_ID'];
		$result = $this->Actor_model->Leave_object($actor_id);

		return array(
			'type' => 'json',
			'data' => $result
		);
	}
	
	public function Transfer_to_inventory() {
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			$this->Json_response_not_logged_in();
		}

		$actor_id = $this->Input_post('actor_id');

		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($this->Session_get('userid'), $actor_id)) {
			return $this->Json_response_not_your_actor();
		}
		
		$inventory_id = $this->Input_post('inventory_id');
		$resources = $this->Input_post('resources');
		$products = $this->Input_post('products');

		$this->Load_model('Inventory_model');
		$result = $this->Inventory_model->Transfer_to_inventory($actor_id, $inventory_id, $resources, $products);
		return array(
			'type' => 'json',
			'data' => $result
		);
	}
	
	public function Expand_inventory_product() {
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			$this->Json_response_not_logged_in();
		}

		$actor_id = $this->Input_post('actor_id');

		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($this->Session_get('userid'), $actor_id)) {
			return $this->Json_response_not_your_actor();
		}
		
		$inventory_id = $this->Input_post('inventory_id');
		$product_id = $this->Input_post('product_id');
		
		$this->Load_model('Inventory_model');
		$result = $this->Inventory_model->Get_inventory_product_objects($actor_id, $inventory_id, $product_id);
		if(!$result) {
			return array(
				'type' => 'json',
				'data' => array(
					'success' => false, 
					'reason' => 'Could not load objects'
				)
			);
		}

		return array(
			'type' => 'json',
			'view' => 'single_view_json',
			'data' => array(
				'success' => true,
				'html' => array(
					'view' => 'inventory_objects_view',
					'data' => array(
						'actor_id' => $actor_id,
						'objects' => $result,
						'product_id' => $product_id,
						'inventory_id' => $inventory_id
					)
				)
			)
		);
	}

	public function Open_container() {
		$actor_id = $this->Input_post('actor_id');

		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($this->Session_get('userid'), $actor_id)) {
			return $this->Json_response_not_your_actor();
		}
		
		$inventory_id = $this->Input_post('inventory_id');
		
		$this->Load_model('Inventory_model');
		
		if(!$this->Inventory_model->Is_inventory_accessible($actor_id, $inventory_id)) {
			return array(
				'type' => 'json',
				'data' => array(
					'success' => false,
					'reason' => 'You can not access this inventory'
				)
			);
		}
		
		$inventory = $this->Inventory_model->Get_inventory($inventory_id);
		if(!$inventory) {
			return array(
				'type' => 'json',
				'data' => array(
					'success' => false,
					'reason' => 'Could not load inventory'
				)
			);
		}

		return array(
			'type' => 'json',
			'view' => 'single_view_json',
			'data' => array(
				'success' => true,
				'html' => array(
					'view' => 'inventory_view',
					'data' => array(
						'inventory_title' => 'Object inventory',
						'inventory_id' => $inventory_id,
						'inventory' => $inventory, 
						'actor_id' => $actor_id
					)
				)
			)
		);
	}

	public function Label_object() {
		$actor_id = $_POST['actor_id'];

		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($_SESSION['userid'], $actor_id)) {
			echo json_encode(array('success' => false, 'reason' => 'Not your actor'));
			return;
		}
		
		$object_id = $_POST['object_id'];
		$label = $_POST['label'];
		
		$this->Load_model('Inventory_model');
		
		$result = $this->Inventory_model->Label_object($actor_id, $object_id, $label);

		echo json_encode($result);
	}

	public function Attach_lock() {
		$actor_id = $_POST['actor_id'];

		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($_SESSION['userid'], $actor_id)) {
			echo json_encode(array('success' => false, 'reason' => 'Not your actor'));
			return;
		}
		
		$object_id = $_POST['object_id'];
		$lock_id = $_POST['lock_id'];
		
		$this->Load_model('Inventory_model');
		
		$result = $this->Inventory_model->Attach_lock($actor_id, $object_id, $lock_id);

		echo json_encode($result);
	}

	public function Detach_lock() {
		$actor_id = $_POST['actor_id'];

		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($_SESSION['userid'], $actor_id)) {
			echo json_encode(array('success' => false, 'reason' => 'Not your actor'));
			return;
		}
		
		$object_id = $_POST['object_id'];
		$lockside = $_POST['lockside'];
		
		$this->Load_model('Inventory_model');
		
		$result = $this->Inventory_model->Detach_lock($actor_id, $object_id, $lockside);

		echo json_encode($result);
	}

	public function Lock_object() {
		$actor_id = $_POST['actor_id'];

		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($_SESSION['userid'], $actor_id)) {
			echo json_encode(array('success' => false, 'reason' => 'Not your actor'));
			return;
		}
		
		$object_id = $_POST['object_id'];
		$lockside = $_POST['lockside'];
		
		$this->Load_model('Inventory_model');
		
		$result = $this->Inventory_model->Lock_object($actor_id, $object_id, $lockside);

		echo json_encode($result);
	}

	public function Unlock_object() {
		$actor_id = $_POST['actor_id'];

		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($_SESSION['userid'], $actor_id)) {
			echo json_encode(array('success' => false, 'reason' => 'Not your actor'));
			return;
		}
		
		$object_id = $_POST['object_id'];
		$lockside = $_POST['lockside'];
		
		$this->Load_model('Inventory_model');
		
		$result = $this->Inventory_model->Unlock_object($actor_id, $object_id, $lockside);

		echo json_encode($result);
	}
}
