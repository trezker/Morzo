<?php
require_once 'libraries/adodb/adodb.inc.php';

if(!function_exists ('Load_database'))
{
	function Load_database()
	{
		if(!isset($db))
		{
			include 'config.php';
			$db = ADONewConnection('mysql');
			$db->Connect($config['db_host'], $config['db_user'], $config['db_password'], $config['db_database']);
		}
		return $db;
	}
}
?>
