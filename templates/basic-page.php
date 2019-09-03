<?php namespace ProcessWire; ?>

<?php if (page()->hasChildren()): ?>

<div id="body" data-pw-append>

	<div class="flex flex-wrap flex-center text-component">

	<?php foreach( page()->children() as $item ) :  ?>

		<div class="item col-4@lg padding-sm">

			<h3><?= $item->title ?></h3>

			<p><?= $item->intro ?></p>

			<a class='btn btn--primary' href="<?= $item->url ?>"><?= __('Read more') ?></a>

		</div>

	<?php endforeach; ?>

	</div>

</div>

<?php endif;

