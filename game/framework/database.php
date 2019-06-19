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
	
	function Debug($on) {
		$this->database->debug = $on;
	}

	function Execute($query, $args = array()) {
		$result = $this->database->Execute($query, $args);
		return $result;
	}
	
	function Affected_Rows() {
		return $this->database->Affected_Rows();
	}
	
	function StartTrans() {
		$this->database->StartTrans();
	}

	function FailTrans() {
		$this->database->FailTrans();
	}

	function HasFailedTrans() {
		return $this->database->HasFailedTrans();
	}

	function CompleteTrans() {
		$this->database->CompleteTrans();
	}

	function Insert_id() {
		return $this->database->Insert_id();
	}

	function ErrorMsg() {
		return $this->database->ErrorMsg();
	}
}
