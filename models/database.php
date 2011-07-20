<?php
require_once 'libraries/adodb/adodb.inc.php';

if(!function_exists ('Load_database'))
{
	function Load_database()
	{
		if(!isset($db))
		{
			$db = ADONewConnection('mysql');
			$db->Connect('localhost', 'morzo', 'gAgRoWyPLr', 'morzo');
		}
		return $db;
	}
}
?>
