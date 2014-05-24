<?php
require_once "../controllers/base.php";

class User extends Base {
	public function Logged_in() {
		if(NULL == $this->Session_get('userid')) {
			return false;
		}
		if(Get_cache('kick_user_' . $this->Session_get('userid'))) {
			Delete_cache('kick_user_' . $this->Session_get('userid'));
			$this->Kick_user();
			return false;
		}
		return true;
	}

	public function Index() {
		if(!$this->Logged_in()) {
			return array(
				'view' => 'redirect',
				'data' => '/'
			);
		}

		$this->Load_model('Actor_model');
		$actors = $this->Actor_model->Get_actors($this->Session_get('userid'));
		$actor_limit = $this->Actor_model->Get_users_actor_limit($this->Session_get('userid'));

		return array(
			'view' => 'user_view',
			'data' => array(
				'actors' => $actors,
				'actor_limit' => $actor_limit,
				'username' => $this->Session_get('username'),
				'admin' => $this->Session_get('admin')
			)
		);
	}

	public function Request_actor() {
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			return $this->Json_response_not_logged_in();
		}
		$this->Load_model('Actor_model');
		$r = $this->Actor_model->Request_actor($this->Session_get('userid'));
		
		return array(
			'type' => 'json',
			'data' => $r
		);
	}

	private function Start_openid_verification($finish_path) {
		$openid = $this->Input_post('openid');
		if(NULL === $openid) {
			return array(
				'view' => 'data_json',
				'data' => array('success' => false, 'reason' => 'Must give an openid')
			);
		}

		require_once "Auth/OpenID/Consumer.php";
		require_once "Auth/OpenID/FileStore.php";

		$store = new Auth_OpenID_FileStore('../oid_store');
		$consumer = new Auth_OpenID_Consumer($store);

		$auth = $consumer->begin($openid);
		if (!$auth) {
			return array(
				'view' => 'data_json',
				'data' => array('success' => false, 'reason' => 'Could not start openid login process')
			);
		}

		$url = $auth->redirectURL($GLOBALS['base_url'], $GLOBALS['base_url'].$finish_path);
		return array(
			'view' => 'data_json',
			'data' => array('success' => true, 'redirect_url' => $url)
		);
	}

	public function Start_openid_login() {
		return $this->Start_openid_verification('user/Finish_openid_login');
	}

	public function Start_add_openid() {
		return $this->Start_openid_verification('user/Finish_add_openid');
	}

	public function Finish_openid_verification($finish_path) {
		require_once "Auth/OpenID/Consumer.php";
		require_once "Auth/OpenID/FileStore.php";

		$store = new Auth_OpenID_FileStore('../oid_store');
		$consumer = new Auth_OpenID_Consumer($store);
		$response = $consumer->complete($GLOBALS['base_url'].$finish_path);

  		if ($response->status == Auth_OpenID_SUCCESS) {
			$_SESSION['OPENID'] = $response->identity_url;
			return true;
		} else {
			if ($response->status == Auth_OpenID_CANCEL) {
				// This means the authentication was cancelled.
				echo 'Verification cancelled.';
			} else if ($response->status == Auth_OpenID_FAILURE) {
				// Authentication failed; display the error message.
				echo "OpenID authentication failed: " . $response->message;
			}
			echo ' = Denied!';
		}
		return false;
	}

	public function Finish_openid_login() {
		if($this->Finish_openid_verification('user/Finish_openid_login') == true)
			$this->Login_openid($_SESSION['OPENID']);
	}

	public function Finish_add_openid() {
		if($this->Finish_openid_verification('user/Finish_add_openid')) {
			$this->Load_model('User_model');
			$r = $this->User_model->Add_user_openid($_SESSION['userid'], $_SESSION['OPENID']);
			if($r == false) {
				header( 'Location: /user/Settings' );
//				echo "Failed to add id";
			} else {
				header( 'Location: /user/Settings' );
//				echo "Adding id was successful";
			}
		}
	}

	/* Login openid is a private function
	 * Must only be called from Finish_openid_login which verifies the openid.
	 * */
	private function Login_openid($openid) {
		$this->Load_model('User_model');
		$r = $this->User_model->Login_openid($openid);
		if(is_array($r))
		{
			if(isset($_SESSION['userid']))
			{
				return 0;
			}
			$_SESSION['username'] = $r['Username'];
			$_SESSION['userid'] = $r['ID'];
			$_SESSION['admin'] = $this->User_model->User_has_access($_SESSION['userid'], 'Admin');
			header('Location: /user');
		} elseif($r == 'Not found') {
			$_SESSION['OPENID_AUTH'] = true;
			header('Location: /user/create_account');
		} else {
			echo 'TODO: openid login failure. '.$r;
			//header('Location: /front')
		}
	}

	public function Create_account() {
		$this->Load_view('signup_view');
	}
	
	public function Create_user() {
		header('Content-type: application/json');
		if($_SESSION['OPENID_AUTH'] !== true) {
			echo json_encode(array('success' => false, 'reason' => 'No authorized openid'));
			return;
		}
		if(!isset($_POST['username']) || $_POST['username'] == "") {
			echo json_encode(array('success' => false, 'reason' => 'No username'));
			return;
		}

		$this->Load_model('User_model');
		$r = $this->User_model->Create_user_openid($_POST['username'], $_SESSION['OPENID']);
		if($r['success'] == false) {
			echo json_encode(array('success' => false, 'reason' => $r['reason']));
			return;
		} else {
			$_SESSION['username'] = $_POST['username'];
			$_SESSION['userid'] = $r['ID'];
			$_SESSION['admin'] = $this->User_model->User_has_access($_SESSION['userid'], 'Admin');
			echo json_encode(array('success' => true));
			return;
		}
	}

	private function Kick_user() {
		$this->Session_clear();
	}
	
	public function Logout() {
		$this->Kick_user();
		return array(
			'view' => 'data_json', 
			'data' => array(
				"success" => true
			)
		);
	}
	
	public function Settings() {
		$this->Load_model('User_model');
		if(!$this->Logged_in()) {
			header("Location: /front");
			return;
		}
		$openids = $this->User_model->Get_user_openids($_SESSION['userid']);
		$openid_icons = $this->User_model->Get_openid_icons();

		$common_head_view = $this->Load_view('common_head_view', array());
		$this->Load_view('user_settings_view', array(
														'openids' => $openids,
														'openid_icons' => $openid_icons, 
														'common_head_view' => $common_head_view
													));
	}
	
	public function Delete_openid() {
		header('Content-type: application/json');
		$this->Load_model('User_model');
		if(!$this->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		
		$r = $this->User_model->Delete_user_openid($_SESSION['userid'], $_POST['id']);
		
		echo json_encode($r);
	}
}

?>
