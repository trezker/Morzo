<?php
require_once "../controllers/controller.php";

class Project_admin extends Controller
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

		$this->Load_model('Project_model');
		$recipes = $this->Project_model->Get_recipes();
		include '../views/project_admin_view.php';
	}
	
	public function edit_recipe() {
		header('Content-type: application/json');
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		if($_SESSION['admin'] != true) {
			echo "You need to be admin to access this page";
			return;
		}

		$this->Load_model('Project_model');
		$recipe = $this->Project_model->Get_recipe($_POST['id']);
		$this->Load_model('Location_model');
		$resources = $this->Location_model->Get_resources();

		ob_start();
		include '../views/recipe_edit_view.php';
		$edit_recipe_view = ob_get_clean();

		echo json_encode(array('success' => true, 'data' => $edit_recipe_view));
	}

	public function save_recipe() {
		header('Content-type: application/json');
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		if($_SESSION['admin'] != true) {
			echo "You need to be admin to access this page";
			return;
		}

		$data = array();
		$data['recipe'] = array(
				'id' => $_POST['id'],
				'name' => $_POST['name'],
				'cycle_time' => $_POST['cycle_time'],
				'allow_fraction_output' => $_POST['allow_fraction_output'],
				'require_full_cycle' => $_POST['require_full_cycle'],
			);
		
		$data['outputs'] = array();
		foreach($_POST['outputs'] as $o) {
			$data['outputs'][] = array(
					'id' => $o['id'],
					'amount' => $o['amount'],
					'resource_id' => $o['resource']
				);
		}

		$this->Load_model('Project_model');
		$id = $this->Project_model->Save_recipe($data);

		if($id != -1) {
			$recipe = $this->Project_model->Get_recipe($id);
		} else {
			$recipe = array();
			$recipe['recipe'] = array(
					'ID' => -1,
					'Name' => '',
					'Cycle_time' => 1,
					'Allow_fraction_output' => 1,
					'Require_full_cycle' => 1
				);
		}

		$this->Load_model('Location_model');
		$resources = $this->Location_model->Get_resources();

		ob_start();
		include '../views/recipe_edit_view.php';
		$edit_recipe_view = ob_get_clean();

		echo json_encode(array('success' => true, 'data' => $edit_recipe_view, 'id' => $id));
	}
}

?>
