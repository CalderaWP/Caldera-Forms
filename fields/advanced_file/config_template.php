<?php
$fields = array(
	Caldera_Forms_Admin_UI::checkbox_field(
		'attach',
		__('Attach to mailer', 'caldera-forms'),
		array( 'attach' => __('Enable', 'caldera-forms') )
	),
	Caldera_Forms_Admin_UI::checkbox_field(
		'allow_multiple',
		__('Allow Multiple Files', 'caldera-forms'),
		array( 'allow_multiple' => __('Enable', 'caldera-forms') )
	),
	Caldera_Forms_Admin_UI::checkbox_field(
		'media_lib',
		__('Add to Media Library', 'caldera-forms' ),
		array( 'allow_multiple' => __('Enable', 'caldera-forms') )
	),
	Caldera_Forms_Admin_UI::text_field(
		'allow_multiple_text',
		__('Add to Media Library', 'caldera-forms' )
	),
	Caldera_Forms_Admin_UI::text_field(
		'allowed',
		__('Allowed Types', 'caldera-forms' ),
		__('Comma separated eg. jpg,pdf,txt. No periods. Must be allowed by WordPress/ server.', 'caldera-forms')
	),
);
echo Caldera_Forms_Admin_UI::fields( $fields, 'advanced_file' );

?>


{{#script}}
	jQuery(function($){

		$('#{{_id}}_allow_multiple').change(function(){

			if( $(this).prop('checked') ){
				$('#{{_id}}_allow_multiple_text_wrap').show();
			}else{
				$('#{{_id}}_allow_multiple_text_wrap').hide();
			}
		});

		$('#{{_id}}_allow_multiple').trigger('change');
	});
{{/script}}