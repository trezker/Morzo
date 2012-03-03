<?php

require_once '../models/database.php';

class Actor_model
{
	public function User_owns_actor($user_id, $actor_id)
	{
		$db = Load_database();

		$rs = $db->Execute('
			select ID
			from Actor A
			where A.User_ID = ? and A.ID = ?
			', array($user_id, $actor_id));
		
		if(!$rs)
		{
			return false;
		}
		if($rs->RecordCount()==1)
		{
			return true;
		}

		return false;
	}
	
	public function Spawn_actor($location_id) {
		$db = Load_database();

		$rs = $db->Execute('
			select count(*)<C.Value as Allow_more_actors from Actor A join Count C on C.Name = \'Max_actors\';
			', array());

		if(!$rs || $rs->fields['Allow_more_actors'] != 1)
		{
			return false;
		}

		$rs = $db->Execute('
			insert into Actor(Location_ID)
			values (?)
			', array($location_id));
		
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
			select A.ID, AN.Name
			from Actor A
			left join Actor_name AN on A.ID = AN.Named_actor_ID and A.ID = AN.Actor_ID
			where A.User_ID = ?
			', array($user_id));
		
		if(!$rs)
		{
			return false;
		}
		$actors = array();
		foreach ($rs as $row) {
			if(!$row['Name'])
				$row['Name'] = 'Unnamed actor';
    		$actors[] = $row;
		}
		return $actors;
	}

	public function Get_actor($actor_id)
	{
		$db = Load_database();

		$rs = $db->Execute('
			select 
				AN.Name as Name, 
				LN.Name as Location, 
				A.Location_ID as Location_ID, 
				B.Name as Biome_name,
				T.Value as Time
			from Actor A
			left join Actor_name AN on A.ID = AN.Actor_ID and A.ID = AN.Named_actor_ID
			left join Location_name LN on A.ID = LN.Actor_ID and A.Location_ID = LN.Location_ID
			left join Location L on A.Location_ID = L.ID
			left join Biome B on L.Biome_ID = B.ID
			join Count T on T.Name = \'Update\'
			where A.ID = ?
			', array($actor_id));
		
		if(!$rs)
		{
			return false;
		}
		if($rs->RecordCount()!=1) {
			echo "CAUT HERE?";
			return false;
		}

		return $rs->fields;
	}
	
	public function Change_actor_name($actor_ID, $named_actor_ID, $new_name)
	{
		$db = Load_database();

		$rs = $db->Execute('
			insert into Actor_name(Named_actor_ID, Actor_ID, Name) values(?, ?, ?)
			on duplicate key update Name = ?
			', array($named_actor_ID, $actor_ID, $new_name, $new_name));
		
		if(!$rs) {
			return false;
		}
		return true;
	}
	
	public function Get_visible_actors($actor_ID) {
		$db = Load_database();

		$rs = $db->Execute('
			select A.ID, AN.Name
			from Actor Me
			join Actor A on Me.Location_ID = A.Location_ID and not Me.ID = A.ID
			left join Actor_name AN on A.ID = AN.Named_actor_ID and Me.ID = AN.Actor_ID
			where Me.ID = ?
			', array($actor_ID));
		
		if(!$rs) {
			return false;
		}
		$actors = array();
		foreach ($rs as $row) {
			if(!$row['Name'])
				$row['Name'] = 'Unnamed actor';
    		$actors[] = $row;
		}
		return $actors;
	}
}

