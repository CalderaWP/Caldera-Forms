/* Baldrick handlebars.js templating plugin */
(function($){

	var wm_hasModal = false;
	
	$.fn.baldrick.registerhelper('wordpress_modal', {
		refresh: function(obj){
			if(obj.params.trigger.data('modalAutoclose')){
				$('#' + obj.params.trigger.data('modalAutoclose') + '_baldrickModalCloser').trigger('click');
			}
		},
		event : function(el){
			var triggers = $(el), modal_id = 'wm';
			if(triggers.data('modal') && wm_hasModal === false){
				if(triggers.data('modal') !== 'true'){
					modal_id = triggers.data('modal');
				}
				if(!$('#' + modal_id + '_baldrickModal').length){
					//wm_hasModal = true;
					// write out a template wrapper.
					var modal = $('<div>', {
							id          : modal_id + '_baldrickModal',
							tabIndex      : -1,
							"ariaLabelled-by" : modal_id + '_baldrickModalLable',
							"class"       : "caldera-modal-wrap",
						}),
					//modalDialog = $('<div>', {"class" : "modal-dialog"});
					modalBackdrop = $('<div>', {"class" : "caldera-backdrop"});
					modalContent = $('<div>', {"class" : "caldera-modal-body",id: modal_id + '_baldrickModalBody'});
					modalFooter = $('<div>', {"class" : "caldera-modal-footer",id: modal_id + '_baldrickModalFooter'});
					modalHeader = $('<div>', {"class" : "caldera-modal-title", id : modal_id + '_baldrickModalTitle'});
					modalCloser = $('<a>', { "href" : "#close", "class":"caldera-modal-closer", "data-dismiss":"modal", "aria-hidden":"true",id: modal_id + '_baldrickModalCloser'}).html('&times;');
					modalTitle = $('<h3>', {"class" : "modal-label", id : modal_id + '_baldrickModalLable'});

					modalHeader.append(modalCloser).append(modalTitle).appendTo(modal);

					if( ! trigger.data('static') ){
						
						modalBackdrop.on('dismiss', function(e){
							e.preventDefault();
							modalBackdrop.fadeOut(200);
							modal.fadeOut(200, function(){
								$(this).remove();
								modalBackdrop.remove();
							});
						})
					}
					modalCloser.on('click', function(e){
						e.preventDefault();
						modalBackdrop.fadeOut(200);
						modal.fadeOut(200, function(){
							$(this).remove();
							modalBackdrop.remove();
						});
					})

					modalContent.appendTo(modal);
					modalFooter.appendTo(modal);
					
					modal.appendTo($('body')).hide().fadeIn(200);
					modalBackdrop.insertBefore(modal).hide().fadeIn(200);
				}
			}
		},
		request_complete  : function(obj, params){
			if(obj.params.trigger.data('modal')){
				var modal_id = 'wm',loadClass = 'loading', modal, modalBody;
				if(obj.params.trigger.data('modal') !== 'true'){
					modal_id = obj.params.trigger.data('modal');
				}

				modal 			= $('#' + modal_id + '_baldrickModal');
				modalBody 	= $('#' + modal_id + '_baldrickModalBody');
				modalTitle 	= $('#' + modal_id + '_baldrickModalTitle');
				if(obj.params.trigger.data('loadClass')){
					loadClass = obj.params.trigger.data('loadClass');
				}

				if(obj.params.trigger.data('modalLife')){
					var delay = parseFloat(obj.params.trigger.data('modalLife'));
					if(delay > 0){
						setTimeout(function(){
							$('#' + modal_id + '_baldrickModalCloser').trigger('click');
						}, delay);
					}else{
						$('#' + modal_id + '_baldrickModalCloser').trigger('click');
					}
				}
				//$('#' + modal_id + '_baldrickModalLoader').hide();
				modalBody.removeClass(loadClass).show();
				


			}
		},
		after_filter  : function(obj){
			if(obj.params.trigger.data('modal')){
				if(obj.params.trigger.data('targetInsert')){
					var modal_id = 'wm';
					if(obj.params.trigger.data('modal') !== 'true'){
						modal_id = obj.params.trigger.data('modal');
					}
					var data = $(obj.data).prop('id', modal_id + '_baldrickModalBody');
					obj.data = data;
				}
			}
			return obj;
		},
		params  : function(params,defaults){

			var trigger = params.trigger, modal_id = 'wm', loadClass = 'loading';
			if(params.trigger.data('modal') !== 'true'){
				modal_id = params.trigger.data('modal');
			}
			if(params.trigger.data('loadClass')){
				loadClass = params.trigger.data('loadClass');
			}

			if(trigger.data('modal') && (params.url || trigger.data('modalContent'))){
				var modal;

				if(params.url){
					params.target = $('#' + modal_id + '_baldrickModalBody');
					params.loadElement = $('#' + modal_id + '_baldrickModalLoader');
					params.target.empty();
				}

				if(trigger.data('modalTemplate')){
					modal = $(trigger.data('modalTemplate'));
				}else{
					modal = $('#' + modal_id + '_baldrickModal');
				}
				// close if already open
				if($('.modal-backdrop').length){
					//modal.modal('hide');
				}

				// get options.
				var label = $('#' + modal_id + '_baldrickModalLable'),
					//loader  = $('#' + modal_id + '_baldrickModalLoader'),
					title  = $('#' + modal_id + '_baldrickModalTitle'),
					body  = $('#' + modal_id + '_baldrickModalBody'),
					footer  = $('#' + modal_id + '_baldrickModalFooter');

				// reset modal
				//modal.removeClass('fade');

				label.empty().parent().hide();
				body.addClass(loadClass);

				footer.empty().hide();
				if(trigger.data('modalTitle')){
					label.html(trigger.data('modalTitle')).parent().show();
				}
				if(trigger.data('modalButtons')){
					var buttons = $.trim(trigger.data('modalButtons')).split(';'),
						button_list = [];

					if(buttons.length){
						for(b=0; b<buttons.length;b++){
							var options   = buttons[b].split('|'),
								buttonLabel = options[0],
								callback  = options[1].trim(),
								atts    = $.extend({}, {"class":'button ' + defaults.triggerClass.substr(1)}, ( callback.substr(0,1) === '{' ? jQuery.parseJSON(callback) : {"data-callback" : callback} ) ),
								button    = $('<button>', atts);
							if(options[2]){
								button.addClass(options[2]);
							}
							if(callback === 'dismiss'){
								button.on('click', function(){
									$('#' + modal_id + '_baldrickModalCloser').trigger('click');
								})
							}
							footer.append(button.html(buttonLabel));
							if(b<buttons.length){
								footer.append('&nbsp;');
							}
						}
						footer.show();
					}

				}

				// RESET SIZE				
				var resize = {};
				if(params.trigger.data('modalWidth')){
					// width
					resize.width = parseInt( params.trigger.data('modalWidth') );
				}
				if(params.trigger.data('modalHeight')){
					// width
					resize.maxHeight = params.trigger.data('modalHeight');

				}

				if(resize.width || resize.maxHeight){

					// ne left offset
					if(resize.width){
						resize.marginLeft = ( resize.width / 2 ) - resize.width;
					}

					modal.css(resize);

				}


				//optional content
				if(trigger.data('modalContent')){
					body.html($(trigger.data('modalContent')).html());
					loader.hide();
					body.show();
					$(defaults.triggerClass).baldrick(defaults);
				}
				// launch
				/*modal.modal('show').on('hidden.bs.modal', function (e) {
					wm_hasModal = false;
					$(this).remove();
				});*/
			}
		}
	});

})(jQuery);