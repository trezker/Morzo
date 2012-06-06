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
				if($from_result_units < 0) {
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
}
