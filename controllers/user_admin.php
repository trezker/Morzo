<?php
require_once "../controllers/controller.php";

class User_admin extends Controller
{
	public function Index()
	{
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			header("Location: front");
			return;
		}
		if($_SESSION['admin'] != true) {
			echo "You need to be admin to access this page";
			return;
		}

		$this->Load_model('User_model');
		$users = $this->User_model->Get_users();
		
		$this->Load_view('user_admin_view', array('users' => $users));
	}
	
	public function Kick_user()
	{
		header('Content-type: application/json');

		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		if($_SESSION['admin'] != true) {
			echo json_encode(array('success' => false, 'reason' => 'Requires admin privilege'));
			return;
		}
		if(!is_numeric($_POST['id'])) {
			echo json_encode(array('success' => false, 'reason' => 'Must give a user id'));
			return;
		}
		
		Set_cache('kick_user_'.$_POST['id'], true);
		
		echo json_encode(array('success' => true));
	}
	
	public function Ban_user()
	{
		header('Content-type: application/json');

		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		if($_SESSION['admin'] != true) {
			echo json_encode(array('success' => false, 'reason' => 'Requires admin privilege'));
			return;
		}
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

		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		if($_SESSION['admin'] != true) {
			echo json_encode(array('success' => false, 'reason' => 'Requires admin privilege'));
			return;
		}
		if(!is_numeric($_POST['id'])) {
			echo json_encode(array('success' => false, 'reason' => 'Must give a user id'));
			return;
		}
		
		$this->Load_model('User_model');
		$success = $this->User_model->Set_user_actor_limit($_POST['id'], $_POST['actor_limit']);
		
		echo json_encode(array('success' => $success));
	}
	
	public function Login_as()
	{
		header('Content-type: application/json');

		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		if($_SESSION['admin'] != true) {
			echo json_encode(array('success' => false, 'reason' => 'Requires admin privilege'));
			return;
		}
		
		$this->Load_model('User_model');
		$this->User_model->Login($_POST['id']);
		
		$_SESSION['username'] = $_POST['username'];
		$_SESSION['userid'] = $_POST['id'];
		$_SESSION['admin'] = false;

		echo json_encode(array('success' => true));
	}
}


