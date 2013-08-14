<div style="float: left; width: 500px;">
	<?php
		if($post['ID'] == -1) {
			echo 'Creating new post';
		} else {
			echo 'Editing old post';
		}
		$hidden = '';
		if($post['Hidden']==1)
			$hidden = 'checked="true"'; 
	?>
	<br />
	<input type="hidden" id="blog_id" value="<?php echo $blog['ID']; ?>" />
	<input type="hidden" id="post_id" value="<?php echo $post['ID']; ?>" />
	<label for="new_post_title">Title: </label>
	<input type="text" id="new_post_title" style="width: 99%;" value="<?php echo $post['Title']; ?>" /><br />
	<label for="new_post_content">Content: </label>
	<textarea id="new_post_content" style="width: 99%;" rows="25"><?php echo $post['Content']; ?></textarea><br />
	<input type="checkbox" id="new_post_hidden" value="hidden" <?php echo $hidden ?> /> Hidden
	<?php
	$template = '
		<span class="action" onclick="submit_blog_post();">Submit blogpost</span>
		<a class="action" href="/blog/View/{UnderscoredName}">View blog</a>
	';
	$blog['UnderscoredName'] = str_replace(" ", "_", $blog['Name']);
	echo expand_template($template, $blog);

	if($post['ID'] != -1) {
		echo '
			<br /><br />
			<span class="action" onclick="delete_blogpost();">X</span> Delete this post
		';
	}
?>
</div>

<div style="float: left;">
	Old posts
	<ul>
		<?php
		$template = '
			<li><a href="/blog/Control_panel/{blog_name}/{ID}">{Title}{Hidden_text}</a></li>
		  ';
		foreach($titles as $title) {
			$title['Hidden_text'] = '';
			if($title['Hidden'])
				$title['Hidden_text'] = ' {Hidden}';
			$title['blog_name'] = $blog_name;
			echo expand_template($template, $title);
		}
		?>
	</ul>
</div>

<div style="clear: both;"></div>
