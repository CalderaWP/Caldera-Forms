jQuery(function($){
	$('body').on('click', '.cf-toggle-group-buttons button', function(){

		var clicked = $(this),
			parent = clicked.closest('.caldera-config-field'),
			input = parent.find('[data-ref="'+clicked.attr('id')+'"]');


		parent.find('.btn').removeClass('btn-primary').addClass('btn-default');
		clicked.addClass('btn-primary').removeClass('btn-default');
		input.prop('checked', true).trigger('change');
	});
});

function toggle_button_init(id, el){	

	var field 		= jQuery(el),
		checked		= field.find('.cf-toggle-group-radio:checked');

	if(checked.length){
		jQuery('#' + checked.data('ref') ).trigger('click');
	}
	
}