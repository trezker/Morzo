<?php

require_once 'models/database.php';

class User_model
{
	public function Login_openid($openid)
	{
		$db = Load_database();

		$query = '
			SELECT
				u.ID,
				u.Username
			FROM User u
			JOIN User_openID uo ON uo.UserID = u.ID
			WHERE uo.OpenID = ?';
		$rs = $db->Execute($query, array($openid));
		if(!$rs)
		{
			return 'Query failed';
		}
		if($rs->RecordCount()!=1)
		{
			return 'Not found';
		}

		$query = '
			UPDATE User SET Session_ID = ?
			WHERE ID = ?';
		$session_id = session_id();
		$rs2 = $db->Execute($query, array($session_id, $rs->fields['ID']));
		if(!$rs2) {
			return 'Query failed';
		}
		
		return array(
			'ID' => $rs->fields['ID'],
			'Username' => $rs->fields['Username'],
		);
	}

	public function Create_user_openid($username, $openid)
	{
		$db = Load_database();
		
		$db->StartTrans();
		$query = 'INSERT INTO User (Username) VALUES(?)';
		$rs = $db->Execute($query, array($username));
		if(!$rs) {
			$db->FailTrans();
			$db->CompleteTrans();
			return array(
				'success' => false,
				'reason' => $db->ErrorMsg(),
			);
		}
		$query = 'SELECT ID FROM User WHERE Username = ?';
		$rs = $db->Execute($query, array($username));
		if(!$rs) {
			$db->FailTrans();
			$db->CompleteTrans();
			return array(
				'success' => false,
				'reason' => $db->ErrorMsg(),
			);
		}
		if($rs->RecordCount()!=1) {
			$db->FailTrans();
			$db->CompleteTrans();
			return array(
				'success' => false,
				'reason' => 'Could not find user row, weird.',
			);
		}
		$userid = $rs->fields['ID'];

		$query = 'INSERT INTO User_openID (OpenID, UserID) VALUES(?, ?)';
		$rs = $db->Execute($query, array($openid, $userid));
		if(!$rs) {
			$db->FailTrans();
			$db->CompleteTrans();
			return array(
				'success' => false,
				'reason' => $db->ErrorMsg(),
			);
		}
		
		$db->CompleteTrans();
		return array(
			'success' => true,
			'ID' => $userid
		);
	}

	public function User_has_access($user_id, $accessname)
	{
		$db = Load_database();

		$rs = $db->Execute('
			select U.ID
			from User_access UA
			join User U on U.ID = UA.User_ID
			join Access A on A.ID = UA.Access_ID
			where U.ID = ? and A.Accessname = ?
			', array($user_id, $accessname));
		if(!$rs)
		{
			return false;
		}
		if($rs->RecordCount() == 1)
		{
			return true;
		}
		return false;
	}
	
	public function Get_users()
	{
		$db = Load_database();

		$rs = $db->Execute('
			select U.ID, U.Username
			from User U
			', array());
		if(!$rs)
		{
			return false;
		}
		if($rs->RecordCount() > 0)
		{
			$users = array();
			foreach ($rs as $row) {
				$users[] = $row;
			}
			return $users;
		}
		return false;
	}
}
?>
