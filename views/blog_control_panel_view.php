<?php require_once '../util/wikitexttohtml.php'; ?>

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
			<li class="action"><a href="/blog/Control_panel/{blogname}/{ID}">{Created_date}<br />{Title}{Hidden_text}</a></li>
		  ';
		foreach($titles as $title) {
			$title['blogname'] = $blog['UnderscoredName'];
			$title['Hidden_text'] = '';
			if($title['Hidden'])
				$title['Hidden_text'] = ' (hidden)';
			echo expand_template($template, $title);
		}
		?>
	</ul>
</div>

<div style="clear: both;">
	<div class="accordion">
		<h3>Formatting</h3>
		<div>
			<table class="bordered_table" style="width: 100%;">
				<tr> <!-- titles -->
					<td>
						<?php
							$sample = "= Title =\n"
									. "== Title ==\n"
									. "...\n"
									. "===== Title =====\n";
							echo "<pre>" . $sample . "</pre>";
						?>
					</td>
					<td>
						<?php
							echo WikiTextToHTML::convertWikiTextToHTML_ex($sample);
						?>
					</td>
				</tr>
				<tr> <!-- img -->
					<td>
						<?php
							$sample = "[img /data/flattr-badge-large.png]";
							echo "<pre>" . $sample . "</pre>";
						?>
					</td>
					<td>
						<?php
							echo WikiTextToHTML::convertWikiTextToHTML_ex($sample);
						?>
					</td>
				</tr>
				<tr> <!-- link -->
					<td>
						<?php
							$sample = "[a http://www.google.com Test]";
							echo "<pre>" . $sample . "</pre>";
						?>
					</td>
					<td>
						<?php
							echo WikiTextToHTML::convertWikiTextToHTML_ex($sample);
						?>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>
