<?php namespace ProcessWire;
// Get image
$img = page()->images;
?>

<main id='main-content'>

	<div class="container max-width-xl">

		<h1 id='title' class='title'>
			<?= page()->title ?>
		</h1>

		<?= $noFound ?>

		<?= $pagination ?>

		<div class='grid grid-gap-md text-component'>

			<!-- BODY -->
			<div class='body col-9@md'>
				<?= $content ?>
			</div>

			<!-- SIDEBAR -->
			<div class='sidebar col-3@md text-sm'>

				<?php if ( $img && count($img) ) {
					$imgWebp = $img->first->webp();
					$imgBasic = $img->first;
					  echo "<picture>
								<source srcset='$imgWebp->url' type='image/webp'>\n
								<img src='$imgBasic->url' alt='$imgBasic->description'>\n
							</picture>";
					}
				?>

				<?= $sidebar ?>

				<?= files()->render('parts/_blog-sidebar.php') ?>
			</div>

		</div>

		<?= $pagination ?>

	</div>

</main>