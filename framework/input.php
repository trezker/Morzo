<?php

class Input {
	private $get = array();
	private $post = array();
	private $cookie = array();
	private $header = array();
	
	function __construct() {
		$this->get = $_GET;
		$this->post = $_POST;
		$this->cookie = $_COOKIE;
		$this->header = apache_request_headers();
	}

	public function Get($key) {
		if(isset($this->get[$key]))
			return $this->get[$key];
		return null;
	}
	
	public function Get_post($key) {
		if(isset($this->post[$key]))
			return $this->post[$key];
		return null;
	}

	public function Get_cookie($key) {
		if(isset($this->cookie[$key]))
			return $this->cookie[$key];
		return null;
	}

	public function Get_header($key) {
		if(isset($this->header[$key]))
			return $this->header[$key];
		return null;
	}

	public function Show_headers() {
		echo "<pre>";
		var_dump($this->header);
		echo "</pre>";
	}
}
