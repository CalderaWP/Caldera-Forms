<?php
$args = array(
	'type' => 'text',
	'args' => array(
		'block' => true,
		'magic' => true,
		'classes' => 'color-field'
	)
);
$color_fields = array();
$color_field_settings = array(
	array(
		'name' => 'trackcolor',
		'label' => __( 'Track', 'caldera-forms' ),
	),
	array(
		'name' => 'color',
		'label' => __( 'Highlight', 'caldera-forms' ),
	),

	array(
		'name' => 'handle',
		'label' => __( 'Handle', 'caldera-forms' ),
	),
	array(
		'name' => 'handleborder',
		'label' =>  __( 'Border', 'caldera-forms' )
	)

);

foreach ( $color_field_settings as $settings ){
	$obj = new Caldera_Forms_Admin_Field();
	$obj->set_from_array(
		wp_parse_args( $settings, $args )
	);
	$color_fields[] = $obj;
}


$fields = array_merge( array(
	'default',
), $color_fields );

$fields = array_merge( $fields, array(
		Caldera_Forms_Admin_UI::number_field(
			'step',
			__( 'Steps', 'caldera-forms' ),
			'',
			array(
				'min' => '0',
				'style' => 'width:70px;'
			)
		),
		Caldera_Forms_Admin_UI::number_field(
			'min',
			__( 'Minimum', 'caldera-forms' ),
			'',
			array(
				'style' => 'width:70px;'
			)
		),
		Caldera_Forms_Admin_UI::number_field(
			'max',
			__( 'Maximum', 'caldera-forms' ),
			'',
			array(
				'style' => 'width:70px;'
			)
		),
		Caldera_Forms_Admin_UI::checkbox_field(
			'showval',
			__( 'Show Value?', 'caldera-forms' ),
			array(
				'showval' => __( 'Enable', 'caldera-forms' ),
			)
		),
		Caldera_Forms_Admin_UI::text_field(
			'prefix',
			__( 'Prefix', 'caldera-forms' )
		),
		Caldera_Forms_Admin_UI::text_field(
			'suffix',
			__( 'Suffix', 'caldera-forms' )
		)
	)

);

echo Caldera_Forms_Admin_UI::fields( $fields, 'range_slider' );

?>

{{#script}}
jQuery(function($){
	//jQuery('#{{_id}}_trackcolor').miniColors();
	//jQuery('#{{_id}}_color').miniColors();
	//jQuery('#{{_id}}_handle').miniColors();
	//jQuery('#{{_id}}_handleborder').miniColors();

	/*jQuery('#{{_id}}_pollyfill_check').on('change', function(){

		var clicked = jQuery(this);
		if(clicked.prop('checked')){
			jQuery('#{{_id}}_style').hide();
		}else{
			jQuery('#{{_id}}_style').show();
		}
	});
	jQuery('#{{_id}}_pollyfill_check').trigger('change');*/
});
{{/script}}




