<?php
require_once "controllers/controller.php";

class World_admin extends Controller
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

		$this->Load_model('Location_model');
		$locations = $this->Location_model->Get_deficient_locations();
		include 'views/world_admin_view.php';
	}
	
	public function Edit_location()
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

		$this->Load_model('Location_model');
		$location = $this->Location_model->Get_location($_POST['id']);
		$location_resources = $this->Location_model->Get_location_resources($_POST['id']);
		if($location['Biome_name'] === NULL)
			$location['Biome_name'] = "N/A";
		$biomes = $this->Location_model->Get_biomes();
		$resources = $this->Location_model->Get_resources();
		ob_start();
		include 'views/location_edit_view.php';
		$location_admin_view = ob_get_clean();

		echo json_encode(array('success' => true, 'data' => $location_admin_view));
	}
	
	public function Add_biome()
	{
		header('Content-type: application/json');
		if($_SESSION['admin'] != true) {
			echo json_encode(array('success' => false, 'reason' => 'Requires admin privilege'));
			return;
		}
		if(!is_string($_POST['name']) || $_POST['name'] == '') {
			echo json_encode(array('success' => false, 'reason' => $_POST['name'].'Must give a name'));
			return;
		}

		$this->Load_model('Location_model');
		if(!$this->Location_model->Add_biome($_POST['name'])) {
			echo json_encode(array('success' => false, 'reason' => 'Failed to add biome'));
			return;
		}
		$biomes = $this->Location_model->Get_biomes();
		ob_start();
		include 'views/biomes_view.php';
		$biomes_view = ob_get_clean();

		echo json_encode(array('success' => true, 'data' => $biomes_view));
	}

	public function Add_resource()
	{
		header('Content-type: application/json');
		if($_SESSION['admin'] != true) {
			echo json_encode(array('success' => false, 'reason' => 'Requires admin privilege'));
			return;
		}
		if(!is_string($_POST['name']) || $_POST['name'] == '') {
			echo json_encode(array('success' => false, 'reason' => $_POST['name'].'Must give a name'));
			return;
		}

		$this->Load_model('Location_model');
		if(!$this->Location_model->Add_resource($_POST['name'])) {
			echo json_encode(array('success' => false, 'reason' => 'Failed to add resource'));
			return;
		}
		$resources = $this->Location_model->Get_resources();
		ob_start();
		include 'views/resources_view.php';
		$resources_view = ob_get_clean();

		echo json_encode(array('success' => true, 'data' => $resources_view));
	}
}

?>
