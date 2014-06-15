<?php

require_once '../libraries/adodb/adodb.inc.php';

class Database {
	private $database = null;
	
	function __construct($config) {
		$this->database = ADONewConnection('mysqli');
		$this->database->Connect($config['host'], $config['user'], $config['password'], $config['database']);
		$this->database->Execute("set names 'utf8'");
		$this->database->SetFetchMode(ADODB_FETCH_ASSOC);
	}

	function Execute($query, $args) {
		$result = $this->database->Execute($query, $args);
		return $result;
	}
	
	function Affected_Rows() {
		return $this->database->Affected_Rows();
	}
}
