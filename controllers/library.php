<?php
require_once "../controllers/base.php";

class Library extends Base {
	public function Index() {
		return array(
			'view' => 'library_introduction_view',
			'data' => array()
		);
	}
}
