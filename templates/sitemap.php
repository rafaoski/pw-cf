<?php namespace ProcessWire; ?>

<div id='body'>

	<div class='container text-component text-sm'>
		<?php
			$maxDepth = 4;
			renderNavTree($pages->get('/'), $maxDepth);
		?>
	</div>

</div>
