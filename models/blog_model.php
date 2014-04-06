<?php

require_once '../models/model.php';

class Blog_model extends Model {
	public function Get_blogs() {
		$rs = $this->db->Execute('
			select ID, Name from Blog
			', array());

		if(!$rs) {
			return false;
		}
		
		$blogs = $rs->getArray();
		return $blogs;
	}

	public function Get_blog($blog_id) {
		$rs = $this->db->Execute('
			select ID, Name from Blog
			where ID = ?
			', array($blog_id));

		if(!$rs) {
			return false;
		}
		
		$blog = $rs->fields;
		return $blog;
	}

	public function Get_blog_by_name($blog_name) {
		$blog_name = str_replace('_', ' ', $blog_name);

		$rs = $this->db->Execute('
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
		
		$rs = $this->db->Execute('
			select User_ID from Blog
			where Name = ?
			', array($blog_name));

		if(!$rs or $rs->fields['User_ID'] != $user_id) {
			return false;
		}
		
		return true;
	}

	public function User_owns_blog($blog_id, $user_id) {
		$rs = $this->db->Execute('
			select User_ID from Blog
			where ID = ?
			', array($blog_id));

		if(!$rs or $rs->fields['User_ID'] != $user_id) {
			return false;
		}
		
		return true;
	}

	public function Get_blog_from_post_id($post_id) {
		$rs = $this->db->Execute('
			select B.ID from Blogpost P
			join Blog B on B.ID = P.Blog_ID
			where P.ID = ?
			', array($post_id));

		if(!$rs) {
			echo $this->db->ErrorMsg();
			return false;
		}
		
		return $rs->fields['ID'];
	}

	public function Get_user_blogs($user_id) {
		$rs = $this->db->Execute('
			select ID, Name from Blog
			where User_ID = ?
			', array($user_id));

		if(!$rs) {
			return false;
		}
		
		$blogs = $rs->getArray();
		return $blogs;
	}

	public function Get_posts($blog = null, $limit = null, $offset = null) {
		$args = array();
		$wheretail = '';
		if(is_numeric($blog)) {
			$blog = intval($blog);
			$wheretail = ' and B.ID = ?';
			$args[] = $blog;
		}
		
		$offsetsql = '';
		if(is_int($offset)) {
			$offsetsql = $offset . ',';
		}

		$limitsql = '';
		if(is_int($limit)) {
			$limitsql = 'limit ' . $offsetsql . $limit;
		}
		
		$rs = $this->db->Execute('
			select 
				P.ID, 
				P.Title, 
				P.Content, 
				P.Created_date,
				B.Name as Blog_name, 
				B.ID as Blog_ID
			from Blogpost P
			join Blog B on B.ID = P.Blog_ID
			where P.Hidden = 0'.$wheretail.'
			order by P.Created_date DESC
			'.$limitsql.'
			', $args);

		if(!$rs) {
			echo $this->db->errorMsg();
			return false;
		}
		
		$posts = $rs->getArray();
		return $posts;
	}

	public function Get_blog_post($post_id) {
		$rs = $this->db->Execute('
			select ID, Title, Content, Hidden
			from Blogpost
			where ID = ?
			', array($post_id));

		if(!$rs) {
			return false;
		}

		$post = $rs->fields;
		return $post;
	}
	
	public function Get_blog_post_titles($blog_id) {
		$rs = $this->db->Execute('
			select P.ID, P.Title, P.Created_date, P.Hidden
			from Blogpost P
			where P.Blog_ID = ?
			order by P.Created_date
			', array($blog_id));

		if(!$rs) {
			return false;
		}
		
		$titles = $rs->getArray();
		return $titles;
	}

	public function Create_blog($user_id, $name) {
		$name = str_replace('_', ' ', $name);
		$rs = $this->db->Execute('
			insert into Blog(User_ID, Name, Created_date)
			values(?, ?, NOW())
			', array($user_id, $name));

		if(!$rs) {
			return array('success' => false, 'reason' => 'database failure');
		}
		
		$blog_id = $this->db->Insert_id();
		return array('success' => true, 'blog_id' => $blog_id);
	}

	public function Create_blog_post($blog_id, $title, $content, $hidden) {
		$rs = $this->db->Execute('
			insert into Blogpost(Blog_ID, Title, Content, Created_date, Hidden)
			values(?, ?, ?, NOW(), ?)
			', array($blog_id, $title, $content, $hidden));

		if(!$rs) {
			return array('success' => false, 'reason' => 'database failure');
		}
		
		$blog_id = $this->db->Insert_id();
		return array('success' => true, 'blog_id' => $blog_id);
	}

	public function Update_blog_post($post_id, $title, $content, $hidden) {
		$rs = $this->db->Execute('
			update Blogpost set Title = ?, Content = ?, Hidden = ?
			where ID = ?
			', array($title, $content, $hidden, $post_id));

		if(!$rs) {
			echo $this->db->ErrorMsg();
			return array('success' => false, 'reason' => 'database failure');
		}
		
		return array('success' => true);
	}

	public function Delete_blogpost($post_id) {
		$rs = $this->db->Execute('
			delete from Blogpost
			where ID = ?
			', array($post_id));

		if(!$rs) {
			echo $this->db->ErrorMsg();
			return array('success' => false, 'reason' => 'database failure');
		}
		
		return array('success' => true);
	}
	
	public function Hide_blogpost($post_id) {
		$rs = $this->db->Execute('
			update Blogpost set Hidden = ?
			where ID = ?
			', array(1, $post_id));

		if(!$rs) {
			echo $this->db->ErrorMsg();
			return array('success' => false, 'reason' => 'database failure');
		}
		
		return array('success' => true);
	}
}
?>
