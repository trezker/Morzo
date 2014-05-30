<?php

require_once '../framework/model.php';

class Language_model extends Model
{
	public function Translate_event($event, $actor_id)
	{
		$text = $this->Translate_handle($event['Translation_handle']);

		$grammar = array();
		if($event['From_actor_ID'] == $actor_id)
			$grammar['EN_was'] = 'were';
		else
			$grammar['EN_was'] = 'was';

		$text = expand_template($text, $grammar);

		return $text;
	}

	public function Translate_handle($handle)
	{
		$cleanhandle = trim($handle);
		$cleanhandle = substr($cleanhandle, 1, strlen($cleanhandle)-2);
		$query = '
			SELECT
				Text
			FROM Translation
			WHERE Handle = ? AND Language_ID = ?';
		$rs = $this->db->Execute($query, array($cleanhandle, 1));
		if(!$rs) 
		{
			return $handle;
		}
		if($rs->RecordCount()!=1)
		{
			return $handle;
		}
		
		return $rs->fields['Text'];
	}

	public function Replace_callback($matches) {
		return $this->Translate_handle($matches[0]);
	}
	
	public function Translate_tokens($text) {
		$text = preg_replace_callback('/{LNG_[^}]*}/', array($this, 'Replace_callback'), $text);
		return $text;
	}
	
	public function Get_languages(){
		$query = '
			SELECT
				ID,
				Name
			FROM Language';
		$rs = $this->db->Execute($query, array());
		if(!$rs){
			return false;
		}

		return $rs->getArray();
	}
	
	public function Get_translations_for_translator($target_language){
		$query = '
			SELECT
				e.Handle,
				e.Text as English,
				t.Text
			FROM Translation e
			LEFT JOIN Translation t on t.Handle = e.Handle and t.Language_ID = ?
			WHERE e.Language_ID = 1
			ORDER BY e.Handle
			';
		$rs = $this->db->Execute($query, array($target_language));
		if(!$rs){
			return false;
		}

		return $rs->getArray();
	}
	
	public function Save_translation($language_id, $handle, $text){
		$query = '
			INSERT INTO Translation (Language_ID, Handle, Text)
			VALUES (?, ?, ?)
			ON DUPLICATE KEY
			UPDATE Text = ?
			';
		$rs = $this->db->Execute($query, array($language_id, $handle, $text, $text));
		if(!$rs){
			return false;
		}

		return true;
	}

	public function New_translation($handle, $text){
		$query = '
			INSERT INTO Translation (Language_ID, Handle, Text)
			VALUES (1, ?, ?)
			';
		$rs = $this->db->Execute($query, array($handle, $text));
		if(!$rs){
			return false;
		}
		return true;
	}
}
