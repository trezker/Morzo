<?php

require_once '../models/database.php';

class Language_model
{
	public function Translate_event($event)
	{
		$text = $this->Translate_handle($event['Translation_handle']);

		return $text;
	}

	public function Translate_handle($handle)
	{
		$db = Load_database();

		$query = '
			SELECT
				Text
			FROM Translation
			WHERE Handle = ? AND Language_ID = ?';
		$rs = $db->Execute($query, array($handle, 1));
		if(!$rs ) 
		{
			return $handle;
		}
		if($rs->RecordCount()!=1)
		{
			return $handle;
		}
		
		return $rs->fields['Text'];
	}
}

