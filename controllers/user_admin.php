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
		
		$common_head_view = $this->Load_view('common_head_view', array());
		$this->Load_view('user_admin_view', array('users' => $users, 'common_head_view' => $common_head_view));
	}
	
	public function Kick_user() {
		header('Content-type: application/json');

		if(!is_numeric($_POST['id'])) {
			echo json_encode(array('success' => false, 'reason' => 'Must give a user id'));
			return;
		}
		
		Set_cache('kick_user_'.$_POST['id'], true);
		
		echo json_encode(array('success' => true));
	}
	
	public function Ban_user() {
		header('Content-type: application/json');

		if(!is_numeric($_POST['id'])) {
			echo json_encode(array('success' => false, 'reason' => 'Must give a user id'));
			return;
		}
		
		$this->Load_model('User_model');
		$success = $this->User_model->Set_ban($_POST['id'], $_POST['to_date']);
		
		echo json_encode(array('success' => $success));
	}
	
	public function set_user_actor_limit() {
		header('Content-type: application/json');

		if(!is_numeric($_POST['id'])) {
			echo json_encode(array('success' => false, 'reason' => 'Must give a user id'));
			return;
		}
		
		$this->Load_model('User_model');
		$success = $this->User_model->Set_user_actor_limit($_POST['id'], $_POST['actor_limit']);
		
		echo json_encode(array('success' => $success));
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


