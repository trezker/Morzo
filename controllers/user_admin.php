<?php
require_once "controllers/controller.php";

class User_admin extends Controller
{
	public function Index()
	{
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
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
}

?>
