<?php
require_once "../controllers/base.php";

class Project_admin extends Base {
	function Precondition($args) {
		$header_accept = $this->Input_header("Accept");
		$json_request = false;
		if (strpos($header_accept,'application/json') !== false) {
			$json_request = true;
		}
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			if($json_request === true) {
				return $this->Json_response_not_logged_in();
			} else {
				return array("view" => "redirect", "data" => "/");
			}
		}

		if($this->Session_get('admin') != true) {
			if($json_request === true) {
				return array(
					"view" => "data_json", 
					"data" => array('success' => false, 'reason' => "You need to be admin to access this page")
				);
			} else {
				return array("view" => "redirect", "data" => "/");
			}
		}
		return true;
	}

	public function Index() {
		$this->Load_model('Project_model');
		$recipes = $this->Project_model->Get_recipes();
		$this->Load_model('Resource_model');
		$resources = $this->Resource_model->Get_resources();
		$this->Load_model('Product_model');
		$products = $this->Product_model->Get_products();
		$this->Load_model('Category_model');
		$categories = $this->Category_model->Get_categories();
		return array(
			'view' => 'project_admin_view', 
			'data' => array(
				'recipes' => $recipes, 
				'resources' => $resources, 
				'products' => $products,
				'categories' => $categories
			)
		);
	}
	
	public function edit_recipe() {
		$this->Load_model('Project_model');
		$recipe = $this->Project_model->Get_recipe($this->Input_post('id'));
		$this->Load_model('Resource_model');
		$resources = $this->Resource_model->Get_resources();
		$measures = $this->Resource_model->Get_measures();
		$this->Load_model('Product_model');
		$products = $this->Product_model->Get_products();
		$this->Load_model('Category_model');
		$tools = $this->Category_model->Get_tool_categories();

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

		return array(
			'view' => 'single_view_json',
			'data' => array(
				'success' => true,
				'html' => array(
					'view' => 'recipe_edit_view',
					'data' => array(
						'resources' => $resources,
						'products' => $products,
						'tools' => $tools,
						'measures' => $measures,
						'recipe' => $recipe,
						'measure_descriptions' => $measure_descriptions
					)
				)
			)
		);
	}

	public function save_recipe() {
		$data = array();
		$data['recipe'] = array(
				'id' => $this->Input_post('id'),
				'name' => $this->Input_post('name'),
				'cycle_time' => $this->Input_post('cycle_time'),
				'allow_fraction_output' => $this->Input_post('allow_fraction_output'),
				'require_full_cycle' => $this->Input_post('require_full_cycle')
			);
		
		$data['outputs'] = array();
		if(null !== $this->Input_post('outputs')) {
			foreach($this->Input_post('outputs') as $o) {
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
		if(null !== $this->Input_post('inputs')) {
			foreach($this->Input_post('inputs') as $i) {
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
		if(null !== $this->Input_post('product_outputs')) {
			foreach($this->Input_post('product_outputs') as $o) {
				$data['product_outputs'][] = array(
						'id' => $o['id'],
						'amount' => $o['amount'],
						'product_id' => $o['product'],
						'remove' => (isset($o['remove']) && $o['remove'] == 'true') ? true: false
					);
			}
		}

		$data['product_inputs'] = array();
		if(null !== $this->Input_post('product_inputs')) {
			foreach($this->Input_post('product_inputs') as $i) {
				$data['product_inputs'][] = array(
						'id' => $i['id'],
						'amount' => $i['amount'],
						'product_id' => $i['product'],
						'remove' => (isset($i['remove']) && $i['remove'] == 'true') ? true: false
					);
			}
		}

		$data['tools'] = array();
		if(null !== $this->Input_post('tools')) {
			foreach($this->Input_post('tools') as $t) {
				$data['tools'][] = array(
						'id' => $t['id'],
						'category_id' => $t['category_id'],
						'remove' => (isset($t['remove']) && $t['remove'] == 'true') ? true: false
					);
			}
		}

		$this->Load_model('Project_model');
		$id = $this->Project_model->Save_recipe($data);
		
		if($id == false) {
			return array(
				'view' => 'data_json',
				'data' => array('success' => false, 'reason' => 'Failed to save')
			);
		}

		return array(
			'view' => 'data_json',
			'data' => array('success' => true)
		);
	}

	public function add_recipe_output() {
		header('Content-type: application/json');
		
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

		$this->Load_model('Category_model');
		$tool = $this->Category_model->Get_category($_POST['category_id']);
		if(!$tool) {
			echo json_encode(array('success' => false, 'reason' => 'Category not found'));
		}

		$tool['Category_Name'] = $tool['Name'];
		$tool['Category_ID'] = $tool['ID'];
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
			<span class="category" data-id="{Category_ID}">{Category_Name}</span>
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

	public function edit_resource() {
		header('Content-type: application/json');

		$this->Load_model('Resource_model');
		$r = $this->Resource_model->Get_resource($_POST['id']);
		$resource = $r['resource'];
		$categories = $r['categories'];
		$measures = $this->Resource_model->Get_measures();

		$this->Load_model('Category_model');
		$category_list = $this->Category_model->Get_categories();
		foreach($categories as $n => $category) {
			$categories[$n]["properties"] = $this->get_category_properties_template($category);
		}

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

		$this->Load_model('Resource_model');
		$result = $this->Resource_model->Save_resource($_POST);

		echo json_encode(array('success' => $result));
	}

	public function edit_product() {
		header('Content-type: application/json');

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
			$categories = array();
		}

		$this->Load_model('Category_model');
		$category_list = $this->Category_model->Get_categories();

		foreach($categories as $n => $category) {
			$categories[$n]["properties"] = $this->get_category_properties_template($category);
		}
		
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

		$category_id = $_POST['category_id'];

		$this->Load_model('Category_model');
		$category = $this->Category_model->Get_category($category_id);
		
		$success = true;
		if($category === false)
			$success = false;
		$category["Food_nutrition"] = "";
		$category["Container_mass_limit"] = "";
		$category["Container_volume_limit"] = "";
		$category["Tool_efficiency"] = "";

		$category["properties"] = $this->get_category_properties_template($category);
		$categorytemplate =	$this->get_category_template();
		$categoryhtml = expand_template($categorytemplate, $category);

		echo json_encode(array('success' => $success, 'html' => $categoryhtml));
	}

	function get_category_properties_template($category) {
		$properties = "&nbsp;";
		if($category["Name"] == "Food")
		{
			$properties = 'Nutrition <input type="text" data-property="nutrition" value="{Food_nutrition}" />';
		}
		elseif($category["Name"] == "Container")
		{
			$properties = '	Mass limit <input type="text" data-property="mass_limit" value="{Container_mass_limit}" /><br />
							Volume limit <input type="text" data-property="volume_limit" value="{Container_volume_limit}" />';
		}
		elseif($category["Is_tool"] == 1)
		{
			$properties = 'Efficiency <input type="text" data-property="efficiency" value="{Tool_efficiency}" />';
		}
		return $properties;
	}

	public function save_product() {
		header('Content-type: application/json');

		$this->Load_model('Product_model');
		$result = $this->Product_model->Save_product($_POST);

		echo json_encode(array('success' => $result));
	}

	public function edit_category() {
		header('Content-type: application/json');

		$this->Load_model('Category_model');
		$category = $this->Category_model->Get_category($_POST['id']);

		if($category == false) {
			$category = array(
					'ID' => '-1',
					'Name' => '',
					'Is_tool' => 0
				);
		}
		$edit_category_view = $this->Load_view('category_edit_view',
												array(
													'category' => $category
												), 
												true);

		echo json_encode(array('success' => true, 'data' => $edit_category_view));
	}

	public function save_category() {
		header('Content-type: application/json');

		$this->Load_model('Category_model');
		$result = $this->Category_model->Save_category($_POST);

		echo json_encode(array('success' => $result));
	}
}
?>
