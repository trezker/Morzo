<?php

require_once 'models/database.php';

class Actor_model
{
	public function Request_actor($user_id)
	{
		$db = Load_database();

		$rs = $db->Execute('
			update Actor A
			set A.User_ID = ?
			where A.User_ID is null and A.Inhabitable = true
			order by A.ID asc
			limit 1
			', array($user_id));
		
		if(!$rs)
		{
			return false;
		}
		if($db->Affected_Rows() == 1)
		{
			return true;
		}

		return false;
	}
	
	public function Get_actors($user_id)
	{
		$db = Load_database();

		$rs = $db->Execute('
			select A.ID
			from Actor A
			where A.User_ID = ?
			', array($user_id));
		
		if(!$rs)
		{
			return false;
		}
		$actors = array();
		foreach ($rs as $row) {
    		$actors[] = $row['ID'];
		}
		return $actors;
	}

	public function Get_actor($actor_id)
	{
		$db = Load_database();

		$rs = $db->Execute('
			select AN.Name as Name, LN.Name as Location, A.Location_ID as Location_ID
			from Actor A
			left join Actor_name AN on A.ID = AN.Actor_ID and A.ID = AN.Named_actor_ID
			left join Location_name LN on A.ID = LN.Actor_ID and A.Location_ID = LN.Location_ID
			where A.ID = ?
			', array($actor_id));
		
		if(!$rs)
		{
			return false;
		}
		return Array(
			'Name' => $rs->fields['Name'], 
			'Location' => $rs->fields['Location'], 
			'Location_ID' => $rs->fields['Location_ID']
		);
	}
	
	public function Change_actor_name($actor_ID, $named_actor_ID, $new_name)
	{
		$db = Load_database();

		$rs = $db->Execute('
			update Actor_name set name = ?
			where Actor_ID = ? and Named_actor_ID = ?
			', array($new_name, $actor_ID, $named_actor_ID));
		
		if(!$rs)
		{
			return false;
		}
		return true;
	}
}
?>
