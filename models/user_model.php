<?php

require_once '../models/model.php';

class User_model extends Model
{
	public function Login_openid($openid)
	{
		$query = '
			SELECT
				u.ID,
				u.Username,
				u.Banned_from,
				u.Banned_to
			FROM User u
			JOIN User_openID uo ON uo.UserID = u.ID
			WHERE uo.OpenID = ?';
		$rs = $this->db->Execute($query, array($openid));
		if(!$rs)
		{
			return 'Query failed';
		}
		if($rs->RecordCount()!=1)
		{
			return 'Not found';
		}
		
		$banned_from = strtotime($rs->fields['Banned_from']);
		$banned_to = strtotime($rs->fields['Banned_to']);
		$now = time();

		if($banned_from != NULL && $banned_from <= $now
		&& ($banned_to == NULL || $banned_to > $now)) {
			return 'Banned';
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
		$query = '
			UPDATE User SET Session_ID = ?
			WHERE ID = ?';
		$session_id = session_id();
		$rs2 = $this->db->Execute($query, array($session_id, $id));
		if(!$rs2) {
			return 'Query failed';
		}
		return true;
	}

	public function Create_user_openid($username, $openid)
	{
		//$this->db->debug = true;
		$this->db->StartTrans();
		$query = "
			INSERT INTO User (Username, Max_actors)
			select ?, Value from Count where Name = 'Max_actors_account'
		";
		$rs = $this->db->Execute($query, array($username));
		if(!$rs) {
			$reason = $this->db->ErrorMsg();
			$this->db->FailTrans();
			$this->db->CompleteTrans();
			return array(
				'success' => false,
				'reason' => $reason
			);
		}
		$query = 'SELECT ID FROM User WHERE Username = ?';
		$rs = $this->db->Execute($query, array($username));
		if(!$rs) {
			$reason = $this->db->ErrorMsg();
			$this->db->FailTrans();
			$this->db->CompleteTrans();
			return array(
				'success' => false,
				'reason' => $reason
			);
		}
		if(!$rs) {
			$reason = $this->db->ErrorMsg();
			$this->db->FailTrans();
			$this->db->CompleteTrans();
			return array(
				'success' => false,
				'reason' => $reason
			);
		}
		if($rs->RecordCount()!=1) {
			$this->db->FailTrans();
			$this->db->CompleteTrans();
			return array(
				'success' => false,
				'reason' => 'Could not find user row, weird.',
			);
		}
		$userid = $rs->fields['ID'];

		$query = 'INSERT INTO User_openID (OpenID, UserID) VALUES(?, ?)';
		$rs = $this->db->Execute($query, array($openid, $userid));
		if(!$rs) {
			$reason = $this->db->ErrorMsg();
			$this->db->FailTrans();
			$this->db->CompleteTrans();
			return array(
				'success' => false,
				'reason' => $reason
			);
		}
		
		$this->db->CompleteTrans();
		return array(
			'success' => true,
			'ID' => $userid
		);
	}
	
	public function Add_user_openid($userid, $openid) {
		$query = 'INSERT INTO User_openID (OpenID, UserID) VALUES(?, ?)';
		$rs = $this->db->Execute($query, array($openid, $userid));
		if(!$rs) {
			$reason = $this->db->ErrorMsg();
			return array(
				'success' => false,
				'reason' => $reason
			);
		}
		
		return array(
			'success' => true,
			'ID' => $userid
		);
	}

	public function User_has_access($user_id, $accessname)
	{
		$rs = $this->db->Execute('
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
		$rs = $this->db->Execute('
			select U.ID, U.Username, U.Banned_from, U.Banned_to, U.Max_actors, U.Last_active
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
		$rs = $this->db->Execute('
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
		if($to_date == "") {
			$to_date = NULL;
		}

		$rs = $this->db->Execute('
			update User set Banned_from = NOW(), Banned_to = ?
			where ID = ?
			', array($to_date, $user_id));
		if(!$rs)
		{
			echo $this->db->ErrorMsg();
			return false;
		}

		if($this->db->Affected_rows() == 0)
		{
			return false;
		}
		return true;
	}

	public function Set_user_actor_limit($user_id, $actor_limit) {
		$actor_limit = intval($actor_limit);

		$rs = $this->db->Execute('
			update User set Max_actors = ?
			where ID = ?
			', array($actor_limit, $user_id));

		if(!$rs)
		{
			echo $this->db->ErrorMsg();
			return false;
		}

		if($this->db->Affected_rows() == 0)
		{
			return false;
		}
		return true;
	}
	
	public function Get_user_openids($userid) {
		$rs = $this->db->Execute('
			select ID, OpenID from User_openID where UserID = ?
			', array($userid));

		if(!$rs) {
			echo $this->db->ErrorMsg();
			return false;
		}
		
		return $rs->getArray();
	}
	
	public function Get_openid_icons() {
		$openid_icons = array(
			array(	'icon' => '/data/openid_icons/google.ico.png',
					'URI'	=> 'https://www.google.com/accounts/o8/id',
					'name'	=> 'Google'),
			array(	'icon' => '/data/openid_icons/myopenid.ico.png',
					'URI'	=> 'https://www.myopenid.com',
					'name'	=> 'myOpenID')
		);
		return $openid_icons;
	}

	public function Delete_user_openid($userid, $openid) {
		$rs = $this->db->Execute('
			select count(1) as Num_ids from User_openID where UserID = ?
			', array($userid));

		if(!$rs) {
			return array('success' => false, 'reason' => $this->db->ErrorMsg());
		}
		
		if($rs->fields['Num_ids'] < 2) {
			return array('success' => false, 'reason' => 'You may not delete your last openid');
		}

		$rs = $this->db->Execute('
			delete from User_openID where UserID = ? and ID = ?
			', array($userid, $openid));

		if(!$rs) {
			return array('success' => false, 'reason' => $this->db->ErrorMsg());
		}
		
		return array('success' => true);
	}
	
	public function Update_user_activity($userid) {
		$rs = $this->db->Execute('update User set Last_active = NOW() where ID = ?', array($userid));
	}
}
