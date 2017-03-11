<?php
$config_field = new Caldera_Forms_Admin_Field();
$config_field->set_from_array(
	array(
		'type' => 'hidden',
		'name' => 'build_formula',
		'label' => '  ',
		'args' => array(
			'value' => '{{#if config/group}}{{json config}}{{else}}{{config}}{{/if}}',
			'attrs' => array( 'class' => 'build_formula' ),
			'block' => false,
			'magic' => false,
		)
	)
);

$fields = array(
	Caldera_Forms_Admin_UI::text_field(
		'element',
		__('Element', 'caldera-forms' )
	),
	Caldera_Forms_Admin_UI::text_field(
		'classes',
		__( 'Classes', 'caldera-forms' )
	),
	Caldera_Forms_Admin_UI::text_field(
		'before',
		__( 'Before', 'caldera-forms' )
	),
	Caldera_Forms_Admin_UI::text_field(
		'after',
		__( 'After', 'caldera-forms' )
	),
	Caldera_Forms_Admin_UI::checkbox_field(
		'fixed',
		__( 'Format', 'caldera-forms' ),
		array(
			'fixed' => __( 'Money Format', 'caldera-forms' )
		)
	),
	Caldera_Forms_Admin_UI::text_field(
		'sep',
		__( 'Thousands Separator', 'caldera-forms' )
	),
	Caldera_Forms_Admin_UI::text_field(
		'sep_decimal',
		__( 'Decimal Separator', 'caldera-forms' )
	),
	Caldera_Forms_Admin_UI::checkbox_field(
		'manual',
		__( 'Manual Format', 'caldera-forms' ),
		array(
			'fixed' => __( 'Enable', 'caldera-forms' )
		)
	),
	Caldera_Forms_Admin_UI::hidden_field(
		'formular',
		array(
			'class' => 'calculation-formular',
		)
	),
);

echo Caldera_Forms_Admin_UI::fields( $fields, 'calculation' );

?>

<div id="{{_id}}_autobox">
	<div class="caldera-config-group caldera-config-group-full">

	</div>
	<br>
	<div id="{{_id}}_operator_groups" class="calculation-groups-wrap"></div>
	<input type="hidden" class="block-input field-config calculation-formular" name="{{_name}}[formular]" id="{{_id}}_formular" value="{{formular}}">

</div>
<div id="{{_id}}_manualbox" style="display:none;">
	<textarea name="{{_name}}[manual_formula]" class="field-config block-input">{{manual_formula}}</textarea>
	<p class="description"><?php echo __('Use %field_slug% as field value variables', 'caldera-forms'); ?></p>
</div>


<input type="hidden" class="block-input field-config build_formula" name="{{_name}}[config]" id="{{_id}}_config" value="{{#if config/group}}{{json config}}{{else}}{{config}}{{/if}}">




