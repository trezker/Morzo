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

		$query = 'update Actor set Health = Health - 1 where Hunger >= 128';
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
			$tail = 'join Actor A on A.Inventory_ID = I.Inventory_ID where A.ID = ?';
		} elseif($table == 'Location_inventory') {
			$tail = 'join Location L on L.Inventory_ID = I.Inventory_ID
					join Actor A on A.Location_ID = L.ID
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
			from Inventory_resource I
			left join Resource R on I.Resource_ID = R.ID
			join Measure M on R.Measure = M.ID
			'.$tail
			, array($actor_id));
		
		if(!$rs) {
			echo $db->ErrorMsg();
			return false;
		}

		$result = array();
		$result['resources'] = $rs->getArray();

		if($table == 'Actor_inventory') {
			$tail = 'join Actor A on A.Inventory_ID = O.Inventory_ID
					where A.ID = ?';
		} elseif($table == 'Location_inventory') {
			$tail = 'join Location L on L.Inventory_ID = O.Inventory_ID
					join Actor A on L.ID = A.Location_ID
					where A.ID = ?';
		} else {
			return false;
		}

		$rs = $db->Execute('
			select
				P.ID,
				count(P.ID) as Amount,
				P.Name
			from Object O
			join Product P on P.ID = O.Product_ID
			'.$tail.'
			group by P.ID
			', array($actor_id));
		
		if(!$rs) {
			echo $db->ErrorMsg();
			return false;
		}

		$result['products'] = $rs->getArray();

		return $result;
	}

	public function Drop_resource($actor_id, $resource_id, $amount) {
		$db = Load_database();
		//$db->debug = true;
		
		$db->StartTrans();

		$rs = $db->Execute('
			select
				I.Amount as Actor_amount,
				I.Inventory_ID as Actor_inventory_ID,
				R.Mass,
				R.Volume,
				M.Name as Measure_name
			from Inventory_resource I
			join Actor A on I.Inventory_ID = A.Inventory_ID
			join Resource R on R.ID = I.Resource_ID
			join Measure M on R.Measure = M.ID
			where A.ID = ? and I.Resource_ID = ?
			'
			, array($actor_id, $resource_id));

		if(!$rs || $rs->RecordCount() != 1) {
			echo $db->ErrorMsg();
			echo "fail 1";
			$db->FailTrans();
		} else {
			$actor_inventory_id = $rs->fields['Actor_inventory_ID'];
			$actor_amount = $rs->fields['Actor_amount'];
			$measure_name = $rs->fields['Measure_name'];
			if($measure_name == 'Mass') {
				$amount_factor = $rs->fields['Mass'];
			} elseif($measure_name == 'Volume') {
				$amount_factor = $rs->fields['Volume'];
			} else {
				$amount_factor = 1;
			}
			
			$amount /= $amount_factor;
			
			if($actor_amount >= $amount) {
				if($actor_amount > $amount) {
					$rs = $db->Execute('
						update Inventory_resource set Amount = Amount - ?
						where Inventory_ID = ? and Resource_ID = ? and Amount >= ?
						', array($amount, $actor_inventory_id, $resource_id, $amount));
				} else {
					$rs = $db->Execute('
						delete from Inventory_resource
						where Inventory_ID = ? and Resource_ID = ?
						', array($actor_inventory_id, $resource_id));
				}
				if(!$rs || $db->Affected_Rows() !== 1) {
					$db->FailTrans();
				} else {
					$rs = $db->Execute('
						insert into Inventory_resource (Inventory_ID, Resource_ID, Amount)
						select L.Inventory_ID, ?, ?
						from Actor A
						join Location L on L.ID = A.Location_ID
						where A.ID = ? limit 1
						on duplicate key update Amount = Amount + ?
						', array($resource_id, $amount, $actor_id, $amount));

					if(!$rs) {
						echo $db->ErrorMsg();
						$db->FailTrans();
					}
				}
			}
		}

		$success = !$db->HasFailedTrans();
		$db->CompleteTrans();
		if($success != true)
			return false;

		return true;
	}

	public function Pick_up_resource($actor_id, $resource_id, $amount) {
		$db = Load_database();
		//$db->debug = true;
		
		$db->StartTrans();

		$rs = $db->Execute('
			select
				A.Location_ID,
				LI.Amount as Location_amount,
				LI.Inventory_ID as Location_inventory_ID,
				R.Mass,
				R.Volume,
				M.Name as Measure_name
			from Inventory_resource LI
			join Location L on L.Inventory_ID = LI.Inventory_ID
			join Actor A on A.Location_ID = L.ID
			join Resource R on R.ID = LI.Resource_ID
			join Measure M on R.Measure = M.ID
			where A.ID = ? and LI.Resource_ID = ?
			', array($actor_id, $resource_id));

		if(!$rs || $rs->RecordCount() != 1) {
			echo $db->ErrorMsg();
			echo "fail 1";
			$db->FailTrans();
		} else {
			$location_inventory_id = $rs->fields['Location_inventory_ID'];
			$location_amount = $rs->fields['Location_amount'];
			$location_id = $rs->fields['Location_ID'];
			$measure_name = $rs->fields['Measure_name'];
			if($measure_name == 'Mass') {
				$amount_factor = $rs->fields['Mass'];
			} elseif($measure_name == 'Volume') {
				$amount_factor = $rs->fields['Volume'];
			} else {
				$amount_factor = 1;
			}
			
			$amount /= $amount_factor;
			
			if($location_amount >= $amount) {
				if($location_amount > $amount) {
					$rs = $db->Execute('
						update Inventory_resource set Amount = Amount - ?
						where Inventory_ID = ? and Resource_ID = ? and Amount >= ?
						', array($amount, $location_inventory_id, $resource_id, $amount));
				} else {
					$rs = $db->Execute('
						delete from Inventory_resource
						where Inventory_ID = ? and Resource_ID = ?
						', array($location_inventory_id, $resource_id));
				}
				if(!$rs || $db->Affected_Rows() !== 1) {
					$db->FailTrans();
				} else {
					$rs = $db->Execute('
						insert into Inventory_resource (Inventory_ID, Resource_ID, Amount)
						select A.Inventory_ID, ?, ?
						from Actor A where A.ID = ? limit 1
						on duplicate key update Amount = Amount + ?
						', array($resource_id, $amount, $actor_id, $amount));

					if(!$rs) {
						echo $db->ErrorMsg();
						$db->FailTrans();
					}
				}
			}
		}

		$success = !$db->HasFailedTrans();
		$db->CompleteTrans();
		if($success != true)
			return false;

		return true;
	}

	public function Drop_product($actor_id, $product_id, $amount) {
		$inventories = $this->Get_actor_and_location_inventory($actor_id);
		if(!$inventories) {
			return false;
		}
		$this->Load_model('Inventory_model');
		return $this->Inventory_model->Transfer_product($inventories['Actor_inventory'], $inventories['Location_inventory'], $product_id, $amount);
	}

	public function Pick_up_product($actor_id, $product_id, $amount) {
		$inventories = $this->Get_actor_and_location_inventory($actor_id);
		if(!$inventories) {
			return false;
		}
		$this->Load_model('Inventory_model');
		return $this->Inventory_model->Transfer_product($inventories['Location_inventory'], $inventories['Actor_inventory'], $product_id, $amount);
	}
	
	public function Get_actor_and_location_inventory($actor_id) {
		$db = Load_database();
		//$db->debug = true;
		
		$rs = $db->Execute('
			select
				A.Inventory_ID as Actor_inventory,
				L.Inventory_ID as Location_inventory
			from Actor A
			join Location L on A.Location_ID = L.ID
			where A.ID = ?
			'
			, array($actor_id));
		
		if(!$rs) {
			return false;
		}
		return $rs->fields;
	}
}
