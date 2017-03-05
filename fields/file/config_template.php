<?php
echo Caldera_Forms_Admin_UI::fields( Caldera_Forms_Admin_UI::file_field_ui(), 'file' );

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