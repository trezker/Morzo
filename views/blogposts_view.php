<?php
require_once '../util/wikitexttohtml.php';

$post_template = '
		<div class="blogpost">
			<div class="blogtitle">
				{Title}
				<span style="float:right; font-size: smaller;">
					{YMD}
				</span></div>
			<div class="blogcontent"s>{!Content2}</div>
		</div>
	  ';
foreach($posts as $post) {
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

