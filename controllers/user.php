<?php
require_once "../controllers/base.php";

class User extends Base
{
	public function Logged_in()
	{
		if(!isset($_SESSION['userid'])) {
			return false;
		}
		if(Get_cache('kick_user_'.$_SESSION['userid'])) {
			Delete_cache('kick_user_'.$_SESSION['userid']);
			$this->Kick_user();
			return false;
		}
		return true;
	}

	public function Index()
	{
		if(!$this->Logged_in())
		{
			header("Location: front");
			return;
		}

		$this->Load_model('Actor_model');
		$actors = $this->Actor_model->Get_actors($_SESSION['userid']);
		$actor_limit = $this->Actor_model->Get_users_actor_limit($_SESSION['userid']);

		$common_head_view = $this->Load_view('common_head_view', array());
		$this->Load_view('user_view', array('actors' => $actors, 'actor_limit' => $actor_limit, 'common_head_view' => $common_head_view));
	}

	private function Start_openid_verification($finish_path) {
		header('Content-type: application/json');
		if(!isset($_POST['openid'])) {
			echo json_encode(array('success' => false, 'reason' => 'Must give an openid'));
			return;
		}

		require_once "Auth/OpenID/Consumer.php";
		require_once "Auth/OpenID/FileStore.php";

		$store = new Auth_OpenID_FileStore('../oid_store');
		$consumer = new Auth_OpenID_Consumer($store);

		$auth = $consumer->begin($_POST['openid']);
		if (!$auth) {
			echo json_encode(array('success' => false, 'reason' => 'Could not start openid login process'));
			return;
		}

		$url = $auth->redirectURL($GLOBALS['base_url'], $GLOBALS['base_url'].$finish_path);
		echo json_encode(array('success' => true, 'redirect_url' => $url));
	}

	public function Start_openid_login() {
		$this->Start_openid_verification('user/Finish_openid_login');
	}

	public function Start_add_openid()
	{
		$this->Start_openid_verification('user/Finish_add_openid');
	}

	public function Finish_openid_verification($finish_path)
	{
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
	private function Login_openid($openid)
	{
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
		// Unset all of the session variables.
		$_SESSION = array();

		// If it's desired to kill the session, also delete the session cookie.
		// Note: This will destroy the session, and not just the session data!
		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000,
				$params["path"], $params["domain"],
				$params["secure"], $params["httponly"]
			);
		}

		session_destroy();
	}
	
	public function Logout()
	{
		header('Content-type: application/json');
		$this->Kick_user();
		echo json_encode(array('success' => true));
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
