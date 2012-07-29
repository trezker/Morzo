<?php

require_once '../models/database.php';

class Blog_model {
	public function Get_blogs() {
		$db = Load_database();
		
		$rs = $db->Execute('
			select ID, Name from Blog
			', array());

		if(!$rs) {
			return false;
		}
		
		$blogs = $rs->getArray();
		return $blogs;
	}

	public function Get_blog_by_name($blog_name) {
		$blog_name = str_replace('_', ' ', $blog_name);
		$db = Load_database();
		
		$rs = $db->Execute('
			select ID, Name from Blog
			where Name = ?
			', array($blog_name));

		if(!$rs) {
			return false;
		}
		
		$blog = $rs->fields;
		return $blog;
	}

	public function User_owns_blog_name($blog_name, $user_id) {
		$blog_name = str_replace('_', ' ', $blog_name);
		$db = Load_database();
		
		$rs = $db->Execute('
			select User_ID from Blog
			where Name = ?
			', array($blog_name));

		if(!$rs or $rs->fields['User_ID'] != $user_id) {
			return false;
		}
		
		return true;
	}

	public function User_owns_blog($blog_id, $user_id) {
		$db = Load_database();
		
		$rs = $db->Execute('
			select User_ID from Blog
			where ID = ?
			', array($blog_id));

		if(!$rs or $rs->fields['User_ID'] != $user_id) {
			return false;
		}
		
		return true;
	}

	public function Get_user_blogs($user_id) {
		$db = Load_database();
		
		$rs = $db->Execute('
			select ID, Name from Blog
			where User_ID = ?
			', array($user_id));

		if(!$rs) {
			return false;
		}
		
		$blogs = $rs->getArray();
		return $blogs;
	}

	public function Get_latest_titles() {
		$db = Load_database();
		
		$rs = $db->Execute('
			select P.ID, P.Title, B.Name as Blog_name, B.ID as Blog_ID
			from Blogpost P
			join Blog B on B.ID = P.Blog_ID
			order by P.Created_date
			limit 10
			', array());

		if(!$rs) {
			return false;
		}
		
		$titles = $rs->getArray();
		return $titles;
	}

	public function Create_blog($user_id, $name) {
		$name = str_replace('_', ' ', $name);
		$db = Load_database();
		
		$rs = $db->Execute('
			insert into Blog(User_ID, Name, Created_date)
			values(?, ?, NOW())
			', array($user_id, $name));

		if(!$rs) {
			return array('success' => false, 'reason' => 'database failure');
		}
		
		$blog_id = $db->Insert_id();
		return array('success' => true, 'blog_id' => $blog_id);
	}

	public function Create_blog_post($blog_id, $title, $content) {
		$db = Load_database();
		
		$rs = $db->Execute('
			insert into Blogpost(Blog_ID, Title, Content, Created_date)
			values(?, ?, ?, NOW())
			', array($blog_id, $title, $content));

		if(!$rs) {
			return array('success' => false, 'reason' => 'database failure');
		}
		
		$blog_id = $db->Insert_id();
		return array('success' => true, 'blog_id' => $blog_id);
	}
}
?>
