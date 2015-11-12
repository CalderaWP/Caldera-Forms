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

		if( typeof cfValidatorLocal !== 'undefined' ){
			window.Parsley.setLocale( cfValidatorLocal );
		}
		if( typeof cfModals !== 'undefined' && typeof cfModals.config !== 'undefined' && typeof cfModals.config.validator_lang !== 'undefined' ){
			window.Parsley.setLocale( cfModals.config.validator_lang );
		}
		window.Parsley.on('field:validated', function() {
			setTimeout( function(){$(document).trigger('cf.error');}, 15 );
		});
		if( typeof resBaldrickTriggers === 'undefined' ){
			$('.caldera_forms_form').parsley({
				errorsWrapper : '<span class="help-block caldera_ajax_error_block"></span>',
				errorTemplate : '<span></span>'
			});
		}

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
			form 	 = clicked.closest('form.caldera_forms_form'),
			page	 = page_box.data('formpage') ? page_box.data('formpage') : clicked.data('page') ,
			breadcrumb = $('.breadcrumb[data-form="' + form.prop('id') + '"]'),
			next,
			prev,
			fields,
			run = true,
			checks = {};
		if( !form.length ){
			return;
		}

		fields = form.find('[data-field]');
		
		form.find('.has-error[data-page]').removeClass('has-error');


		for(var f = 0; f < fields.length; f++){
			var this_field = $(fields[f]);
			if( this_field.is(':radio,:checkbox') ){
				if( !this_field.hasClass('option-required') || false === this_field.is(':visible') ){continue}
				if( !checks[this_field.data('field')] ){
					checks[this_field.data('field')] = [];
				}
				checks[this_field.data('field')].push(this_field.prop('checked'));
			}else{
				if(this_field.prop('required')){
					//console.log( this_field.is(":visible") );
					if( true !== this_field.parsley().validate() ){
						// ye nope!
						if( this_field.is(":visible") ){
							// on this page.
							e.preventDefault();
							return;
						}else{
							// not on this page
							//get page and highlight if lower than this one (aka backwards not forwards)
							var that_page = parseFloat( this_field.closest('.caldera-form-page[data-formpage]').data('formpage') );
							if(  that_page < parseFloat(page) ){
								form.find('[data-page="' + that_page + '"]').addClass('has-error');
							}
						}
						run = false;
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