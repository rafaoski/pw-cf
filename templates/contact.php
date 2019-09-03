<?php namespace ProcessWire;

// Reset variables
$warning = '';

// Get e-mail adress
$eMail = sanitizer()->email( setting('e-mail') );

// Privacy Page
$privacyPage = pages()->get("template=privacy-policy");

// Check E-mail
if( $eMail == '' ) {

// Reset Form
	$form = '';

// Warning Text
	$warning =  '<h3>' . setting('email-warning') . '</h3>';

} else {

// Render form
	$form = files()->render('parts/_basic-form',
			[
				'eMail' => $eMail,
				'privacyPage' => $privacyPage
			]);
}
?>

<head id='html-head' pw-append>
	<style>
		/* HONEYPOT */
		.hide-robot{
			display: none;
		}
	</style>
</head>

<div id="body">

	<div class='grid grid-gap-md'>
		<?php if (!$session->contactFormMessage ): ?>
			<div class='<?= $form ? 'col-5@md' : 'col-12' ?>'>

				<h3><?= $warning ?></h3>

				<?= page()->body ?>

			</div>

			<?php if ($form): ?>
			<div id='content-form' class='content-form col-7@md'>
				<?= $form ?>
			</div>
			<?php endif ?>

		<?php else: ?>
		<div class='success-message'>
			<?php
				// Session contactFormMessage
				echo $session->contactFormMessage;
			?>
		</div>
		<?php endif ?>
	</div>

</div>

<?php
// Remove contactFormMessage
$session->remove('contactFormMessage');
