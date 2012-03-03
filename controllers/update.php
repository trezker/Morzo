<?php
require_once "../controllers/controller.php";

class Update extends Controller
{
	public function Index()
	{
		$this->Load_model("Travel_model");
		echo $this->Travel_model->Tick();
		$this->Update_travel();
		return;
	}
	
	public function Get_time_units($time) {
		return array(
			'hour' => ($time % 16) + 1,
			'day' => (intval($time / 16) % 16) + 1,
			'month' => (intval($time / 256) % 16) +1,
			'year' => intval($time / 4096)
		);
	}

	private function Update_travel() {
		$this->Load_model("Travel_model");

		$update = $this->Travel_model->Get_update_count();
		$travels = $this->Travel_model->Get_outdated_travel($update);
		foreach($travels as $travel) {
			$time_difference = $update - $travel['UpdateTick'];
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
				$move_success = $this->Travel_model->Move($move, $update);
			} else {
				$arrive = array(array(
					'Actor' => $travel['ActorID'],
					'Destination' => $travel['DestinationID']
				));
				$arrive_success = $this->Travel_model->Arrive($arrive);
			}
		}
	}
}


