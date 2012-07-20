<?php

require_once '../models/database.php';

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

		$db->StartTrans();
		
		//Create actor inventory
		$r = $db->Execute('
			insert into Inventory values()', array());
		
		if(!$r) {
			$db->FailTrans();
		} else {
			$inventory_ID = $db->Insert_id();
		}

		if(!$db->HasFailedTrans()) {
			$rs = $db->Execute('
				insert into Location (x, y, Inventory_ID) values(?, ?, ?)
				', array($x, $y, $inventory_ID));
		}

		$failed = $db->HasFailedTrans();
		$db->CompleteTrans();
		
		if($failed)
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
	
	public function Get_locations($center_x, $center_y)
	{
		$db = Load_database();
		$rs = $db->Execute('
			select
				L.ID,
				L.X,
				L.Y,
				L.Biome_ID,
				B.Name,
				count(LR.ID) as Resource_count
			from Location L
			left join Biome B on L.Biome_ID = B.ID
			left join Location_resource LR on L.ID = LR.Location_ID
			where L.X >= ? and L.Y >= ? && L.X <= ? && L.Y <= ? 
			group by L.ID
			order by L.Y, L.X
			;', array($center_x-5, $center_y-5, $center_x+5, $center_y+5));

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
	
	public function Get_landscapes()
	{
		$db = Load_database();
		
		$rs = $db->Execute('
			select L.ID, L.Name from Landscape L
			', array());

		if(!$rs)
		{
			return false;
		}
		
		$landscapes = array();
		foreach ($rs as $row) {
    		$landscapes[] = $row;
		}
		return $landscapes;
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

	public function Add_landscape($name)
	{
		$db = Load_database();
		
		$rs = $db->Execute('
			insert into Landscape(name) values(?)
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

	public function Get_location_resources($location_id, $landscape = NULL)
	{
		$db = Load_database();
		
		$query = '
			select 
				R.ID, 
				R.Name, 
				L.ID as Landscape_ID, 
				L.Name as Landscape_name
			from Location_resource LR
			join Resource R on R.ID = LR.Resource_ID
			join Landscape L on L.ID = LR.Landscape_ID
			where LR.Location_ID = ?
			';
		
		$args = array($location_id);

		if($landscape != NULL)
		{
			$query .= ' AND LR.Landscape_ID = ?';
			$args[] = $landscape;
		} else {
			$query .= ' order by L.Name ASC';
		}

		$rs = $db->Execute($query, $args);

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
	public function Add_location_resource($location_id, $resource_id, $landscape_id)
	{
		$db = Load_database();
		
		$rs = $db->Execute('
			insert into Location_resource(Location_ID, Resource_ID, Landscape_ID) values(?, ?, ?)
			', array($location_id, $resource_id, $landscape_id));

		if(!$rs)
		{
			return false;
		}
		return true;
	}
	public function Remove_location_resource($location_id, $resource_id, $landscape_id)
	{
		$db = Load_database();
		
		$rs = $db->Execute('
			delete from Location_resource where Location_ID=? and Resource_ID = ? and Landscape_ID = ?
			', array($location_id, $resource_id, $landscape_id));

		if(!$rs)
		{
			return false;
		}
		return true;
	}

	public function Landscape_resource_count($location_id, $landscape_id)
	{
		$db = Load_database();
		
		$rs = $db->Execute('
			select count(*) as C from Location_resource where Location_ID=? and Landscape_ID = ?
			', array($location_id, $landscape_id));

		if(!$rs)
		{
			return false;
		}
		return $rs->fields['C'];
	}
	
	public function Get_max_actors() {
		$db = Load_database();
		
		$rs = $db->Execute('
			select Value from Count where Name = \'Max_actors\';
			', array());

		if(!$rs)
		{
			return false;
		}
		return $rs->fields['Value'];
	}

	public function Set_max_actors($value) {
		$db = Load_database();
		
		$rs = $db->Execute('
			update Count set Value = ? where Name = \'Max_actors\';
			', array($value));

		if(!$rs)
		{
			return false;
		}
		return true;
	}

	public function Get_max_actors_account() {
		$db = Load_database();
		
		$rs = $db->Execute('
			select Value from Count where Name = \'Max_actors_account\';
			', array());

		if(!$rs)
		{
			return false;
		}
		return $rs->fields['Value'];
	}

	public function Set_max_actors_account($value) {
		$db = Load_database();
		
		$rs = $db->Execute('
			update Count set Value = ? where Name = \'Max_actors_account\';
			', array($value));

		if(!$rs)
		{
			return false;
		}
		return true;
	}
}
?>
