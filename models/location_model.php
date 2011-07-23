<?php

require_once 'models/database.php';

class Location_model
{
	public function Change_location_name($actor_ID, $location_ID, $new_name)
	{
		$db = Load_database();

		$rs = $db->Execute('
			insert into Location_name (Name, Actor_ID, Location_ID) values(?, ?, ?)
			on duplicate key update Name = ?
			', array($new_name, $actor_ID, $location_ID, $new_name));
		
		if(!$rs)
		{
			return false;
		}
		return true;
	}

	public function Get_neighbouring_locations($actor_ID)
	{
		$db = Load_database();

		$rs = $db->Execute('
			select L.x, L.y from Location L
			join Actor A on A.Location_ID = L.ID
			where A.ID = ?
			', array($actor_ID));
		
		if(!$rs)
		{
			return false;
		}

		$x = $rs->fields['x'];
		$y = $rs->fields['y'];
		
		$rs = $db->Execute('
			select L.ID, L.x, L.y, LN.Name as Name from Location L
			left join (
				select Name, ILN.Location_ID from Location_name ILN
				join Actor A on ILN.Actor_ID = A.ID
				where A.ID = ?
				) LN on L.ID = LN.Location_ID
			where L.x >= ? and L.y >= ? and L.x <= ? and L.y <= ?
			', array($actor_ID, $x-1, $y-1, $x+1, $y+1));

		if(!$rs)
		{
			return false;
		}
		
		$locations = array();
		foreach ($rs as $row) {
			if($row['x'] == $x && $row['y'] == $y)
				continue;
    		$locations[] = array(
    			'ID' => $row['ID'],
    			'x' => $row['x'] - $x,
    			'y' => $row['y'] - $y,
    			'Name' => $row['Name']
    		);
		}

		return $locations;
	}
	
	public function Create_location($actor_id, $direction)
	{
		$db = Load_database();

		$rs = $db->Execute('
			select L.x, L.y from Location L
			join Actor A on A.Location_ID = L.ID
			where A.ID = ?
			', array($actor_id));
		
		if(!$rs)
		{
			return false;
		}

		$x = $rs->fields['x'];
		$y = $rs->fields['y'];
		if($direction == "east")
			$x++;
		else if($direction == "west")
			$x--;
		else if($direction == "south")
			$y++;
		else if($direction == "north")
			$y--;
		else
			return false;

		$rs = $db->Execute('
			insert into Location (x, y) values(?, ?)
			', array($x, $y));

		if(!$rs)
		{
			return false;
		}
		
		$rs = $db->Execute('
			select ID from Location where x = ? and y = ?
			', array($x, $y));

		if(!$rs)
		{
			return false;
		}

		return $rs->fields['ID'];
	}
}
?>
