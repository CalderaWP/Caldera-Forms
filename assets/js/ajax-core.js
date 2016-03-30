var resBaldrickTriggers;
 
jQuery(function($){

	// admin stuff!
	// Baldrick Bindings
	resBaldrickTriggers = function(){
		$('.cfajax-trigger').baldrick({
			request			:	'./',
			method			:	'POST',
			before			: function(el, ev){

				var form	=	$(el),
					buttons = 	form.find(':submit');

				if( form.data('_cf_manual') ){
					form.find('[name="cfajax"]').remove();
					return false;
				}

				var validate = form.parsley({
					errorsWrapper : '<span class="help-block caldera_ajax_error_block"></span>',
					errorTemplate : '<span></span>'
				});
				if( !validate.isValid() ){
					return false;
				}

				if( !form.data( 'postDisable' ) ){
					buttons.prop('disabled',true);
				}

			},
			error : function( obj ){
				if( obj.jqxhr.status === 404){
					this.trigger.data('_cf_manual', true ).trigger('submit');
				}
			},
			callback		: function(obj){
				
				obj.params.trigger.find(':submit').prop('disabled',false);
				
				var instance = obj.params.trigger.data('instance');

				// run callback if set.
				if( obj.params.trigger.data('customCallback') && typeof window[obj.params.trigger.data('customCallback')] === 'function' ){
					
					window[obj.params.trigger.data('customCallback')](obj.data);
				
				}

				if( !obj.params.trigger.data('inhibitnotice') ){

					$('.caldera_ajax_error_wrap').removeClass('caldera_ajax_error_wrap').removeClass('has-error');
					$('.caldera_ajax_error_block').remove();

					if(obj.data.status === 'complete' || obj.data.type === 'success'){
						if(obj.data.html){
							obj.params.target.html(obj.data.html);
						}
						if(obj.params.trigger.data('hiderows')){
							obj.params.trigger.find('div.row').remove();
						}
					}else if(obj.data.status === 'preprocess'){
						obj.params.target.html(obj.data.html);
					}else if(obj.data.status === 'error'){
						obj.params.target.html(obj.data.html);
					}

				}
				// hit reset
				if( ( obj.data.status === 'complete' || obj.data.type === 'success' ) && !obj.data.entry ){
					obj.params.trigger[0].reset();
				}

				// do a redirect if set
				if(obj.data.url){
					obj.params.trigger.hide();
					window.location = obj.data.url;
				}
				
				if(obj.data.fields){

					for(var i in obj.data.fields){

						var field = obj.params.trigger.find('[data-field="' + i + '"]');

						//debug type
						//console.log(jQuery.type(obj.data.fields[i]));

						switch ( jQuery.type(obj.data.fields[i]) ) {

							// single field only (bottom of field)
							case 'string':

								wrap = field.parent();
								
								if( wrap.is('label') ) {
									wrap = wrap.parent();
									if( wrap.hasClass('checkbox') || wrap.hasClass('radio') ) {
										wrap = wrap.parent();
									}
								}

								var has_block = wrap.find('.help-block').not('.caldera_ajax_error_block');
		
								wrap.addClass('has-error').addClass('caldera_ajax_error_wrap');
								if( has_block.length ){
									has_block.hide();
								}
								wrap.append('<span class="help-block caldera_ajax_error_block">' + obj.data.fields[i] + '</span>');

								break;

								// field without options, next to each option
							case 'object':

								// debug option
								//obj.data.fields[i];
								//console.log(field);

								// loop through errors
								for(var j in obj.data.fields[i]) {
									
									if (!obj.data.fields[i].hasOwnProperty(j)) continue;

									//check all options for match 
									field.each( function() {
										if ( $(this).is('[id*="' + j + '"]') ) {

											wrap = $(this).parent();

											// not sure if this is correct for all occasions
											if( wrap.is('label') && (wrap.parent().hasClass('checkbox') || wrap.parent().hasClass('radio') ) ) {

												wrap = wrap.parent();
												var has_block = wrap.find('.help-block').not('.caldera_ajax_error_block');
		
												wrap.addClass('has-error').addClass('caldera_ajax_error_wrap');
												if( has_block.length ){
													has_block.hide();
												}
												wrap.append('<span class="help-block caldera_ajax_error_block">' + obj.data.fields[i][j] + '</span>');
											}

										}
									
									});
								
								}
								
								break;

						}
							
					}
				}
				// trigger global event
				$( document ).trigger( 'cf.submission' );
				$( document ).trigger( 'cf.' + obj.data.type );

				//custom_callback
				// was modal?
				//setTimeout(function(){
				//	obj.params.target.closest('.caldera-front-modal-container').hide();
				//}, 1000);
			}
		});
	};

	resBaldrickTriggers();
});
