<?php

require_once 'models/database.php';

class Event_model
{
	public function Get_events($actor_id) {
		$db = Load_database();

		$rs = $db->Execute('
			SELECT E.From_actor_ID, E.To_actor_ID, E.Message, E.Ingame_time, E.Real_time FROM Event E
			JOIN Actor_event AE on AE.Event_ID = E.ID
			WHERE AE.Actor_ID = ?
			ORDER BY E.Real_time DESC
			', array($actor_id));
		if(!$rs) {
			return false;
		}
		if($rs->RecordCount() > 0) {
			$events = $rs->GetArray();
			return $events;
		}
		return false;
	}

	public function Speak($actor_id, $message) {
		$db = Load_database();

		//Make a transaction, no use storing an event that noone sees.
		$rs = $db->Execute('
			insert into Event(From_actor_ID, Message, Ingame_time, Real_time) 
			select ?, ?, C.Value, NOW() from Count C where Name = \'Update\' limit 1
			', array($actor_id, $message));
		if(!$rs) {
			return false;
		}
		
		$event_id = $db->Insert_ID();
		if(!$event_id) {
			return false;
		}
		
		$rs = $db->Execute('
			insert into Actor_event(Actor_ID, Event_ID)
			select B.ID, ? from Actor A
			join Actor B on A.Location_ID = B.Location_ID
			where A.ID = ?
			', array($event_id, $actor_id));
		if(!$rs) {
			return false;
		}
		
		if($db->Affected_rows() > 0) {
			return true;
		}
		return false;
	}
}
?>
