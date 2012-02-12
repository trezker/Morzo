<?php

require_once 'models/database.php';
require_once "models/model.php";

class Travel_model extends Model
{
	public function Travel($actor, $destination, $origin) {
		$db = Load_database();

		$rs = $db->Execute("
			select Value from Count where Name = 'Update'
			");
		$update = $rs->fields['Value'];

		$db->StartTrans();
		$rs = $db->Execute('
			insert into Travel (ActorID, DestinationID, OriginID, X, Y, UpdateTick)
			select ?, ?, ?, X, Y, ? from Location l where ID = ?
			', array($actor, $destination, $origin, $update, $origin));

		$this->Load_model('Event_model');
		$this->Event_model->Save_event($actor, NULL, '{From_actor_name} left {Location '.$origin.'} going towards {Location '.$destination.'}');

		$success = !$db->HasFailedTrans();
		$db->CompleteTrans();
		return $success;
	}
	
	public function Get_travel_info($actor) {
		$db = Load_database();

		$rs = $db->Execute('
			select
				DestinationID,
				OriginID,
				lnd.Name as DestinationName,
				lno.Name as OriginName,
				t.X,
				t.Y
			from Travel t
			left join Location_name lnd on t.DestinationID = lnd.Location_ID and lnd.Actor_ID = ?
			left join Location_name lno on t.OriginID = lno.Location_ID and lno.Actor_ID = ?
			where ActorID = ?
			', array($actor, $actor, $actor));
		
		if(!$rs)
		{
			return false;
		}

		return $rs->fields;
	}

	public function Get_travels() {
		$db = Load_database();

		$rs = $db->Execute("
			select Value from Count where Name = 'Update'
			");
		$update = $rs->fields['Value'];

		$rs = $db->Execute('
			select
				t.ActorID,
				t.X as CurrentX,
				t.Y as CurrentY,
				ld.X as DestinationX,
				ld.Y as DestinationY,
				t.DestinationID
			from Travel t
			join Location ld on ld.ID = t.DestinationID
			where t.UpdateTick = ?
			', array($update));
		
		if(!$rs)
		{
			return false;
		}

		echo '<pre>';
		var_dump($rs->fields);
		echo '</pre>';

		$travels = array();
		foreach ($rs as $row) {
			$travels[] = $row;
		}
		echo 'TRAVELS: <pre>';
		var_dump($travels);
		echo '</pre>';

		return $travels;
	}

	public function Tick() {
		$db = Load_database();

		$rs = $db->Execute("
			update Count SET Value=Value+1 Where Name='Update'
			");
		return $this->Get_update_count();
	}

	public function Get_update_count() {
		$db = Load_database();

		$rs = $db->Execute("
			select Value from Count where Name = 'Update'
			");
		return $rs->fields['Value'];
	}

	public function Get_outdated_travel($actor_id, $update) {
		$db = Load_database();

		$rs = $db->Execute('
			select
				t.X as CurrentX,
				t.Y as CurrentY,
				ld.X as DestinationX,
				ld.Y as DestinationY,
				t.DestinationID,
				t.UpdateTick
			from Travel t
			join Location ld on ld.ID = t.DestinationID
			where t.UpdateTick < ? and t.ActorID = ?
			', array($update, $actor_id));
		
		if(!$rs)
		{
			echo $db->ErrorMsg();
			return false;
		}
		return $rs->fields;
	}
	
	public function Move($moves, $update) {
		$db = Load_database();

		$db->StartTrans();
		foreach($moves as $move) {
			$rs = $db->Execute('
				update Travel set X = ?, Y = ?, UpdateTick = ? where ActorID = ?
				', array($move['x'], $move['y'], $update, $move['actor']));
			
			if(!$rs) {
				$db->FailTrans();
				break;
			}
		}
		if($db->HasFailedTrans()) {
			echo $db->ErrorMsg();
			$success = false;
		} else {
			$success = true;
		}
		$db->CompleteTrans();
		return $success;
	}

	public function Arrive($arrives) {
		$db = Load_database();

		$db->StartTrans();
		foreach($arrives as $arrive) {
			$rs = $db->Execute('
				update Actor set Location_ID = ? where ID = ?
				', array($arrive['Destination'], $arrive['Actor']));
			
			if(!$rs) {
				$db->FailTrans();
				break;
			}

			$rs = $db->Execute('
				delete from Travel where ActorID = ?
				', array($arrive['Actor']));
			
			if(!$rs) {
				$db->FailTrans();
				break;
			}
		}
		if($db->HasFailedTrans()) {
			$success = false;
		} else {
			$success = true;
		}
		$db->CompleteTrans();
		return $success;
	}
}
?>
