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

		$db->StartTrans();
		
		//Create actor inventory
		$r = $db->Execute('
			insert into Inventory values()', array());
		
		if(!$r) {
			$db->FailTrans();
		} else {
			$inventory_ID = $db->Insert_id();
		}

		if(!$db->HasFailedTrans()) {
			$rs = $db->Execute('
				insert into Actor(Location_ID, Inventory_ID)
				values (?,?)
				', array($location_id, $inventory_ID));
		}

		$failed = $db->HasFailedTrans();
		$db->CompleteTrans();
		
		if($failed)
		{
			return false;
		}

		$from_actor_id = $db->Insert_id();
		$from_actor_id = $db->Insert_ID();
		$this->Load_model('Event_model');
		$this->Event_model->Save_event("{LNG_Actor_born}", $from_actor_id, NULL);
		return true;
	}
	
	public function Update_actors($time) {
		$db = Load_database();

		$this->Load_model('Event_model');
		//if(($time % 16) + 1 == 8)
		{
			$db->StartTrans();
			
			//Select all Actors with more than 8 hunger. They haven't eaten manually lately.
			$query = "select ID, Hunger, Health, Inventory_ID from Actor where Hunger > 8";
			$args = array();
			$hungry_actors = $db->Execute($query, $args);

			foreach ($hungry_actors as $hungry_actor) {
				$query = "
					select R.ID, R.Name, CF.Nutrition, R.Mass, CF.Nutrition / R.Mass as Efficiency, IR.Amount as Amount, 'Resource' as Setname from Resource R
					join Resource_category RC on R.ID = RC.Resource_ID
					join Category_food CF on RC.Category_ID = CF.Category_ID
					join Inventory_resource IR on IR.Resource_ID = R.ID
					where IR.Inventory_ID = ?
					union DISTINCT 
					select P.ID, P.Name, CF.Nutrition, P.Mass, CF.Nutrition / P.Mass as Efficiency, count(P.ID) as Amount, 'Product' as Setname from Product P
					join Product_category PC on P.ID = PC.Product_ID
					join Category_food CF on PC.Category_ID = CF.Category_ID
					join Object O on O.Product_ID = P.ID
					where O.Inventory_ID = ?
					group by P.ID
					order by Efficiency asc
				";
				$args = array($hungry_actor['Inventory_ID'], $hungry_actor['Inventory_ID']);
				$rs = $db->Execute($query, $args);

				$food_names = "";
				$hunger = $hungry_actor['Hunger'];
				foreach ($rs as $r) {
					if($r['Setname'] = 'Resource') {
						$consume_nutrition = $r['Nutrition'] * $r['Amount'];
					
						if($hunger <= $consume_nutrition) {
							$consume_nutrition = $hunger;
							$hunger = 0;
						} else {
							$hunger -= $consume_nutrition;
						}
						echo " consuming: " . $consume_nutrition / $r['Nutrition'] . ' ' . $r['Name'];
						if($hunger > 0) {
							$query = "
								delete from Inventory_resource where Resource_ID = ? and Inventory_ID = ?
							";
							$args = array($r['ID'], $hungry_actor['Inventory_ID']);
							$rs = $db->Execute($query, $args);
							if(!$rs) {
								echo $db->ErrorMsg();
							}
						} else {
							$query = "
								update Inventory_resource set Amount = ? where Resource_ID = ? and Inventory_ID = ?
							";
							$args = array($r['Amount'] - ($consume_nutrition / $r['Nutrition']), $r['ID'], $hungry_actor['Inventory_ID']);
							$rs = $db->Execute($query, $args);
							if(!$rs) {
								echo $db->ErrorMsg();
							}
						}
					}
					if($food_names != "") {
						$food_names .= ", ";
					}
					$food_names .= $r['Name'];
				}
				$query = "
					update Actor set Hunger = ? where ID = ?
				";
				$args = array($hunger, $hungry_actor['ID']);
				$rs = $db->Execute($query, $args);
				if($hunger < $hungry_actor['Hunger']) {
					$this->Event_model->Save_event("{LNG_Actor_ate} ", $hungry_actor['ID'], NULL, $food_names, NULL, NULL, true);
				}
			}
			$success = !$db->HasFailedTrans();
			$db->CompleteTrans();
			if($success == false) {
				return false;
			}
		}

		$query = 'update Actor set Health = Health - 1 where Hunger >= 128 and Health > 0';
		$args = array();
		$rs = $db->Execute($query, $args);
		if(!$rs) {
			echo $db->ErrorMsg();
			return false;
		}

		$query = 'update Actor set Health = Health + 1 where Hunger < 128 and Health < 128';
		$args = array();
		$rs = $db->Execute($query, $args);
		if(!$rs) {
			echo $db->ErrorMsg();
			return false;
		}

		$query = 'update Actor set Hunger = Hunger + 1 where Hunger < 128';
		$args = array();
		$rs = $db->Execute($query, $args);
		if(!$rs) {
			echo $db->ErrorMsg();
			return false;
		}
		return true;
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
				A.Inside_object_ID,
				P.Name as Inside_object_name,
				B.Name as Biome_name,
				T.Value as Time,
				A.Hunger,
				A.Health
			from Actor A
			left join Actor_name AN on A.ID = AN.Actor_ID and A.ID = AN.Named_actor_ID
			left join Location_name LN on A.ID = LN.Actor_ID and A.Location_ID = LN.Location_ID
			left join Location L on A.Location_ID = L.ID
			left join Object O on O.ID = A.Inside_object_ID
			left join Product P on P.ID = O.Product_ID
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
			join Actor A on not Me.ID = A.ID and (Me.Inside_object_ID is NULL and A.Inside_object_ID is NULL and Me.Location_ID = A.Location_ID)
							or Me.Inside_object_ID = A.Inside_object_ID
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
	
	public function Get_containers_on_location($actor_id) {
		$db = Load_database();

		$sql = '
				select
					O.ID,
					P.Name
				from Actor A
				join Location L on A.Location_ID = L.ID
				left join Object_inventory II on II.Object_ID = A.Inside_object_ID
				
				join Object O on (A.Inside_object_ID is null and O.Inventory_ID = L.Inventory_ID) 
							  or (A.Inside_object_ID is not null and O.Inventory_ID = II.Inventory_ID)
				join Object_inventory OI on OI.Object_ID = O.ID
				join Product P on P.ID = O.Product_ID
				where A.ID = ?
				';

		$rs = $db->Execute($sql, array($actor_id));
		
		if(!$rs) {
			echo $db->ErrorMsg();
			return false;
		}

		return $rs->getArray();
	}
	
	public function Get_actor_and_location_inventory($actor_id) {
		$db = Load_database();
		//$db->debug = true;
		
		$rs = $db->Execute('
			select
				A.Inventory_ID as Actor_inventory,
				ifnull(O.Inventory_ID, L.Inventory_ID) as Location_inventory
			from Actor A
			join Location L on A.Location_ID = L.ID
			left join Object_inventory O on O.Object_ID = A.Inside_object_ID
			where A.ID = ?
			'
			, array($actor_id));
		
		if(!$rs) {
			return false;
		}
		return $rs->fields;
	}

	public function Enter_object($actor_id, $object_id) {
		$db = Load_database();
		//$db->debug = true;
		
		$db->StartTrans();

		//I will need information about the state to generate an event about moving locations
		//The object is either found while I'm not in an object or the object has to be inside the object I'm in.
		//OO checks that the object is a container, later on check for sufficient capacity.
		$rs = $db->Execute('
			select
				OO.Inventory_ID
			from Actor A
			join Location L on A.Location_ID = L.ID
			left join Object_inventory OI on A.Inside_object_ID = OI.Object_ID
			join Object O on O.Inventory_ID = L.Inventory_ID or O.Inventory_ID = OI.Inventory_ID
			join Object_inventory OO on O.ID = OO.Object_ID
			where A.ID = ? and O.ID = ? and (OI.Inventory_ID is NULL or OI.Inventory_ID = O.Inventory_ID)
			', array($actor_id, $object_id));
		
		if(!$rs) {
			$db->FailTrans();
			$db->CompleteTrans();
			return array('success' => false, 'data' => 'Query error');
		}
		if($rs->RecordCount() !== 1) {
			$db->FailTrans();
			$db->CompleteTrans();
			return array('success' => false, 'data' => 'Not allowed');
		}

		$this->Load_model('Inventory_model');
		if(!$this->Inventory_model->Is_inventory_accessible($actor_id, $rs->fields['Inventory_ID'])) {
			$db->FailTrans();
			$db->CompleteTrans();
			return array('success' => false, 'data' => 'Inventory not accessible');
		}

		$object_name = $this->Inventory_model->Get_object_name($object_id);
		$this->Load_model('Event_model');
		$r = $this->Event_model->Save_event('{LNG_Actor_entered_object}', $actor_id, NULL, $object_name, NULL, NULL, false, $object_id);
		if(!$r) {
			$db->FailTrans();
			$db->CompleteTrans();
			return array('success' => false, 'data' => 'Failed to generate event');
		}

		$rs = $db->Execute('
			update Actor set Inside_object_ID = ? where ID = ?
			'
			, array($object_id, $actor_id));
		
		$success = !$db->HasFailedTrans();
		$db->CompleteTrans();
		if(!$success) {
			return array('success' => false, 'data' => 'Database error');
		}
		return array('success' => true);
	}

	public function Leave_object($actor_id) {
		$db = Load_database();
		//$db->debug = true;
		
		$db->StartTrans();

		//TODO: When you can enter objects recursively, move to the container of the current object.
		//Check for locked status when implemented

		//Will get either an Object_ID or Location_ID, the other will be null
		$sql = '
				select 
					O.ID as Object_ID,
					L.ID as Location_ID,
					OO.Object_ID as From_object_ID
				from Actor A
				join Object IO on A.Inside_object_ID = IO.ID
				left join Location L on L.Inventory_ID = IO.Inventory_ID
				left join Object_inventory OI on IO.Inventory_ID = OI.Inventory_ID
				left join Object O on OI.Object_ID = O.ID
				join Object_inventory OO on OO.Object_ID = A.Inside_object_ID
				where A.ID = ?
				';

		$rs = $db->Execute($sql, array($actor_id));
		if(!$rs) {
			$db->FailTrans();
			$db->CompleteTrans();
			return array('success' => false, 'reason' => 'Database error');
		}
		if($rs->RecordCount() !== 1) {
			$db->FailTrans();
			$db->CompleteTrans();
			return array('success' => false, 'data' => 'Not inside an object');
		}
		$from_object_id = $rs->fields['From_object_ID'];
		$this->Load_model('Inventory_model');

		if($this->Inventory_model->Is_object_locked($from_object_id)) {
			$db->FailTrans();
			$db->CompleteTrans();
			return array('success' => false, 'data' => 'Locked');
		}

		if($rs->fields['Object_ID']) {
			$rs = $db->Execute('update Actor set Inside_object_ID = ? where ID = ?', array($rs->fields['Object_ID'], $actor_id));
		} else {
			$rs = $db->Execute('update Actor set Inside_object_ID = NULL, Location_ID = ? where ID = ?', array($rs->fields['Location_ID'], $actor_id));
		}

		$object_name = $this->Inventory_model->Get_object_name($from_object_id);
		$this->Load_model('Event_model');
		$r = $this->Event_model->Save_event('{LNG_Actor_left_object}', $actor_id, NULL, $object_name, NULL, NULL, false, $from_object_id);
		if(!$r) {
			$db->FailTrans();
			$db->CompleteTrans();
			return array('success' => false, 'data' => 'Failed to generate event');
		}
		
		$success = !$db->HasFailedTrans();
		$db->CompleteTrans();
		if(!$success) {
			return array('success' => false, 'data' => 'Database error');
		}

		return array('success' => true);
	}
}
