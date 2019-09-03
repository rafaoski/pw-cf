<?php namespace ProcessWire;

// Reset variables
$content = $noFound = $pagination = '';

// Get tags
$blogCategories = page()->children('limit=18');

// No found
if( !count($blogCategories) ) {
	$noFound .= "<h1>" . setting('no-found') . "</h1>";
}

// Pagination
$pagination = basicPagination($blogCategories);

// Content
$content .= files()->render('parts/_blog-categories', ['blogCategories' => $blogCategories]);

// Render all content
echo files()->render('layouts/_layout-blog',
[
	'noFound' => $noFound,
	'content' => $content,
	'sidebar' => '',
	'pagination' => $pagination,
]);