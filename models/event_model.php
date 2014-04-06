<?php

require_once '../models/model.php';

class Event_model extends Model
{
	public function Get_events($actor_id) {
		$rs = $this->db->Execute('
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

	public function Save_event($translation_handle, $from_actor_id, $to_actor_id, $message = NULL, $from_location = NULL, $to_location = NULL, $private = false, $inside_object_id = NULL) {
		//$this->db->StartTrans();
		$rs = $this->db->Execute('
			insert into Event(From_actor_ID, To_actor_ID, Message, Ingame_time, Real_time, From_location_ID, To_location_ID, Translation_handle)
			select ?, ?, ?, C.Value, NOW(), ?, ?, ? from Count C where Name = \'Update\' limit 1
			', array($from_actor_id, $to_actor_id, $message, $from_location, $to_location, $translation_handle));
		
		$event_id = $this->db->Insert_ID();

		if($private == false) {
			$args = array($event_id, $from_actor_id);
			$extra_location = '';
			if($inside_object_id !== NULL) {
				$extra_location = 'or B.Inside_object_ID = ?';
				$args = array($event_id, $inside_object_id, $from_actor_id);
			}
			$rs = $this->db->Execute('
				insert into Actor_event(Actor_ID, Event_ID)
				select B.ID, ? from Actor A
				join Actor B on (A.Location_ID = B.Location_ID)
							 and ((A.Inside_object_ID is NULL and B.Inside_object_ID is NULL)
							 or A.Inside_object_ID = B.Inside_object_ID ' . $extra_location . ')
				where A.ID = ?
				', $args);
			if($this->db->Affected_rows() == 0) {
				$this->db->FailTrans();
			}
		} else {
			$rs = $this->db->Execute('
				insert into Actor_event(Actor_ID, Event_ID)
				values(?, ?)
				', array($from_actor_id, $event_id));

			if($this->db->Affected_rows() != 1) {
				$this->db->FailTrans();
			}
			
			if($to_actor_id != NULL) {
				$rs = $this->db->Execute('
					insert into Actor_event(Actor_ID, Event_ID)
					values(?, ?)
					', array($to_actor_id, $event_id));

				if($this->db->Affected_rows() != 1) {
					$this->db->FailTrans();
				}
			}
		}
		
		$success = !$this->db->HasFailedTrans();
		//$this->db->CompleteTrans();
		return $success;
	}

	public function Save_hunt_event($translation_handle, $hunt_id) {
		$this->db->StartTrans();
		$rs = $this->db->Execute('
			insert into Event(Ingame_time, Real_time, Translation_handle)
			select C.Value, NOW(), ? from Count C where Name = \'Update\' limit 1
			', array($translation_handle));
		
		$event_id = $this->db->Insert_ID();

		$rs = $this->db->Execute('
			insert into Actor_event(Actor_ID, Event_ID)
			select A.ID, ? from Actor A
			where A.Hunt_ID = ?
			', array($event_id, $hunt_id));
		if($this->db->Affected_rows() == 0) {
			$this->db->FailTrans();
		}
		
		$success = !$this->db->HasFailedTrans();
		$this->db->CompleteTrans();
		return $success;
	}
	
	public function Delete_old_events() {
		$sql = 'delete FROM Event where Real_time < DATE_SUB(NOW(), INTERVAL 30 day)';
		$rs = $this->db->Execute($sql, array());
		if(!$rs) {
			echo $this->db->ErrorMsg();
		}
	}
}

