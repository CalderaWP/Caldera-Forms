var resBaldrickTriggers;
 
jQuery(function($){

	// admin stuff!
	// Baldrick Bindings
	resBaldrickTriggers = function(){
		$('.cfajax-trigger').baldrick({
			request			:	'./',
			method			:	'POST',
			before			: function(el){
				
				var form	=	$(el),
					buttons = 	form.find(':submit');
				
				if( !form.data( 'postDisable' ) ){
					buttons.prop('disabled',true);
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
						var field = $('[data-field="' + i + '_' + instance + '"]'),
							wrap = field.closest('.form-group');

							wrap.addClass('has-error').addClass('caldera_ajax_error_wrap');
							wrap.append('<span class="help-block caldera_ajax_error_block">' + obj.data.fields[i] + '</span>');
					}
				}


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
