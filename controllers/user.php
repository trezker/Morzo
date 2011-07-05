<?php

class User
{
	public function Load_model($model)
	{
		include "models/$model.php";
		$this->$model = new $model();
	}
	
	public function Logged_in()
	{
		session_start();

		if(!isset($_SESSION['userid']))
		{
			include 'views/not_logged_in.php';
			return false;
		}
		return true;
	}

	public function Index()
	{
		if(!$this->Logged_in())
		{
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

	public function Request_actor()
	{
		if(!$this->Logged_in())
		{
			return;
		}
		$this->Load_model('User_model');
		$r = $this->User_model->Request_actor($_SESSION['userid']);
		echo $r;
	}
	
	public function Actor($actor_id)
	{
		//Todo: check user session owns this actor.
		$this->Load_model('User_model');
		$actor = $this->User_model->Get_actor($actor_id);
		if($actor['Name'] == NULL)
		{
			$actor['Name'] = 'Unnamed actor';
		}
		if($actor['Location'] == NULL)
		{
			$actor['Location'] = 'Unnamed location';
		}
		$locations = $this->User_model->Get_neighbouring_locations($actor_id);
		$east = false;
		$west = false;
		$north = false;
		$south = false;
		foreach ($locations as &$location) {
			if($location['x'] == 1 && $location['y'] == 0)
				$east = true;
			if($location['x'] == -1 && $location['y'] == 0)
				$west = true;
			if($location['x'] == 0 && $location['y'] == 1)
				$south = true;
			if($location['x'] == 0 && $location['y'] == -1)
				$north = true;

			if(!$location['Name'])
    			$location['Name'] = 'Unnamed location';
    		$location['Compass'] = 90+rad2deg(atan2($location['y'], $location['x']));
    		if($location['Compass'] < 0)   $location['Compass'] += 360;
    		if($location['Compass'] > 360) $location['Compass'] -= 360;
    		if($location['Compass'] < 22.5 || $location['Compass'] >= 337.5)
				$location['Compass'] = 'N';
			else if($location['Compass'] < 22.5+45)
				$location['Compass'] = 'NE';
			else if($location['Compass'] < 22.5+90)
				$location['Compass'] = 'E';
			else if($location['Compass'] < 22.5+135)
				$location['Compass'] = 'SE';
			else if($location['Compass'] < 22.5+180)
				$location['Compass'] = 'S';
			else if($location['Compass'] < 22.5+225)
				$location['Compass'] = 'SW';
			else if($location['Compass'] < 22.5+270)
				$location['Compass'] = 'W';
			else if($location['Compass'] < 22.5+315)
				$location['Compass'] = 'NW';
    	}
		unset($location);
		if(!$east)
		{
    		$locations[] = array(
    			'ID' => 'east',
    			'x' => 1,
    			'y' => 0,
    			'Name' => 'Unnamed location'
    		);
		}
		if(!$west)
		{
    		$locations[] = array(
    			'ID' => 'west',
    			'x' => -1,
    			'y' => 0,
    			'Name' => 'Unnamed location'
    		);
		}
		if(!$south)
		{
    		$locations[] = array(
    			'ID' => 'south',
    			'x' => 0,
    			'y' => 1,
    			'Name' => 'Unnamed location'
    		);
		}
		if(!$north)
		{
    		$locations[] = array(
    			'ID' => 'north',
    			'x' => 0,
    			'y' => -1,
    			'Name' => 'Unnamed location'
    		);
		}

		include 'views/actor_view.php';
	}
	
	public function Change_location_name($actor_id, $location_id, $new_name)
	{
		//Todo: check user session owns this actor.
		$this->Load_model('User_model');
		if(!is_numeric($location_id))
		{
			$r = $this->User_model->Create_location($actor_id, $location_id);
			if(!$r)
			{
				echo false;
				return;
			}
			$location_id = $r;
		}
		$r = $this->User_model->Change_location_name($actor_id, $location_id, $new_name);
		if($r == false)
		{
			echo false;
			return;
		}
		else
		{
			if(strlen($new_name) == 0)
			{
				echo 'Unnamed location';
			}
			else
			{
				echo $new_name;
			}
		}
	}
	
	public function Change_actor_name($actor_id, $named_actor_id, $new_name)
	{
		if(strlen($new_name) == 0)
		{
			echo false;
		}
		//Todo: check user session owns this actor.
		$this->Load_model('User_model');
		$r = $this->User_model->Change_actor_name($actor_id, $named_actor_id, $new_name);
		if($r == false)
		{
			echo false;
			return;
		}
		else
		{
			if(strlen($new_name) == 0)
			{
				echo 'Unnamed actor';
			}
			else
			{
				echo $new_name;
			}
		}
	}

	public function Actors()
	{	
		if(!$this->Logged_in())
		{
			return;
		}

		$this->Load_model('User_model');
		$actors = $this->User_model->Get_actors($_SESSION['userid']);

		include 'views/actors_view.php';
	}
}

?>
