<?php

require_once '../models/model.php';
require_once '../models/database.php';

class Actor_model extends Model
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
			$from_actor_id = $db->Insert_ID();
			$this->Load_model('Event_model');
			$this->Event_model->Save_event("{LNG_Actor_born}", $from_actor_id, NULL);
			return true;
		}

		return false;
	}
	
	public function Request_actor($user_id)
	{
		$db = Load_database();

		$rs = $db->Execute('
			select count(*) >= U.Max_actors as Max_actors_reached, U.Max_actors
			from Actor A 
			join User U on U.ID = A.User_ID
			where U.ID = ?
			', array($user_id));
			
		if(!$rs)
		{
			return array('success' => false, 'reason' => 'Database failure');
		}
		
		if($rs->fields['Max_actors_reached'] == 1) {
			return array('success' => false, 'reason' => 'Reached max number of actors');
		}

		$rs = $db->Execute('
			update Actor A
			set A.User_ID = ?
			where A.User_ID is null and A.Inhabitable = true
			order by A.ID asc
			limit 1
			', array($user_id));
		
		if(!$rs)
		{
			return array('success' => false, 'reason' => 'Database failure');
		}
		if($db->Affected_Rows() == 1)
		{
			return array('success' => true);
		}

		return array('success' => false, 'reason' => 'No actors available');
	}

	public function Get_users_actor_limit($user_id)
	{
		$db = Load_database();

		$rs = $db->Execute('
			select count(*) >= U.Max_actors as Max_actors_reached, U.Max_actors, count(*) as Num_actors
			from Actor A 
			join User U on U.ID = A.User_ID
			where U.ID = ?
			', array($user_id));
			
		if(!$rs)
		{
			return false;
		}

		return $rs->fields;
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
	
	public function Get_actor_inventory($actor_id) {
		return $this->Get_inventory($actor_id, 'Actor_inventory');
	}

	public function Get_location_inventory($actor_id) {
		return $this->Get_inventory($actor_id, 'Location_inventory');
	}
	
	private function Get_inventory($actor_id, $table) {
		$db = Load_database();

		if($table == 'Actor_inventory') {
			$tail = 'where I.Actor_ID = ?';
		} elseif($table == 'Location_inventory') {
			$tail = 'join Actor A on A.Location_ID = I.Location_ID
					where A.ID = ?';
		} else {
			return false;
		}
		
		$rs = $db->Execute('
			select
				I.ID,
				I.Resource_ID,
				I.Amount,
				R.Name,
				R.Mass,
				R.Volume,
				M.Name as Measure_name
			from '.$table.' I
			left join Resource R on I.Resource_ID = R.ID
			join Measure M on R.Measure = M.ID
			'.$tail
			, array($actor_id));
		
		if(!$rs) {
			echo $db->ErrorMsg();
			return false;
		}
		return $rs->getArray();
	}

	public function Drop_resource($actor_id, $resource_id, $amount) {
		$db = Load_database();
		$db->debug = true;
		
		$db->StartTrans();

		$rs = $db->Execute('
			select
				AI.Amount as Actor_amount,
				LI.Amount as Location_amount
			from Actor A
			join Actor_inventory AI on A.ID = AI.Actor_ID
			left join Location_inventory LI on A.Location_ID = LI.Location_ID and LI.Resource_ID = AI.Resource_ID
			where A.ID = ? and AI.Resource_ID = ?
			'
			, array($actor_id, $resource_id));

		//TODO: update or delete in actor inventory
		
		$rs = $db->Execute('
			update Actor_inventory set Amount = Amount - ?
			where Actor_ID = ? and Resource_ID = ? and Amount >= ?
			'
			, array($amount, $actor_id, $resource_id, $amount));
			
		if(!$rs || $db->Affected_Rows() !== 1) {
			$db->FailTrans();
		}

		if(!$db->HasFailedTrans()) {
			$rs = $db->Execute('
				insert into Location_inventory (Location_ID, Resource_ID, Amount)
				select Location_ID, ?, ? from Actor where ID = ? limit 1
				on duplicate key update Amount = Amount + ?
				'
				, array($resource_id, $amount, $actor_id, $amount));
		}

		if(!$rs || $db->Affected_Rows() !== 1) {
			echo $db->ErrorMsg();
			$db->FailTrans();
		}
		
		$success = !$db->HasFailedTrans();
		$db->CompleteTrans();
		if($success != true)
			return false;

		return true;
	}
}

