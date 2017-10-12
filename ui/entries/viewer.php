<?php
if( ! defined( 'ABSPATH' ) ){
	exit;
}

if( ! isset( $with_toolbar ) ){
	$with_toolbar = true;
}
?>
<div class="form-entries-wrap" aria-live="polite" aria-relevant="additions removals">
	<?php
		if( $with_toolbar ) {
			include CFCORE_PATH . 'ui/entries/toolbar.php';
		}

	?>
	<div id="form-entries-viewer"></div>
	<?php include CFCORE_PATH . 'ui/entries/pagination.php'; ?>
</div>
