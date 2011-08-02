<?php

require_once 'models/database.php';

class Travel_model
{
	public function Travel($actor, $destination, $origin) {
		$db = Load_database();

		$rs = $db->Execute('
			insert into Travel (ActorID, DestinationID, OriginID, X, Y)
			select ?, ?, ?, X, Y from Location l where ID = ?
			', array($actor, $destination, $origin, $origin));

		if(!$rs)
		{
			return false;
		}
		return true;
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

		echo 'Actor: '.$actor;
		echo '<pre>';
		var_dump($rs->fields);
		echo '</pre>';

		return $rs->fields;
	}

	public function Get_travels() {
		$db = Load_database();

		$rs = $db->Execute('
			select
				t.X as CurrentX,
				t.Y as CurrentY,
				ld.X as DestinationX,
				ld.Y as DestinationY,
			from Travel t
			join Location ld on l.ID = t.DestinationID
			', array($actor, $actor, $actor));
		
		if(!$rs)
		{
			return false;
		}

		echo '<pre>';
		var_dump($rs->fields);
		echo '</pre>';

		return $rs->fields;
	}
}
?>
