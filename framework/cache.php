<?php

class Cache {
	private $memcache = null;

	function __construct() {
		$this->memcache = new Memcache;	
		$this->memcache->addServer('127.0.0.1', 11211);
	}

	function Get($key) {
		return $this->memcache->get($key);
	}

	function Set($key, $object, $timeout = 60) {
		return $this->memcache->set($key, $object, 0, $timeout);
	}

	function Delete($key) {
		return $this->memcache->delete($key);
	}
}
