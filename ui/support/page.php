<?php
/**
 * Support page -- main view
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */

?>
<div class="caldera-editor-header">
	<ul class="caldera-editor-header-nav">
		<li class="caldera-editor-logo">
			<span class="caldera-forms-name">
				<?php esc_html_e( 'Caldera Forms: Support', 'caldera-forms' ); ?>
			</span>

		</li>
		<li class="caldera-forms-toolbar-link" id="support-nav-info">
			<a href="#info">
				<?php esc_html_e( 'How To Get Support', 'caldera-forms' ); ?>
			</a>
		</li>
		<li class="caldera-forms-toolbar-link" id="support-nav-debug">
			<a href="#debug">
				<?php esc_html_e( 'Debug Information', 'caldera-forms' ); ?>
			</a>
		</li>
		<li class="caldera-forms-toolbar-link" id="support-nav-beta">
			<a href="#beta">
				<?php esc_html_e( 'Get Latest Beta', 'caldera-forms' ); ?>
			</a>
		</li>

	</ul>
</div>
<div class="support-admin-page-wrap" style="margin-top: 75px;">
	<div class="support-panel-wrap" id="panel-support-info" style="visibility: visible" aria-hidden="false">
		<?php include CFCORE_PATH  . 'ui/support/panels/support.php'; ?>
	</div>
	<div class="support-panel-wrap" id="panel-support-debug" style="visibility: hidden" aria-hidden="true">
		<?php include CFCORE_PATH  . 'ui/support/panels/debug.php'; ?>
	</div>
	<div class="support-panel-wrap" id="panel-support-beta" style="visibility: hidden" aria-hidden="true">
		<?php include CFCORE_PATH  . 'ui/support/panels/beta.php'; ?>
	</div>
</div>

