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
	/*
	if( typeof wp !== 'undefined' && typeof wp.media !== 'undefined' ){

		var media = wp.media;
		if( typeof wp.mce.views.register === "function"){
			wp.mce.views.register( 'caldera_form', {
				View: {
					template: media.template( 'editor-caldera-forms' ),

					initialize: function( options ) {
						this.shortcode = options.shortcode;
						this.fetch();
					},
					loadingPlaceholder: function() {
						return '' +
							'<div class="loading-placeholder">' +
								'<div class="dashicons dashicons-cf-logo"></div>' +
								'<div class="wpview-loading"><ins></ins></div>' +
							'</div>';
					},
					fetch: function() {
						var self = this;


						options = {};
						options.context = this;
						options.data = {
							action:  'cf_get_form_preview',
							post_id: $('#post_ID').val(),
							atts: this.shortcode.attrs
						};

						this.form = media.ajax( options );
						this.dfd = this.form.done( function(form) {
							this.form.data = form;
							self.render( true );
						} );
					},

					getHtml: function() {
						var attrs = this.shortcode.attrs.named,
							attachments = false,
							options;

						// Don't render errors while still fetching attachments
						if ( this.dfd && 'pending' === this.dfd.state() && ! this.form.length ) {
							return '';
						}

						return this.template( this.form.data );
					}
				},

				edit: function( node ) {
					jQuery('#caldera-forms-form-insert').trigger('click');
				}
			} );
		}
	}*/

});//
