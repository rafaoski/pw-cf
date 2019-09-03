<?php namespace ProcessWire;

/**
 * ProcessWire Bootstrap API Ready
 * ===============================
 * This ready.php file is called during ProcessWire bootstrap initialization process.
 * This occurs after the current page has been determined and the API is fully ready
 * to use, but before the current page has started rendering. This file receives a
 * copy of all ProcessWire API variables. This file is an idea place for adding your
 * own hook methods.
 *
 */

/** @var ProcessWire $wire */

// Maintenance Mode
$maintenance = pages('options')->checkbox;

// Clean Admin Tree
$cleanAdmin = false;

// https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/503
// https://yoast.com/http-503-site-maintenance-seo/
wire()->addHookAfter('Page::render', function($event) use($maintenance) {

	$page = $event->object;

	// we'll only apply this to the front-end of our site
	if($page->template == 'admin') return;

	if($maintenance == 0) return;

	// tell ProcessWire we are replacing the method we've hooked
	$event->replace = true;

		if( user()->isSuperuser() ) {
			$value  = $event->return;
			$value = str_replace("<body>",
			"<body><h1 class='text-xxxl text-center' style='color: red; background: black'>". __('Maintenance Mode !!!') .
			"</h1>", $value);
			// set the modified value back to the return value
			$event->return = $value;

		} else {
			$event->return = files()->render('layouts/_layout-maintenance');
		}
  });


/** Admin Pages Custom CSS // Style inside head ( Clean Admin Tree ) */
$wire->addHookAfter('Page::render', function($event) use($cleanAdmin) {

	$style = '';

	if(page()->template != 'admin') return; // Check if is Admin Panel
	$value  = $event->return; // Return Content
	$templates = urls()->templates; // Get Template folder URL

if( $cleanAdmin == true ) {
	$style .= "\n<link rel='stylesheet' href='{$templates}assets/css/admin/clean-admin-tree.css'>\n";
}

$event->return = str_replace("</head>", "\n\t$style</head>", $value); // Return All Changes
});

// Remove unnecessary categories
wire()->addHook('Pages::save', function($event) {

	$page = $event->arguments('page');

	if($page->template != 'blog-categories' || $page->checkbox == 0) return;

	// Disable the check box after saving the page ( you do not need it the next time you edit the options page )
	$page->setAndSave('checkbox', 0);

	// Categories
	$cat_pages = '';

	foreach (pages()->get("template=blog-categories")->children("include=all") as $category) {
		if(!$category->references->count()) {
			$cat_pages .= $category->title . ' , ';
			$category->trash();
		}
	}

	if( $cat_pages ) {
		$event->return = $this->message( __('Unnecessary categories have been moved to the trash: ') . ' ' . $cat_pages );
	} else {
		$event->return = $this->message( __('No categories found that could be moved to the trash.') );
	}

});

// https://processwire.com/docs/modules/hooks/
wire()->addHookProperty('Page::intro', function($event) {
	$page = $event->object;
	$intro = substr(strip_tags($page->body), 0, 255);
	$lastPeriodPos = strrpos($intro, '.');
	if($lastPeriodPos !== false) $intro = substr($intro, 0, $lastPeriodPos);
	$event->return = $intro . ' ... ';
});

// https://processwire.com/blog/posts/webp-images-on-an-existing-site/
if(config()->useWebP && page()->template != 'admin') {
	$wire->addHookAfter('Pageimage::url', function(HookEvent $event) {
		static $n = 0;
		$image = $event->object;
		if(++$n === 1 && in_array($image->ext, [ 'jpeg', 'jpg', 'png' ])) {
		$event->return = $image->webp()->url();
		}
		$n--;
	});
}

/**
 * Adds a webpInfo() method to Page objects
 *
 * Usage:
 *   $s = $page->webpInfo(); // get info string
 *   $a = $page->webpInfo(true); // get info array
 *
 * Requires ProcessWire 3.0.138
 *
 */
$wire->addHook('Page::webpInfo', function(HookEvent $event) {

	$page = $event->object;
	$files = $event->wire('files');
	$getArray = $event->arguments(0) === true;
	$fileTotal = 0;
	$webpTotal = 0;
	$qty = 0;

	if($page->hasFilesPath()) {

	  $imageFiles = $files->find($page->filesPath(), [
		'extensions' => [ 'jpeg', 'jpg', 'png' ],
		'recursive' => 0
	  ]);

	  foreach($imageFiles as $file) {

		$info = pathinfo($file);
		$webp = "$info[dirname]/$info[filename].webp";

		if(!file_exists($webp)) {
		  // if file.webp does not exist, try file with extension plus .webp
		  $webp = "$info[dirname]/$info[basename].webp"; // i.e. file.jpg.webp
		  if(!file_exists($webp)) continue; // no webp file available
		}

		// webp file is available
		$qty++;
		$webpTotal += filesize($webp);
		$fileTotal += filesize($file);
	  }
	}

	$pct = $fileTotal ? round((($fileTotal-$webpTotal)/$fileTotal)*100) : 0;

	$a = [
	  'qty' => $qty,
	  'qtyStr' => sprintf(_n('%d file', '%d files', $qty), $qty),
	  'pct' => $pct,
	  'fileSize' => $fileTotal,
	  'fileStr' => wireBytesStr($fileTotal, true),
	  'webpSize' => $webpTotal,
	  'webpStr' => wireBytesStr($webpTotal, true),
	  'saveSize' => $fileTotal - $webpTotal,
	  'saveStr' => wireBytesStr($fileTotal - $webpTotal),
	];

	$a['str'] =
	  "$a[fileStr] → $a[webpStr] webp / " .
	  "saved $a[saveStr] ($a[pct]%) for $a[qtyStr]";

	$event->return = $getArray ? $a : $a['str'];
  });

// https://processwire.com/blog/posts/pw-3.0.137/
//   $wire->addHookAfter('Pagefile::url, Pagefile::filename', function($event) {

// 	$config = $event->wire('config');
// 	$file = $event->return;

// 	if($event->method == 'url') {
// 	  // convert url to disk path
// 	  $file = $config->paths->root . substr($file, strlen($config->urls->root));
// 	}

// 	if(!file_exists($file)) {
// 	  // download file from source if it doesn't exist here
// 	  $src = 'https://domain.com/site/assets/files/';
// 	  $url = str_replace($config->paths->files, $src, $file);
// 	  $http = new WireHttp();
// 	  $http->download($url, $file);
// 	}
//   });