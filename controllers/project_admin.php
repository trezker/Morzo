<?php
require_once "../controllers/base.php";

class Project_admin extends Base
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
		$common_head_view = $this->Load_view('common_head_view', array());
		$this->Load_view('project_admin_view', array(
														'recipes' => $recipes, 
														'resources' => $resources, 
														'products' => $products,
														'categories' => $categories,
														'common_head_view' => $common_head_view
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
		
		$measure_descriptions = $this->get_measure_descriptions();
		$resource_output_template = $this->get_resource_output_template();
		$resource_input_template = $this->get_resource_input_template();
		$product_output_template = $this->get_product_output_template();
		$product_input_template = $this->get_product_input_template();
		$tool_template = $this->get_tool_template();

		$edit_recipe_view = $this->Load_view('recipe_edit_view',array(	'resources' => $resources,
																		'products' => $products,
																		'measures' => $measures,
																		'recipe' => $recipe,
																		'measure_descriptions' => $measure_descriptions,
																		'resource_output_template' => $resource_output_template,
																		'resource_input_template' => $resource_input_template,
																		'product_output_template' => $product_output_template,
																		'product_input_template' => $product_input_template,
																		'tool_template' => $tool_template
																		), true);

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
				'require_full_cycle' => $_POST['require_full_cycle']
			);
		
		$data['outputs'] = array();
		if(isset($_POST['outputs'])) {
			foreach($_POST['outputs'] as $o) {
				$data['outputs'][] = array(
						'id' => $o['id'],
						'amount' => $o['amount'],
						'measure' => $o['measure'],
						'resource_id' => $o['resource'],
						'remove' => (isset($o['remove']) && $o['remove'] == 'true') ? true: false
					);
			}
		}

		$data['inputs'] = array();
		if(isset($_POST['inputs'])) {
			foreach($_POST['inputs'] as $i) {
				$data['inputs'][] = array(
						'id' => $i['id'],
						'amount' => $i['amount'],
						'measure' => $i['measure'],
						'resource_id' => $i['resource'],
						'from_nature' => $i['from_nature'],
						'remove' => (isset($i['remove']) && $i['remove'] == 'true') ? true: false
					);
			}
		}

		$data['product_outputs'] = array();
		if(isset($_POST['product_outputs'])) {
			foreach($_POST['product_outputs'] as $o) {
				$data['product_outputs'][] = array(
						'id' => $o['id'],
						'amount' => $o['amount'],
						'product_id' => $o['product'],
						'remove' => (isset($o['remove']) && $o['remove'] == 'true') ? true: false
					);
			}
		}

		$data['product_inputs'] = array();
		if(isset($_POST['product_inputs'])) {
			foreach($_POST['product_inputs'] as $i) {
				$data['product_inputs'][] = array(
						'id' => $i['id'],
						'amount' => $i['amount'],
						'product_id' => $i['product'],
						'remove' => (isset($i['remove']) && $i['remove'] == 'true') ? true: false
					);
			}
		}

		$data['tools'] = array();
		if(isset($_POST['tools'])) {
			foreach($_POST['tools'] as $t) {
				$data['tools'][] = array(
						'id' => $t['id'],
						'product_id' => $t['product'],
						'remove' => (isset($t['remove']) && $t['remove'] == 'true') ? true: false
					);
			}
		}

		$this->Load_model('Project_model');
		$id = $this->Project_model->Save_recipe($data);
		
		if($id == false) {
			echo json_encode(array('success' => false, 'reason' => 'Failed to save'));
			return;
		}

		echo json_encode(array('success' => true));
	}
	/*
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
	*/
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

		/*
		$this->Load_model('Project_model');
		$r = $this->Project_model->Add_recipe_output($_POST['recipe_id'], $_POST['resource_id'], $_POST['measure_id'], 1);

		echo json_encode($r);
		*/
		
		$this->Load_model('Resource_model');
		$r = $this->Resource_model->Get_resource($_POST['resource_id']);
		if(!$r) {
			echo json_encode(array('success' => false, 'reason' => 'Resource not found'));
		}
		$output = $r['resource'];
		$measure_descriptions = $this->get_measure_descriptions();
		$output['Measuredesc'] = $measure_descriptions[$output['Measure']];
		$output['Amount'] = 0;
		$output['Resource_Name'] = $output['Name'];
		$output['Measure_ID'] = $output['Measure'];
		$output['Resource_ID'] = $output['ID'];
		$output['ID'] = $_POST['new_output_id'];
		$html = expand_template($this->get_resource_output_template(), $output);
		
		echo json_encode(array('success' => true, 'html' => $html));
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

		$this->Load_model('Resource_model');
		$r = $this->Resource_model->Get_resource($_POST['resource_id']);
		if(!$r) {
			echo json_encode(array('success' => false, 'reason' => 'Resource not found'));
		}
		$output = $r['resource'];
		$measure_descriptions = $this->get_measure_descriptions();
		$output['Measuredesc'] = $measure_descriptions[$output['Measure']];
		$output['Amount'] = 0;
		$output['Resource_Name'] = $output['Name'];
		$output['Measure_ID'] = $output['Measure'];
		$output['Resource_ID'] = $output['ID'];
		$output['ID'] = $_POST['new_input_id'];
		$html = expand_template($this->get_resource_input_template(), $output);
		
		echo json_encode(array('success' => true, 'html' => $html));
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

		$this->Load_model('Product_model');
		$r = $this->Product_model->Get_product($_POST['product_id']);
		if(!$r) {
			echo json_encode(array('success' => false, 'reason' => 'Product not found'));
		}

		$output = $r['product'];
		$output['Amount'] = 0;
		$output['Product_Name'] = $output['Name'];
		$output['Product_ID'] = $output['ID'];
		$output['ID'] = $_POST['new_product_output_id'];
		$html = expand_template($this->get_product_output_template(), $output);
		
		echo json_encode(array('success' => true, 'html' => $html));
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

		$this->Load_model('Product_model');
		$r = $this->Product_model->Get_product($_POST['product_id']);
		if(!$r) {
			echo json_encode(array('success' => false, 'reason' => 'Product not found'));
		}

		$output = $r['product'];
		$output['Amount'] = 0;
		$output['Product_Name'] = $output['Name'];
		$output['Product_ID'] = $output['ID'];
		$output['ID'] = $_POST['new_product_input_id'];
		$html = expand_template($this->get_product_input_template(), $output);
		
		echo json_encode(array('success' => true, 'html' => $html));
	}

	public function add_recipe_tool() {
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
		$r = $this->Product_model->Get_product($_POST['product_id']);
		if(!$r) {
			echo json_encode(array('success' => false, 'reason' => 'Product not found'));
		}

		$tool = $r['product'];
		$tool['Product_Name'] = $tool['Name'];
		$tool['Product_ID'] = $tool['ID'];
		$tool['ID'] = $_POST['new_tool_id'];
		$html = expand_template($this->get_tool_template(), $tool);
		
		echo json_encode(array('success' => true, 'html' => $html));
	}
	
	public function get_resource_output_template() {
		return '
		<div class="output" id="resource_output_{ID}" data-id="{ID}">
			<input class="amount" type="number" value="{Amount}" />
			<span class="measuredesc" data-id="{Measure_ID}">{Measuredesc}</span>
			<span class="resource" data-id="{Resource_ID}">{Resource_Name}</span>
			<span class="action" style="float: right;" onclick="remove_output({ID})">Remove</span>
		</div>';
	}

	public function get_resource_input_template() {
		return '
		<div class="input" id="resource_input_{ID}" data-id="{ID}">
			<input class="amount" type="number" value="{Amount}" />
			<span class="measuredesc" data-id="{Measure_ID}">{Measuredesc}</span>
			<span class="resource" data-id="{Resource_ID}">{Resource_Name}</span>
			(from nature: <input type="checkbox" class="from_nature" {From_nature_checked} />)
			<span class="action" style="float: right;" onclick="remove_input({ID})">Remove</span>
		</div>';
	}

	public function get_product_output_template() {
		return '
		<div class="product_output" id="product_output_{ID}" data-id="{ID}">
			<input class="amount" type="number" value="{Amount}" />
			<span class="product" data-id="{Product_ID}">{Product_Name}</span>
			<span class="action" style="float: right;" onclick="remove_product_output({ID})">Remove</span>
		</div>';
	}

	public function get_product_input_template() {
		return '
		<div class="product_input" id="product_input_{ID}" data-id="{ID}">
			<input class="amount" type="number" value="{Amount}" />
			<span class="product" data-id="{Product_ID}">{Product_Name}</span>
			<span class="action" style="float: right;" onclick="remove_product_input({ID})">Remove</span>
		</div>';
	}

	public function get_tool_template() {
		return '
		<div class="tool" id="tool_{ID}" data-id="{ID}">
			<span class="product" data-id="{Product_ID}">{Product_Name}</span>
			<span class="action" style="float: right;" onclick="remove_tool({ID})">Remove</span>
		</div>';
	}
	
	public function get_measure_descriptions() {
		$measures = $this->Resource_model->Get_measures();
		$measure_descriptions = array();
		foreach($measures as $key => $measure) {
			$measure_descriptions[$measure['ID']] = '';
			if($measure['Name'] == 'Mass') {
				$measure_descriptions[$measure['ID']] = 'g';
			}
			elseif($measure['Name'] == 'Volume') {
				$measure_descriptions[$measure['ID']] = 'l';
			}
		}
		return $measure_descriptions;
	}

	/*
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
	*/

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
		$categorytemplate =	'<tr class="category" id="category_{ID}" data-category_id="{ID}">
								<td>{Name}</td>
								<td>{!properties}</td>
								<td>
									<a href="javascript:void(0)" class="action" onclick="remove_category({ID})">X</a>
								</td>
							</tr>';
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

		$category["properties"] = "&nbsp;";
		if($category["Name"] == "Food")
		{
			$category["properties"] = '<input type="text" data-property="nutrition" />';
		}

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
		$container = $this->Category_model->Get_container_properties($_POST['id']);

		if($category == false) {
			$category = array(
					'ID' => '-1',
					'Name' => ''
				);
		}
		$edit_category_view = $this->Load_view('category_edit_view',
												array(
													'category' => $category,
													'container' => $container
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
}
?>
