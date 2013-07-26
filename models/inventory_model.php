<?php

require_once '../models/model.php';
require_once '../models/database.php';

class Inventory_model extends Model
{
	private function Get_inventory_resources($id) {
		$db = Load_database();

		$rs = $db->Execute('
			select
				I.ID,
				I.Resource_ID,
				I.Amount,
				R.Name,
				R.Mass,
				R.Volume,
				M.Name as Measure_name
			from Inventory_resource IR
			left join Resource R on IR.Resource_ID = R.ID
			join Measure M on R.Measure = M.ID
			where IR.Inventory_ID = ?
			', array($id));
		
		if(!$rs) {
			echo $db->ErrorMsg();
			return false;
		}
		return $rs->getArray();
	}

	public function Transfer_resource($from_id, $to_id, $resource_id, $amount) {
		$error = NULL;
				
		$db = Load_database();
		//$db->debug = true;
		
		$db->StartTrans();

		$rs = $db->Execute('
			select
				IR.Amount,
				R.Mass,
				R.Volume,
				M.Name as Measure_name,
				I.Mass_limit,
				I.Volume_limit,
				I.ID
			from Inventory I
			join Resource R on R.ID = ?
			join Measure M on R.Measure = M.ID
			left join Inventory_resource IR on I.ID = IR.Inventory_ID and R.ID = IR.Resource_ID
			where (I.ID = ? or I.ID = ?)
			', array($resource_id, $from_id, $to_id));

		if(!$rs || $rs->RecordCount() != 2) {
			echo $db->ErrorMsg();
			$error = "Query broken or inventory missing";
			$db->FailTrans();
		} else {
			$from_row = NULL;
			$to_row = NULL;
			while ($arr = $rs->FetchRow()) {
				if($arr['ID'] == $from_id)
					$from_row = $arr;
				if($arr['ID'] == $to_id)
					$to_row = $arr;
	        }
/*
	        echo "<pre>FROM";
	        var_dump($from_row);
	        echo "TO";
	        var_dump($to_row);
	        echo "</pre>";
*/	        
	        $mass_factor = $to_row['Mass'];
	        $volume_factor = $to_row['Volume'];
	        $measure_name = $to_row['Measure_name'];
	        
			$amount_units = $amount;
			if($measure_name == "Mass")
				$amount_units = $amount / $mass_factor;
			elseif($measure_name == "Volume")
				$amount_units = $amount / $volume_factor;
			
			$result_units = $amount_units;
	        if($to_row['Amount'] !== NULL) {
        		$result_units += $to_row['Amount'];
			}
			
			$from_result_units = $from_row['Amount'] - $amount_units;
	        if($from_result_units < 0) {
				$error = "Not enough in source to transfer";
				$db->FailTrans();
			}
			elseif($to_row['Mass_limit'] != NULL and $result_units > $to_row['Mass_limit'] / $mass_factor) {
				$error = "Transfer would exceed mass limit of destination";
				$db->FailTrans();
			}
			elseif($to_row['Volume_limit'] != NULL and $result_units > $to_row['Volume_limit'] / $volume_factor) {
				$error = "Transfer would exceed volume limit of destination";
				$db->FailTrans();
			}
			if(!$db->HasFailedTrans()) {
				if($from_result_units <= 0) {
					$rs = $db->Execute('
						delete from Inventory_resource
						where Inventory_ID = ? and Resource_ID = ?
						', array($from_id, $resource_id));
				} else {
					$rs = $db->Execute('
						update Inventory_resource set Amount = ?
						where Inventory_ID = ? and Resource_ID = ?
						', array($from_result_units, $from_id, $resource_id));
				}

				if(!$rs) {
					$error = $db->ErrorMsg();
					$db->FailTrans();
				} else {
					$rs = $db->Execute('
						insert into Inventory_resource (Inventory_ID, Resource_ID, Amount)
						values (?, ?, ?)
						on duplicate key update Amount = ?
						', array($to_id, $resource_id, $result_units, $result_units));

					if(!$rs) {
						$error = $db->ErrorMsg();
						$db->FailTrans();
					}
				}
			}
		}

		$success = !$db->HasFailedTrans();
		$db->CompleteTrans();
		if($success != true)
			return array("success" => false, "error" => $error);

		return array("success" => true);
	}
	
	public function Transfer_product($from_id, $to_id, $product_id, $amount) {
		$db = Load_database();

		$rs = $db->Execute('
			update Object O
			set O.Inventory_ID = ?
			where O.Inventory_ID = ? and O.Product_ID = ?
			and not exists(
				select OI.ID from Object_inventory OI 
				where O.ID = OI.Object_ID and OI.Inventory_ID = ?
			)
			limit '.intval($amount)
			, array($to_id, $from_id, $product_id, $to_id, $amount));

		if(!$rs) {
			return false;
		}

		return true;
	}
	
	public function Transfer_to_inventory($actor_id, $inventory_id, $resources, $products) {
		/* We need to check for each inventory that it is accessible by the actor.
		 * We will not allow recursive checking for containers in containers.
		 * The actor will have to move a container out of its parent in order to access its contents.
		 * 
		 * TODO: check container objects for allowed inventories.
		 * 		make an array containing all allowed inventories before looping and just check against that!
		 * */
		 
		$db = Load_database();
		$db->StartTrans();
		//$db->debug = true;

		if(!$this->Is_inventory_accessible($actor_id, $inventory_id)) {
			$db->FailTrans();
			$db->CompleteTrans();
			return array("success" => false, "error" => "Inaccessible target inventory");
		}

		if($resources) {
			foreach($resources as $resource) {
				if(!$this->Is_inventory_accessible($actor_id, $resource['inventory_id'])) {
					$db->FailTrans();
					$db->CompleteTrans();
					return array("success" => false, "error" => "Inaccessible source inventory");
				}
					
				$this->Transfer_resource($resource['inventory_id'], $inventory_id, $resource['resource_id'], $resource['amount']);
			}
		}

		if($products) {
			foreach($products as $product) {
				if(!$this->Is_inventory_accessible($actor_id, $product['inventory_id'])) {
					$db->FailTrans();
					$db->CompleteTrans();
					return array("success" => false, "error" => "Inaccessible source inventory");
				}
				$this->Transfer_product($product['inventory_id'], $inventory_id, $product['product_id'], $product['amount']);
			}
		}

		$success = !$db->HasFailedTrans();
		$db->CompleteTrans();
		if($success != true)
			return array("success" => false, "error" => $error);

		return array("success" => true);
	}
	
	public function Is_inventory_accessible($actor_id, $inventory_id) {
		$db = Load_database();
		$sql = '
				select A.ID
					from Actor A
					join Location L on L.ID = A.Location_ID
					left join Object_inventory OI on OI.Object_ID = A.Inside_object_ID
					left join Object O on O.Inventory_ID = L.Inventory_ID or O.Inventory_ID = A.Inventory_ID or O.Inventory_ID = OI.Inventory_ID
					left join Object_inventory CI on CI.Object_ID = O.ID
				where A.ID = ? and (
						A.Inventory_ID = ? 
						or (A.Inside_object_ID is NULL 
						and L.Inventory_ID = ?) 
						or OI.Inventory_ID = ? 
						or CI.Inventory_ID = ?
					)
			';
		$args = array($actor_id, $inventory_id, $inventory_id, $inventory_id, $inventory_id);
		$rs = $db->Execute($sql, $args);
		if(!$rs || $rs->RecordCount() == 0) {
			return false;
		}
		return true;
	}
	
	public function Get_inventory_product_objects($actor_id, $inventory_id, $product_id) {
		$db = Load_database();

		if(!$this->Is_inventory_accessible($actor_id, $inventory_id)) {
			return false;
		}
		
		$sql = '
				select
					O.ID,
					O.Label,
					P.Name,
					OI.Inventory_ID as Object_inventory_ID,
					OL.ID as Object_lock_ID,
					OL.Attached_object_ID as Is_attached,
					AL.ID as Has_lock,
					IFNULL(OL.Is_locked, AL.Is_locked) as Is_locked
				from Object O
				join Product P on P.ID = O.Product_ID
				left join Object_inventory OI on OI.Object_ID = O.ID
				left join Object_lock OL on OL.Object_ID = O.ID
				left join Object_lock AL on AL.Attached_object_ID = O.ID
				where O.Inventory_ID = ? and O.Product_ID = ?
			';
		$args = array($inventory_id, $product_id);
		$rs = $db->Execute($sql, $args);
		if(!$rs) {
			return false;
		}
		return $rs->getArray();
	}

	public function Get_inventory($inventory_id) {
		$db = Load_database();

		$rs = $db->Execute('
			select
				I.ID,
				I.Inventory_ID,
				I.Resource_ID,
				I.Amount,
				R.Name,
				R.Mass,
				R.Volume,
				M.Name as Measure_name
			from Inventory_resource I
			left join Resource R on I.Resource_ID = R.ID
			join Measure M on R.Measure = M.ID
			where I.Inventory_ID = ?'
			, array($inventory_id));
		
		if(!$rs) {
			echo $db->ErrorMsg();
			return false;
		}

		$result = array();
		$result['resources'] = $rs->getArray();

		$rs = $db->Execute('
			select
				P.ID,
				O.Inventory_ID,
				count(P.ID) as Amount,
				P.Name
			from Object O
			join Product P on P.ID = O.Product_ID
			where O.Inventory_ID = ?
			group by P.ID
			', array($inventory_id));
		
		if(!$rs) {
			echo $db->ErrorMsg();
			return false;
		}

		$result['products'] = $rs->getArray();

		return $result;
	}

	public function Label_object($actor_id, $object_id, $label) {
		$db = Load_database();

		$rs = $db->Execute('
			select
				Inventory_ID
			from Object
			where ID = ?'
			, array($object_id));
		
		if(!$rs) {
			echo $db->ErrorMsg();
			return array('success' => false, 'reason' => 'Database error');
		}

		if(!$this->Is_inventory_accessible($actor_id, $rs->fields['Inventory_ID'])) {
			return array('success' => false, 'reason' => 'Inventory not accessible');
		}

		$rs = $db->Execute('update Object set Label = ? where ID = ?', array($label, $object_id));
		
		if(!$rs) {
			echo $db->ErrorMsg();
			return array('success' => false, 'reason' => 'Database error');
		}

		return array('success' => true);
	}

	public function Attach_lock($actor_id, $object_id, $lock_id) {
		$db = Load_database();

		//Check access to object
		$rs = $db->Execute('
			select
				Inventory_ID
			from Object
			where ID = ?'
			, array($object_id));
		
		if(!$rs) {
			echo $db->ErrorMsg();
			return array('success' => false, 'reason' => 'Database error');
		}

		if(!$this->Is_inventory_accessible($actor_id, $rs->fields['Inventory_ID'])) {
			return array('success' => false, 'reason' => 'Inventory not accessible');
		}

		//Check access to lock object
		$rs = $db->Execute('
			select
				Inventory_ID
			from Object
			where ID = ?'
			, array($lock_id));
		
		if(!$rs) {
			echo $db->ErrorMsg();
			return array('success' => false, 'reason' => 'Database error');
		}

		if(!$this->Is_inventory_accessible($actor_id, $rs->fields['Inventory_ID'])) {
			return array('success' => false, 'reason' => 'Inventory not accessible');
		}

		$rs = $db->Execute('update Object_lock set Attached_object_ID = ? where Object_ID = ?', array($object_id, $lock_id));
		
		if(!$rs) {
			echo $db->ErrorMsg();
			return array('success' => false, 'reason' => 'Database error');
		}

		return array('success' => true);
	}

	public function Detach_lock($actor_id, $object_id, $lockside) {
		$db = Load_database();

		//Check access to object
		$rs = $db->Execute('
			select
				Inventory_ID
			from Object
			where ID = ?'
			, array($object_id));
		
		if(!$rs) {
			echo $db->ErrorMsg();
			return array('success' => false, 'reason' => 'Database error');
		}

		if(!$this->Is_inventory_accessible($actor_id, $rs->fields['Inventory_ID'])) {
			return array('success' => false, 'reason' => 'Inventory not accessible');
		}

		if($lockside == 'false') {
			$rs = $db->Execute('
				select
					Object_ID
				from Object_lock
				where Attached_object_ID = ?'
				, array($object_id));
			
			if(!$rs) {
				return array('success' => false, 'reason' => 'Database error');
			}
			$lock_id = $rs->fields['Object_ID'];
		} else {
			$lock_id = $object_id;
		}

		$rs = $db->Execute('update Object_lock set Attached_object_ID = NULL where Object_ID = ?', array($lock_id));
		
		if(!$rs) {
			echo $db->ErrorMsg();
			return array('success' => false, 'reason' => 'Database error');
		}

		return array('success' => true);
	}
}
