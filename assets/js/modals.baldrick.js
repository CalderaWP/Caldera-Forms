/* Baldrick handlebars.js templating plugin */
jQuery(function($){

	var wm_hasModal = false;
	
	jQuery.fn.baldrick.registerhelper('wordpress_modal', {
		refresh: function(obj){
			if(obj.params.trigger.data('modalAutoclose')){
				jQuery('#' + obj.params.trigger.data('modalAutoclose') + '_baldrickModalCloser').trigger('click');
			}
		},
		event : function(el){
			var triggers = jQuery(el), modal_id = 'wm';
			if(triggers.data('modal') && wm_hasModal === false){
				if(triggers.data('modal') !== 'true'){
					modal_id = triggers.data('modal');
				}
				if(!jQuery('#' + modal_id + '_baldrickModal').length){
					//wm_hasModal = true;
					// write out a template wrapper.
					var modal = jQuery('<div>', {
							id          : modal_id + '_baldrickModal',
							tabIndex      : -1,
							"ariaLabelled-by" : modal_id + '_baldrickModalLable',
							"class"       : "caldera-modal-wrap",
						}),
					//modalDialog = jQuery('<div>', {"class" : "modal-dialog"});
					modalBackdrop = jQuery('<div>', {"class" : "caldera-backdrop"});
					modalContent = jQuery('<div>', {"class" : "caldera-modal-body",id: modal_id + '_baldrickModalBody'});
					modalFooter = jQuery('<div>', {"class" : "caldera-modal-footer",id: modal_id + '_baldrickModalFooter'});
					modalHeader = jQuery('<div>', {"class" : "caldera-modal-title", id : modal_id + '_baldrickModalTitle'});
					modalCloser = jQuery('<a>', { "href" : "#close", "class":"caldera-modal-closer", "data-dismiss":"modal", "aria-hidden":"true",id: modal_id + '_baldrickModalCloser'}).html('&times;');
					modalTitle = jQuery('<h3>', {"class" : "modal-label", id : modal_id + '_baldrickModalLable'});

					modalHeader.append(modalCloser).append(modalTitle).appendTo(modal);

					if( ! trigger.data('static') ){
						
						modalBackdrop.on('dismiss', function(e){
							e.preventDefault();
							modalBackdrop.fadeOut(200);
							modal.fadeOut(200, function(){
								jQuery(this).remove();
								modalBackdrop.remove();
							});
						})
					}
					modalCloser.on('click', function(e){
						e.preventDefault();
						modalBackdrop.fadeOut(200);
						modal.fadeOut(200, function(){
							jQuery(this).remove();
							modalBackdrop.remove();
						});
					})

					modalContent.appendTo(modal);
					modalFooter.appendTo(modal);
					
					modal.appendTo(jQuery('body')).hide().fadeIn(200);
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

				modal 			= jQuery('#' + modal_id + '_baldrickModal');
				modalBody 	= jQuery('#' + modal_id + '_baldrickModalBody');
				modalTitle 	= jQuery('#' + modal_id + '_baldrickModalTitle');
				if(obj.params.trigger.data('loadClass')){
					loadClass = obj.params.trigger.data('loadClass');
				}

				if(obj.params.trigger.data('modalLife')){
					var delay = parseFloat(obj.params.trigger.data('modalLife'));
					if(delay > 0){
						setTimeout(function(){
							jQuery('#' + modal_id + '_baldrickModalCloser').trigger('click');
						}, delay);
					}else{
						jQuery('#' + modal_id + '_baldrickModalCloser').trigger('click');
					}
				}
				//jQuery('#' + modal_id + '_baldrickModalLoader').hide();
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
					var data = jQuery(obj.data).prop('id', modal_id + '_baldrickModalBody');
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
					params.target = jQuery('#' + modal_id + '_baldrickModalBody');
					params.loadElement = jQuery('#' + modal_id + '_baldrickModalLoader');
					params.target.empty();
				}

				if(trigger.data('modalTemplate')){
					modal = jQuery(trigger.data('modalTemplate'));
				}else{
					modal = jQuery('#' + modal_id + '_baldrickModal');
				}
				// close if already open
				if(jQuery('.modal-backdrop').length){
					//modal.modal('hide');
				}

				// get options.
				var label = jQuery('#' + modal_id + '_baldrickModalLable'),
					//loader  = jQuery('#' + modal_id + '_baldrickModalLoader'),
					title  = jQuery('#' + modal_id + '_baldrickModalTitle'),
					body  = jQuery('#' + modal_id + '_baldrickModalBody'),
					footer  = jQuery('#' + modal_id + '_baldrickModalFooter');

				// reset modal
				//modal.removeClass('fade');

				label.empty().parent().hide();
				body.addClass(loadClass);

				footer.empty().hide();
				if(trigger.data('modalTitle')){
					label.html(trigger.data('modalTitle')).parent().show();
				}
				if(trigger.data('modalButtons')){
					var buttons = trigger.data('modalButtons').trim().split(';'),
						button_list = [];

					if(buttons.length){
						for(b=0; b<buttons.length;b++){
							var options   = buttons[b].split('|'),
								buttonLabel = options[0],
								callback  = options[1].trim(),
								atts    = jQuery.extend({}, {"class":'button ' + defaults.triggerClass.substr(1)}, ( callback.substr(0,1) === '{' ? JSON.parse(callback) : {"data-callback" : callback} ) ),
								button    = jQuery('<button>', atts);
							if(options[2]){
								button.addClass(options[2]);
							}
							if(callback === 'dismiss'){
								button.on('click', function(){
									jQuery('#' + modal_id + '_baldrickModalCloser').trigger('click');
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
					body.html(jQuery(trigger.data('modalContent')).html());
					loader.hide();
					body.show();
					jQuery(defaults.triggerClass).baldrick(defaults);
				}
				// launch
				/*modal.modal('show').on('hidden.bs.modal', function (e) {
					wm_hasModal = false;
					jQuery(this).remove();
				});*/
			}
		}
	});

});