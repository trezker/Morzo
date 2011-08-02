<?php
require_once "controllers/controller.php";

class Update extends Controller
{
	public function Index()
	{
		$this->Load_model("Travel_model");
		$travels = $this->Travel_model->Get_travels();
		
	}
}

?>
