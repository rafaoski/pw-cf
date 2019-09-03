<?php namespace ProcessWire;

// reset variables
$readMore = '';

// text read more
$textMore = setting('read-more');

// user nick name
$nick_name = $item->createdUser->nick_name ?: __('Please update "Nick Name" in your user profile');

// if single blog post
if ($item->id == page('id')) {

// Blog Title
	$title = '';

// page body
	$body = $item->body . "\n";

} else {

// blog title
	$title = "<h3><a title='$item->title' href='$item->url'>$item->title</a></h3>";

// page body
	$body = '<p>' . $item->intro . '</p>';

// read more button
	$readMore = "<a class='btn btn--primary' href='$item->url' title='$textMore'>$textMore</a>";
}
?>

<article class='blog-article <?= 'article_' . $item->id ?> margin-y-md'>
	<header class='entry-header text-sm'>
		<?= $title ?>
		<p>
			<?= icon('calendar') . ' ' . $item->date ?>
			<?= icon('user') . ' ' .  $nick_name ?: setting('update-profile') ?>
			<?= countComments($item) ?>
		</p>

		<?php if ( $item->images && count($item->images) && page('template') != 'blog-post' ): ?>
			<picture>
				<source srcset="<?=$item->images->first->webp()->url?>" type="image/webp">
				<img src="<?= page()->images->first->url ?>" class='parallax-page'
					 style='max-height: 220px' alt="<?=$item->images->first->description?>">
			</picture>
		<?php endif; ?>
	</header>

	<div class='entry-content margin-y-xs'>
		<?= $body ?>
	</div>

	<footer class='entry-footer'>
		<?php if ( $item->categories && count($item->categories) ): ?>
			<p class='text-sm'><?= icon('hashtag') ?>
				<?= $item->categories->each("<a href='{url}'>{title}</a> ") ?></p>
		<?php endif; ?>
	</footer>

	<?= $readMore ?>
</article>
