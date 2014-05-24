<?php

class Session {
	function __construct() {
		session_start();
	}
	
	public function Clear() {
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

	public function Set($key, $value) {
		$_SESSION[$key] = $value;
	}
	
	public function Get($key) {
		if(isset($_SESSION[$key]))
			return $_SESSION[$key];
		return NULL;
	}
}
