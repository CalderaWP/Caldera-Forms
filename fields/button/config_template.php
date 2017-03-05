<?php
$fields = array(
	Caldera_Forms_Admin_UI::select_field(
		'buttontype',
		__( 'Type', 'caldera-forms' ),
		array(
			'submit'   => __( 'Submit', 'caldera-forms' ),
			'button'   => __( 'Button', 'caldera-forms' ),
			'next'     => __( 'Next Page', 'caldera-forms' ),
			'previous' => __( 'Previous Page', 'caldera-forms' ),
			'reset'    => __( 'Reset', 'caldera-forms' ),
		)
	),
	Caldera_Forms_Admin_UI::text_field(
		'target',
		__('Click Target', 'caldera-forms' )
	)
)
?>

{{#script}}
jQuery(function($){
	
	$('#buttontype_{{_id}}').on('change', function(){

		if( this.value === 'button' ){
			$('#event{{_id}}').show();
		}else{
			$('#event{{_id}}').hide();
		}

	});

});
{{/script}}