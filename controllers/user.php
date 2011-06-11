<?php

class User
{
	public function Load_model($model)
	{
		include "models/$model.php";
		$this->$model = new $model();
	}

	public function Index()
	{
		session_start();

		if(!isset($_SESSION['userid']))
		{
			include 'views/not_logged_in.php';
			return;
		}

		$this->Load_model('User_model');
		$actors = $this->User_model->Get_actors($_SESSION['userid']);

		include 'views/user_view.php';
	}
	
	public function RegisterQueue($email)
	{
		$email = mb_strtolower($email);
		if(!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			echo 'Please provide a valid email. <br />';
		}
		else
		{
			include 'models/User_model.php';
			$this->Load_model('User_model');
			$r = $this->User_model->RegisterQueue($email);
			if($r === false)
			{
				echo 'Failed to register \' ' . $email . '\': ' . $r . ' <br />';
			}
			else
			{
				if($r == 'Duplicate')
				{
					echo $email . ' has already been registered. <br />';
				}
				else
				{
					echo 'Registered \' ' . $email . '\'. Thank you. <br />';
				}
			}
		}
	}
	
	public function Register($username, $password)
	{
		$this->Load_model('User_model');
		$r = $this->User_model->Register($username, $password);
		return $r;
	}
	
	public function Login($username, $password)
	{
		$this->Load_model('User_model');
		$r = $this->User_model->Login($username, $password);
		if(is_numeric($r))
		{
			session_start();
			if(isset($_SESSION['userid']))
			{
				return 0;
			}
			$_SESSION['username'] = $username;
			$_SESSION['userid'] = $r;
			$_SESSION['admin'] = $this->User_model->User_has_access($_SESSION['userid'], 'Admin');
			echo 1;
		}
		else
		{
			echo 0;
		}
	}
	
	public function Logout()
	{
		session_start();

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
		echo 1;
	}

	public function New_actor()
	{
		sleep(1);
		echo 1;
	}
}

?>
