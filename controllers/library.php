<?php
require_once "../controllers/base.php";

class Library extends Base
{
	public function Index()
	{
		$common_head_view = $this->Load_view('common_head_view', array());
		$this->Load_view('library_introduction_view', array('common_head_view' => $common_head_view));
	}
}
