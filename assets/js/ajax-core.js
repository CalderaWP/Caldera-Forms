var resBaldrickTriggers;

jQuery(function($){
	function fieldErrors(fields, $form, $notice) {
		for (var i in fields) {
			var field = $form.find('[data-field="' + i + '"]'),
				wrap = field.parent();
			if (!field.length) {
				$notice.html('<p class="alert alert-danger ">' + fields[i] + '</p>');

			} else {
				if (wrap.is('label')) {
					wrap = wrap.parent();
					if (wrap.hasClass('checkbox') || wrap.hasClass('radio')) {
						wrap = wrap.parent();
					}
				}
				var has_block = wrap.find('.help-block').not('.caldera_ajax_error_block');

				wrap.addClass('has-error').addClass('caldera_ajax_error_wrap');
				if (has_block.length) {
					has_block.hide();
				}
				wrap.append('<span class="help-block caldera_ajax_error_block">' + fields[i] + '</span>');
			}

		}
	}
    var cf_upload_queue = [];
    // admin stuff!
    var cf_push_file_upload = function( form, file_number, data ){
        var progress = jQuery('#progress-file-' + file_number ),
            filesize = jQuery('.' + file_number + ' .file-size');
        cf_upload_queue.push(1);
        cf_uploader_filelist[ file_number ].state = 2;
        jQuery.ajax({
            xhr: function(){
                var xhr = new window.XMLHttpRequest();
                //Upload progress
                xhr.upload.addEventListener("progress", function(evt){
                    if (evt.lengthComputable) {
                        var percentComplete = ( evt.loaded / evt.total ) * 100;
                        progress.width( percentComplete + '%' );
                        filesize.html( size_format(evt.loaded) + ' / ' + size_format( evt.total ) );
                    }
                }, false);
                //Download progress
                xhr.addEventListener("progress", function(evt){
                    if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total;
                        //Do something with download progress

                    }
                }, false);
                return xhr;
            },
            url : form.data('request') + "/upload/",
            type: "POST",
            data : data,
            processData: false,
            contentType: false,
            success:function(data, textStatus, jqXHR){

                if( data.success && data.success === true ){

                    cf_upload_queue.pop();
                    var file_remover = jQuery('[data-file="' + file_number + '"]');
                    file_remover.next().addClass('file-uploaded');
                    file_remover.remove();

                    cf_uploader_filelist[ file_number ].state = 3;

                    form.submit();


                }else if( data.data && !data.success ){
                    //show error
                    jQuery('.' + file_number ).addClass('has-error');
                    form.find(':submit').prop('disabled',false);
                    form.find('.cf-uploader-trigger').slideDown();
                    jQuery('.' + file_number +' .file-error' ).html( data.data );

                    return;
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                //if fails  - push error
                if( !form.data( 'postDisable' ) ){
                    buttons.prop('disabled',false);
                }
            }
        });
    }
    // Baldrick Bindings
    resBaldrickTriggers = function(){
        var trackedElements = {};
        /**
         * Get Element for notices
         *
         * @since 1.5.x
         *
         * @param obj
         * @returns {*|jQuery|HTMLElement}
         */
        var getNoticeEl = function(obj) {
            return jQuery('#caldera_notices_' + obj.params.trigger.data('instance'));
        };

        /**
         * Get breadcrumbs Element
         *
         * @since 1.6.0
         *
         * @param obj
         * @returns {*|jQuery|HTMLElement}
         */
        var getBreadCrumbsEl = function (obj) {
            return jQuery('#caldera-forms-breadcrumb_' + obj.params.trigger.data('instance'));
        };

        /**
         * Show breadcrumbs if possible
         *
         * @since 1.6.0
         *
         * @param obj
         */
        var maybeShowBreadCrumbs = function (obj) {
            var $breadcrumbs = getBreadCrumbsEl(obj);
            if ($breadcrumbs.length) {
                $breadcrumbs.show().attr('aria-hidden', false).css('visibility', 'visible');
            }
        };

        /**
         * Show breadcrumbs if possible
         *
         * @since 1.6.0
         *
         * @param obj
         */
        var maybeHideBreadCrumbs = function (obj) {
            var $breadcrumbs = getBreadCrumbsEl(obj);
            if ($breadcrumbs.length) {
                $breadcrumbs.show().attr('aria-hidden', true ).css('visibility', 'hidden');
            }
        };

        jQuery('.cfajax-trigger').baldrick({
            request			:	'./',
            method			:	'POST',
            init			: function(el, ev){

                ev.preventDefault();

                var $form	=	jQuery(el),
                    buttons = 	$form.find(':submit');
                var pending = [];
                var fieldsBlocking = [];

				/**
                 * This event is triggered directly before the request for form submission is made
                 *
                 * Runs after cf.form.submit
                 *
                 * @since 1.8.0
				 */
				jQuery( document ).trigger( 'cf.ajax.request', {
                    $form: $form,
                    formIdAttr: $form.attr( 'id' ),
                    displayFieldErrors:fieldErrors,
					fieldsBlocking: fieldsBlocking,
                    $notice: jQuery( '#caldera_notices_' + $form.data( 'instance' ) )
                });

				//Check if any cf2 fields are blocking submit
                if( 'object' === typeof  window.cf2 ){
                    var cf2 = window.cf2[ $form.attr( 'id' ) ];
                }
				if( 'object' === typeof cf2 ){
					if( cf2.hasOwnProperty( 'pending' ) && 0 !== cf2.pending.length ){
						return false;
					}

					if( cf2.hasOwnProperty( 'fieldsBlocking' ) && 0 !== cf2.fieldsBlocking.length ){
						return false;
					}
				}

                if( $form.data('_cf_manual') ){
                    $form.find('[name="cfajax"]').remove();
                    return false;
                }

                if( !$form.data( 'postDisable' ) ){
                    buttons.prop('disabled',true);
                }


                if( typeof cf_uploader_filelist === 'object'  ){
                    // verify required
                    $form.find('.cf-uploader-trigger').slideUp();
                    // setup file uploader
                    var has_files = false;
                    var count = cf_upload_queue.length;
                    for( var file in cf_uploader_filelist ){
                        if( cf_uploader_filelist[ file ].state > 1 || cf_uploader_filelist[ file ].state === 0 ){
                            // state 2 and 3 is transferring and complete, state 0 is error and dont upload
                            continue;
                        }

                        has_files = true;
                        var data = new FormData(),
                            file_number = file,
                            field = jQuery('#' + file_number.split('_file_')[0] );
                        data.append( field.data('field'), cf_uploader_filelist[ file ].file );
                        data.append( 'field', field.data('field') );
                        data.append( 'control', field.data('controlid') );
                        cf_push_file_upload( $form, file_number, data );
                        field.val('');//@see https://github.com/CalderaWP/Caldera-Forms/issues/2514#issuecomment-395213433
                        field.attr('type','hidden');
                        field.val(field.data('controlid'));
                        count++;
                        if( count === 1 ){
                            break;
                        }

                    }
                    if( true === has_files || cf_upload_queue.length ){
                        return false;
                    }
                }



            },
            error : function( obj ){
                if( obj.jqxhr.status === 404){
                    this.trigger.data('_cf_manual', true ).trigger('submit');
                }else{
                    var $notice = getNoticeEl(obj);
                    if( obj.jqxhr.responseJSON.data.html ){
                        $notice.html (obj.jqxhr.responseJSON.data.html );
                        jQuery('html,body').animate({
                            scrollTop: $notice.offset().top - $notice.outerHeight()
                        }, 300 );

                    }
                }

            },
            callback		: function(obj){
                obj.params.trigger.find(':submit').prop('disabled',false);

                var $notice = getNoticeEl( obj );

                // run callback if set.
                if( obj.params.trigger.data('customCallback') && typeof window[obj.params.trigger.data('customCallback')] === 'function' ){

                    window[obj.params.trigger.data('customCallback')](obj.data);

                }


                if( !obj.params.trigger.data('inhibitnotice') ){

                    jQuery('.caldera_ajax_error_wrap').removeClass('caldera_ajax_error_wrap').removeClass('has-error');
                    jQuery('.caldera_ajax_error_block').remove();

                    if(obj.data.status === 'complete' || obj.data.type === 'success'){
                        maybeHideBreadCrumbs(obj);
                        if(obj.data.html){
                            obj.params.target.html(obj.data.html);
                        }
                        if(obj.params.trigger.data('hiderows')){
                            obj.params.trigger.find('div.row').remove();
                        }
                    }else if(obj.data.status === 'preprocess'){
                        maybeShowBreadCrumbs(obj);
                        obj.params.target.html(obj.data.html);
                    }else if(obj.data.status === 'error'){
                        maybeShowBreadCrumbs(obj);
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
                // show trigger
                obj.params.trigger.find('.cf-uploader-trigger').slideDown();
                if(obj.data.fields){
                    var $form = obj.params.trigger;
                    var fields = obj.data.fields;
                    fieldErrors(fields, $form, $notice);
                }

                if ( 'undefined' != obj.data.scroll ) {
                    var el = document.getElementById( obj.data.scroll );
                    if ( null != el ) {
                        var $scrollToEl = jQuery( el );
                        jQuery('html,body').animate({
                            scrollTop: $scrollToEl.offset().top - $scrollToEl.outerHeight() - 12
                        }, 300);
                    }
                }

                // trigger global event
                jQuery( document ).trigger( 'cf.submission', obj );
                jQuery( document ).trigger( 'cf.' + obj.data.type );

            }
        });
    };

    resBaldrickTriggers();
});
