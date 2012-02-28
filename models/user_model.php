<?php

require_once '../models/database.php';

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

		$l = $this->Login($rs->fields['ID']);
		if($l !== true) {
			return $l;
		}
		
		return array(
			'ID' => $rs->fields['ID'],
			'Username' => $rs->fields['Username'],
		);
	}

	public function Login($id)
	{
		$db = Load_database();
		$query = '
			UPDATE User SET Session_ID = ?
			WHERE ID = ?';
		$session_id = session_id();
		$rs2 = $db->Execute($query, array($session_id, $id));
		if(!$rs2) {
			return 'Query failed';
		}
		return true;
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
			select U.ID, U.Username, U.Banned_from, U.Banned_to
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
	
	public function Get_session_id($user_id)
	{
		$db = Load_database();

		$rs = $db->Execute('
			select U.Session_ID
			from User U
			where U.ID = ?
			', array($user_id));
		if(!$rs)
		{
			return false;
		}

		if($rs->RecordCount() !== 1)
		{
			return false;
		}
		return $rs->fields['Session_ID'];
	}

	public function Set_ban($user_id, $to_date)
	{
		$db = Load_database();
		if($to_date == "") {
			$to_date = NULL;
		}

		$rs = $db->Execute('
			update User set Banned_from = NOW(), Banned_to = ?
			where ID = ?
			', array($to_date, $user_id));
		if(!$rs)
		{
			echo $db->ErrorMsg();
			return false;
		}

		if($db->Affected_rows() == 0)
		{
			return false;
		}
		return true;
	}
}

