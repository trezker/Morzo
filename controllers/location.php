<?php
require_once "controllers/controller.php";

class Location extends Controller
{
	public function Get_neighbouring_locations($actor_id)
	{
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			return;
		}
		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($_SESSION['userid'], $actor_id)) {
			die("This is not the actor you are looking for.");
		}
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

		if(!$east)
		{
    		$locations[] = array(
    			'ID' => 'east',
    			'x' => 1,
    			'y' => 0,
    			'Name' => 'Unnamed location'
    		);
		}
		if(!$west)
		{
    		$locations[] = array(
    			'ID' => 'west',
    			'x' => -1,
    			'y' => 0,
    			'Name' => 'Unnamed location'
    		);
		}
		if(!$south)
		{
    		$locations[] = array(
    			'ID' => 'south',
    			'x' => 0,
    			'y' => 1,
    			'Name' => 'Unnamed location'
    		);
		}
		if(!$north)
		{
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
			if($location['Direction'] < 0)   $location['Direction'] += 360;
			if($location['Direction'] > 360) $location['Direction'] -= 360;
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
		function compare_direction($a, $b)
		{
			return $a['Direction'] > $b['Direction'];
		}
		unset($location);
		usort($locations, 'compare_direction');
		return $locations;
	}
	
	public function Location_list()
	{
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			return;
		}
		if(!isset($_POST['actor'])) {
			die("No actor requested.");
		}
		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($_SESSION['userid'], $_POST['actor'])) {
			die("This is not the actor you are looking for.");
		}
		$this->Load_model('Location_model');
		$locations = $this->Get_neighbouring_locations($_POST['actor']);
		
		include 'views/locations_view.php';
	}
	
	private function Get_location($actor_id, $location_id) {
		if(!is_numeric($location_id)) {
			$r = $this->Location_model->Create_location($actor_id, $location_id);
			if(!$r) {
				return false;
			}
			return $r;
		}
		else {
			return $location_id;
		}
	}
	
	public function Change_location_name($actor_id, $location_id, $new_name)
	{
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			return;
		}
		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($_SESSION['userid'], $actor_id)) {
			die("This is not the actor you are looking for.");
		}
		$this->Load_model('Location_model');
		$lcoation_id = $this->Get_location($actor_id, $location_id);
		if(!$location_id) {
			echo false;
			return;
		}
		$r = $this->Location_model->Change_location_name($actor_id, $location_id, $new_name);
		if($r == false)
		{
			echo false;
			return;
		}
		else
		{
			if(strlen($new_name) == 0)
			{
				echo 'Unnamed location';
			}
			else
			{
				echo $new_name;
			}
		}
	}
	
	public function Travel()
	{
		if(!isset($_POST['actor'])) {
			die("No actor requested.");
		}
		if(!isset($_POST['destination'])) {
			die("No destination requested.");
		}
		if(!isset($_POST['origin'])) {
			die("No origin provided.");
		}
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			return;
		}
		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($_SESSION['userid'], $_POST['actor'])) {
			die("This is not the actor you are looking for.");
		}
		
		$this->Load_model('Location_model');
		$destination = $this->Get_location($_POST['actor'], $_POST['destination']);
		if(!$destination) {
			echo json_encode(array("success" => false));
		}
		
		$this->Load_model('Travel_model');
		$r = $this->Travel_model->Travel($_POST['actor'], $destination, $_POST['origin']);
		echo json_encode(array("success" => $r));
	}
}

?>
