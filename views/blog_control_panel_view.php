<input type="hidden" id="blog_id" value="<?php echo $blog['ID']; ?>" /><br />
<label for="new_post_title">Title: </label>
<input type="text" id="new_post_title" style="width: 99%;" /><br />
<label for="new_post_content">Content: </label>
<textarea id="new_post_content" style="width: 99%;" rows="25"></textarea><br />
<?php
$template = '
	<span class="action" onclick="new_blog_post({ID});">Create blogpost</span>
	<a class="action" href="/blog/{Name}">View blog</a>
  ';
echo expand_template($template, $blog);
?>
