<?php

require_once '../models/database.php';

class Event_model
{
	public function Get_events($actor_id) {
		$db = Load_database();

		$rs = $db->Execute('
			SELECT
				E.Translation_handle,
				E.From_actor_ID,
				FAN.Name AS From_actor_name,
				E.To_actor_ID,
				TAN.Name AS To_actor_name,
				E.To_actor_ID,
				E.Message,
				E.Ingame_time,
				E.Real_time,
				E.From_location_ID,
				FLN.Name AS From_location_name,
				E.To_location_ID,
				TLN.Name AS To_location_name
			FROM Event E
			JOIN Actor_event AE on AE.Event_ID = E.ID
			LEFT JOIN Actor_name FAN ON FAN.Named_actor_ID = E.From_actor_ID AND FAN.Actor_ID = AE.Actor_ID
			LEFT JOIN Actor_name TAN ON TAN.Named_actor_ID = E.To_actor_ID AND TAN.Actor_ID = AE.Actor_ID
			LEFT JOIN Location_name FLN ON FLN.Location_ID = E.From_location_ID AND FLN.Actor_ID = AE.Actor_ID
			LEFT JOIN Location_name TLN ON TLN.Location_ID = E.To_location_ID AND TLN.Actor_ID = AE.Actor_ID
			WHERE AE.Actor_ID = ?
			ORDER BY E.Real_time DESC
			', array($actor_id));
		if(!$rs) {
			return false;
		}
		$events = $rs->GetArray();
		return $events;
	}

	public function Save_event($translation_handle, $from_actor_id, $to_actor_id, $message = NULL, $from_location = NULL, $to_location = NULL, $private = false) {
		$db = Load_database();

		$db->StartTrans();
		$rs = $db->Execute('
			insert into Event(From_actor_ID, To_actor_ID, Message, Ingame_time, Real_time, From_location_ID, To_location_ID, Translation_handle)
			select ?, ?, ?, C.Value, NOW(), ?, ?, ? from Count C where Name = \'Update\' limit 1
			', array($from_actor_id, $to_actor_id, $message, $from_location, $to_location, $translation_handle));
		
		$event_id = $db->Insert_ID();

		if($private == false) {
			$rs = $db->Execute('
				insert into Actor_event(Actor_ID, Event_ID)
				select B.ID, ? from Actor A
				join Actor B on A.Location_ID = B.Location_ID
				where A.ID = ?
				', array($event_id, $from_actor_id));
			if($db->Affected_rows() == 0) {
				$db->FailTrans();
			}
		} else {
			$rs = $db->Execute('
				insert into Actor_event(Actor_ID, Event_ID)
				values(?, ?)
				', array($from_actor_id, $event_id));

			if($db->Affected_rows() != 1) {
				$db->FailTrans();
			}
			
			if($to_actor_id != NULL) {
				$rs = $db->Execute('
					insert into Actor_event(Actor_ID, Event_ID)
					values(?, ?)
					', array($to_actor_id, $event_id));

				if($db->Affected_rows() != 1) {
					$db->FailTrans();
				}
			}
		}
		
		$success = !$db->HasFailedTrans();
		$db->CompleteTrans();
		return $success;
	}
}

