<?php

require_once 'models/database.php';

class User_model
{
	public function RegisterQueue($email)
	{
		$db = Load_database();

		$rs = $db->Execute('SELECT ID FROM RegistrationQueue WHERE Email = ?', array($email));
		if(!$rs)
		{
			return false;
		}
		if($rs->RecordCount()>0)
		{
			return 'Duplicate';
		}
		$rs = $db->Execute('INSERT INTO RegistrationQueue (Email) VALUES (?)', array($email));
		if(!$rs)
		{
			return false;
		}
		
		return 'Registered';
	}
	
	public function Register($username, $password)
	{
		$salt = '8s7gh2W9WQ';
		$hashed = hash ("sha256", $password);
		$hashed = hash ("sha256", $hashed.$salt);
		$hashed = hash ("sha256", $hashed.$username);

		$db = Load_database();
		$rs = $db->Execute('INSERT INTO User (Username, Password) VALUES (?, ?)', array($username, $hashed));
		if(!$rs)
		{
			return false;
		}
		
		return 'Registered';
	}
	
	public function Login($username, $password)
	{
//		echo 'Login model';
		//get row from database
		$db = Load_database();

		$rs = $db->Execute('SELECT ID, Password FROM User WHERE Username = ?', array($username));
		if(!$rs)
		{
			return 'Query failed';
		}
		if($rs->RecordCount()!=1)
		{
			return 'Not found';
		}
		
		//hash
		$salt = '8s7gh2W9WQ';
		$hashed = hash ("sha256", $password);
		$hashed = hash ("sha256", $hashed.$salt);
		$hashed = hash ("sha256", $hashed.$username);
		if($rs->fields['Password'] != $hashed)
		{
			return 'Wrong password';
		}

		//login
		
		return $rs->fields['ID'];
	}

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
}
?>
