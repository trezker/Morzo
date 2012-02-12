<?php
require_once "controllers/controller.php";

class Update extends Controller
{
	public function Index()
	{
		$this->Load_model("Travel_model");
		echo $this->Travel_model->Tick();
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
}

?>
