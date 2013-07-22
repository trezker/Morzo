<?php
require_once "../controllers/controller.php";

class Language_admin extends Controller
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

		$this->Load_model('Language_model');
		$languages = $this->Language_model->Get_languages();
		
		$this->Load_view('language_admin_view', array('languages' => $languages));
	}

	public function Load_translations(){
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
		if(!is_numeric($_POST['language_id'])) {
			echo json_encode(array('success' => false, 'reason' => 'Must give a language id'));
			return;
		}
		
		$this->Load_model('Language_model');
		$translations = $this->Language_model->Get_translations_for_translator($_POST['language_id']);
		
		$language_translations_view = $this->Load_view('language_translations_view', 
														array('translations' => $translations), true);
		
		echo json_encode(array('success' => true, 'data' => $language_translations_view));
	}
	
	public function Save_translation(){
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

		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		if($_SESSION['admin'] != true) {
			echo json_encode(array('success' => false, 'reason' => 'Requires admin privilege'));
			return;
		}

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


