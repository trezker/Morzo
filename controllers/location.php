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
	
	public function Change_location_name()
	{
		header('Content-type: application/json');
		$actor_id = $_POST['actor'];
		$location_id = $_POST['location'];
		$new_name = $_POST['name'];
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($_SESSION['userid'], $actor_id)) {
			echo json_encode(array('success' => false, 'reason' => 'Not your actor'));
		}
		$this->Load_model('Location_model');
		$lcoation_id = $this->Get_location($actor_id, $location_id);
		if(!$location_id) {
			echo json_encode(array('success' => false, 'reason' => 'Location error'));
			return;
		}
		$r = $this->Location_model->Change_location_name($actor_id, $location_id, $new_name);
		if($r == false)
		{
			echo json_encode(array('success' => false, 'reason' => 'Could not rename location'));
			return;
		}
		else
		{
			if(strlen($new_name) == 0) {
				echo json_encode(array('success' => true, 'data' => 'Unnamed location'));
			}
			else {
				echo json_encode(array('success' => true, 'data' => $new_name));
			}
		}
	}
	
	public function Travel()
	{
		header('Content-type: application/json');
		if(!isset($_POST['actor'])) {
			echo json_encode(array('success' => false, 'reason' => 'No actor requested'));
		}
		if(!isset($_POST['destination'])) {
			echo json_encode(array('success' => false, 'reason' => 'No destination requested'));
		}
		if(!isset($_POST['origin'])) {
			echo json_encode(array('success' => false, 'reason' => 'No origin provided'));
		}
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($_SESSION['userid'], $_POST['actor'])) {
			echo json_encode(array('success' => false, 'reason' => 'Not your actor'));
		}
		
		$this->Load_model('Location_model');
		$destination = $this->Get_location($_POST['actor'], $_POST['destination']);
		if(!$destination) {
			echo json_encode(array('success' => false, 'reason' => 'Could not get destination'));
		}
		
		$this->Load_model('Travel_model');
		$r = $this->Travel_model->Travel($_POST['actor'], $destination, $_POST['origin']);
		if(!$r) {
			echo json_encode(array('success' => false, 'reason' => 'Could not initiate travel'));
		}
		echo json_encode(array('success' => true));
	}
}

?>
