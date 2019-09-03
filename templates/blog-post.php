<?php namespace ProcessWire;

// Reset variables
$content = $noFound = $pagination = '';

// Custom styles / scripts
$content .= loadfiles(
[
	'css' => WireArray([urls()->FieldtypeComments . 'comments.css']),
	'js' => WireArray([urls('jquery'), urls()->FieldtypeComments . 'comments.min.js'])
]);

// Universal Sharing Buttons ( https://www.addtoany.com/ )
$content .= toAny(
[
	'twitter' => true,
	'facebook' => true,
	'email' => true,
	'share_all' => true
]);

// Blog article
$content .= files()->render('parts/_blog-article.php', ['item' => page()]);

// Simple nav
$content .= files()->render('parts/_blog-nav');

// Page Links https://processwire.com/blog/posts/processwire-3.0.107-core-updates/
$content .= files()->render('parts/_page-links');

// Comments
if( setting('disable-comments') == 0 ) {
$content .= files()->render('parts/_blog-comments.php');
}

// Render all content
echo files()->render('layouts/_layout-blog',
[
	'noFound' => $noFound,
	'content' => $content,
	'sidebar' => '',
	'pagination' => $pagination,
]);
