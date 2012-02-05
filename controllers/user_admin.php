<?php
require_once "controllers/controller.php";

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
		
//		echo "<pre>";
//		var_dump($users);
//		echo "</pre>";
		
		include 'views/user_admin_view.php';
//		echo "As admin you will be able to access this page.";
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

		$this->Load_model('User_model');
		$user_session_id = $this->User_model->Get_session_id($_POST['id']);
		
		if(!$user_session_id) {
			echo json_encode(array('success' => false, 'reason' => 'User is not in the system'));
			return;
		}
		session_destroy();
		session_id($user_session_id);
		session_start();
		session_unset();
		session_destroy();
		
		echo json_encode(array('success' => true, 'data' => $user_session_id));
	}
}

?>
