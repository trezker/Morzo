<?php
require_once "../controllers/base.php";

class Location extends Base {
	function Precondition($args) {
		$header_accept = $this->Input_header("Accept");
		$json_request = false;
		if (strpos($header_accept,'application/json') !== false) {
			$json_request = true;
			$actor_id = $this->Input_post('actor_id');
		} else {
			$actor_id = $args[0]; //actor id must always be the first arg.
		}
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			if($json_request === true) {
				return $this->Json_response_not_logged_in();
			} else {
				return array("view" => "redirect", "data" => "/");
			}
		}
		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($this->Session_get('userid'), $actor_id)) {
			if($json_request === true) {
				return $this->Json_response_not_your_actor();
			} else {
				return array("view" => "redirect", "data" => "/");
			}
		}
		return true;
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
	
	public function Change_location_name() {
		$actor_id = $this->Input_post('actor_id');
		$location_id = $this->Input_post('location');
		$new_name = $this->Input_post('name');

		$this->Load_model('Location_model');
		$location_id = $this->Get_location($actor_id, $location_id);
		if(!$location_id) {
			return array(
				'view' => 'data_json',
				'data' => array('success' => false, 'reason' => 'Location error')
			);
		}
		$r = $this->Location_model->Change_location_name($actor_id, $location_id, $new_name);
		if($r == false) {
			return array(
				'view' => 'data_json',
				'data' => array('success' => false, 'reason' => 'Could not rename location')
			);
		}
		else {
			if(strlen($new_name) == 0) {
				$new_name = 'Unnamed location';
			}
			return array(
				'view' => 'data_json',
				'data' => array('success' => true, 'reason' => $new_name)
			);
		}
	}
	
	public function Travel() {
		$actor_id = $this->Input_post('actor_id');
		$destination = $this->Input_post('destination');
		$origin = $this->Input_post('origin');
		
		$this->Load_model('Location_model');
		$destination = $this->Get_location($actor_id, $destination);
		if(!$destination) {
			return array(
				'view' => 'data_json',
				'data' => array('success' => false, 'reason' => 'Could not get destination')
			);
		}
		
		$this->Load_model('Travel_model');
		$r = $this->Travel_model->Travel($actor_id, $destination, $origin);
		if(!$r) {
			return array(
				'view' => 'data_json',
				'data' => array('success' => false, 'reason' => 'Could not initiate travel')
			);
		}
		return array(
			'view' => 'data_json',
			'data' => array('success' => true)
		);
	}
	
	public function Cancel_travel() {
		header('Content-type: application/json');
		if(!isset($_POST['actor_id'])) {
			echo json_encode(array('success' => false, 'reason' => 'No actor requested'));
			return;
		}
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($_SESSION['userid'], $_POST['actor_id'])) {
			echo json_encode(array('success' => false, 'reason' => 'Not your actor'));
			return;
		}
		
		$this->Load_model('Travel_model');
		$r = $this->Travel_model->Cancel_travel($_POST['actor_id']);
		if(!$r) {
			echo json_encode(array('success' => false, 'reason' => 'Could not cancel travel'));
			return;
		}
		echo json_encode(array('success' => true));
	}

	public function Turn_around() {
		header('Content-type: application/json');
		if(!isset($_POST['actor_id'])) {
			echo json_encode(array('success' => false, 'reason' => 'No actor requested'));
			return;
		}
		
		$this->Load_model('Travel_model');
		$r = $this->Travel_model->Turn_around($_POST['actor_id']);
		if(!$r) {
			echo json_encode(array('success' => false, 'reason' => 'Could not turn around'));
			return;
		}
		echo json_encode(array('success' => true));
	}
}
