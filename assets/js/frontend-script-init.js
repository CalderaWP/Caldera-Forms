var cf_jsfields_init;
(function($){

	// make init function
	cf_jsfields_init = function(){
		$('.init_field_type[data-type]').each(function(k,v){
			var ftype = $(v);
			if( typeof window[ftype.data('type') + '_init'] === 'function' ){
				window[ftype.data('type') + '_init'](ftype.prop('id'), ftype[0]);
			}
		});
	};	

	$('document').ready(function(){
		// check for init function		
		cf_jsfields_init();		
	});

	// if pages, disable enter
	if( $('.caldera-form-page').length ){
		$('.caldera-form-page').on('keypress', '[data-field]:not(textarea)', function( e ){
			if( e.keyCode === 13 ){
				e.preventDefault();
			}
		});
	}
	// modals activation
	$(document).on('click', '.cf_modal_button', function(e){
		e.preventDefault();
		var clicked = $(this);
		$(clicked.attr('href')).show();
	});
	$(document).on('click', '.caldera-front-modal-closer', function(e){
		e.preventDefault();
		var clicked = $(this);
			clicked.closest('.caldera-front-modal-container').hide();
	});
	// stuff trigger
	$(document).on('cf.add cf.enable cf.disable cf.pagenav', cf_jsfields_init );
	
	// Page navigation
	$(document).on('click', '[data-page]', function(e){

		var clicked = $(this),
			page_box = clicked.closest('.caldera-form-page'),
			form 	 = clicked.closest('.caldera-grid'),
			page	 = page_box.data('formpage'),
			breadcrumb = $('.breadcrumb[data-form="' + form.prop('id') + '"]'),
			next,
			prev,
			fields,
			checks = {};
		if( !form.length ){
			return;
		}

		// pre validate
		if(clicked.data('pagenav')){
			fields = $('#' + clicked.data('pagenav') + ' .caldera-form-page').find('[data-field]');
			form = clicked.closest('.caldera_forms_form');
		}else{
			fields = form.find('.caldera-form-page:visible').find('[data-field]');				
		}
		for(var f = 0; f < fields.length; f++){
			if( $(fields[f]).is(':radio,:checkbox') ){
				if( !$(fields[f]).hasClass('option-required') ){continue}
				if( !checks[$(fields[f]).data('field')] ){
					checks[$(fields[f]).data('field')] = [];
				}
				checks[$(fields[f]).data('field')].push($(fields[f]).prop('checked'));
			}else{
				if($(fields[f]).prop('required')){
					if(!fields[f].value.length){
						e.preventDefault();
						//nope submit form to indicate a fail.
						form.find('[type="submit"]').trigger('click');
						return;
					}
					if(fields[f].type === 'email'){
						if(fields[f].value.indexOf('@') < 0 || fields[f].value.length <= fields[f].value.indexOf('@') + 1){
							e.preventDefault();
							//nope submit form to indicate a fail.
							form.find('[type="submit"]').trigger('click');
							return;

						}
					}
				}
			}
		}

		for( var ch in checks ){
			if( checks[ch].indexOf(true) < 0){
				$('[for="' + ch + '"]').parent().addClass('has-error');
				return false;
			}else{
				$('[for="' + ch + '"]').parent().removeClass('has-error');
			}
		}
		
		
		if(clicked.data('page') === 'next'){
			if(breadcrumb){
				breadcrumb.find('li.active').removeClass('active');
			}
			next = form.find('.caldera-form-page[data-formpage="'+ ( page + 1 ) +'"]');
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
			prev = form.find('.caldera-form-page[data-formpage="'+ ( page - 1 ) +'"]');
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
				$('#' + clicked.data('pagenav') + '	.caldera-form-page[data-formpage="'+ ( clicked.data('page') ) +'"]').show();
				clicked.parent().addClass('active');
			}
			
		}
		$('html, body').animate({
			scrollTop: form.offset().top - 100
		}, 200);
		
		$(document).trigger('cf.pagenav');

	})
	// init page errors
	var tab_navclick;
	$('.caldera-grid .breadcrumb').each(function(k,v){
		$(v).find('a[data-pagenav]').each(function(i,e){
			var tab		= $(e),
				form 	= tab.data('pagenav'),
				page	= $('#'+ form +' .caldera-form-page[data-formpage="' + tab.data('page') + '"]');

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