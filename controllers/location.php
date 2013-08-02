<?php
require_once "../controllers/base.php";

class Location extends Base
{
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
			return;
		}
		$this->Load_model('Location_model');
		$location_id = $this->Get_location($actor_id, $location_id);
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
			return;
		}
		if(!isset($_POST['destination'])) {
			echo json_encode(array('success' => false, 'reason' => 'No destination requested'));
			return;
		}
		if(!isset($_POST['origin'])) {
			echo json_encode(array('success' => false, 'reason' => 'No origin provided'));
			return;
		}
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($_SESSION['userid'], $_POST['actor'])) {
			echo json_encode(array('success' => false, 'reason' => 'Not your actor'));
			return;
		}
		
		$this->Load_model('Location_model');
		$destination = $this->Get_location($_POST['actor'], $_POST['destination']);
		if(!$destination) {
			echo json_encode(array('success' => false, 'reason' => 'Could not get destination'));
			return;
		}
		
		$this->Load_model('Travel_model');
		$r = $this->Travel_model->Travel($_POST['actor'], $destination, $_POST['origin']);
		if(!$r) {
			echo json_encode(array('success' => false, 'reason' => 'Could not initiate travel'));
			return;
		}
		echo json_encode(array('success' => true));
	}
	
	public function Cancel_travel() {
		header('Content-type: application/json');
		if(!isset($_POST['actor'])) {
			echo json_encode(array('success' => false, 'reason' => 'No actor requested'));
			return;
		}
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($_SESSION['userid'], $_POST['actor'])) {
			echo json_encode(array('success' => false, 'reason' => 'Not your actor'));
			return;
		}
		
		$this->Load_model('Travel_model');
		$r = $this->Travel_model->Cancel_travel($_POST['actor']);
		if(!$r) {
			echo json_encode(array('success' => false, 'reason' => 'Could not cancel travel'));
			return;
		}
		echo json_encode(array('success' => true));
	}

	public function Turn_around() {
		header('Content-type: application/json');
		if(!isset($_POST['actor'])) {
			echo json_encode(array('success' => false, 'reason' => 'No actor requested'));
			return;
		}
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($_SESSION['userid'], $_POST['actor'])) {
			echo json_encode(array('success' => false, 'reason' => 'Not your actor'));
			return;
		}
		
		$this->Load_model('Travel_model');
		$r = $this->Travel_model->Turn_around($_POST['actor']);
		if(!$r) {
			echo json_encode(array('success' => false, 'reason' => 'Could not turn around'));
			return;
		}
		echo json_encode(array('success' => true));
	}
}
