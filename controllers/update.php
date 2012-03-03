<?php
require_once "../controllers/controller.php";

class Update extends Controller
{
	public function Index()
	{
		$this->Load_model("Travel_model");
		$time = $this->Travel_model->Tick();
		$this->Update_travel($time);
		$this->Spawn_actors($time);
		echo $time;
	}
	
	public function Get_time_units($time) {
		return array(
			'hour' => ($time % 16) + 1,
			'day' => (intval($time / 16) % 16) + 1,
			'month' => (intval($time / 256) % 16) +1,
			'year' => intval($time / 4096)
		);
	}

	private function Update_travel($time) {
		$this->Load_model("Travel_model");

		$travels = $this->Travel_model->Get_outdated_travel($time);
		foreach($travels as $travel) {
			$time_difference = $time - $travel['UpdateTick'];
			$dx = $travel['DestinationX'] - $travel['CurrentX'];
			$dy = $travel['DestinationY'] - $travel['CurrentY'];
			$d = sqrt($dx*$dx+$dy*$dy);
			if($d > $time_difference) {
				$move_factor = $time_difference / $d;
				$move = array(array(
					'x' => $travel['CurrentX'] + $dx * $move_factor,
					'y' => $travel['CurrentY'] + $dy * $move_factor,
					'actor' => $travel['ActorID']
				));
				$move_success = $this->Travel_model->Move($move, $time);
			} else {
				$arrive = array(array(
					'Actor' => $travel['ActorID'],
					'Destination' => $travel['DestinationID']
				));
				$arrive_success = $this->Travel_model->Arrive($arrive);
			}
		}
	}

	private function Spawn_actors($time) {
		$this->Load_model("Actor_model");
		$success = $this->Actor_model->Spawn_actor(1);
		if($success == true) {
			echo "New actor ";
		} else {
			echo "No new actor ";
		}
	}
}


