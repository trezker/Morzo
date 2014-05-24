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
		$this->Load_model('Resource_model');
		$r = $this->Resource_model->Get_resource($this->Input_post('resource_id'));
		if(!$r) {
			return array(
				'view' => 'data_json',
				'data' => array('success' => false, 'reason' => 'Resource not found')
			);
		}
		$output = $r['resource'];
		$measure_descriptions = $this->get_measure_descriptions();
		$output['Measuredesc'] = $measure_descriptions[$output['Measure']];
		$output['Amount'] = 0;
		$output['Resource_Name'] = $output['Name'];
		$output['Measure_ID'] = $output['Measure'];
		$output['Resource_ID'] = $output['ID'];
		$output['ID'] = $this->Input_post('new_output_id');
		
		return array(
			'view' => 'single_view_json',
			'data' => array(
				'success' => true,
				'html' => array(
					'view' => 'recipe_resource_output_view',
					'data' => $output
				)
			)
		);
	}
	
	public function add_recipe_input() {
		$this->Load_model('Resource_model');
		$r = $this->Resource_model->Get_resource($this->Input_post('resource_id'));
		if(!$r) {
			return array(
				'view' => 'data_json',
				'data' => array('success' => false, 'reason' => 'Resource not found')
			);
		}
		$output = $r['resource'];
		$measure_descriptions = $this->get_measure_descriptions();
		$output['Measuredesc'] = $measure_descriptions[$output['Measure']];
		$output['Amount'] = 0;
		$output['Resource_Name'] = $output['Name'];
		$output['Measure_ID'] = $output['Measure'];
		$output['Resource_ID'] = $output['ID'];
		$output['ID'] = $this->Input_post('new_input_id');
		
		return array(
			'view' => 'single_view_json',
			'data' => array(
				'success' => true,
				'html' => array(
					'view' => 'recipe_resource_input_view',
					'data' => $output
				)
			)
		);
	}

	public function add_recipe_product_output() {
		$this->Load_model('Product_model');
		$r = $this->Product_model->Get_product($this->Input_post('product_id'));
		if(!$r) {
			return array(
				'view' => 'data_json',
				'data' => array('success' => false, 'reason' => 'Product not found')
			);
		}

		$output = $r['product'];
		$output['Amount'] = 0;
		$output['Product_Name'] = $output['Name'];
		$output['Product_ID'] = $output['ID'];
		$output['ID'] = $this->Input_post('new_product_output_id');
		
		return array(
			'view' => 'single_view_json',
			'data' => array(
				'success' => true,
				'html' => array(
					'view' => 'recipe_product_output_view',
					'data' => $output
				)
			)
		);
	}

	public function add_recipe_product_input() {
		$this->Load_model('Product_model');
		$r = $this->Product_model->Get_product($this->Input_post('product_id'));
		if(!$r) {
			return array(
				'view' => 'data_json',
				'data' => array('success' => false, 'reason' => 'Product not found')
			);
		}

		$output = $r['product'];
		$output['Amount'] = 0;
		$output['Product_Name'] = $output['Name'];
		$output['Product_ID'] = $output['ID'];
		$output['ID'] = $this->Input_post('new_product_input_id');
		
		return array(
			'view' => 'single_view_json',
			'data' => array(
				'success' => true,
				'html' => array(
					'view' => 'recipe_product_input_view',
					'data' => $output
				)
			)
		);
	}

	public function add_recipe_tool() {
		$this->Load_model('Category_model');
		$tool = $this->Category_model->Get_category($this->Input_post('category_id'));
		if(!$tool) {
			return array(
				'view' => 'data_json',
				'data' => array('success' => false, 'reason' => 'Category not found')
			);
		}

		$tool['Category_Name'] = $tool['Name'];
		$tool['Category_ID'] = $tool['ID'];
		$tool['ID'] = $this->Input_post('new_tool_id');
		
		return array(
			'view' => 'single_view_json',
			'data' => array(
				'success' => true,
				'html' => array(
					'view' => 'recipe_tool_view',
					'data' => $tool
				)
			)
		);
	}
		
	private function get_measure_descriptions() {
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
		$this->Load_model('Resource_model');
		$r = $this->Resource_model->Get_resource($this->Input_post('id'));
		$resource = $r['resource'];
		$categories = $r['categories'];
		$measures = $this->Resource_model->Get_measures();

		$this->Load_model('Category_model');
		$category_list = $this->Category_model->Get_categories();

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

		return array(
			'view' => 'single_view_json',
			'data' => array(
				'success' => true,
				'html' => array(
					'view' => 'resource_edit_view',
					'data' => array(
						'resource' => $resource,
						'measures' => $measures,
						'categories' => $categories,
						'category_list' => $category_list
					)
				)
			)
		);
	}

	public function save_resource() {
		$this->Load_model('Resource_model');
		$result = $this->Resource_model->Save_resource($this->Input_post());

		return array(
			'view' => 'data_json',
			'data' => $result
		);
	}

	public function edit_product() {
		$this->Load_model('Product_model');
		$r = $this->Product_model->Get_product($this->Input_post('id'));
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

		return array(
			'view' => 'single_view_json',
			'data' => array(
				'success' => true,
				'html' => array(
					'view' => 'product_edit_view',
					'data' => array(
						'product' => $product,
						'categories' => $categories,
						'category_list' => $category_list
					)
				)
			)
		);
	}
	
	public function add_category() {
		$category_id = $this->Input_post('category_id');

		$this->Load_model('Category_model');
		$category = $this->Category_model->Get_category($category_id);
		
		$success = true;
		if($category === false)
			$success = false;
		$category["Food_nutrition"] = "";
		$category["Container_mass_limit"] = "";
		$category["Container_volume_limit"] = "";
		$category["Tool_efficiency"] = "";

		return array(
			'view' => 'single_view_json',
			'data' => array(
				'success' => true,
				'html' => array(
					'view' => 'category_view',
					'data' => $category
				)
			)
		);
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
