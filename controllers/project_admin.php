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
		$this->Load_model('Resource_model');
		$resources = $this->Resource_model->Get_resources();
		$this->Load_model('Product_model');
		$products = $this->Product_model->Get_products();
		$this->Load_model('Category_model');
		$categories = $this->Category_model->Get_categories();
		$this->Load_view('project_admin_view', array(
														'recipes' => $recipes, 
														'resources' => $resources, 
														'products' => $products,
														'categories' => $categories
													));
	}
	
	public function edit_recipe() {
		header('Content-type: application/json');
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		if($_SESSION['admin'] != true) {
			echo json_encode(array('success' => false, 'reason' => 'Not admin'));
			return;
		}

		$this->Load_model('Project_model');
		$recipe = $this->Project_model->Get_recipe($_POST['id']);
		$this->Load_model('Resource_model');
		$resources = $this->Resource_model->Get_resources();
		$measures = $this->Resource_model->Get_measures();
		$this->Load_model('Product_model');
		$products = $this->Product_model->Get_products();

		if($recipe['recipe'] == false) {
			$recipe['recipe'] = array(
					'ID' => '-1',
					'Name' => '',
					'Cycle_time' => '1',
					'Allow_fraction_output' => '1',
					'Require_full_cycle' => '1'
				);
		}
		$edit_recipe_view = $this->Load_view('recipe_edit_view',array(	'resources' => $resources,
																		'products' => $products,
																		'measures' => $measures,
																		'recipe' => $recipe), true);

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
			echo json_encode(array('success' => false, 'reason' => 'Not admin'));
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
		if(is_array($_POST['outputs'])) {
			foreach($_POST['outputs'] as $o) {
				$data['outputs'][] = array(
						'id' => $o['id'],
						'amount' => $o['amount'],
						'measure' => $o['measure'],
						'resource_id' => $o['resource']
					);
			}
		}

		$data['inputs'] = array();
		if(is_array($_POST['inputs'])) {
			foreach($_POST['inputs'] as $i) {
				$data['inputs'][] = array(
						'id' => $i['id'],
						'amount' => $i['amount'],
						'measure' => $i['measure'],
						'resource_id' => $i['resource'],
						'from_nature' => $i['from_nature']
					);
			}
		}

		$data['product_outputs'] = array();
		if(is_array($_POST['product_outputs'])) {
			foreach($_POST['product_outputs'] as $o) {
				$data['product_outputs'][] = array(
						'id' => $o['id'],
						'amount' => $o['amount'],
						'product_id' => $o['product']
					);
			}
		}

		$data['product_inputs'] = array();
		if(is_array($_POST['product_inputs'])) {
			foreach($_POST['product_inputs'] as $i) {
				$data['product_inputs'][] = array(
						'id' => $i['id'],
						'amount' => $i['amount'],
						'product_id' => $i['product'],
					);
			}
		}

		$this->Load_model('Project_model');
		$id = $this->Project_model->Save_recipe($data);
		
		if($id == false) {
			$id = $_POST['id'];
		}

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
		echo json_encode(array('success' => true, 'id' => $id));
	}

	public function remove_recipe_output() {
		header('Content-type: application/json');
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		if($_SESSION['admin'] != true) {
			echo json_encode(array('success' => false, 'reason' => 'Not admin'));
			return;
		}

		$this->Load_model('Project_model');
		$success = $this->Project_model->Remove_recipe_output($_POST['recipe_id'], $_POST['id']);

		echo json_encode(array('success' => $success));
	}

	public function add_recipe_output() {
		header('Content-type: application/json');
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		if($_SESSION['admin'] != true) {
			echo json_encode(array('success' => false, 'reason' => 'Not admin'));
			return;
		}

		$this->Load_model('Project_model');
		$r = $this->Project_model->Add_recipe_output($_POST['recipe_id'], $_POST['resource_id'], $_POST['measure_id'], 1);

		echo json_encode($r);
	}

	public function remove_recipe_input() {
		header('Content-type: application/json');
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		if($_SESSION['admin'] != true) {
			echo json_encode(array('success' => false, 'reason' => 'Not admin'));
			return;
		}

		$this->Load_model('Project_model');
		$success = $this->Project_model->Remove_recipe_input($_POST['recipe_id'], $_POST['id']);

		echo json_encode(array('success' => $success));
	}

	public function add_recipe_input() {
		header('Content-type: application/json');
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		if($_SESSION['admin'] != true) {
			echo json_encode(array('success' => false, 'reason' => 'Not admin'));
			return;
		}

		$this->Load_model('Project_model');
		$r = $this->Project_model->Add_recipe_input($_POST['recipe_id'], $_POST['resource_id'], $_POST['measure_id'], 1);

		echo json_encode($r);
	}

	public function edit_resource() {
		header('Content-type: application/json');
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		if($_SESSION['admin'] != true) {
			echo json_encode(array('success' => false, 'reason' => 'Not admin'));
			return;
		}

		$this->Load_model('Resource_model');
		$r = $this->Resource_model->Get_resource($_POST['id']);
		$resource = $r['resource'];
		$categories = $r['categories'];
		$measures = $this->Resource_model->Get_measures();

		$this->Load_model('Category_model');
		$category_list = $this->Category_model->Get_categories();

		$categorytemplate =	$this->get_category_template();

		if($resource == false) {
			$resource = array(
					'ID' => '-1',
					'Name' => '',
					'Measure' => '1',
					'Mass' => '',
					'Volume' => '',
					'Is_natural' => false
				);
		}
		$edit_resource_view = $this->Load_view('resource_edit_view',array(	'resource' => $resource, 
																			'measures' => $measures,
																			'categories' => $categories,
																			'category_list' => $category_list,
																			'categorytemplate' => $categorytemplate
																		), true);

		echo json_encode(array('success' => true, 'data' => $edit_resource_view));
	}

	public function save_resource() {
		header('Content-type: application/json');
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		if($_SESSION['admin'] != true) {
			echo json_encode(array('success' => false, 'reason' => 'Not admin'));
			return;
		}

		$this->Load_model('Resource_model');
		$result = $this->Resource_model->Save_resource($_POST);

		echo json_encode(array('success' => $result));
	}

	public function edit_product() {
		header('Content-type: application/json');
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		if($_SESSION['admin'] != true) {
			echo json_encode(array('success' => false, 'reason' => 'Not admin'));
			return;
		}

		$this->Load_model('Product_model');
		$r = $this->Product_model->Get_product($_POST['id']);
		$product = $r['product'];
		$categories = $r['categories'];
		if($product == false) {
			$product = array(
					'ID' => '-1',
					'Name' => '',
					'Mass' => '',
					'Volume' => '',
					'Rot_rate' => ''
				);
		}

		$this->Load_model('Category_model');
		$category_list = $this->Category_model->Get_categories();

		$categorytemplate =	$this->get_category_template();
		
		$edit_product_view = $this->Load_view('product_edit_view',
												array(
													'product' => $product,
													'categories' => $categories,
													'category_list' => $category_list,
													'categorytemplate' => $categorytemplate
												), 
												true);

		echo json_encode(array('success' => true, 'data' => $edit_product_view));
	}
	
	public function get_category_template() {
		$categorytemplate =	'<span style="margin-right: 5px;" class="category" id="category_{ID}" data-category_id="{ID}">
								{Name} 
								<span class="action" onclick="remove_category({ID})">X</span>
							</span>';
		return $categorytemplate;
	}
	
	public function add_category() {
		header('Content-type: application/json');
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		if($_SESSION['admin'] != true) {
			echo json_encode(array('success' => false, 'reason' => 'Not admin'));
			return;
		}

		$category_id = $_POST['category_id'];

		$this->Load_model('Category_model');
		$category = $this->Category_model->Get_category($category_id);
		
		$success = true;
		if($category === false)
			$success = false;

		$categorytemplate =	$this->get_category_template();
		$categoryhtml = expand_template($categorytemplate, $category);

		echo json_encode(array('success' => $success, 'html' => $categoryhtml));
	}

	public function save_product() {
		header('Content-type: application/json');
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		if($_SESSION['admin'] != true) {
			echo json_encode(array('success' => false, 'reason' => 'Not admin'));
			return;
		}

		$this->Load_model('Product_model');
		$result = $this->Product_model->Save_product($_POST);

		echo json_encode(array('success' => $result));
	}

	public function edit_category() {
		header('Content-type: application/json');
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		if($_SESSION['admin'] != true) {
			echo json_encode(array('success' => false, 'reason' => 'Not admin'));
			return;
		}

		$this->Load_model('Category_model');
		$category = $this->Category_model->Get_category($_POST['id']);
		$food = $this->Category_model->Get_food_properties($_POST['id']);

		if($category == false) {
			$category = array(
					'ID' => '-1',
					'Name' => ''
				);
		}
		$edit_category_view = $this->Load_view('category_edit_view',
												array(
													'category' => $category,
													'food' => $food
												), 
												true);

		echo json_encode(array('success' => true, 'data' => $edit_category_view));
	}

	public function save_category() {
		header('Content-type: application/json');
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		if($_SESSION['admin'] != true) {
			echo json_encode(array('success' => false, 'reason' => 'Not admin'));
			return;
		}

		$this->Load_model('Category_model');
		$result = $this->Category_model->Save_category($_POST);

		echo json_encode(array('success' => $result));
	}

	public function remove_recipe_product_output() {
		header('Content-type: application/json');
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		if($_SESSION['admin'] != true) {
			echo json_encode(array('success' => false, 'reason' => 'Not admin'));
			return;
		}

		$this->Load_model('Project_model');
		$success = $this->Project_model->Remove_recipe_product_output($_POST['recipe_id'], $_POST['id']);

		echo json_encode(array('success' => $success));
	}

	public function add_recipe_product_output() {
		header('Content-type: application/json');
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		if($_SESSION['admin'] != true) {
			echo json_encode(array('success' => false, 'reason' => 'Not admin'));
			return;
		}

		$this->Load_model('Project_model');
		$r = $this->Project_model->Add_recipe_product_output($_POST['recipe_id'], $_POST['product_id'], 1);

		echo json_encode($r);
	}

	public function add_recipe_product_input() {
		header('Content-type: application/json');
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		if($_SESSION['admin'] != true) {
			echo json_encode(array('success' => false, 'reason' => 'Not admin'));
			return;
		}

		$this->Load_model('Project_model');
		$r = $this->Project_model->Add_recipe_product_input($_POST['recipe_id'], $_POST['product_id'], 1);

		echo json_encode($r);
	}

	public function remove_recipe_product_input() {
		header('Content-type: application/json');
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		if($_SESSION['admin'] != true) {
			echo json_encode(array('success' => false, 'reason' => 'Not admin'));
			return;
		}

		$this->Load_model('Project_model');
		$success = $this->Project_model->Remove_recipe_product_input($_POST['recipe_id'], $_POST['id']);

		echo json_encode(array('success' => $success));
	}
}
?>
