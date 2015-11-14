jQuery(function($){


	$('body').on('click', '#caldera-forms-form-insert,#wp_fs_caldera-forms', function(e){
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
					var self = this;

					wp.ajax.post( 'cf_get_form_preview', {
						post_id: $('#post_ID').val(),
						atts: this.shortcode.attrs
					} )
					.done( function( response ) {
						self.render( response.html );
					} )
					.fail( function( response ) {
						self.render( response.html );
					} );
				},
				edit: function( node ) {
					jQuery('#caldera-forms-form-insert').trigger('click');
				}
			} );
		}
	}

});//
