<?php namespace ProcessWire;

// Get Rexent Posts
$recentPosts = pages()->get("template=blog");

// Get categories
$blogCategories = pages()->get('template=blog-categories');

$out = '';

	$out .= "<!-- RECENT POSTS -->\n" .
	simpleNav($recentPosts->children('limit=12, start=0'),
	[
		'countable' => false,
		'title' => setting('recent-posts'),
		'parent' => $recentPosts
	]);

	$out .= "<!-- CATEGORIES -->\n" .
	simpleNav($blogCategories->children('limit=12, start=0'),
	[
		'countable' => true,
		'title' => $blogCategories->title,
		'parent' => $blogCategories
	]);

return $out;