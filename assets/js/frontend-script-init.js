var cf_jsfields_init, cf_validate_form;
(function($){

	// validation
	cf_validate_form = function( form ){
		return form.parsley({
			errorsWrapper : '<span class="help-block caldera_ajax_error_block"></span>',
			errorTemplate : '<span></span>',
			errorsContainer : function( field ){
				return field.$element.closest('.form-group');
			}					
		}).on('field:error', function() {
			this.$element.closest('.form-group').addClass('has-error');
		}).on('field:success', function() {
			this.$element.closest('.form-group').removeClass('has-error');
		});
	}

	// init sync
	$('[data-sync]').each( function(){
		var field = $( this ),
			binds = field.data('binds'),
			instance = field.closest('form');

		for( var i = 0; i < binds.length; i++ ){
			$( document ).on('keyup change blur mouseover', "[data-field='" + binds[ i ] + "']", function(){
				var str = field.data('sync')
					id = $(this).data('field'),
					reg = new RegExp( "\{\{([^\}]*?)\}\}", "g" ),
					template = str.match( reg );
					if( field.data( 'unsync' ) ){
						return;
					}
					for( var t = 0; t < template.length; t++ ){
						var select = template[ t ].replace(/\}/g,'').replace(/\{/g,'');
						var re = new RegExp( template[ t ] ,"g");
						var sync = instance.find( "[data-field='" + select + "']" );
						var val = '';
						for( var i =0; i < sync.length; i++ ){
							var this_field = $( sync[i] );
							if( ( this_field.is(':radio') || this_field.is(':checkbox') ) && ! this_field.is(':checked') ){
								// skip.
							}else{
								val += this_field.val();
							}

						}
						str = str.replace( re , val );
					}
					field.val( str );
			} );
			$("[data-field='" + binds[ i ] + "']").trigger('change');

		}
	});
	$( document ).on('change keypress', "[data-sync]", function(){
		$(this).data( 'unsync', true );
	});

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
		if( typeof resBaldrickTriggers === 'undefined' && $('.caldera_forms_form').length ){

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
			instance = form.data('instance'),
			current_page = form.find('.caldera-form-page:visible').data('formpage'),
			page	 = page_box.data('formpage') ? page_box.data('formpage') : clicked.data('page') ,
			breadcrumb = $('.breadcrumb[data-form="caldera_form_' + instance + '"]'),
			next,
			prev,
			fields,
			run = true,
			checks = {};
		if( !form.length ){
			return;
		}
		
		cf_validate_form( form ).destroy();

		fields = form.find('[data-field]');		
		form.find('.has-error').removeClass('has-error');

		
		if( clicked.data('page') !== 'prev' && page >= current_page ){

			for(var f = 0; f < fields.length; f++){
				var this_field = $(fields[f]);
				if( this_field.is(':radio,:checkbox') ){
					if( !this_field.hasClass('option-required') || false === this_field.is(':visible') ){continue}
					if( !checks[this_field.data('field')] ){
						checks[this_field.data('field')] = [];
					}
					checks[this_field.data('field')].push(this_field.prop('checked'));
				}else{
					if( this_field.prop('required') && false === this_field.is(':visible') ){ continue }
					if( this_field.prop('required') ){

						if( true !== this_field.parsley().isValid() ){
							// ye nope!
							if( this_field.is(":visible") ){
								// on this page.
								this_field.parsley().validate();
								e.preventDefault();
								//return;
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
		}

		
		for( var ch in checks ){
			if( checks[ch].indexOf(true) < 0){
				$('[for="' + ch + '_' + instance + '"]').parent().addClass('has-error');
				run = false;				
			}else{
				$('[for="' + ch + '_' + instance + '"]').parent().removeClass('has-error');
			}
		}
		
		if( false === run ){
			cf_validate_form( form ).validate();
			return false;
		}
		
		if(clicked.data('page') === 'next'){
			
			if(breadcrumb){
				breadcrumb.find('li.active').removeClass('active').children().attr('aria-expanded', 'false');
			}
			next = form.find('.caldera-form-page[data-formpage="'+ ( page + 1 ) +'"]');
			if(next.length){
				page_box.hide().attr( 'aria-hidden', 'true' ).css( 'visibility', 'hidden' );
				next.show().attr( 'aria-hidden', 'false' ).css( 'visibility', 'visible' );
				if(breadcrumb){
					breadcrumb.find('a[data-page="'+ ( page + 1 ) +'"]').attr('aria-expanded', 'true').parent().addClass('active');
				}
			}
		}else if(clicked.data('page') === 'prev'){
			if(breadcrumb){
				breadcrumb.find('li.active').removeClass('active').children().attr('aria-expanded', 'false');
			}
			prev = form.find('.caldera-form-page[data-formpage="'+ ( page - 1 ) +'"]');
			if(prev.length){
				page_box.hide().attr( 'aria-hidden', 'true' ).css( 'visibility', 'hidden' );
				prev.show().attr( 'aria-hidden', 'false' ).css( 'visibility', 'visible' );
				if(breadcrumb){
					breadcrumb.find('a[data-page="'+ ( page - 1 ) +'"]').attr('aria-expanded', 'true').parent().addClass('active');
				}
			}
		}else{
			if(clicked.data('pagenav')){
				e.preventDefault();
				clicked.closest('.breadcrumb').find('li.active').removeClass('active').children().attr('aria-expanded', 'false');
				$('#' + clicked.data('pagenav') + ' .caldera-form-page').hide().attr( 'aria-hidden', 'true' ).css( 'visibility', 'hidden' );
				$('#' + clicked.data('pagenav') + '	.caldera-form-page[data-formpage="'+ ( clicked.data('page') ) +'"]').show().attr( 'aria-hidden', 'false' ).css( 'visibility', 'visible' );
				clicked.parent().addClass('active').children().attr('aria-expanded', 'true');
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

	// validator
	$( document ).on('click', 'form.caldera_forms_form [type="submit"]', function( e ){
		var clicked = $( this ),
			form = clicked.closest('.caldera_forms_form'),
			validator = cf_validate_form( form );

		if( !validator.validate() ){
			e.preventDefault();
		}else{
			validator.destroy();
		}
	});

	

})(jQuery);