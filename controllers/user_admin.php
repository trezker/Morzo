<?php
require_once "../controllers/base.php";

class User_admin extends Base {
	function Precondition($args) {
		$header_accept = $this->Input_header("Accept");
		$json_request = false;
		if (strpos($header_accept,'application/json') !== false) {
			$json_request = true;
		}
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			if($json_request === true) {
				return $this->Json_response_not_logged_in();
			} else {
				return array("view" => "redirect", "data" => "/");
			}
		}
		if($this->Session_get('admin') != true) {
			if($json_request === true) {
				return array(
					'view' => 'data_json',
					'data' => array(
						'success' => false,
						'reason' => 'Requires admin privilege'
					)
				);
			} else {
				return array("view" => "redirect", "data" => "/");
			}
		}
		return true;
	}

	public function Index() {
		$this->Load_model('User_model');
		$users = $this->User_model->Get_users();
		
		return array(
			'view' => 'user_admin_view',
			'data' => array('users' => $users)
		);
	}
	
	public function Kick_user() {
		$id = $this->Input_post('id');
		if(!is_numeric($id)) {
			return array(
				'view' => 'data_json',
				'data' => array('success' => false, 'reason' => 'Must give a user id')
			);
		}
		
		Set_cache('kick_user_' . $id, true);
		
		return array(
			'view' => 'data_json',
			'data' => array('success' => true)
		);
	}
	
	public function Ban_user() {
		$id = $this->Input_post('id');
		if(!is_numeric($id)) {
			return array(
				'view' => 'data_json',
				'data' => array('success' => false, 'reason' => 'Must give a user id')
			);
		}
		
		$this->Load_model('User_model');
		$success = $this->User_model->Set_ban($id, $this->Input_post('to_date'));
		
		return array(
			'view' => 'data_json',
			'data' => array('success' => $success)
		);
	}
	
	public function set_user_actor_limit() {
		$id = $this->Input_post('id');
		if(!is_numeric($id)) {
			return array(
				'view' => 'data_json',
				'data' => array('success' => false, 'reason' => 'Must give a user id')
			);
		}
		
		$this->Load_model('User_model');
		$success = $this->User_model->Set_user_actor_limit($id, $this->Input_post('actor_limit'));
		
		return array(
			'view' => 'data_json',
			'data' => array('success' => $success)
		);
	}
	
	public function Login_as() {
		header('Content-type: application/json');

		$this->Load_model('User_model');
		$this->User_model->Login($_POST['id']);
		
		$_SESSION['username'] = $_POST['username'];
		$_SESSION['userid'] = $_POST['id'];
		$_SESSION['admin'] = false;

		echo json_encode(array('success' => true));
	}
}


