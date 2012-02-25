<?php
require_once "../controllers/controller.php";

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
		$this->Load_view('world_admin_view', array('locations' => $locations));
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
		$location_resources = $this->Location_model->Get_location_resources($_POST['id'], 1);
		if($location['Biome_name'] === NULL)
			$location['Biome_name'] = "N/A";
		$landscapes = $this->Location_model->Get_landscapes();

		$biomes = $this->Location_model->Get_biomes();
		$biomes_view = $this->Load_view('biomes_view', array('biomes' => $biomes), true);

		$resources = $this->Location_model->Get_resources();
		$resources_view = $this->Load_view('resources_view', array('resources' => $resources), true);
		
		$location_admin_view = $this->Load_view('location_edit_view', 
												array('biomes_view' => $biomes_view,
												'landscapes' => $landscapes,
												'location' => $location,
												'resources_view' => $resources_view,
												'location_resources' => $location_resources), true);

		echo json_encode(array('success' => true, 'data' => $location_admin_view));
	}
	
	public function get_landscape_resources()
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
		$location_resources = $this->Location_model->Get_location_resources($_POST['location'], $_POST['landscape']);
		$resources = $this->Location_model->Get_resources();

		$resources_view = $this->Load_view('resources_view', array('resources' => $resources,
																'location_resources' => $location_resources), true);

		echo json_encode(array('success' => true, 'data' => $resources_view));
	}
	
	public function Add_biome()
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
		$biomes_view = $this->Load_view('biomes_view', array('biomes' => $biomes), true);

		echo json_encode(array('success' => true, 'data' => $biomes_view));
	}

	public function Add_resource()
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
		if(!is_string($_POST['name']) || $_POST['name'] == '') {
			echo json_encode(array('success' => false, 'reason' => $_POST['name'].'Must give a name'));
			return;
		}

		$this->Load_model('Location_model');
		if(!$this->Location_model->Add_resource($_POST['name'], $_POST['natural'])) {
			echo json_encode(array('success' => false, 'reason' => 'Failed to add resource'));
			return;
		}
		$resources = $this->Location_model->Get_resources();
		$resources_view = $this->Load_view('resources_view', array('resources' => $resources), true);

		echo json_encode(array('success' => true, 'data' => $resources_view));
	}

	public function Set_location_biome() {
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
		if(!is_string($_POST['location']) || $_POST['location'] == '') {
			echo json_encode(array('success' => false, 'reason' => 'Must give a location'));
			return;
		}
		if(!is_string($_POST['biome']) || $_POST['biome'] == '') {
			echo json_encode(array('success' => false, 'reason' => 'Must give a biome'));
			return;
		}
		
		$this->Load_model('Location_model');
		if(!$this->Location_model->Set_location_biome($_POST['location'], $_POST['biome'])) {
			echo json_encode(array('success' => false, 'reason' => 'Failed to set biome'));
			return;
		}

		echo json_encode(array('success' => true));
	}
	public function Add_location_resource() {
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
		if(!is_string($_POST['location']) || $_POST['location'] == '') {
			echo json_encode(array('success' => false, 'reason' => 'Must give a location'));
			return;
		}
		if(!is_string($_POST['resource']) || $_POST['resource'] == '') {
			echo json_encode(array('success' => false, 'reason' => 'Must give a resource'));
			return;
		}

		$this->Load_model('Location_model');
		if(!$this->Location_model->Add_location_resource($_POST['location'], $_POST['resource'], $_POST['landscape'])) {
			echo json_encode(array('success' => false, 'reason' => 'Failed to add resource'));
			return;
		}

		echo json_encode(array('success' => true));
	}
	public function Remove_location_resource() {
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
		if(!is_string($_POST['location']) || $_POST['location'] == '') {
			echo json_encode(array('success' => false, 'reason' => 'Must give a location'));
			return;
		}
		if(!is_string($_POST['resource']) || $_POST['resource'] == '') {
			echo json_encode(array('success' => false, 'reason' => 'Must give a resource'));
			return;
		}
		
		$this->Load_model('Location_model');
		if(!$this->Location_model->Remove_location_resource($_POST['location'], $_POST['resource'], $_POST['landscape'])) {
			echo json_encode(array('success' => false, 'reason' => 'Failed to remove resource'));
			return;
		}

		echo json_encode(array('success' => true));
	}
}

?>
