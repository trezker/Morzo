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
		$this->Update_projects($time);
		echo $time;
		echo '

';
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

	private function Update_projects($time) {
		$this->Load_model("Project_model");
		$update_success = $this->Project_model->Update_projects($time);
		if($update_success == false) {
			echo "Projects failed to update";
		} else {
			$outputs = $this->Project_model->Get_output_from_finished_cycles();
			echo '<pre>';
//			var_dump($outputs);
			echo '</pre>';
			$projects = array();
			foreach($outputs as $output) {
				$project_id = $output['Project_ID'];
				if(!isset($projects[$project_id])) {
					$projects[$project_id]['outputs'] = array();
					$projects[$project_id]['Project_ID'] = $output['Project_ID'];
					$projects[$project_id]['Cycles_left'] = $output['Cycles_left'];
				}
				$projects[$project_id]['outputs'][] = $output;
			}
			echo '<pre>';
//			var_dump($projects);
			echo '</pre>';
			
			$this->Project_model->Process_finished_projects($projects);
		}
	}
}
