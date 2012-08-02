<?php
$post_template = '
		<div class="blogpost">
			<div class="blogtitle">
				{Title}
				<span style="float:right; font-size: smaller;">
					{YMD}
				</span></div>
			<div class="blogcontent"s>{Content}</div>
		</div>
	  ';
foreach($posts as $post) {
	list($YMD, $HMS) = explode(' ', $post['Created_date']);
	$post['YMD'] = $YMD;
	$post['HMS'] = $HMS;
	echo expand_template($post_template, $post);
}
