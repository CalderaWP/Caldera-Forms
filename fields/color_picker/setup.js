
jQuery(function($){

	$('body').on('change', '.minicolor-picker.field-config', function(){

		var parent = $(this).closest('.caldera-editor-field-config-wrapper'),
			preview = $('[data-for="' + parent.prop('id') + '"]').css('backgroundColor', this.value);


	})

});