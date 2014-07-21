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
			form 	 = clicked.closest('.caldera-grid'),
			page	 = page_box.data('formpage'),
			breadcrumb = $('.breadcrumb[data-form="' + form.prop('id') + '"]'),
			next,
			prev;
		
		if(clicked.data('page') === 'next'){
			if(breadcrumb){
				breadcrumb.find('li.active').removeClass('active');
			}
			next = $('.caldera-form-page[data-formpage="'+ ( page + 1 ) +'"]');
			if(next.length){
				page_box.hide();
				next.show();
				if(breadcrumb){
					breadcrumb.find('a[data-page="'+ ( page + 1 ) +'"]').parent().addClass('active');
				}
			}
		}else if(clicked.data('page') === 'prev'){
			if(breadcrumb){
				breadcrumb.find('li.active').removeClass('active');
			}
			prev = $('.caldera-form-page[data-formpage="'+ ( page - 1 ) +'"]');
			if(prev.length){
				page_box.hide();
				prev.show();
				if(breadcrumb){
					breadcrumb.find('a[data-page="'+ ( page - 1 ) +'"]').parent().addClass('active');
				}
			}
		}else{
			if(clicked.data('pagenav')){
				e.preventDefault();
				clicked.closest('.breadcrumb').find('li.active').removeClass('active');
				$('#' + clicked.data('pagenav') + ' .caldera-form-page').hide();
				$('.caldera-form-page[data-formpage="'+ ( clicked.data('page') ) +'"]').show();
				clicked.parent().addClass('active');
			}
			
		}

		$(document).trigger('cf.pagenav');

	})
	// init page errors
	var tab_navclick;
	$('.caldera-grid .breadcrumb').each(function(k,v){
		$(v).find('a[data-pagenav]').each(function(i,e){
			var tab		= $(e),
				form 	= tab.data('pagenav'),
				page	= $('.caldera-form-page[data-formpage="' + tab.data('page') + '"]');

			if(page.find('.has-error').length){
				tab.parent().addClass('error');
				if(typeof tab_navclick === 'undefined'){
					tab.trigger('click');
					tab_navclick = true;
				}

			}

		});
	});
	// trigger last page

	

})(jQuery);