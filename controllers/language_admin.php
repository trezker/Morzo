<?php
require_once "../controllers/base.php";

class Language_admin extends Base {
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
		$this->Load_model('Language_model');
		$languages = $this->Language_model->Get_languages();
		
		return array(
			'view' => 'language_admin_view', 
			'data' => array(
				'languages' => $languages
			)
		);
	}

	public function Load_translations(){
		if(!is_numeric($this->Input_post('language_id'))) {
			return array(
				'view' => 'data_json',
				'data' => array(
					'success' => false,
					'reason' => 'Must give a language id'
				)
			);
		}
		
		$this->Load_model('Language_model');
		$translations = $this->Language_model->Get_translations_for_translator($this->Input_post('language_id'));
		
		return array(
			'view' => 'single_view_json',
			'data' => array(
				'success' => true,
				'html' => array(
					'view' => 'language_translations_view',
					'data' => array(
						'translations' => $translations
					)
				)
			)
		);
	}
	
	public function Save_translation(){
		header('Content-type: application/json');

		if(!is_numeric($_POST['language_id'])) {
			echo json_encode(array('success' => false, 'reason' => 'Must give a language id'));
			return;
		}

		if(!isset($_POST['handle'])) {
			echo json_encode(array('success' => false, 'reason' => 'Must give a handle'));
			return;
		}

		if(!isset($_POST['text'])) {
			echo json_encode(array('success' => false, 'reason' => 'Must give a text'));
			return;
		}

		$this->Load_model('Language_model');
		$translation_result = $this->Language_model->Save_translation(	$_POST['language_id'],
																		$_POST['handle'],
																		$_POST['text']);
																		
		if($translation_result == false)
			$success = false;
		else
			$success = true;
		
		echo json_encode(array('success' => $success, 'data' => $translation_result));
	}

	public function New_translation(){
		header('Content-type: application/json');

		if(!isset($_POST['handle']) || trim($_POST['handle']) == "") {
			echo json_encode(array('success' => false, 'reason' => 'Must give a handle'));
			return;
		}
		if(!isset($_POST['text']) || trim($_POST['text']) == "") {
			echo json_encode(array('success' => false, 'reason' => 'Must give a text'));
			return;
		}

		$this->Load_model('Language_model');
		$translation_result = $this->Language_model->New_translation($_POST['handle'], $_POST['text']);

		if($translation_result == false) {
			echo json_encode(array('success' => false, 'reason' => 'Failed'));
			return;
		}
		else
			$success = true;
		
		echo json_encode(array('success' => $success, 'data' => $translation_result));
	}
}


