<?php
/**
 * Support page -- debug view
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */

?>
<div id="caldera-config-group-short">
	<h3><?php esc_html_e( 'Short Debug Information', 'caldera-form' ); ?></h3>

	<?php echo Caldera_Forms_Support::short_debug_info(); ?>

</div>

<div id="caldera-config-group-full">
	<h3><?php esc_html_e( 'Full Debug Information', 'caldera-form' ); ?></h3>

	<?php echo Caldera_Forms_Support::debug_info(); ?>

</div>
