<?php namespace ProcessWire;

// echo page()->webpInfo();
?>
<!DOCTYPE html>
<html lang='<?= setting('lang-code') ?>' class='<?= setting('html-classes')->implode(' ') ?>'>
<head id='html-head'>
<?= siteHead() ?>
</head>
<body>

<!-- MAIN HEADER -->
	<header id='main-header' style="background-color: var(--color-contrast-lower);">

		<div class="container padding-y-lg">

			<?= langMenu(page()) ?>

			<div class='branding flex flex-center flex-column flex-wrap margin-y-sm'>
				<?= siteBranding() ?>
			</div>

			<nav class='main-nav text-center'>
				<a href='<?= setting('home')->url ?>'>
					<img src='<?= setting('logo') ?>' height='40'
						class='logo transition-bounce-s-r'  alt='<?= setting('site-name') ?>'>
				</a>
				<?= navLinks() ?>
			</nav>

		</div>

	</header>

	<?= editBtn() ?>

<!-- MAIN CONTENT -->
	<main id='main-content' class='margin-top-md margin-bottom-lg'>

		<div class="container max-width-lg">

			<h1 id='title' class='title'><?= page()->title ?></h1>

			<?php if( page()->images && count(page()->images) ) : ?>
			<picture class='flex flex-center'>
				<source srcset="<?= page()->images->first->webp()->url ?>" type="image/webp">
				<img src="<?= page()->images->first->url ?>" class='parallax-page'
					 alt="<?= page()->images->first->description ?>">
			</picture>
			<?php endif; ?>

			<div id='body' class='body text-component'>
				<?= page()->body ?>
			</div>

		</div>

	</main>

	<?= debugInfo() ?>

<!-- MAIN FOOTER -->
	<footer id='main-footer' style="background-color: var(--color-contrast-lower);">

		<div class='container padding-y-lg text-component text-sm'>

			<div class="flex flex-wrap justify-between flex-gap-sm margin-y-sm">
				<?php
					foreach (setting('footer-sidebar') as $sidebar) {
						echo "<div><edit field='body' page='$sidebar->id'>$sidebar->body</edit></div>";
					}
				?>
			</div>

			<div  class="flex flex-wrap flex-center flex-gap-sm">
				<p>
					&copy; <a href='<?= setting('home')->url ?>' aria-label='home'><?= setting('site-name') ?></a>
					<?= date('Y') . ' ' . setting('all-right') ?>
				</p>
				<div class='search'>
					<?= searchForm() ?>
				</div>
			</div>

		</div>

	</footer>

	<script>
	/* https://simpleparallax.com/#examples */
		var image = document.getElementsByClassName('parallax');
		new simpleParallax(image);
    /* Single view */
		var imagePage = document.getElementsByClassName('parallax-page');
			new simpleParallax(imagePage, {
			orientation: 'left'
		});
	</script>

</body>
</html>
