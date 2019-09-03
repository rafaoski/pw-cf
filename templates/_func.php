<?php namespace ProcessWire;

/**
 * Return site head
 *
 * @param array|string $options Options to modify default behavior:
 *  - `css` (url): CSS files url.
 *  - `js` (url): JS files url.
 *  - `favicon` (url): Favicon url.
 *  - `title` (string): Title tag.
 *  - `description` (string): Meta description tag.
 *  - `seo_maestro` (module): Seo Maestro module. ( https://modules.processwire.com/modules/seo-maestro/ )
 *  - `templates_noindex` (array): Templates name to skip in search results.
 *
 */
function siteHead($options = array()) {

	// $out is where we store the markup we are creating in this function
	$out = '';

	// Default Options
	$defaults = array(
		'css' => setting('css-files'),
		'js' => setting('js-files'),
		'favicon' => setting('favicon'),
		'title' => page('meta_title|title'),
		'description' => page('meta_description'),
		'seo_maestro' => null, // page()->seo
		'templates_noindex' => array(),
	);
	// Merge Options
	$options = _mergeOptions($defaults, $options);

	$out .= "<meta http-equiv='content-type' content='text/html; charset=utf-8'>\n";
	$out .= "<meta name='viewport' content='width=device-width, initial-scale=1.0'>\n";
	$out .= "<link rel='icon' href='$options[favicon]'/>\n";
	if( !$options['seo_maestro'] ) {
	$out .= "<title>$options[title]</title>\n";
	}
	if( $options['description'] && !$options['seo_maestro'] ) {
		$out .= "<meta id='head-description' name='description' content='$options[description]'/>\n";
	}
	if( $options['seo_maestro'] ) {
		$out .= page()->seo; // if installed Seo Maestro
	}
	if ( in_array( page()->template, $options['templates_noindex']) ) {
		$out .= "<meta name='robots' content='noindex'>\n";  // No index follow categories or tags
	}
	$out .= $options['css']->each("<link rel='stylesheet' href='{value}'>\n");
	$out .= $options['js']->each("<script src='{value}'></script>\n");
	$out .= "<script>document.getElementsByTagName('html')[0].className += ' js';</script>"; // Codyhouse
	$out .= hreflang(page()); // the hreflang parameter
	$out .= seoPagination(); // seo meta robots ( 'noindex, follow' ) or seo pagination
	$out .= gwCode( setting('gw-code') );
	$out .= gaCode( setting('ga-code') );
// return all
	return $out;
}

/**
 * Return the hreflang parameter
 *
 * @param Page $page
 *
 */
function hreflang($page) {
  if(!$page->getLanguages()) return;
  if (!modules()->isInstalled("LanguageSupportPageNames")) return;

  // $out is where we store the markup we are creating in this function
  $out = '';

  // handle output of 'hreflang' link tags for multi-language
  foreach(languages() as $language) {
	// if this page is not viewable in the language, skip it
	if(!$page->viewable($language)) continue;
	// get the http URL for this page in the given language
	$url = $page->localHttpUrl($language);
	// hreflang code for language uses language name from homepage
	$hreflang = setting('home')->getLanguageValue($language, 'name');
	if($hreflang == 'home') $hreflang = setting('lang-code');
	// output the <link> tag: note that this assumes your language names are the same as required by hreflang.
	$out .= "<link rel='alternate' hreflang='$hreflang' href='$url' />\n";
  }
  return $out;
}

/**
 * Return seo meta robots ( 'noindex, follow' ) or seo pagination
 *
 * @return mixed
 *
 */
function seoPagination() {
	// If not any pageNum or pageHeaderTags
	if( input()->pageNum == null || config()->pagerHeadTags == null ) return;

	// $out is where we store the markup we are creating in this function
	$out = '';

	// https://processwire.com/blog/posts/processwire-2.6.18-updates-pagination-and-seo/
	if (input()->pageNum > 1) {
			$out .= "<meta name='robots' content='noindex,follow'>\n";
	}
	// https://weekly.pw/issue/222/
	if (config()->pagerHeadTags) {
			$out .= config()->pagerHeadTags . "\n";
	}
	return $out;
}

/**
 * Given a group of pages render a tree of navigation
 *
 * @param Page|PageArray $items Page to start the navigation tree from or pages to render
 * @param int $maxDepth How many levels of navigation below current should it go?
 *
 */
function renderNavTree($items, $maxDepth = 3) {

	// if we've been given just one item, convert it to an array of items
	if($items instanceof Page) $items = array($items);

	// if there aren't any items to output, exit now
	if(!count($items)) return;

	// $out is where we store the markup we are creating in this function
	// start our <ul> markup
	echo "<ul class='list'>";

	// cycle through all the items
	foreach($items as $item) {

		// markup for the list item...
		// if current item is the same as the page being viewed, add a "current" class and
		// visually hidden text for screen readers to it
		if($item->id == wire('page')->id) {
			echo "<li class='current'>";
		} else {
			echo "<li>";
		}

		// markup for the link
		echo "<a href='$item->url'>$item->title</a>";

		// if the item has children and we're allowed to output tree navigation (maxDepth)
		// then call this same function again for the item's children
		if($item->hasChildren() && $maxDepth) {
			renderNavTree($item->children, $maxDepth-1);
		}

		// close the list item
		echo "</li>";
	}

	// end our <ul> markup
	echo "</ul>";
}

/**
 * Return Language Menu
 *
 * @param Page $page
 * @param array|string $options Options to modify default behavior:
 *  - `id` (string): Selector div id.
 *  - `class` (string): Selector div class.
 *  - `current_class` (string): Selector class current item.
 *  - `no_current` (string): Selector class no current item.
 *  - `style` (string): CSS style for the div element.
 *
 */
function langMenu($page, $options = array()) {

	if(!$page->getLanguages()) return;

	if (!modules()->isInstalled("LanguageSupportPageNames")) return;

	// $out is where we store the markup we are creating in this function
	$out = '';

	// Default Options
	$defaults = array(
	'id' => 'lang-menu',
	'class' => 'lang-menu padding-sm text-sm margin-y-md',
	'current_class' => 'color-warning',
	'no_current' => 'color-inherit',
	'style' => 'overflow: auto; white-space: nowrap;'
	);
	// Merge Options
	$options = _mergeOptions($defaults, $options);

	$style = $options['style'] ? " style='$options[style]'" : '';

	// $out is where we store the markup we are creating in this function
	$out = "\n\t<div id='$options[id]' class='$options[class]'$style>\n";

	foreach(languages() as $language) {

		if(!$page->viewable($language)) continue; // is page viewable in this language?

		$current = $language->id == user()->language->id ? $options['current_class'] : $options['no_current'];

		$url = $page->localUrl($language);

		$hreflang = setting('home')->getLanguageValue($language, 'name');

		if($hreflang == 'home') $hreflang = setting('lang-code');

		$out .= "\t\t<a class='lang-item $current' hreflang='$hreflang' href='$url'>$language->title</a>\n";
	}

	$out .= "\t</div>\n\n";

	return $out;
}

/**
 * Return Navigation Links
 *
 * @param array|string $options Options to modify default behavior:
 *  - `root_url` (link): Home Page URL.
 *
 */
function navLinks($options = array()) {

	// $out is where we store the markup we are creating in this function
	$out = '';

	// Default Options
	$defaults = array(
		'root_url' => pages('/')->and(pages('/')->children)
	);
	// Merge Options
	$options = _mergeOptions($defaults, $options);

	foreach($options['root_url'] as $item) {
		$class = $item->id == page()->id ? 'current-item' : 'color-inherit';
		$out .= "<a class='$class' href='$item->url'>$item->title</a>\n";
	}

  return $out;
}

/**
 * Return unordered HTML list
 *
 * @param PageArray $items
 * @param array|string $options Options to modify default behavior:
 *  - `class` (string): list class.
 *  - `countable` (bool): Count of item references to blog post ( categories, tags ).
 *  - `title` (string): Name of item.
 *  - `parent` (page): Parent Page.
 *
 */
function simpleNav($items, $options = array() ) {
	// $out is where we store the markup we are creating in this function
	$out = '';

	if( !count($items) ) return;

  // Default Options
	$defaults = array(
		'class' => 'nav-items text-component',
		'countable' => false,
		'title' => '',
		'parent' => ''
	);
	// Merge Options
	$options = _mergeOptions($defaults, $options);

	$out .= "<ul class='$options[class]'>";

	$out .= "<li class='text-md text-underline' style='list-style: none'>";

	if( $options['parent'] ) {

	$out .= "<a href='" . $options['parent']->url . "'>";
	$out .= 	$options['title'];
	$out .=  "</a>";

	} else {

	$out .=  $options['title'];

	}

	$out .= "</li>";

	foreach ($items as $item) {

	$count = "<span class='text-xs'>( " . count($item->references()) . " )</span>";

		if ($options['countable']) {

			if( count($item->references()) ) {
				$out .= "<li><a href='$item->url' title='$item->name'>$item->title $count </a></li>";
			}

		} else {

			$out .= "<li><a href='$item->url' title='$item->name'>$item->title</a></li>";
		}
  	}

  	$out .= "</ul>";

	return $out;
}

/**
 *  Return Basic Pagination
 *  https://processwire.com/docs/front-end/markup-pager-nav/
 *
 * @param PageArray $results
 * @param array|string $options Options to modify default behavior:
 * - 'base_url' (link): The baseUrl (string) from which the navigiation item links will start.
 * - 'get_vars' (array): Array of GET vars that should appear in the pagination links, or leave empty and populate $input->whitelist (preferred). .
 *
 */
function basicPagination($results, $options = array()) {

	if ( !count($results) ) return;

	// Default Options
	$defaults = array(
		'base_url' => null,
		'get_vars' => array(),
	);
	// Merge Options
	$options = _mergeOptions($defaults, $options);

	return $results->renderPager(array(
		'nextItemLabel' => setting('next'),
		'previousItemLabel' => setting('previous'),
		'listMarkup' => "<ul class='MarkupPagerNav flex flex-wrap flex-center' style='list-style: none'>{out}</ul>",
		'itemMarkup' => "<li class='{class} margin-xs'>{out}</li>",
		'linkMarkup' => "<a href='{url}'><span>{out}</span></a>",
		$options['base_url'] ? "'baseUrl' => $options[base_url]" : null,
		'getVars' => array_filter($options['get_vars']),
	));
}

/**
 * Return Previous Next Button Page
 * @param Page $item
 *
 */
function navPage($item) {
// If  item is empty return null
if(!$item) return;
// $out is where we store the markup we are creating in this function
$out = '';
// Prev Next Button
		$p_next = $item->next();
		$p_prev = $item->prev();
// link to the prev blog post, if there is one
		if ($p_prev->id) {
		$out .= "<a href='$p_prev->url'>";
		$out .= $p_prev->title . "</a>";
		}
// link to the next blog post, if there is one
		if ($p_next->id) {
				$out .= "<a href='$p_next->url'>";
				$out .= $p_next->title  . "</a>";
		}
		return $out;
}

/**
 * Return css / js files.
 *
 * @param array|string $options Options to modify default behavior:
 *  - `css` ( WireArray() ): CSS files url.
 *  - `js` ( WireArray() ): JS files url.
 *
 */
function loadfiles($options = array()) {

	// $out is where we store the markup we are creating in this function
	$out = '';

	// Default Options
	$defaults = array(
		'css' => WireArray(),
		'js' => WireArray(),
	);
	// Merge Options
	$options = _mergeOptions($defaults, $options);

	if( count($options['css']) ) $out .= $options['css']->each("<link rel='stylesheet' href='{value}'>\n");

	if( count($options['js']) ) $out .= $options['js']->each("<script src='{value}'></script>\n");

	return $out . "\n";
}

/**
 * Return site branding.
 *
 * @param array|string $options Options to modify default behavior:
 * 'home' (link): linkt to home url.
 * 'site_name' (string): site name.
 * 'site_description' (string): site description.
 *
 */
function siteBranding($options = array()) {

// if home or not home ( set headings )
if(page()->id == 1) {
	$h1 = 'h1';
	$h2 = 'h2';
} else {
	$h1 = 'p';
	$h2 = 'p';
}

// Default Options
	$defaults = array(
		'home' => setting('home')->url,
		'site_name' => setting('site-name'),
		'site_description' => setting('site-description'),
		);
// Merge Options
	$options = _mergeOptions($defaults, $options);
// Return content
	return "
	<$h1 class='site-name text-xxl'>
		<a href='$options[home]'>$options[site_name]</a>
	</$h1>
	<$h2 class='site-description text-md'>$options[site_description]</$h2>";
}

/**
 * Return search form
 *
 * @param array|string $options Options to modify default behavior:
 *  - `search_page` (link): search page URL.
 *  - `input_placeholder` (string): input placeholder text.
 *  - `search_label` (string): label text.
 *
 */
function searchForm($options = array()) {

// Default Options
$defaults = array(
  'search_page' => pages()->get('template=search')->url,
  'input_placeholder' => setting('search-placeholder'),
  'search_label' => setting('search-label')
);
// Merge Options
$options = _mergeOptions($defaults, $options);

// return search form
return "
  <form action='$options[search_page]' method='get'>
	<label style='color: var(--color-black); font-size: var(--text-md)' class='form-label' for='q'>$options[search_label]</label><br>
	<input type='search' name='q' class='form-control width-100%' id='q' placeholder='$options[input_placeholder] &hellip;' required>
  </form>";
}

/**
 * Return icon ( https://fontawesome.com/icons?m=free )
 *
 * @param string $icon icon
 * @param array|string $options Options to modify default behavior:
 *  - `size` (int): css property font-size.
 *  - `color` (string): css property color.
 *  - `class` (string): css class property.
 *
 */
function icon($icon, $options = array()) {
	// If  item is empty return null
	if(!$icon) return;

	// $out is where we store the markup we are creating in this function
	$out = '';

    // Cleanup icon
	$icon = sanitizer()->removeWhitespace($icon);

	// Default Options
	$defaults = array(
		'size' => 1,
		'color' => 'black',
		'class' => "icon-$icon",
		'prefix' => 'fas'
	);
	// Merge Options
	$options = _mergeOptions($defaults, $options);

	$out .= "<span style='font-size: $options[size]em; color: $options[color];'>\n";
	$out .= "<i class='$options[prefix] fa-$icon $options[class]'></i>\n";
	$out .= "</span>\n";

	return $out;
}

/**
 * Return navigation
 * @param Page $item page item
 * @param array|string $options Options to modify default behavior:
 *  - `class` (string): list class.
 *  - `comment_icon` (string): String.
 *
 */
function countComments($item, $options = array()) {

	if( setting('disable-comments') == 1 ) return;

	if( !count($item->comments) ) return;

	// $out is where we store the markup we are creating in this function
	$out = '';

	// Default Options
	$defaults = array(
	'class' => 'comments',
	'icon' => icon('comments')
	);
	// Merge Options
	$options = _mergeOptions($defaults, $options);

 	// num comments
	$out .= ' | ' . $options['icon'] . " <a class='$options[class]' href='{$item->url}#CommentForm'>";
	$out .= count($item->comments) . '</a>';

	return $out;
}

/**
 * Return AddToAny social share buttons script
 * https://www.addtoany.com/
 *
 * @param array $options Basic Usage toAny(['twitter' => true'])
 * 'twitter' => true,
 * 'facebook' => true,
 * 'google_plus' => false,
 * 'linkedin' => false,
 * 'rreddit' => false,
 * 'email' => false,
 * 'google_gmail' => false,
 * 'share_all' => true,
 *
 */
function toAny($options = array()) {

// $out is where we store the markup we are creating in this function
	$out = '';
// Reset variables
	$buttonLinks = '';
// Default share links
	$links = [
		'twitter' => "<a class='a2a_button_twitter'></a>",
		'facebook' => "<a class='a2a_button_facebook'></a>",
		'google_plus' => "<a class='a2a_button_google_plus'></a>",
		'linkedin' => "<a class='a2a_button_linkedin'></a>",
		'rreddit' => "<a class='a2a_button_reddit'></a>",
		'email' => "<a class='a2a_button_email'></a>",
		'google_gmail' => "<a class='a2a_button_google_gmail'></a>",
		'share_all' => "<a class='a2a_dd' href='https://www.addtoany.com/share'></a>"
	];
// Foreach Items
	foreach ($options as $key => $value) {
		if($options[$key] == true) {
			$buttonLinks .= $links[$key];
		}
	}
// Start Content
	$out .= "<!-- AddToAny BEGIN -->
	<div class='a2a_kit a2a_kit_size_32 a2a_floating_style a2a_vertical_style'
			 style='right:0px; top:210px; background-color: #2e2d2d99;'>";
	$out .= $buttonLinks; // Show Links
	$out .= "</div>
	<script async src='https://static.addtoany.com/menu/page.js'></script>
	<!-- /AddToAny END -->";
	return $out;
}

/**
 * Return Google Webmaster Tools Verification Code
 *
 * @param string $code
 *
 */
function gwCode($code) {
// If code is empty return null
if(!$code) return;

// Return Google Verification Code
return "<!-- Google Site Verification Code -->
<meta name='google-site-verification' content='$code' />\n";
}

/**
 * Return Google Analytics Tracking Code
 * https://developers.google.com/analytics/devguides/collection/analyticsjs/
 *
 * @param string $code {your-google-analytics-code}
 *
 */
function gaCode($code) {
// If code is empty return null
if(!$code) return;

// Return Google Analytics Tracking Code
return "<!-- Google Analytics Tracking Code -->
<script defer src='https://www.googletagmanager.com/gtag/js?id=UA-{$code}'></script>
<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());
	gtag('config', 'UA-{$code}');
</script>\n";
}

/**
 * Return Link to Edit Page
 *
 * @param array|string $options Options to modify default behavior:
 *  - `id` (string): Selector id.
 *  - `div_class` (string): Selector div class.
 *  - `link_class` (string): Selector link class.
 *  - `edit_text` (string): The name of the buttont.
 *  - `edit_url` (link): Url to edit the page
 *
 */
function editBtn($options = array())
{
	// if not Page Editable return null
	if(!page()->editable()) return;

	// Default Options
	$defaults = array(
	'id' => 'edit-btn',
	'div_class' => 'edit-btn container margin-y-lg',
	'link_class' => 'link-button btn btn--primary btn--lg',
	'edit_text' => setting('edit'),
	'edit_url' => page()->editURL,
	);
	// Merge Options
	$options = _mergeOptions($defaults, $options);

	// Display region debugging info
	return "<div id='$options[id]' class='$options[div_class]'>
	<a class='$options[link_class]' href='$options[edit_url]'>$options[edit_text]</a></div>";
}

/**
 * Return region debugging info
 *
 * @param array|string $options Options to modify default behavior:
 *  - `id` (string): Selector id.
 *  - `class` (string): Selector class.
 *
 */
function debugInfo($options = array())
{
	// Default Options
	$defaults = array(
		'id' => 'debug-bar',
		'class' => 'debug-bar container margin-y-md'
	);
	// Merge Options
	$options = _mergeOptions($defaults, $options);

	// display region debugging info
	if(config()->debug && user()->isSuperuser()) {
		return "\n\t<div id='$options[id]' class='$options[class]'>
			<!--PW-REGION-DEBUG-->
		</div>\n";
	}
}

/*****************************************************************************************
 * Internal support functions
 *
 */

/**
 * Prepare and merge an $options argument
 *
 * - This converts PW selector strings data attribute strings to associative arrays.
 * - This converts non-associative attributes to associative boolean attributes.
 * - This merges $defaults with $options.
 *
 * @param array $defaults
 * @param array|string $options
 * @return array
 * @internal
 *
 */
function _mergeOptions(array $defaults, $options) {

	// allow for ProcessWire selector style strings
	// allow for Uikit data attribute strings
	if(is_string($options)) {
		$options = str_replace(';', ',', $options);
		$o = explode(',', $options);
		$options = array();
		foreach($o as $value) {
			if(strpos($value, '=')) {
				// key=value
				list($key, $value) = explode('=', $value, 2);
			} else if(strpos($value, ':')) {
				// key: value
				list($key, $value) = explode(':', $value, 2);
			} else {
				// boolean
				$key = $value;
				$value = true;
			}
			$key = trim($key);
			if(is_string($value)) {
				$value = trim($value);
				// convert boolean strings to real booleans
				$v = strtolower($value);
				if($v === 'false') $value = false;
				if($v === 'true') $value = true;
			}
			$options[$key] = $value;
		}
	}

	if(!is_array($options)) {
		$options = array();
	}

	foreach($options as $key => $value) {
		if(is_int($key) && is_string($value)) {
			// non-associative options convert to boolean attribute
			$defaults[$value] = true;
		}
	}

	return array_merge($defaults, $options);
}
