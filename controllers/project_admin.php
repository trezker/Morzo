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
			header("Location: front");
			return;
		}
		if($_SESSION['admin'] != true) {
			echo "You need to be admin to access this page";
			return;
		}

		$this->Load_model('Project_model');
		$recipe = $this->Project_model->Get_recipe($_POST['id']);

		ob_start();
		include '../views/recipe_edit_view.php';
		$edit_recipe_view = ob_get_clean();

		echo json_encode(array('success' => true, 'data' => $edit_recipe_view));
	}
}

?>
