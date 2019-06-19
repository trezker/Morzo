<?php
require_once '../libraries/adodb/adodb.inc.php';

if(!function_exists ('Create_database_connection')) {
	function Create_database_connection($config) {
		if(!isset($db)) {
			$db = ADONewConnection('mysqli');
			$db->Connect($config['host'], $config['user'], $config['password'], $config['database']);
			$db->Execute("set names 'utf8'");
			$db->SetFetchMode(ADODB_FETCH_ASSOC);
		}
		return $db;
	}
}

