<?php

// apache_request_headers replacement for nginx
if (!function_exists('apache_request_headers')) { 
	function apache_request_headers() { 
		foreach($_SERVER as $key=>$value) { 
			if (substr($key,0,5)=="HTTP_") { 
				$key=str_replace(" ","-",ucwords(strtolower(str_replace("_"," ",substr($key,5))))); 
				$out[$key]=$value; 
			}
			else {
				$out[$key]=$value; 
			}
		} 
		return $out; 
	}
}

class Input {
	private $get = array();
	private $post = array();
	private $cookie = array();
	private $header = array();
	private $globals = array();
	
	function __construct() {
		$this->get = $_GET;
		$this->post = $_POST;
		$this->cookie = $_COOKIE;
		$this->header = apache_request_headers();
		$this->globals = $GLOBALS;
	}

	public function Get($key) {
		if(isset($this->get[$key]))
			return $this->get[$key];
		return null;
	}
	
	public function Get_post($key = NULL) {
		if($key === NULL)
			return $this->post;
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

	public function Get_global($key) {
		if(isset($this->globals[$key]))
			return $this->globals[$key];
		return null;
	}

	public function Show_headers() {
		echo "<pre>";
		var_dump($this->header);
		echo "</pre>";
	}
}
