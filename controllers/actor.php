<?php
require_once "../controllers/controller.php";

class Actor extends Controller
{
	public function Request_actor()
	{
		header('Content-type: application/json');
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		$this->Load_model('Actor_model');
		$r = $this->Actor_model->Request_actor($_SESSION['userid']);
		if($r) {
			echo json_encode(array('success' => true));
		} else {
			echo json_encode(array('success' => false, 'reason' => 'Could not give you an actor'));
		}
	}
	
	public function Show_actor($actor_id, $tab = 'events')
	{
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			header("Location: /front");
			return;
		}
		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($_SESSION['userid'], $actor_id)) {
			die("This is not the actor you are looking for.");
		}

		$this->Load_model("Travel_model");
		$this->Update_travel($actor_id);

		$actor = $this->Actor_model->Get_actor($actor_id);
		if($actor['Name'] == NULL)
		{
			$actor['Name'] = 'Unnamed actor';
		}
		if($actor['Location'] == NULL)
		{
			$actor['Location'] = 'Unnamed location';
		}
		
		$this->Load_controller('Update');
		$time = $this->Update->Get_time_units($actor['Time']);
		
		$tab_view = '';
		if($tab == 'locations') {
			$travel = $this->Travel_model->Get_travel_info($actor_id);
			if($travel) {
				if(!$travel['OriginName'])
					$travel['OriginName'] = 'Unnamed location';
				if(!$travel['DestinationName'])
					$travel['DestinationName'] = 'Unnamed location';
			}
			$this->Load_controller('Location');
			$locations = $this->Location->Get_neighbouring_locations($actor_id);
			$tab_view = $this->Load_view('locations_tab_view', array('locations' => $locations, 'travel' => $travel, 'actor' => $actor), true);
		} elseif ($tab == 'people') {
			$actors = $this->Actor_model->Get_visible_actors($actor_id);
			$tab_view = $this->Load_view('people_tab_view', array('actors' => $actors), true);
		} elseif ($tab == 'events') {
			$this->Load_model("Event_model");
			$events = $this->Event_model->Get_events($actor_id);
			foreach ($events as $key => $event) {
				$events[$key]['Time_values'] = $this->Update->Get_time_units($event['Ingame_time']);
			}
			$tab_view = $this->Load_view('events_tab_view', array('events' => $events, 'actor_id' => $actor_id), true);
		} elseif ($tab == 'resources') {
			$this->Load_model("Location_model");
			$resources = $this->Location_model->Get_location_resources($actor['Location_ID']);
			$tab_view = $this->Load_view('resources_tab_view', array('resources' => $resources, 'actor_id' => $actor_id), true);
		} elseif ($tab == 'projects') {
			$this->Load_model("Project_model");
			$projects = $this->Project_model->Get_projects($actor_id);
			$tab_view = $this->Load_view('projects_tab_view', array('projects' => $projects), true);
		}
		
		$this->Load_view('actor_view', array('tab' => $tab, 'actor_id' => $actor_id, 'tab_view' => $tab_view, 'time' => $time, 'actor' => $actor), false);
	}
	
	public function Change_actor_name()
	{
		header('Content-type: application/json');
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		$actor_id = $_POST['actor'];
		$named_actor_id = $_POST['named_actor'];
		$new_name = $_POST['name'];
		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($_SESSION['userid'], $actor_id)) {
			echo json_encode(array('success' => false, 'reason' => 'Not your actor'));
			return;
		}
		$r = $this->Actor_model->Change_actor_name($actor_id, $named_actor_id, $new_name);
		if($r == false) {
			echo json_encode(array('success' => false, 'reason' => 'Could not change actor name'));
			return;
		}
		else {
			if(strlen($new_name) == 0) {
				echo json_encode(array('success' => true, 'data' => 'Unnamed actor'));
			}
			else {
				echo json_encode(array('success' => true, 'data' => $new_name));
			}
		}
	}

	private function Update_travel($actor_id) {
		$this->Load_model("Travel_model");

		$update = $this->Travel_model->Get_update_count();
		$travel = $this->Travel_model->Get_outdated_travel($actor_id, $update);
		if($travel) {
			$time_difference = $update - $travel['UpdateTick'];
			$dx = $travel['DestinationX'] - $travel['CurrentX'];
			$dy = $travel['DestinationY'] - $travel['CurrentY'];
			$d = sqrt($dx*$dx+$dy*$dy);
			if($d > $time_difference) {
				$move_factor = $time_difference / $d;
				$move = array(array(
					'x' => $travel['CurrentX'] + $dx * $move_factor,
					'y' => $travel['CurrentY'] + $dy * $move_factor,
					'actor' => $actor_id
				));
				$move_success = $this->Travel_model->Move($move, $update);
			} else {
				$arrive = array(array(
					'Actor' => $actor_id,
					'Destination' => $travel['DestinationID']
				));
				$arrive_success = $this->Travel_model->Arrive($arrive);
			}
		}
	}
	
	public function Speak() {
		header('Content-type: application/json');
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		$actor_id = $_POST['actor'];
		$message = $_POST['message'];
		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($_SESSION['userid'], $actor_id)) {
			echo json_encode(array('success' => false, 'reason' => 'Not your actor'));
		}
		
		$this->Load_model('Event_model');
		$r = $this->Event_model->Save_event($actor_id, NULL, '{From_actor_name} said: '.$message);
		if($r == false) {
			echo json_encode(array('success' => false, 'reason' => 'Could not save your message'));
			return;
		}
		else {
			echo json_encode(array('success' => true));
		}
	}
	
	function Natural_resource_dialog() {
		header('Content-type: application/json');
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}

		$this->Load_model('Project_model');
		
		$actor_id = $_POST['actor_id'];
		$resource_id = $_POST['resource'];

		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($_SESSION['userid'], $actor_id)) {
			echo json_encode(array('success' => false, 'reason' => 'Not your actor'));
			return;
		}

		$recipe_list = $this->Project_model->Get_recipes_with_nature_resource($actor_id, $resource_id);

		$recipe_selection_view = $this->Load_view('recipe_selection_view', array('recipe_list' => $recipe_list, 'actor_id' => $actor_id), true);
		
		echo json_encode(array('success' => true, 'data' => $recipe_selection_view));
	}
	
	function Start_project_form()
	{
		header('Content-type: application/json');
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		
		$recipe_id = $_POST['recipe_id'];
		$actor_id = $_POST['actor_id'];

		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($_SESSION['userid'], $actor_id)) {
			echo json_encode(array('success' => false, 'reason' => 'Not your actor'));
		}

		$this->Load_model('Project_model');
		$recipe = $this->Project_model->Get_recipe($recipe_id);
		
		$start_project_view = $this->Load_view('start_project_view', array('recipe' => $recipe, 'actor_id' => $actor_id), true);

		echo json_encode(array('success' => true, 'data' => $start_project_view));
	}

	function Start_project()
	{
		header('Content-type: application/json');
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		
		$recipe_id = $_POST['recipe_id'];
		$actor_id = $_POST['actor_id'];

		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($_SESSION['userid'], $actor_id)) {
			echo json_encode(array('success' => false, 'reason' => 'Not your actor'));
		}

		$this->Load_model('Project_model');
		$success = $this->Project_model->Start_project($actor_id, $recipe_id);

		echo json_encode(array('success' => $success));
	}
}
