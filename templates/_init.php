<?php namespace ProcessWire;
/**
 * This _init.php file is called automatically by ProcessWire before every page render
 *
 */

/** @var ProcessWire $wire */

// Set jQuery
urls()->set('jquery', 'https://code.jquery.com/jquery-3.4.1.min.js');

// Simple Parallax
urls()->set('simple-parallax', 'https://cdn.jsdelivr.net/npm/simple-parallax-js@5.0.2/dist/simpleParallax.min.js');

// Font Awesome
urls()->set('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0-12/css/all.min.css');

setting([
// Basic options
	'home' => pages('/'),
	'lang-code' => __('en'),
	'logo' => pages('options')->logo ? pages('options')->logo->url : '',
	'logo_alt' => pages('options')->logo ? pages('options')->logo->description : '',
	'favicon' => pages('options')->favicon ? pages('options')->favicon->url : '',
	'site-name' => pages('options')->site_name,
	'site-description' => pages('options')->site_description,
	'footer-sidebar' => pages('options')->footer_sidebar,
	'e-mail' => pages()->get("template=contact")->e_mail,
	'save-messages' => pages()->get("template=contact")->checkbox,
	'disable-comments' => pages()->get("template=blog")->checkbox,
	'gw-code' => '',
	'ga-code' => '',
// Custom body class
	'html-classes' => WireArray([
		'template-' . page()->template->name,
		'page-' . page()->id,
	]),
// Get Styles
	'css-files' => WireArray([
		urls('templates') . 'assets/css/style.css',
		urls('font-awesome'),
	]),
// Get Scripts
	'js-files' => WireArray([
		urls('templates') . 'assets/js/scripts.min.js',
		urls('simple-parallax')
	]),
// Basic Translation
	'lang-code' => __('en'),
	'edit' => __('Edit'),
	'next' => __('Next'),
	'previous' => __('Previous'),
	'search-placeholder' => __('Search'),
	'search-label' => __('Search Word'),
	'found-pages' => __("Found %d page(s)."),
	'no-found' =>  __('Sorry, no results were found.'),
	'also-like' => __('You might also like:'),
	'all-right' => __('All rights reserved'),
	'learn-more' => __('Learn more about us'),
	'read-more' => __('Read more'),
	'maintenace-info' => __('Sorry the site is undergoing maintenance'),
// Blog Translation
	'recent-posts' => __('Recent posts'),
// Comments Form Translation
	'post-comment' => __('Post a comment'),
	'comment-text' => __('Comments'),
	'comment-header' => __('Posted by %s on %s'),
	'success-message' => __('Thank you, your comment has been posted.'),
	'pending-message' => __('Your comment has been submitted and will appear once approved by the moderator.'),
	'error-message' => __('Your comment was not saved due to one or more errors.') . ' ' .
	__('Please check that you have completed all fields before submitting again.'),
	'comment-cite' => __('Your Name'),
	'comment-email' => __('Your E-Mail'),
	'comment-website' => __('Website'),
	'comment-stars' => __('Your Rating'),
	'comment-submit' => __('Submit'),
	'stars-required' => __('Please select a star rating'),
// Contact Page Translation
	'contact-name' => __('Name'),
	'contact-email' => __("Email"),
	'contact-advphone' => __("Phone"),
	'contact-message' => __("Message"),
	'contact-submit' => __("Submit"),
	'contact-acceptprivacy' => __("Accept Privacy Policy"),
	'contact-privacy' =>  __('Before checking, read our privacy policy'),
	'csrf-failed' => __('CSRF check failed!'),
	'contact-yourmessage' => __("Your Message"),
	'contact-errorfix' => __('There were errors, please fix !!!'),
	'contact-return' => __('Return to contact page'),
	'contact-subject' => __('Inquiry from the customer: %s - %s'),
	'contact-succes' => __('Thanks %s, we just received your message ... We will try to reply to your e-mail ( %s ) as soon as possible.'),
	'email-warning' => __('You must complete the E-mail adress on the options page !!!')

]);

include_once('./_func.php');

// RSS
if( strlen(input()->urlSegment1) && $input->urlSegment1 == 'rss' ) {
// retrieve the RSS module$rss = $modules->get("MarkupRSS");
$rss = modules()->get('MarkupRSS');

// RSS title
$rss->title = setting('rss-title');

// RSS description
$rss->description = setting('rss-description');

// find the pages you want to appear in the feed.
// this can be any group of pages returned by $pages->find() or $page->children(), etc.
$items = $pages->find('template=blog-post, limit=10, sort=-modified');

// send the output of the RSS feed, and you are done
$rss->render($items);

// do not show main
exit();

}

// ADD USER => https://processwire.com/api/variables/user/
	// $u = $users->add('demo1');
	// $u->parent = 1176;
	// $u->template = 'member-user';
	// $u->pass = "pass-1";
	// $u->addRole("guest");
	// $u->save();

// RESET PASSWORD => https://processwire.com/talk/topic/1736-forgot-backend-password-how-do-you-reset/
	// $u = $users->get('admin'); // or whatever your username is
	// $u->of(false);
	// $u->pass = 'your-new-password';
	// $u->save();