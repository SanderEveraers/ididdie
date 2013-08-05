<?php

/*
 *	Display a markdown generated file
 */
?>

<div class='container'>
	<div class='row'>
		<div class='span12'>
		<?php echo $this->Markdown->transform($textInMarkdownFormat); ?>
		</div>
	</div>
</div>
