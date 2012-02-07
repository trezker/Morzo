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
	
	public function Get_deficient_locations()
	{
		$db = Load_database();
		$rs = $db->Execute('
			select L.ID, L.X, L.Y, L.Biome_ID, B.Name from Location L
			left join Biome B on L.Biome_ID = B.ID
			where Biome_ID is NULL or not exists (
				select * from Location_resource LR where L.ID = LR.Location_ID
			);
			', array());

		if(!$rs)
		{
			return false;
		}
		
		$locations = array();
		foreach ($rs as $row) {
    		$locations[] = $row;
		}
		return $locations;
	}
	
	public function Get_biomes()
	{
		$db = Load_database();
		
		$rs = $db->Execute('
			select B.ID, B.Name from Biome B
			', array());

		if(!$rs)
		{
			return false;
		}
		
		$biomes = array();
		foreach ($rs as $row) {
    		$biomes[] = $row;
		}
		return $biomes;
	}
	
	public function Add_biome($name)
	{
		$db = Load_database();
		
		$rs = $db->Execute('
			insert into Biome(name) values(?)
			', array($name));

		if(!$rs)
		{
			return false;
		}
		return true;
	}
	
	public function Get_resources()
	{
		$db = Load_database();
		
		$rs = $db->Execute('
			select R.ID, R.Name from Resource R
			', array());

		if(!$rs)
		{
			return false;
		}
		
		$resources = array();
		foreach ($rs as $row) {
    		$resources[] = $row;
		}
		return $resources;
	}

	public function Add_resource($name)
	{
		$db = Load_database();
		
		$rs = $db->Execute('
			insert into Resource(name) values(?)
			', array($name));

		if(!$rs)
		{
			return false;
		}
		return true;
	}
	
	public function Get_location($id)
	{
		$db = Load_database();
		
		$rs = $db->Execute('
			select L.ID, L.X, L.Y, L.Biome_ID, B.Name as Biome_name from Location L
			left join Biome B on L.Biome_ID = B.ID
			where L.ID = ?
			', array($id));

		if(!$rs)
		{
			return false;
		}
		if($rs->RecordCount()!=1)
		{
			return 'Not found';
		}
		
		return array(
			'ID' => $rs->fields['ID'],
			'X' => $rs->fields['X'],
			'Y' => $rs->fields['Y'],
			'Biome_ID' => $rs->fields['Biome_ID'],
			'Biome_name' => $rs->fields['Biome_name']
		);
	}

	public function Get_location_resources($location_id)
	{
		$db = Load_database();
		
		$rs = $db->Execute('
			select R.ID, R.Name from Location_resource LR
			join Resource R on R.ID = LR.Resource_ID
			where LR.Location_ID = ?
			', array($location_id));

		if(!$rs)
		{
			return false;
		}
		$resources = array();
		foreach ($rs as $row) {
    		$resources[] = $row;
		}
		return $resources;
	}

	public function Set_location_biome($location_id, $biome_id)
	{
		$db = Load_database();
		
		$rs = $db->Execute('
			update Location set Biome_ID = ? where ID = ?
			', array($biome_id, $location_id));

		if(!$rs)
		{
			return false;
		}
		return true;
	}
	public function Add_location_resource($location_id, $resource_id)
	{
		$db = Load_database();
		
		$rs = $db->Execute('
			insert into Location_resource(Location_ID, Resource_ID) values(?, ?)
			', array($location_id, $resource_id));

		if(!$rs)
		{
			return false;
		}
		return true;
	}
	public function Remove_location_resource($location_id, $resource_id)
	{
		$db = Load_database();
		
		$rs = $db->Execute('
			delete from Location_resource where Location_ID=? and Resource_ID = ?
			', array($location_id, $resource_id));

		if(!$rs)
		{
			return false;
		}
		return true;
	}
}
?>
