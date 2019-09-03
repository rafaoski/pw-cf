<?php namespace ProcessWire;

$recentPosts = pages()->get("template=blog")->children("limit=2");

if(!count($recentPosts)) return; ?>

<div id="body" data-pw-append>

	<h3><?= setting('recent-posts') ?></h3>

	<div class="flex flex-wrap flex-center">

	<?php foreach( $recentPosts as $item ) : ?>

		<div class="item col-6@md padding-sm">

			<a  href="<?= $item->url ?>">
				<h3 class='text-center text-md'><?= $item->title ?></h3>
				<?php if( $item->images && count($item->images) ) : ?>
					<picture>
						<source srcset="<?= $item->images->first->webp()->url ?>" type="image/webp">
						<img src="<?= $item->images->first->url ?>"  class='parallax' style='max-height: 300px'
							alt="<?= $item->images->first->description ?>">
					</picture>
				<?php endif; ?>
			</a>

		</div>

	<?php endforeach; ?>

	</div>

</div>
