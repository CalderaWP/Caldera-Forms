<style>
	.caldera-editor-header-nav {
		height: 48px;
	}

	li.caldera-forms-toolbar-item {
		padding: 0;
	}


	.panel-footer {
		position: absolute;
		bottom: 0px;
		padding: 6px;
		background: none repeat scroll 0 0 rgba(0, 0, 0, 0.03);
		left: 0px;
		right: 0px;
		border-top: 1px solid rgba(0, 0, 0, 0.06);
	}
	a.button {
		text-align: center;
		width: 100%;
		background-color: #ff7e30 !important;
		color: #fff !important;

	}
	a.button:hover {
		background-color: #a3bf61 !important;
		color: #fff !important;

	}

	.addon-panel {
		margin: 10px;
		width: 220px;
		float: left;
		height: 250px;
		position: relative;
		padding: 0;
		border: 1px solid #a3bf61;
		border-radius: 2px;
		background: #fff;
	}
</style>

<div class="caldera-editor-header">
	<ul class="caldera-editor-header-nav">
		<li class="caldera-editor-logo">
			<span class="caldera-forms-name">Caldera Forms</span>
		</li>
		<li class="caldera-forms-toolbar-item active">
			<a href="https://calderaforms.com/caldera-forms-add-ons?utm_source=dashboard&utm_medium=extend-submenu&utm_campaign=caldera-forms" title="<?php esc_attr_e( 'View Caldera Forms Add-ons', 'caldera-forms' ); ?>" target="_blank">
				<?php esc_html_e('Caldera Forms Add-ons', 'caldera-forms'); ?>
			</a>
		</li>
		<li class="caldera-forms-toolbar-item">
			<a href="https://calderaforms.com/caldera-forms-bundles?utm_source=dashboard&utm_medium=extend-submenu&utm_campaign=caldera-forms" title="<?php esc_attr_e( 'View Caldera Forms Bundles', 'caldera-forms' ); ?>" target="_blank">
				<?php esc_html_e('Save With Bundles', 'caldera-forms'); ?>
			</a>
		</li>
		<li class="caldera-forms-toolbar-item">
			<a href="https://caldera.space?utm_source=dashboard&utm_medium=extend-submenu&utm_campaign=caldera-forms" title="<?php esc_attr_e( 'Get Caldera Forms To PDF', 'caldera-forms' ); ?>" target="_blank">
				<?php esc_html_e('Form To PDF', 'caldera-forms'); ?>
			</a>
		</li>
	</ul>
</div>

<div class="form-extend-page-wrap" id="form-extend-viewer" style="visibility:visible;">
	<div id="cf-addons"></div>
</div>

<script type="text/javascript">
	jQuery( document ).ready(function($){
		<?php
		$data = Caldera_Forms_Admin_Feed::get_cf_addons();
		$addons[ 'extensions' ] = $data;

		echo "var add_ons = " . json_encode($addons).";";
		?>
		var source   = $('#tmpl-addons').html();

		var template = Handlebars.compile(source);
		var html    = template(add_ons);
		$( '#cf-addons' ).html( html );
	});




</script>
<!-- Template for Addons-->
<script type="text/html" id="tmpl-addons">

	{{#if extensions}}
		{{#each extensions}}
			<div class="addon-panel" >
			{{#if image_src}}
				<img src="{{image_src}}" style="width:100%;vertical-align: top;">
			{{/if}}
			{{#if name}}
				<h2>{{name}}</h2>
			{{/if}}
			{{#if tagline}}
				<div style="margin: 0px; padding: 6px 7px;">{{{tagline}}}</div>
			{{/if}}

			<div class="panel-footer">

				<a class="button" href="{{link}}" target="_blank" rel="nofollow">
					<?php esc_html_e( 'Learn More', 'caldera-forms' ); ?>
				</a>

			</div>

			</div>
		{{/each}}

	{{/if}}


</script>