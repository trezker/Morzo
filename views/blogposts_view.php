<?php
require_once '../util/wikitexttohtml.php';

$post_template = '
		<div class="blogpost">
			<div class="blogtitle">
				{Title}
				<span style="float:right; font-size: smaller;">
					{YMD}
	';
if($show_owner_controls == true) {
	$post_template .= '
					<a class="action" href="/blog/Control_panel/{Blog_name_u}/{ID}">Edit</a>
					<span onclick="delete_blogpost({ID});" class="action">Hide</span>
		';
}
$post_template .= '
				</span>
			</div>
			<div class="blogcontent"s>{!Content2}</div>
		</div>
	';


foreach($posts as $post) {
	$post['Blog_name_u'] = str_replace(" ", "_", $post['Blog_name']);
	// parse and display input
	$input = explode("\n", $post['Content']);

	// convert input to HTML output array
	$output = WikiTextToHTML::convertWikiTextToHTML($input);
	
	// output to stream with newlines
	$content = '';
	foreach($output as $line) {
		$content .= "${line}\n";
	}
	$post['Content2'] = $content;

	list($YMD, $HMS) = explode(' ', $post['Created_date']);
	$post['YMD'] = $YMD;
	$post['HMS'] = $HMS;
	//echo expand_template($post_template, $post);
	echo expand_template($post_template, $post);
}

