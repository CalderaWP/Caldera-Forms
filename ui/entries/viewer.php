<?php
if( ! defined( 'ABSPATH' ) ){
	exit;
}
?>
<div class="form-entries-wrap" aria-live="polite" aria-relevant="additions removals">
	<?php include CFCORE_PATH . 'ui/entries/toolbar.php'; ?>
	<div id="form-entries-viewer"></div>
	<?php include CFCORE_PATH . 'ui/entries/pagination.php'; ?>
</div>
