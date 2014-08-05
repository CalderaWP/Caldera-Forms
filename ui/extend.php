<div class="caldera-editor-header">
	<ul class="caldera-editor-header-nav">
		<li class="caldera-editor-logo">
			<?php echo __('Caldera Forms', 'caldera-forms'); ?>
		</li>
		<li class="caldera-element-type-label">
			SOmething HERE
		</li>
		<li class="caldera-element-type-label">
			<?php echo __('Extensions & Addons', 'caldera-forms'); ?>
		</li>

	</ul>
</div>

<div class="caldera-editor-header caldera-editor-subnav">
	<ul class="caldera-editor-header-nav">
		<li id="tab_layout" data-load-class="spinner" data-group="main-nav" data-target="#form-extend-viewer" data-request="<?php echo CFCORE_EXTEND_URL . 'extensionsbeta/?version=' . CFCORE_VER; ?>" data-error="extend_fail_notice" data-template="#extensions-modal-tmpl" class="ajax-trigger" data-autoload="true"><a href="#layout-config-panel">Extensions</a></li>
		<li id="tab_layout"><a href="#layout-config-panel">Addons</a></li>
		<li id="tab_layout"><a href="#layout-config-panel">Processors</a></li>
	</ul>
</div>
<div class="form-extend-page-wrap" id="form-extend-viewer"></div>

<?php
	do_action('caldera_forms_admin_templates');
?>