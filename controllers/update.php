<?php
require_once "controllers/controller.php";

class Update extends Controller
{
	public function Index()
	{
		$this->Load_model("Travel_model");
		$travels = $this->Travel_model->Get_travels();
		$move = array();
		$arrive = array();
		foreach($travels as $travel) {
			$dx = $travel['DestinationX'] - $travel['CurrentX'];
			$dy = $travel['DestinationY'] - $travel['CurrentY'];
			$d = sqrt($dx*$dx+$dy*$dy);
			if($d > 1) {
				$move[] = array(
					'x' => $travel['CurrentX'] + $dx / $d,
					'y' => $travel['CurrentY'] + $dy / $d,
					'actor' => $travel['ActorID']
				);
			} else {
				$arrive[] = array(
					'Actor' => $travel['ActorID'],
					'Destination' => $travel['DestinationID']
				);
			}
			$move_success = $this->Travel_model->Move($move);
			$arrive_success = $this->Travel_model->Arrive($arrive);
			echo "Move processing: ". $move_success;
			echo "Arrive processing: ". $arrive_success;
		}
	}
}

?>
