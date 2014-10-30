var resBaldrickTriggers;

jQuery(function($){

	// admin stuff!
	// Baldrick Bindings
	resBaldrickTriggers = function(){
		$('.cfajax-trigger').baldrick({
			request			:	'./',
			method			:	'POST',
			before			: function(el){
				
				var buttons = $(el).find(':submit');
					buttons.prop('disabled',true);

			},
			callback		: function(obj){
                if ( 1 == $(obj.params.trigger ).attr( 'data-post-disable')){
                    obj.params.trigger.find(':submit').prop('disabled',false);
                }
				
				obj.params.trigger.find(':submit').prop('disabled',false);
				
				var instance = obj.params.trigger.data('instance');

				$('.caldera_ajax_error_wrap').removeClass('caldera_ajax_error_wrap').removeClass('has-error');
				$('.caldera_ajax_error_block').remove();

				if(obj.data.status === 'complete' || obj.data.type === 'success'){
					if(obj.data.html){
						obj.params.target.html(obj.data.html);
					}
					if(!obj.data.entry){
						obj.params.trigger[0].reset();
					}
					if(obj.params.trigger.data('hiderows')){
						obj.params.trigger.find('div.row').remove();
					}
				}else if(obj.data.status === 'preprocess'){
					obj.params.target.html(obj.data.html);
				}else if(obj.data.status === 'error'){
					obj.params.target.html(obj.data.html);
				}
				// do a redirect if set
				if(obj.data.url){
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
				// was modal?
				//setTimeout(function(){
				//	obj.params.target.closest('.caldera-front-modal-container').hide();
				//}, 1000);
			}
		});
	};

	resBaldrickTriggers();
});
