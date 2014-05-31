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

		$url = $auth->redirectURL($this->Input_global('base_url'), $this->Input_global('base_url') . $finish_path);
		return array(
			'view' => 'data_json',
			'data' => array(
				'success' => true,
				'redirect_url' => $url
			)
		);
	}

	public function Start_openid_login() {
		return $this->Start_openid_verification('user/Finish_openid_login');
	}

	public function Start_add_openid() {
		return $this->Start_openid_verification('user/Finish_add_openid');
	}

	private function Finish_openid_verification($finish_path) {
		require_once "Auth/OpenID/Consumer.php";
		require_once "Auth/OpenID/FileStore.php";

		$store = new Auth_OpenID_FileStore('../oid_store');
		$consumer = new Auth_OpenID_Consumer($store);
		$response = $consumer->complete($this->Input_global('base_url').$finish_path);

  		if ($response->status == Auth_OpenID_SUCCESS) {
			$this->Session_set('OPENID', $response->identity_url);
			return true;
		} else {
			/*
			if ($response->status == Auth_OpenID_CANCEL) {
				// This means the authentication was cancelled.
				echo 'Verification cancelled.';
			} else if ($response->status == Auth_OpenID_FAILURE) {
				// Authentication failed; display the error message.
				echo "OpenID authentication failed: " . $response->message;
			}
			echo ' = Denied!';
			*/
		}
		return false;
	}

	public function Finish_openid_login() {
		if($this->Finish_openid_verification('user/Finish_openid_login') == true) {
			$openid = $this->Session_get('OPENID');
			$this->Load_model('User_model');
			$r = $this->User_model->Login_openid($openid);
			if(is_array($r)) {
				if(NULL !== $this->Session_get('userid')) {
					return 0;
				}
				$this->Session_set('username', $r['Username']);
				$this->Session_set('userid', $r['ID']);
				$this->Session_set('admin', $this->User_model->User_has_access($r['ID'], 'Admin'));
				return array('view' => 'redirect', 'data' => '/user');
			} elseif($r == 'Not found') {
				$this->Session_set('OPENID_AUTH', true);
				return array('view' => 'redirect', 'data' => '/user/create_account');
			} else {
				return array('view' => 'redirect', 'data' => '/');
			}
		}
	}

	public function Finish_add_openid() {
		if($this->Finish_openid_verification('user/Finish_add_openid')) {
			$this->Load_model('User_model');
			$r = $this->User_model->Add_user_openid($this->Session_get('userid'), $this->Session_get('OPENID'));
			return array(
				'view' => 'redirect',
				'data' => '/user/Settings'
			);
		}
	}

	public function Create_account() {
		return array('view' => 'signup_view');
	}
	
	public function Create_user() {
		if($this->Session_get('OPENID_AUTH') !== true) {
			return array(
				'view' => 'data_json',
				'data' => array('success' => false, 'reason' => 'No authorized openid')
			);
		}
		$username = $this->Input_post('username');
		if(null === $username || $username == "") {
			return array(
				'view' => 'data_json',
				'data' => array('success' => false, 'reason' => 'No username')
			);
		}

		$this->Load_model('User_model');
		$r = $this->User_model->Create_user_openid($username, $this->Session_get('OPENID'));
		if($r['success'] == false) {
			return array(
				'view' => 'data_json',
				'data' => array('success' => false, 'reason' => $r['reason'])
			);
		} else {
			$this->Session_set('username', $username);
			$this->Session_set('userid', $r['ID']);
			return array(
				'view' => 'data_json',
				'data' => array('success' => true)
			);
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
			return array('view' => 'redirect', 'data' => '/');
		}
		$openids = $this->User_model->Get_user_openids($this->Session_get('userid'));
		$openid_icons = $this->User_model->Get_openid_icons();

		return array(
			'view' => 'user_settings_view',
			'data' => array(
				'openids' => $openids,
				'openid_icons' => $openid_icons
			)
		);
	}
	
	public function Delete_openid() {
		$this->Load_model('User_model');
		if(!$this->Logged_in()) {
			return array(
				'view' => 'data_json',
				'data' => array('success' => false, 'reason' => 'Not logged in')
			);
		}
		
		$r = $this->User_model->Delete_user_openid($this->Session_get('userid'), $this->Input_post('id'));
		return array(
			'view' => 'data_json',
			'data' => $r
		);
	}
}

?>
