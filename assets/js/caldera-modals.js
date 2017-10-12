(function($){
 	
 	var calderaBackdrop = null,
 		calderaModals 	= {},
 		activeModals 	= [],
 		activeSticky	= [],
		pageHTML		= $('html'),
		pageBody		= $('body'),
 		mainWindow 		= $(window);

	var positionModals = function(){

		if( !activeModals.length && !activeSticky.length ){
			return;
		}
		var modalId	 = ( activeModals.length ? activeModals[ ( activeModals.length - 1 ) ] : activeSticky[ ( activeSticky.length - 1 ) ] ),
			windowWidth  = mainWindow.width(),
			windowHeight = mainWindow.height(),
			modalHeight  = calderaModals[ modalId ].config.height,
			modalOuterHeight  = modalHeight,
			modalWidth  = calderaModals[ modalId ].config.width,
			top 		 = 0,
			flickerBD	 = false,
			modalReduced = false;

		if( calderaBackdrop ){ pageHTML.addClass('has-caldera-modal'); }

		// top
		top = (windowHeight - calderaModals[ modalId ].config.height ) / 2.2;

		if( top < 0 ){
			top = 0;
		}
		if( modalHeight + ( calderaModals[ modalId ].config.padding * 2 ) > windowHeight && calderaBackdrop ){
			modalHeight = windowHeight - ( calderaModals[ modalId ].config.padding * 2 );
			modalOuterHeight = '100%';
			if( calderaBackdrop ){ 
				calderaBackdrop.css( {
					paddingTop: calderaModals[ modalId ].config.padding,
					paddingBottom: calderaModals[ modalId ].config.padding,
				});
			}
			modalReduced = true;
		}
		if( modalWidth + ( calderaModals[ modalId ].config.padding * 2 ) >= windowWidth ){
			modalWidth = '100%';
			if( calderaBackdrop ){ 
				calderaBackdrop.css( {
					paddingLeft: calderaModals[ modalId ].config.padding,
					paddingRight: calderaModals[ modalId ].config.padding,
				});
			}
			modalReduced = true;
		}

		if( true === modalReduced ){
			if( windowWidth <= 700 && windowWidth > 600 ){
				if( calderaBackdrop ){ modalHeight = windowHeight - ( calderaModals[ modalId ].config.padding * 2 ); }
				modalWidth = windowWidth;
				modalOuterHeight = modalHeight - ( calderaModals[ modalId ].config.padding * 2 );
				modalWidth = '100%';
				top = 0;
				if( calderaBackdrop ){ calderaBackdrop.css( { padding : calderaModals[ modalId ].config.padding } ); }
			}else if( windowWidth <= 600 ){
				if( calderaBackdrop ){ modalHeight = windowHeight; }
				modalWidth = windowWidth;
				modalOuterHeight = '100%';
				top = 0;
				if( calderaBackdrop ){ calderaBackdrop.css( { padding : 0 } ); }
			}
		}


		// set backdrop
		if( calderaBackdrop && calderaBackdrop.is(':hidden') ){
			flickerBD = true;
			calderaBackdrop.show();
		}
		// title?
		if( calderaModals[ modalId ].header ){
			if( calderaBackdrop ){ calderaBackdrop.show(); }
			modalHeight -= calderaModals[ modalId ].header.outerHeight();
			calderaModals[ modalId ].closer.css( { 
				padding		: ( calderaModals[ modalId ].header.outerHeight() / 2 ) - 0.5
			} );
			calderaModals[ modalId ].title.css({ paddingRight: calderaModals[ modalId ].closer.outerWidth() } );
		}
		// footer?
		if( calderaModals[ modalId ].footer ){
			if( calderaBackdrop ){ calderaBackdrop.show(); }
			modalHeight -= calderaModals[ modalId ].footer.outerHeight();			
		}

		if( calderaBackdrop && flickerBD === true ){
			calderaBackdrop.hide();
			flickerBD = false;
		}

		// set final height
		if( modalHeight != modalOuterHeight ){
			calderaModals[ modalId ].body.css( { 
				height		: modalHeight			
			} );
		}
		calderaModals[ modalId ].modal.css( {
			width		: modalWidth	
		} );
		
		if( calderaModals[ modalId ].config.sticky && calderaModals[ modalId ].config.minimized ){
			var toggle = {},
				minimizedPosition = calderaModals[ modalId ].title.outerHeight() - calderaModals[ modalId ].modal.outerHeight();
			if( calderaModals[ modalId ].config.sticky.indexOf( 'bottom' ) > -1 ){
				toggle['margin-bottom'] = minimizedPosition;
			}else if( calderaModals[ modalId ].config.sticky.indexOf( 'top' ) > -1 ){
				toggle['margin-top'] = minimizedPosition;
			}
			calderaModals[ modalId ].modal.css( toggle );
			if( calderaModals[ modalId ].config.sticky.length >= 3 ){
				pageBody.css( "margin-" + calderaModals[ modalId ].config.sticky[0] , calderaModals[ modalId ].title.outerHeight() );
				if( modalReduced ){
					calderaModals[ modalId ].modal.css( calderaModals[ modalId ].config.sticky[1] , 0 );
				}else{
					calderaModals[ modalId ].modal.css( calderaModals[ modalId ].config.sticky[1] , parseFloat( calderaModals[ modalId ].config.sticky[2] ) );
				}
			}
		}
		if( calderaBackdrop ){
			calderaModals[ modalId ].modal.css( {
				marginTop 	: top,
				height		: modalOuterHeight
			} );
			setTimeout( function(){
				calderaModals[ modalId ].modal.addClass( 'caldera-animate' );
			}, 10);

			calderaBackdrop.fadeIn( calderaModals[ modalId ].config.speed );
		}
		calderaModals[ modalId ].resize = positionModals;
		return calderaModals; 
	}

	var closeModal = function( obj ){	
		var modalId = $(obj).data('modal'),
			position = 0,
			toggle = {};

		if( obj && calderaModals[ modalId ].config.sticky ){

			if( calderaModals[ modalId ].config.minimized ){
				calderaModals[ modalId ].config.minimized = false
				position = 0;
			}else{
				calderaModals[ modalId ].config.minimized = true;
				position = calderaModals[ modalId ].title.outerHeight() - calderaModals[ modalId ].modal.outerHeight();
			}
			if( calderaModals[ modalId ].config.sticky.indexOf( 'bottom' ) > -1 ){
				toggle['margin-bottom'] = position;
			}else if( calderaModals[ modalId ].config.sticky.indexOf( 'top' ) > -1 ){
				toggle['margin-top'] = position;
			}
			calderaModals[ modalId ].modal.stop().animate( toggle , calderaModals[ modalId ].config.speed );
			return;
		}
		var lastModal;
		if( activeModals.length ){
			
			lastModal = activeModals.pop();
			if( calderaModals[ lastModal ].modal.hasClass( 'caldera-animate' ) && !activeModals.length ){
				calderaModals[ lastModal ].modal.removeClass( 'caldera-animate' );
				setTimeout( function(){
					calderaModals[ lastModal ].modal.remove();
					delete calderaModals[ lastModal ];
				}, 500 );
			}else{
				if( calderaBackdrop ){
					calderaModals[ lastModal ].modal.hide( 0 , function(){
						$( this ).remove();
						delete calderaModals[ lastModal ];
					});
				}
			}

		}

		if( !activeModals.length ){
			if( calderaBackdrop ){ 
				calderaBackdrop.fadeOut( 250 , function(){
					$( this ).remove();
					calderaBackdrop = null;
				});
			}
			pageHTML.removeClass('has-caldera-modal');
		}else{			
			calderaModals[ activeModals[ ( activeModals.length - 1 ) ] ].modal.show();
		}

	}
	$.calderaModal = function(opts){
		var defaults    = $.extend(true, {
			element				:	'div',
			height				:	550,
			width				:	620,
			padding				:	12,
			speed				:	250
		}, opts );
		if( !calderaBackdrop && ! defaults.sticky ){
			calderaBackdrop = $('<div>', {"class" : "caldera-backdrop"});
			if( ! defaults.focus ){
				calderaBackdrop.on('click', function( e ){
					if( e.target == this ){
						closeModal();
					}
				});
			}
			pageBody.append( calderaBackdrop );
			calderaBackdrop.hide();
		}



		// create modal element
		var modalElement = defaults.element,
			modalId = defaults.modal;

		if( activeModals.length ){

			if( activeModals[ ( activeModals.length - 1 ) ] !== modalId ){
				calderaModals[ activeModals[ ( activeModals.length - 1 ) ] ].modal.hide();
			}
		}

		if( typeof calderaModals[ modalId ] === 'undefined' ){
			if( defaults.sticky ){
				defaults.sticky = defaults.sticky.split(' ');
				if( defaults.sticky.length < 2 ){
					defaults.sticky = null;
				}
				activeSticky.push( modalId );
			}
			calderaModals[ modalId ] = {
				config	:	defaults,
				modal	:	$('<' + modalElement + '>', {
					id					: modalId + '_calderaModal',
					tabIndex			: -1,
					"ariaLabelled-by"	: modalId + '_calderaModalLable',
					"class"				: "caldera-modal-wrap" + ( defaults.sticky ? ' caldera-sticky-modal ' + defaults.sticky[0] + '-' + defaults.sticky[1] : '' )
				})
			};
			if( !defaults.sticky ){ activeModals.push( modalId ); }
		}else{
			calderaModals[ modalId ].config = defaults;
			calderaModals[ modalId ].modal.empty();
		}
		// add animate		
		if( defaults.animate && calderaBackdrop ){
			var animate 		= defaults.animate.split( ' ' ),
				animateSpeed 	= defaults.speed + 'ms',
				animateEase		= ( defaults.animateEase ? defaults.animateEase : 'ease' );

			if( animate.length === 1){
				animate[1] = 0;
			}

			calderaModals[ modalId ].modal.css( { 
				transform				: 'translate(' + animate[0] + ', ' + animate[1] + ')',
				'-web-kit-transition'	: 'transform ' + animateSpeed + ' ' + animateEase,
				'-moz-transition'		: 'transform ' + animateSpeed + ' ' + animateEase,
				transition				: 'transform ' + animateSpeed + ' ' + animateEase
			} );

		}
		calderaModals[ modalId ].body = $('<div>', {"class" : "caldera-modal-body",id: modalId + '_calderaModalBody'});
		calderaModals[ modalId ].content = $('<div>', {"class" : "caldera-modal-content",id: modalId + '_calderaModalContent'});


		// padd content		
		calderaModals[ modalId ].content.css( { 
			margin : defaults.padding
		} );
		calderaModals[ modalId ].body.append( calderaModals[ modalId ].content ).appendTo( calderaModals[ modalId ].modal );
		if( calderaBackdrop ){ calderaBackdrop.append( calderaModals[ modalId ].modal ); }else{
			calderaModals[ modalId ].modal . appendTo( $( 'body' ) );
		}


		if( defaults.footer ){
			calderaModals[ modalId ].footer = $('<div>', {"class" : "caldera-modal-footer",id: modalId + '_calderaModalFooter'});
			calderaModals[ modalId ].footer.css({ padding: defaults.padding });
			calderaModals[ modalId ].footer.appendTo( calderaModals[ modalId ].modal );
			// function?
			if( typeof window[defaults.footer] === 'function' ){
				calderaModals[ modalId ].footer.append( window[defaults.footer]( opts, this ) );
			}else if( typeof defaults.footer === 'string' ){
				// is jquery selector?
				  try {
				  	var footerElement = $( defaults.footer );
				  	calderaModals[ modalId ].footer.html( footerElement.html() );
				  } catch (err) {
				  	calderaModals[ modalId ].footer.html( defaults.footer );
				  }
			}
		}

		if( defaults.title ){
			var headerAppend = 'prependTo';
			calderaModals[ modalId ].header = $('<div>', {"class" : "caldera-modal-title", id : modalId + '_calderaModalTitle'});
			calderaModals[ modalId ].closer = $('<a>', { "href" : "#close", "class":"caldera-modal-closer", "data-dismiss":"modal", "aria-hidden":"true",id: modalId + '_calderaModalCloser'}).html('&times;');
			calderaModals[ modalId ].title = $('<h3>', {"class" : "modal-label", id : modalId + '_calderaModalLable'});
			
			calderaModals[ modalId ].title.html( defaults.title ).appendTo( calderaModals[ modalId ].header );
			calderaModals[ modalId ].title.css({ padding: defaults.padding });
			calderaModals[ modalId ].title.append( calderaModals[ modalId ].closer );
			if( calderaModals[ modalId ].config.sticky ){
				if( calderaModals[ modalId ].config.minimized && true !== calderaModals[ modalId ].config.minimized ){
					setTimeout( function(){
						calderaModals[ modalId ].title.trigger('click');
					}, parseInt( calderaModals[ modalId ].config.minimized ) );
					calderaModals[ modalId ].config.minimized = false;
				}
				calderaModals[ modalId ].closer.hide();
				calderaModals[ modalId ].title.addClass( 'caldera-modal-closer' ).data('modal', modalId).appendTo( calderaModals[ modalId ].header );
				if( calderaModals[ modalId ].config.sticky.indexOf( 'top' ) > -1 ){
					headerAppend = 'appendTo';
				}
			}else{
				calderaModals[ modalId ].closer.data('modal', modalId).appendTo( calderaModals[ modalId ].header );
			}
			calderaModals[ modalId ].header[headerAppend]( calderaModals[ modalId ].modal );
		}
		// hide modal
		calderaModals[ modalId ].modal.outerHeight( defaults.height );
		calderaModals[ modalId ].modal.outerWidth( defaults.width );

		if( defaults.content ){
			// function?
			if( typeof defaults.content === 'function' ){
				calderaModals[ modalId ].content.append( defaults.content( opts, calderaModals[ modalId ], this ) );
			}else if( typeof window[defaults.content] === 'function' ){
				calderaModals[ modalId ].content.append( window[defaults.content]( opts, calderaModals[ modalId ], this ) );
			}else if( typeof defaults.content === 'string' ){
				// is jquery selector?
				  try {
				  	var contentElement = $( defaults.content );
				  	if( contentElement.length ){
				  		calderaModals[ modalId ].content.append( contentElement.detach() );
						contentElement.show();
				  	}else{
				  		calderaModals[ modalId ].content.html( defaults.content );
				  	}
				  } catch (err) {
				  	calderaModals[ modalId ].content.html( defaults.content );
				  }
			}
		}

		// set position;
		positionModals();
		// return main object
		return calderaModals[ modalId ];
	}

	$.fn.calderaModal = function( opts ){
		if( !opts ){ opts = {}; }
		opts = $.extend( {}, this.data(), opts );
		return $.calderaModal( opts );
	}

	// setup resize positioning and keypresses
    if ( window.addEventListener ) {
        window.addEventListener( "resize", positionModals, false );
        window.addEventListener( "keypress", function(e){
        	if( e.keyCode === 27 && calderaBackdrop !== null ){
        		calderaBackdrop.trigger('click');
        	}
        }, false );

    } else if ( window.attachEvent ) {
        window.attachEvent( "onresize", positionModals );
    } else {
        window["onresize"] = positionModals;
    }



	$(document).on('click', '[data-modal]:not(.caldera-modal-closer)', function( e ){
		e.preventDefault();
		$(this).calderaModal();
	});
	$(window).load( function(){
		$('[data-modal][data-autoload]').each( function(){
			$( this ).calderaModal();
		});
	});

	$(document).on( 'click', '.caldera-modal-closer', function( e ) {
		e.preventDefault();
		closeModal( this );
	})


})(jQuery);
