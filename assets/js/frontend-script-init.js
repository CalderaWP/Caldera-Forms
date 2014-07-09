(function($){
	$('document').ready(function(){
		// check for init function
		$('.init_field_type[data-type]').each(function(k,v){
			var ftype = $(v);
			if( typeof window[ftype.data('type') + '_init'] === 'function' ){
				window[ftype.data('type') + '_init'](ftype.prop('id'), ftype[0]);
			}
		});
	});

	// Page navigation
	$(document).on('click', '[data-page]', function(e){
		var clicked = $(this),
			page_box = clicked.closest('.caldera-form-page'),
			page	 = page_box.data('formpage'),
			next,
			prev;
		
		if(clicked.data('page') === 'next'){
			next = $('.caldera-form-page[data-formpage="'+ ( page + 1 ) +'"]');
			if(next.length){
				page_box.hide();
				next.show();
			}
		}else if(clicked.data('page') === 'prev'){
			prev = $('.caldera-form-page[data-formpage="'+ ( page - 1 ) +'"]');
			if(prev.length){
				page_box.hide();
				prev.show();
			}
		}

	})

})(jQuery);