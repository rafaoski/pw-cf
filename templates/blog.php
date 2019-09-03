<?php namespace ProcessWire;

// Reset variables
$content = $noFound = $pagination = '';

// Get all blog entries
$blogPosts = page()->children('limit=12');

// No found
if( !count($blogPosts) ) {
	$noFound .= "<h1>" . setting('no-found') . "</h1>";
}

// Pagination
$pagination = basicPagination($blogPosts);

// Content articles
$content .= "<div class='grid grid-gap-md'>";
foreach ($blogPosts as $item) {
	$content .= "<div class='col-6@md'>";
	$content .= files()->render('parts/_blog-article.php',['item' => $item]);
	$content .= "</div>";
}
$content .= "</div>";

// Render all content
echo files()->render('layouts/_layout-blog',
[
	'noFound' => $noFound,
	'content' => $content,
	'sidebar' => '',
	'pagination' => $pagination,
]);
