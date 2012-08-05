<div style="float: left; width: 500px;">
	<?php
		if($post['ID'] == -1) {
			echo 'Creating new post';
		} else {
			echo 'Editing old post';
		}
	?>
	<br />
	<input type="hidden" id="blog_id" value="<?php echo $blog['ID']; ?>" />
	<input type="hidden" id="post_id" value="<?php echo $post['ID']; ?>" />
	<label for="new_post_title">Title: </label>
	<input type="text" id="new_post_title" style="width: 99%;" value="<?php echo $post['Title']; ?>" /><br />
	<label for="new_post_content">Content: </label>
	<textarea id="new_post_content" style="width: 99%;" rows="25"><?php echo $post['Content']; ?></textarea><br />
	<?php
		$hidden = '';
		if($post['Hidden']==1)
			$hidden = 'checked="true"'; 
	?>
	<input type="checkbox" id="new_post_hidden" value="hidden" <?php echo $hidden ?> /> Hidden
	<?php
	$template = '
		<span class="action" onclick="submit_blog_post();">Submit blogpost</span>
		<a class="action" href="/blog/View/{UnderscoredName}">View blog</a>
	  ';
	$blog['UnderscoredName'] = str_replace(" ", "_", $blog['Name']);
	echo expand_template($template, $blog);
?>
</div>

<div style="float: left;">
	Old posts
	<ul>
		<?php
		$template = '
			<li class="action" onclick="edit_blogpost({ID})">{Title}</li>
		  ';
		foreach($titles as $title) {
			echo expand_template($template, $title);
		}
		?>
	</ul>
</div>

<div style="clear: both;"></div>
