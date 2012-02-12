<?php
require_once "controllers/controller.php";

class Actor extends Controller
{
	public function Request_actor()
	{
		header('Content-type: application/json');
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		$this->Load_model('Actor_model');
		$r = $this->Actor_model->Request_actor($_SESSION['userid']);
		if($r) {
			echo json_encode(array('success' => true));
		} else {
			echo json_encode(array('success' => false, 'reason' => 'Could not give you an actor'));
		}
	}
	
	public function Show_actor($actor_id, $tab = 'events')
	{
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			header("Location: /front");
			return;
		}
		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($_SESSION['userid'], $actor_id)) {
			die("This is not the actor you are looking for.");
		}

		$this->Load_model("Travel_model");
		$this->Update_travel($actor_id);

		$actor = $this->Actor_model->Get_actor($actor_id);
		if($actor['Name'] == NULL)
		{
			$actor['Name'] = 'Unnamed actor';
		}
		if($actor['Location'] == NULL)
		{
			$actor['Location'] = 'Unnamed location';
		}
		$hour = ($actor['Time'] % 16) + 1;
		$day = (intval($actor['Time'] / 16) % 16) + 1;
		$month = (intval($actor['Time'] / 256) % 16) +1;
		$year = intval($actor['Time'] / 4096);
		
		$tab_view = '';
		if($tab == 'locations') {
			$travel = $this->Travel_model->Get_travel_info($actor_id);
			if($travel) {
				if(!$travel['OriginName'])
					$travel['OriginName'] = 'Unnamed location';
				if(!$travel['DestinationName'])
					$travel['DestinationName'] = 'Unnamed location';
			}
			$this->Load_controller('Location');
			$locations = $this->Location->Get_neighbouring_locations($actor_id);
			ob_start();
			include 'views/locations_tab_view.php';
			$tab_view = ob_get_clean();
		} elseif ($tab == 'people') {
			$actors = $this->Actor_model->Get_visible_actors($actor_id);
			ob_start();
			include 'views/people_tab_view.php';
			$tab_view = ob_get_clean();
		} elseif ($tab == 'events') {
			$this->Load_model("Event_model");
			$events = $this->Event_model->Get_events($actor_id);
			ob_start();
			include 'views/events_tab_view.php';
			$tab_view = ob_get_clean();
		}

		include 'views/actor_view.php';
	}
	
	public function Change_actor_name()
	{
		header('Content-type: application/json');
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		$actor_id = $_POST['actor'];
		$named_actor_id = $_POST['named_actor'];
		$new_name = $_POST['name'];
		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($_SESSION['userid'], $actor_id)) {
			echo json_encode(array('success' => false, 'reason' => 'Not your actor'));
		}
		$r = $this->Actor_model->Change_actor_name($actor_id, $named_actor_id, $new_name);
		if($r == false) {
			echo json_encode(array('success' => false, 'reason' => 'Could not change actor name'));
			return;
		}
		else {
			if(strlen($new_name) == 0) {
				echo json_encode(array('success' => true, 'data' => 'Unnamed actor'));
			}
			else {
				echo json_encode(array('success' => true, 'data' => $new_name));
			}
		}
	}

	public function Actors()
	{	
		if(!$this->Logged_in()) {
			header("Location: front");
			return;
		}

		$this->Load_model('Actor_model');
		$actors = $this->Actor_model->Get_actors($_SESSION['userid']);

		include 'views/actors_view.php';
	}
	
	private function Update_travel($actor_id) {
		$this->Load_model("Travel_model");

		$update = $this->Travel_model->Get_update_count();
		$travel = $this->Travel_model->Get_outdated_travel($actor_id, $update);
		if($travel) {
			$time_difference = $update - $travel['UpdateTick'];
			$dx = $travel['DestinationX'] - $travel['CurrentX'];
			$dy = $travel['DestinationY'] - $travel['CurrentY'];
			$d = sqrt($dx*$dx+$dy*$dy);
			if($d > $time_difference) {
				$move_factor = $time_difference / $d;
				$move = array(array(
					'x' => $travel['CurrentX'] + $dx * $move_factor,
					'y' => $travel['CurrentY'] + $dy * $move_factor,
					'actor' => $actor_id
				));
				$move_success = $this->Travel_model->Move($move, $update);
			} else {
				$arrive = array(array(
					'Actor' => $actor_id,
					'Destination' => $travel['DestinationID']
				));
				$arrive_success = $this->Travel_model->Arrive($arrive);
			}
		}
	}
	
	public function Speak() {
		header('Content-type: application/json');
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		$actor_id = $_POST['actor'];
		$message = $_POST['message'];
		$this->Load_model('Actor_model');
		if(!$this->Actor_model->User_owns_actor($_SESSION['userid'], $actor_id)) {
			echo json_encode(array('success' => false, 'reason' => 'Not your actor'));
		}
		
		$this->Load_model('Event_model');
		$r = $this->Event_model->Speak($actor_id, $message);
		if($r == false) {
			echo json_encode(array('success' => false, 'reason' => 'Could not save your message'));
			return;
		}
		else {
			echo json_encode(array('success' => true));
		}
	}
}

?>
