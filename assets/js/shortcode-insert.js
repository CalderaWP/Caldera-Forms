jQuery(function($){

	$('.color-field').wpColorPicker({
		mode : 'rgba'
	});

	$('body').on('click', '#caldera-forms-form-insert,#wp_fs_caldera-forms', function(e){
		e.preventDefault();
		var modal = $('.caldera-forms-insert-modal')
	 		data = $(this).data('settings');

	 	if( data ){

	 		if( data.id ){
	 			$('.selected-form-shortcode[value="' + data.id + '"]').prop('checked', true );
	 		}
	 		if( data.modal ){
	 			$('.set_cf_modal').prop('checked', true );
	 			$('.modal-forms-setup').show();
	 		}
	 		if( data.type ){
	 			$('.modal_trigger_type').val(data.type);
	 		}
	 		if( data.content ){
	 			$('.modal_trigger').val(data.content);
	 		}
	 		if( data.width ){
	 			$('.modal_width').val(data.width);
	 		}
	 		$(this).data('settings', {} );
	 	}


		modal.fadeIn(100);

	});

	$('body').on('click', '.caldera-modal-closer', function(e){
		e.preventDefault();
		var modal = $('.caldera-forms-insert-modal');
		$('#calderaf_forms_shortcode_modal')[0].reset();
		$('.modal-forms-setup').hide();
		modal.fadeOut(100);	

	});
	$('body').on('change', '.set_cf_modal', function(e){
		var clicked = $(this);

		if( clicked.is(':checked') ){
			$('.modal-forms-setup').show();
		}else{
			$('.modal-forms-setup').hide();			
		}
	});
	$('body').on('click', '.caldera-form-shortcode-insert', function(e){
	 	
	 	e.preventDefault();
	 	var form = $('.selected-form-shortcode:checked'),
	 		is_modal = $('.set_cf_modal').prop('checked'),
	 		modal_trigger = $('.modal_trigger').val(),
	 		modal_trigger_type = $('.modal_trigger_type').val(),
	 		width = $('.modal_width').val(),
	 		code;

	 	if(!form.length){
	 		return;
	 	}

	 	var tag = 'caldera_form';
	 	if( is_modal ){
	 		tag = 'caldera_form_modal';
	 	}

	 	code = '[' + tag + ' id="' + form.val() + '"';
	 	if( is_modal === true ){
	 		//code += ' modal="true"';
	 		if( modal_trigger_type === 'button' ){
	 			code += ' type="' + modal_trigger_type + '"';
	 		}
	 	}
		if( width.length ){
			code += ' width="' + width + '"';	
		}
	 	code += ']';

	 	if( is_modal ){
	 		if( modal_trigger.length ){
	 			code += modal_trigger;
	 		}else{
				code += form.parent().text();
	 		}
	 		code += '[/caldera_form_modal]';
	 	}
	 	$('#calderaf_forms_shortcode_modal')[0].reset();
	 	$('.modal-forms-setup').hide();
	 	form.prop('checked', false);	 	
		window.send_to_editor(code);
		$('.caldera-modal-closer').trigger('click');

	});
	
	if( typeof wp !== 'undefined' && typeof wp.media !== 'undefined' ){

		var media = wp.media;
		if( typeof wp.mce.views.register === "function"){
			wp.mce.views.register( 'caldera_form', {
				template: media.template( 'editor-caldera-forms' ),
				initialize: function() {					
					this.fetch();
				},
				setLoader: function() {
					this.setContent(
						'<div class="loading-placeholder">' +
							'<div class="dashicons dashicons-update" style="color:#a3be5f;"></div>' +
							'<div class="wpview-loading"><ins style="background-color:#a3be5f;"></ins></div>' +
						'</div>'
					);
				},
				fetch: function() {
					var self = this,
						data = {
						post_id: $('#post_ID').val(),
						content : self.shortcode.content,
						atts: self.shortcode.attrs
					};

					wp.ajax.post( 'cf_get_form_preview', data )
					.done( function( response ) {
						self.render( response.html );
					} )
					.fail( function( response ) {
						self.render( response.html );
					} );
				},
				edit: function( node ) {
                    var values = this.shortcode.attrs.named;
                    	values.content = this.shortcode.content;

					jQuery('#caldera-forms-form-insert').data('settings', values ).trigger('click');
				}
			} );
		}
	}

});//
