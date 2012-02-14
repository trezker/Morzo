<?php
require_once "../controllers/controller.php";

class User extends Controller
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
			header("Location: /front");
			exit;
		}

		$this->Load_model('Actor_model');
		$actors = $this->Actor_model->Get_actors($_SESSION['userid']);

		include '../views/user_view.php';
	}

	public function Start_openid_login()
	{
		global $base_url;

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

		$url = $auth->redirectURL($base_url, $base_url . 'user/Finish_openid_login');
		$_SESSION['OPENID'] = $_POST['openid'];
		echo json_encode(array('success' => true, 'redirect_url' => $url));
	}

	public function Finish_openid_login()
	{
		global $base_url;

		require_once "Auth/OpenID/Consumer.php";
		require_once "Auth/OpenID/FileStore.php";

		$store = new Auth_OpenID_FileStore('../oid_store');
		$consumer = new Auth_OpenID_Consumer($store);
		$response = $consumer->complete($base_url . 'user/Finish_openid_login');

		if ($response->status == Auth_OpenID_SUCCESS) {
			$this->Login_openid($_SESSION['OPENID']);
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
			$this->Sign_up();
		} else {
			echo 'TODO: openid login failure';
			//header('Location: /front')
		}
	}
	
	private function Sign_up() {
		include '../views/signup_view.php';
	}
	
	public function Create_user() {
		header('Content-type: application/json');
		if($_SESSION['OPENID_AUTH'] !== true) {
			echo json_encode(array('success' => false, 'reason' => 'No authorized openid'));
			return;
		}
		if(!isset($_POST['username'])) {
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
}


