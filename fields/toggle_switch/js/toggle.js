jQuery(function($){
	$('body').on('click', '.cu-toggle-group-buttons button', function(){

		var clicked = $(this),
			parent = clicked.closest('.caldera-config-field'),
			input = parent.find('[data-ref="'+clicked.attr('id')+'"]');


		parent.find('.button').removeClass('button-primary');
		clicked.addClass('button-primary');
		input.prop('checked', true);
	});
});

function toggle_button_init(id, el){	

	var field 		= jQuery(el),
		checked		= field.find('.cu-toggle-group-radio:checked');

	if(checked.length){
		jQuery('#' + checked.data('ref') ).addClass('button-primary');
	}
	
}