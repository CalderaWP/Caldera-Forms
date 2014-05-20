jQuery(function($){


	$('body').on('click', '#caldera-forms-form-insert', function(e){
		e.preventDefault();
		var modal = $('.caldera-forms-insert-modal');

		modal.fadeIn(100);

	});

	$('body').on('click', '.caldera-modal-closer', function(e){
		e.preventDefault();
		var modal = $('.caldera-forms-insert-modal');
		modal.fadeOut(100);		
	});

	$('body').on('click', '.caldera-form-shortcode-insert', function(e){
	 	
	 	e.preventDefault();
	 	var form = $('.selected-form-shortcode:checked'),code;

	 	if(!form.length){
	 		return;
	 		//code = '[cal'
	 	}


	 	code = '[caldera_form id="' + form.val() + '"]';
	 	form.prop('checked', false);	 	
		window.send_to_editor(code);
		$('.caldera-modal-closer').trigger('click');

	});

});//
