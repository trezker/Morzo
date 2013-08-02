<?php
require_once "../controllers/controller.php";

class Base extends Controller
{
	public function Before_page_load() {
		$this->Load_controller('User');
		if($this->User->Logged_in()) {
			$this->Load_model('User_model');
			$this->User_model->Update_user_activity($_SESSION['userid']);
		}
	}
}
