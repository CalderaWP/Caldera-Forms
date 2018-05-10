
var cf_jsfields_init, cf_presubmit;
(function($){

	// validation
	cf_validate_form = function( form ){
		return form.parsley({
			errorsWrapper : '<span class="help-block caldera_ajax_error_block"></span>',
			errorTemplate : '<span></span>',
			errorsContainer : function( field ){
				return field.$element.closest('.form-group');
			}
		}).on('field:error', function( fieldInstance ) {

            this.$element.closest('.form-group').addClass('has-error');
			$( document ).trigger( 'cf.validate.fieldError', {
				inst: fieldInstance,
				form: form,
				el: this.$element
			} );
        }).on('field:success', function( fieldInstance ) {
        	if( 'star' === this.$element.data( 'type' ) && this.$element.prop('required') && 0 == this.$element.val() ){
				fieldInstance.validationResult = false;
				return;
			}
			this.$element.closest('.form-group').removeClass('has-error');
			$( document ).trigger( 'cf.validate.fieldSuccess', {
				inst: fieldInstance,
				form: form,
				el: this.$element
			} );
		}).on('form:success', function ( formInstance ) {
			$( document ).trigger( 'cf.validate.FormSuccess', {
				inst: formInstance,
				form: form,
				el: this.$element
			} );
		}).on( 'form:error', function ( formInstance ) {
			$( document ).trigger( 'cf.validate.FormError', {
				inst: formInstance,
				form: form,
				el: this.$element
			} );
		})
	};

	// make init function
	cf_jsfields_init = function(){
		$('.init_field_type[data-type]').each(function(k,v){
			var ftype = $(v);
			if( typeof window[ftype.data('type') + '_init'] === 'function' ){
				window[ftype.data('type') + '_init'](ftype.prop('id'), ftype[0]);
			}
		});

		window.Parsley.on('field:validated', function() {
			setTimeout( function(){$(document).trigger('cf.error');}, 15 );
		});
		if( typeof resBaldrickTriggers === 'undefined' && $('.caldera_forms_form').length ){

		}

		$( document ).trigger( 'cf.fieldsInit' );

		function setLocale( locale ){
			if ('undefined' != typeof window.Parsley._validatorRegistry.catalog[locale] ){
				window.Parsley.setLocale( locale );
			}

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
			form_id = form.attr( 'id' ),
			instance = form.data('instance'),
			current_page = form.find('.caldera-form-page:visible').data('formpage'),
			page	 = page_box.data('formpage') ? page_box.data('formpage') : clicked.data('page') ,
			breadcrumb = $('.breadcrumb[data-form="caldera_form_' + instance + '"]'),
			next,
			prev,
			fields,
			run = true,
			focusPage = current_page;

		if( !form.length ){
			return;
		}

		cf_validate_form( form ).destroy();

		fields = form.find('[data-field]');
		form.find('.has-error').removeClass('has-error');

		if( clicked.data('page') !== 'prev' && page >= current_page ){
			fields =  $('#caldera_form_' + instance + ' [data-formpage="' + current_page + '"] [data-field]'  );

			var $this_field,
				valid;
			for (var f = 0; f < fields.length; f++) {
				$this_field = $(fields[f]);
				if( $this_field.hasClass( 'cf-multi-uploader' ) || $this_field.hasClass( 'cf-multi-uploader-list') ){
					continue;
				}

				valid = $this_field.parsley().isValid();
				if (true === valid) {
					continue;
				}

				e.preventDefault();
				run = false;

			}

			if( true === run && page > current_page ){
				for( var i = page - 1; i >= 1; i -- ){
					fields =  $('#caldera_form_' + instance + ' [data-formpage="' + i + '"] [data-field]'  );

					for (var f = 0; f < fields.length; f++) {
						$this_field = $(fields[f]);
						$this_field.parsley().validate();
						valid = $this_field.parsley().isValid({force: true});
						if (true === valid) {
							continue;
						}

						e.preventDefault();
						run = false;
						if( i > focusPage ){
							focusPage = i;
						}

					}
				}

			}


		}




		if( false === run ){
			if( focusPage !== current_page ){
				$( '#form_page_' + instance + '_pg_' + current_page ).hide().attr( 'aria-hidden', 'true' ).css( 'visibility', 'hidden' );
				$( '#form_page_' + instance + '_pg_' + focusPage ).show().attr( 'aria-hidden', 'false' ).css( 'visibility', 'visible' );
			}
			cf_validate_form( form ).validate();
			return false;
		}

		if( clicked.data('page') === 'next'){

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

	});

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
		var $clicked = $( this ),
			$form = $clicked.closest('.caldera_forms_form'),
			validator = cf_validate_form( $form );
		$( document ).trigger( 'cf.form.submit', {
			e:e,
			$form:$form
		} );



		if( ! validator.validate() ){
			if( $('.caldera-form-page').length ) {
				var currentPage = $clicked.parents('.caldera-form-page').data('formpage');

				var invalids = [],
					future = [];
				validator.fields.forEach(function (field, i) {
					if( true === field.validationResult ){
						return;
					}
					var $pageParent = field.$element.parents('.caldera-form-page');
					if (undefined != $pageParent && $pageParent.length && field.$element.parents('.caldera-form-page').data('formpage') > currentPage) {
						future.push(field.$element.data(  'field' ) );
						return;
					}

					invalids.push( field );
				});
				if( ! invalids.length ){
					if( future.length ){
						$form.append( '<input type="hidden" name="_cf_future" value="' + future.toString() + '">' );

					}


					validator.destroy();
					return;

				}

			}

			e.preventDefault();
		}else{
			$( document ).trigger( 'cf.form.validated', {
				e:e,
				$form:$form
			} );
			validator.destroy();
		}
	});

})(jQuery);

/** Setup Form Front-end **/
window.addEventListener("load", function(){
	(function( $ ) {
		'use strict';

		window.CALDERA_FORMS = {};

		/** Setup forms */
		if( 'object' === typeof CFFIELD_CONFIG ) {
			var form_id, formId, config_object, config, instance, $el, state, protocolCheck, jQueryCheck, $form,
				jQueryChecked = false,
				protocolChecked = false;
			$('.caldera_forms_form').each(function (i, el) {
				$el = $(el);

				form_id = $el.attr('id');
				instance = $el.data('instance');

				if ('object' === typeof CFFIELD_CONFIG[instance] ) {
					$form = $( document.getElementById( form_id ));

					if ( ! protocolChecked ) {
						//check for protocol mis-match on submit url
						protocolCheck = new CalderaFormsCrossOriginWarning($el, $, CFFIELD_CONFIG[instance].error_strings);
						protocolCheck.maybeWarn();

						//don't check twice
						protocolChecked = true;
					}

					if ( ! jQueryChecked &&  CFFIELD_CONFIG[instance].error_strings.hasOwnProperty( 'jquery_old' ) ) {
						//check for old jQuery
						jQueryCheck = new CalderaFormsJQueryWarning($el, $, CFFIELD_CONFIG[instance].error_strings);
						jQueryCheck.maybeWarn();

						//don't check twice
						jQueryChecked = true;
					}

					formId = $el.data( 'form-id' );
					config = CFFIELD_CONFIG[instance].configs;

					var state = new CFState(formId, $ );
					state.init( CFFIELD_CONFIG[instance].fields.defaults,CFFIELD_CONFIG[instance].fields.calcDefaults );

					if( 'object' !== typeof window.cfstate ){
						window.cfstate = {};
					}

					window.cfstate[ form_id ] = state;

					$form.find( '[data-sync]' ).each( function(){
						var $field = $( this );
                        if ( ! $field.data( 'unsync' ) ) {
                            new CalderaFormsFieldSync($field, $field.data('binds'), $form, $, state);
                        }
					});


					config_object = new Caldera_Forms_Field_Config( config, $(document.getElementById(form_id)), $, state );
					config_object.init();
					$( document ).trigger( 'cf.form.init',{
						idAttr:  form_id,
						formId: formId,
						state: state,
						fieldIds: CFFIELD_CONFIG[instance].fields.hasOwnProperty( 'ids' ) ? CFFIELD_CONFIG[instance].fields.ids : []
					});


				}
			});

		}





	})( jQuery );


});


/**
 * Sets up field synce
 *
 * @since 1.5.0
 *
 * @param $field jQuery object for field
 * @param binds Field IDs to bind to
 * @param $form jQuery object for form
 * @param $ jQuery
 * @param {CFState} state
 * @constructor
 */
function CalderaFormsFieldSync( $field, binds, $form, $, state  ){
	for( var i = 0; i < binds.length; i++ ){

		$( document ).on('keyup change blur mouseover', "[data-field='" + binds[ i ] + "']", function(){
			if( ! $field.data('sync') ){
				return;
			}
			var str = $field.data('sync')
			id = $field.data('field'),
				reg = new RegExp( "\{\{([^\}]*?)\}\}", "g" ),
				template = str.match( reg );
			if( $field.data( 'unsync' ) || undefined == template || ! template.length ){
				return;
			}

			for( var t = 0; t < template.length; t++ ){
				var select = template[ t ].replace(/\}/g,'').replace(/\{/g,'');
				var re = new RegExp( template[ t ] ,"g");
				var sync = $form.find( "[data-field='" + select + "']" );
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
			state.mutateState( $field.attr( 'id' ), val );
			$field.val( str );
		} );
		$("[data-field='" + binds[ i ] + "']").trigger('change');
        $field.on('keyup change', function(){
        	$field.attr( 'data-unsync', '1' );
            $field.removeAttr( 'data-sync' );
            $field.removeAttr( 'data-binds' );
        });

	}
}

/**
 * Handles nonce refresh for forms
 *
 * @since 1.5.0
 *
 * @param formId ID of form
 * @param config API/nonce config (Probably the CF_API_DATA CDATA)
 * @param $ jQuery
 * @constructor
 */
function CalderaFormsResetNonce( formId, config, $ ){

	var $nonceField;

	/**
	 * Run system, replace nonce if needed
	 *
	 * @since 1.5.0
     */
	this.init = function(){
		$nonceField = $( '#' + config.nonce.field + '_' + formId );
		if( isNonceOld( $nonceField.data( 'nonce-time' ) ) ){
			replaceNonce();
		}
	};

	/**
	 * Check if nonce is more than an hour old
	 *
	 * If not, not worth the HTTP request
	 *
	 * @since 1.5.0
	 *
	 * @param time Time nonce was generated
	 * @returns {boolean}
     */
	function isNonceOld( time ){
		var now = new Date().getTime();
		if( now - 36000 > time ){
			return true;
		}
		return false;
	}

	/**
	 * Replace nonce via AJAX
	 *
	 * @since 1.5.0
     */
	function replaceNonce(){
		$.ajax({
			url:config.rest.tokens.nonce,
			method: 'POST',
			beforeSend: function ( xhr ) {
				xhr.setRequestHeader( 'X-WP-Nonce', config.rest.nonce );
			},data:{
				form_id: formId
			}
		}).done( function( r){
			$nonceField.val( r.nonce );
			$nonceField.data( 'nonce-time', new Date().getTime() );
		});
	}
}

/**
 * Check if URL is same protocol as same page
 *
 * @since 1.5.3
 *
 * @param url {String} Url to compare against
 *
 * @returns {boolean} True if same protocol, false if not
 */
function caldera_forms_check_protocol( url ){
	var pageProtocol = window.location.protocol;
	var parser = document.createElement('a');
	parser.href = url;
	return parser.protocol === pageProtocol;

}

/**
 * Add a warning about cross-origin requests
 *
 * @since 1.5.3
 *
 * @param $form {jQuery} Form element
 * @param $ {jQuery}
 * @param errorStrings {Object} Localized error strings for this form
 * @constructor
 */
function CalderaFormsCrossOriginWarning( $form, $, errorStrings ){

	/**
	 * Do the check and warn if needed
	 *
	 * @since 1.5.3
	 */
	this.maybeWarn = function () {
		if( $form.find( '[name="cfajax"]').length ){
			var url = $form.data( 'request' );
			if( ! caldera_forms_check_protocol( url ) ){
				showNotice();
			}

		}

	};

	/**
	 * Append notice
	 *
	 * @since 1.5.3
	 */
	function showNotice() {
		var $target = $( $form.data( 'target' ) );
		$target.append( '<div class="alert alert-warning">' + errorStrings.mixed_protocol + '</div>' );
	}
}

/**
 * Add a warning about bad jQuery versions
 *
 * @since 1.5.3
 *
 * @param $form {jQuery} Form element
 * @param $ {jQuery}
 * @param errorStrings {Object} Localized error strings for this form
 * @constructor
 */
function CalderaFormsJQueryWarning( $form, $, errorStrings ){

	/**
	 * Do the check and warn if needed
	 *
	 * @since 1.5.3
	 */
	this.maybeWarn = function () {
		var version =  $.fn.jquery;
		if(  'string' === typeof  version && '1.12.4' != version ) {
			if( isOld( version ) ){
				showNotice();
			}
		}

	};

	/**
	 * Append notice
	 *
	 * @since 1.5.3
	 */
	function showNotice() {
		var $target = $( $form.data( 'target' ) );
		$target.append( '<div class="alert alert-warning">' + errorStrings.jquery_old + '</div>' );
	}

	/**
	 * Check if version is older than 1.12.4
	 *
	 * @since 1.5.3
	 *
	 * @param version
	 * @returns {boolean}
	 */
	function isOld(version) {
		var split = version.split( '.' );
		if( 1 == split[0] ){
			if( 12 > split[2] ){
				return true;
			}

			if( 4 > split[2]){
				return true;
			}

		}

		return false;

	}
}