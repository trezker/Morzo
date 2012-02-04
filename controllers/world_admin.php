<?php
require_once "controllers/controller.php";

class World_admin extends Controller
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

		$this->Load_model('Location_model');
		$locations = $this->Location_model->Get_deficient_locations();
		
//		echo "<pre>"; 
//		var_dump($locations);
//		echo "</pre>";
		include 'views/world_admin_view.php';
	}
	
	public function Edit_location()
	{
		header('Content-type: application/json');
		if($_SESSION['admin'] != true) {
			echo json_encode(array('success' => false, 'reason' => 'Requires admin privilege'));
			return;
		}
		
		
		
		echo json_encode(array('success' => true, 'data' => 'woopie'));
	}
}

?>
