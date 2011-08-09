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
}

?>
