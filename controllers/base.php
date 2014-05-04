<?php
require_once "../controllers/controller.php";

class Base extends Controller {
	public function Before_page_load() {
		$this->Load_controller('User');
		if($this->User->Logged_in()) {
			$this->Load_model('User_model');
			$this->User_model->Update_user_activity($this->Session_get('userid'));
		}
	}
	
	public function Get_time_to_next_update() {
		$dt = new DateTime();
		$current_time = $dt->format('Y:m:d H:i:s');
		$midnight = $dt->format('Y:m:d 00:00:00');
		$total_minutes = floor((strtotime($current_time) - strtotime($midnight)) / 60);
		$game_hour = floor($total_minutes / 90) +1;
		//Now we know when the next update should occur.
		//Calc hour and minute and construct a datetime
		$minutes_to_next_update = $game_hour * 90 - $total_minutes;
		return $minutes_to_next_update;
	}

	public function Json_response_not_logged_in() {
		return array(
			'type' => 'json',
			'data' => array(
				'success' => false, 
				'reason' => 'Not logged in'
			)
		);
	}
	
	public function Json_response_not_your_actor() {
		return array(
			'type' => 'json',
			'data' => array(
				'success' => false, 
				'reason' => 'Not your actor'
			)
		);
	}
}
