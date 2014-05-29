<?php
require_once "../controllers/base.php";

class World_admin extends Base {
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
					'view' => 'data_json',
					'data' => array(
						'success' => false,
						'reason' => 'Requires admin privilege'
					)
				);
			} else {
				return array("view" => "redirect", "data" => "/");
			}
		}
		return true;
	}

	public function Index() {
		return $this->Map(0, 0);
	}

	public function Map($center_x, $center_y) {
		$this->Load_model('Location_model');
		$locations = $this->Location_model->Get_locations($center_x, $center_y);
		$max_actors = $this->Location_model->Get_max_actors();
		$max_actors_account = $this->Location_model->Get_max_actors_account();
		return array(
			'view' => 'world_admin_view',
			'data' => array(
				'locations' => $locations, 
				'max_actors' => $max_actors,
				'max_actors_account' => $max_actors_account,
				'center_x' => $center_x,
				'center_y' => $center_y
			)
		);
	}
	
	public function Set_max_actors() {
		$this->Load_model('Location_model');
		$success = $this->Location_model->Set_max_actors($this->Input_post('value'));
		
		return array(
			'view' => 'data_json',
			'data' => array('success' => $success)
		);
	}
	
	public function Set_max_actors_account() {
		$this->Load_model('Location_model');
		$success = $this->Location_model->Set_max_actors_account($this->Input_post('value'));
		
		return array(
			'view' => 'data_json',
			'data' => array('success' => $success)
		);
	}
	
	public function Edit_location() {
		$id = $this->Input_post('id');
		
		$this->Load_model('Location_model');
		$location = $this->Location_model->Get_location($id);
		if($location['Biome_name'] === NULL)
			$location['Biome_name'] = "N/A";

		$all_location_resources = $this->Location_model->Get_location_resources($id);
		$landscapes = $this->Location_model->Get_landscapes();
		$biomes = $this->Location_model->Get_biomes();
		$landscapes = $this->Location_model->Get_landscapes();

		$this->Load_model('Species_model');
		$location_species = $this->Species_model->Get_location_species($id);
		$species = $this->Species_model->Get_species();
		$this->Load_model('Product_model');
		$corpse_products = $this->Product_model->Get_products('Corpse');
		
		$biomes_view = array(
			'view' => 'biomes_view',
			'data' => array('biomes' => $biomes, 'location' => $location)
		);
		
		$landscapes_view = array(
			'view' => 'landscapes_view',
			'data' => array('landscapes' => $landscapes, 'location_resources' => $all_location_resources)
		);
		
		$species_view = array(
			'view' => 'species_view',
			'data' => array('species' => $species, 'location_species' => $location_species)
		);
		
		return array(
			'view' => 'single_view_json',
			'data' => array(
				'success' => true,
				'html' => array(
					'view' => 'location_edit_view',
					'data' => array(
						'biomes_view' => $biomes_view,
						'landscapes' => $landscapes,
						'location' => $location,
						'landscapes_view' => $landscapes_view,
						'species_view' => $species_view,
						'corpse_products' => $corpse_products
					)
				)
			)
		);
	}
	
	public function get_landscape_resources() {
		$this->Load_model('Location_model');
		$location_resources = $this->Location_model->Get_location_resources($this->Input_post('location'), $this->Input_post('landscape'));
		$this->Load_model('Resource_model');
		$resources = $this->Resource_model->Get_resources();

		return array(
			'view' => 'single_view_json',
			'data' => array(
				'success' => true,
				'html' => array(
					'view' => 'resources_view',
					'data' => array('resources' => $resources, 'location_resources' => $location_resources)
				)
			)
		);
	}
	
	public function Add_biome() {
		$name = $this->Input_post('name');
		
		if(!is_string($name) || $name == '') {
			return array(
				'view' => 'data_json',
				'data' => array('success' => false, 'reason' => 'Must give a name')
			);
		}

		$this->Load_model('Location_model');
		if(!$this->Location_model->Add_biome($name)) {
			return array(
				'view' => 'data_json',
				'data' => array('success' => false, 'reason' => 'Failed to add biome')
			);
		}
		$biomes = $this->Location_model->Get_biomes();

		return array(
			'view' => 'single_view_json',
			'data' => array(
				'success' => true,
				'html' => array(
					'view' => 'biomes_view',
					'data' => array('biomes' => $biomes)
				)
			)
		);
	}

	public function Add_landscape() {
		$name = $this->Input_post('name');
		
		if(!is_string($name) || $name == '') {
			return array(
				'view' => 'data_json',
				'data' => array('success' => false, 'reason' => 'Must give a name')
			);
		}

		$this->Load_model('Location_model');
		if(!$this->Location_model->Add_landscape($name)) {
			return array(
				'view' => 'data_json',
				'data' => array('success' => false, 'reason' => 'Failed to add landscape')
			);
		}

		$landscapes = $this->Location_model->Get_landscapes();
		$all_location_resources = $this->Location_model->Get_location_resources($this->Input_post('location_id'));

		return array(
			'view' => 'single_view_json',
			'data' => array(
				'success' => true,
				'html' => array(
					'view' => 'landscapes_view',
					'data' => array('landscapes' => $landscapes, 'location_resources' => $all_location_resources)
				)
			)
		);
	}

	public function Set_location_biome() {
		$this->Load_model('Location_model');
		if(!$this->Location_model->Set_location_biome($this->Input_post('location'), $this->Input_post('biome'))) {
			return array(
				'view' => 'data_json',
				'data' => array('success' => false, 'reason' => 'Failed to set biome')
			);
		}

		return array(
			'view' => 'data_json',
			'data' => array('success' => true)
		);
	}

	public function Add_location_resource() {
		$this->Load_model('Location_model');
		if(!$this->Location_model->Add_location_resource(
			$this->Input_post('location'), 
			$this->Input_post('resource'), 
			$this->Input_post('landscape')
		)) {
			return array(
				'view' => 'data_json',
				'data' => array('success' => false, 'reason' => 'Failed to add resource')
			);
		}

		return array(
			'view' => 'data_json',
			'data' => array('success' => true)
		);
	}

	public function Remove_location_resource() {
		$location = $this->Input_post('location');
		$resource = $this->Input_post('resource');
		$landscape = $this->Input_post('landscape');
		
		$this->Load_model('Location_model');
		if(!$this->Location_model->Remove_location_resource(
			$location,
			$resource,
			$landscape
		)) {
			return array(
				'view' => 'data_json',
				'data' => array('success' => false, 'reason' => 'Failed to remove resource')
			);
		}
		$landscape_resource_count = $this->Location_model->Landscape_resource_count($location, $landscape);

		return array(
			'view' => 'data_json',
			'data' => array('success' => true, 'landscape_resource_count' => $landscape_resource_count)
		);
	}

	public function Save_species() {
		header('Content-type: application/json');

		$name = $_POST['name'];
		if(!is_string($name) || $name == '') {
			echo json_encode(array('success' => false, 'reason' => 'Must give a name'));
			return;
		}

		$this->Load_model('Species_model');
		$id = $this->Species_model->Save_species($name, $_POST['id'], $_POST['max_population']);
		if(!$id) {
			echo json_encode(array('success' => false, 'reason' => 'Failed to save species'));
			return;
		}
		if(!$this->Species_model->Save_location_species($id, $_POST['location_id'], $_POST['on_location'], $_POST['population'], $_POST['actor_spawn'])) {
			echo json_encode(array('success' => false, 'reason' => 'Failed to save species on location'));
			return;
		}
		
		$species = $this->Species_model->Get_species();
		$all_location_species = $this->Species_model->Get_location_species($_POST['location_id']);
		$species_view = $this->Load_view('species_view', array('species' => $species, 'location_species' => $all_location_species), true);

		echo json_encode(array('success' => true, 'data' => $species_view));
	}

	public function Get_specie() {
		header('Content-type: application/json');

		$this->Load_model('Species_model');
		$specie = $this->Species_model->Get_specie($_POST['id'], $_POST['location_id']);
		if(!$specie) {
			echo json_encode(array('success' => false, 'reason' => 'Failed to get_specie'));
			return;
		}

		echo json_encode(array('success' => true, 'data' => $specie));
	}
}
?>
