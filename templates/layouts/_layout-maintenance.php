<?php namespace ProcessWire;

if( page()->template != 'home' ) {
	session()->redirect( pages('/')->url );
}

$protocol = 'HTTP/1.0';

if ( $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.1' ) {
	$protocol = 'HTTP/1.1';
}

header( $protocol . ' 503 Service Unavailable', true, 503 );
header( 'Retry-After: 3600' );
?>

<!DOCTYPE html>
<html lang='<?= setting('lang-code') ?>'>
<head id='html-head'>
	<meta http-equiv='content-type' content='text/html; charset=utf-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1.0'>
	<link rel='icon' href='<?= setting('favicon') ?>'/>
	<title><?= setting('maintenace-info') ?></title>
	<link rel='stylesheet' href='<?= urls()->templates ?>assets/css/style.css'>
	<meta name="robots" content="noindex, nofollow">
</head>
<body>

<div class="container">
	<div class='flex flex-center flex-column margin-y-sm'>

	<?php if (setting('logo')): ?>
		<a href='<?= setting('home')->url ?>'>
			<img class='transition-bounce-s-r margin-x-md' src='<?= setting('logo') ?>'
			style='width: 60px; filter: drop-shadow( 1px 2px 2px var(--color-black) );' alt='<?= setting('logo_alt') ?>'>
		</a>
	<?php endif ?>

		<h1><?= setting('maintenace-info') ?></h1>

		<div class='more-info'>
			<?= pages('options')->body ?>
		</div>

		<img style='max-width: 350px; padding: 10px' src="<?= urls()->templates ?>assets/img/maintenance.png" alt="maintenance mode">

	</div>
</div>

</body>
</html>
