<?php
require_once "controllers/controller.php";

class Actor extends Controller
{
	public function Request_actor()
	{
		if(!$this->Logged_in())
		{
			return;
		}
		$this->Load_model('Actor_model');
		$r = $this->Actor_model->Request_actor($_SESSION['userid']);
		echo $r;
	}
	
	public function Show_actor($actor_id)
	{
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			return;
		}
		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($_SESSION['userid'], $actor_id)) {
			die("This is not the actor you are looking for.");
		}

		$this->Load_controller('Location');
		$actor = $this->Actor_model->Get_actor($actor_id);
		if($actor['Name'] == NULL)
		{
			$actor['Name'] = 'Unnamed actor';
		}
		if($actor['Location'] == NULL)
		{
			$actor['Location'] = 'Unnamed location';
		}

		$locations = $this->Location->Get_neighbouring_locations($actor_id);
		
		include 'views/actor_view.php';
	}
	
	public function Change_actor_name($actor_id, $named_actor_id, $new_name)
	{
		if(strlen($new_name) == 0)
		{
			echo false;
		}
		//Todo: check user session owns this actor.
		$this->Load_model('Actor_model');
		$r = $this->Actor_model->Change_actor_name($actor_id, $named_actor_id, $new_name);
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

		$this->Load_model('Actor_model');
		$actors = $this->Actor_model->Get_actors($_SESSION['userid']);

		include 'views/actors_view.php';
	}
}

?>
