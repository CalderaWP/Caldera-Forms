<?php

$fields = array(
	Caldera_Forms_Admin_UI::select_field(
		'email',
		__( 'Email Field', 'caldera-forms' ),
		'{{{_field slug="email" type="email"}}}'
	),
	Caldera_Forms_Admin_UI::select_field(
		'fallback',
		__( 'Default Gravatar', 'caldera-forms' ),
		array(
			'mystery' => __('Mystery Man'),
			'blank' => __('Blank'),
			'gravatar_default' => __('Gravatar Logo'),
			'identicon' => __('Identicon (Generated)'),
			'wavatar' => __('Wavatar (Generated)'),
			'monsterid' => __('MonsterID (Generated)'),
			'retro' => __('Retro (Generated)')
		)

	)
);

?>

<div class="caldera-config-group">
	<label for="{{_id}}_size">
        <?php esc_html_e('Size', 'caldera-forms'); ?>
    </label>
	<div class="caldera-config-field">
		<input id="{{_id}}_size" type="number" class="field-config" name="{{_name}}[size]" value="{{#if size}}{{size}}{{else}}100{{/if}}" style="width:70px;"> px
	</div>
</div>

<div class="caldera-config-group">
	<label for="{{_id}}_border_color">
		<?php esc_html_e('Border Color'); ?>
	</label>
	<div class="caldera-config-field">
		<input type="text" class="color-field field-config" id="{{_id}}_border_color" name="{{_name}}[border_color]" value="{{#if config/border_color}}{{config/border_color}}{{else}}#efefef{{/if}}">
	</div>
</div>

<div class="caldera-config-group">
	<label for="{{_id}}_border_size">
		<?php esc_html_e('Border Size', 'caldera-forms'); ?>
	</label>
	<div class="caldera-config-field">
		<input type="number" class="field-config" name="{{_name}}[border_size]" id="{{_id}}_border_size" value="{{#if border_size}}{{border_size}}{{else}}3{{/if}}" style="width:70px;"> px
	</div>
</div>

<div class="caldera-config-group">
	<label for="{{_id}}_border_radius">
		<?php esc_html_e('Border Radius', 'caldera-forms'); ?>
	</label>
	<div class="caldera-config-field">
		<input type="number" class="field-config" id="{{_id}}_border_radius" name="{{_name}}[border_radius]" value="{{#if border_radius}}{{border_radius}}{{else}}3{{/if}}" style="width:70px;"> px
	</div>
</div>