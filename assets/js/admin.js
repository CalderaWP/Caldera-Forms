var baldrickTriggers, loop_loader;

jQuery(document).ready(function($){
	var adminAJAX;
	if( 'object' === typeof  CF_ADMIN ){
		adminAJAX = CF_ADMIN.adminAjax;
	}else{
		//yolo
		adminAJAX = ajaxurl;
	}

	// admin stuff!
	// Baldrick Bindings
	baldrickTriggers = function(){
		$('.ajax-trigger').baldrick({
			request			:	adminAJAX,
			method			:	'POST',
			before			:	function( el, e ){
				var clicked = $( el );
				// check for a nonce

				var nonce 		= $('#cf_toolbar_actions'),
					referer		= nonce.parent().find('[name="_wp_http_referer"]');

				if( nonce.length && referer.length ){
					clicked.data('cf_toolbar_actions', nonce.val() );
					clicked.data('_wp_http_referer', referer.val() );
				}

				if( clicked.data('trigger') ){
					e.preventDefault();
					var trigger = $( clicked.data('trigger') );

					trigger.trigger( ( trigger.data('event') ? trigger.data('event') : 'click' ) );
					return false;
				}
			},
			complete		:	function(){
				// check for init function
				$('.init_field_type[data-type]').each(function(k,v){
					var ftype = $(v);
					if( typeof window[ftype.data('type') + '_init'] === 'function' ){
						window[ftype.data('type') + '_init'](ftype.prop('id'), ftype[0]);
					}
				});
			}
		});
	};

	// loop loader
	loop_loader = function(params, ev){
		var id = Math.round( ( Math.random() * 10000000 ) );
		return { "__id__" : id };
	};

	baldrickTriggers();


	// Profile TABS
	$('body').on('click', '.modal-side-tab', function(e){
		e.preventDefault();
		var clicked = $(this),
			parent = clicked.closest('.caldera-modal-body'),
			panels = parent.find('.tab-detail-panel'),
			panel = $(clicked.attr('href'));

		parent.find('.modal-side-tab.active').removeClass('active');
		clicked.addClass('active');

		panels.hide();
		panel.show();
	});

	// Profile Repeatable Group Remove
	$('body').on('click', '.caldera-group-remover', function(e){

		e.preventDefault();

		var clicked = $(this),
			parent = clicked.closest('.caldera-repeater-group');

		parent.slideUp(200, function(){
			parent.remove();
		});


	});

	$('body').on('click', '.form-delete a.form-control', function(e){
		var clicked = $(this);
		if(confirm(clicked.data('confirm'))){
			return;
		}else{
			e.preventDefault();
		}

	});

	// bind slugs
	$('body').on('keyup change', '[data-format="key"]', function(e){
		this.value = this.value.replace(/[^a-z0-9]/gi, '-').toLowerCase();
	});
	$('body').on('keyup change', '[data-format="slug"]', function(e){
		this.value = this.value.replace(/[^a-z0-9]/gi, '_').toLowerCase();
	});

	$( window ).on('resize', function(){

		var list_toggle = $('#cf_forms_toggle'),
			forms_panel = $('.form-panel-wrap');

		if( window.innerWidth <= 1420 ){
			if( list_toggle.is(':visible') && forms_panel.is(':visible') ){
				list_toggle.trigger('click');
			}
		}
	});


	//setup clippy on admin, not edit
	var CFclippy;
	if( undefined != typeof  CF_CLIPPY && 'object' == typeof  CF_CLIPPY ){
		CFclippy = new CalderaFormsAdminClippys2(  'caldera-forms-clippy', CF_CLIPPY, $ );
		CFclippy.init();
	}

	$( '.cf-entry-viewer-link' ).on( 'click', function(){
		if ( 'object' == typeof  CFclippy ){
			CFclippy.remove();
		}
	});


  /**
   * Delete all entries saved in a form form Settings tab in the form
   *
   * @since 1.7.0
   */
  //Display controls
  $('#caldera-forms-delete-all-form-entries').click(function(e) {
    e.preventDefault();
    $('#caldera-forms-confirm-delete-all-form-entries').slideToggle("fast");
  });
  //No clicked
  $('#caldera-forms-no-confirm-delete-all-form-entries').click(function(e) {
    e.preventDefault();
    $('#caldera-forms-confirm-delete-all-form-entries').slideToggle("fast");
  });
  //Yes clicked
  $('#caldera-forms-yes-confirm-delete-all-form-entries').click(function(e) {
    e.preventDefault();

    var url = CF_ADMIN.rest.delete_entries;

    var $spinner = jQuery( '#caldera-forms-delete-entries-spinner' );
    $spinner.css({
      visibility: 'visible',
      float:'none'
    });

    wp.apiRequest({
      url: url,
      method: 'GET'
      }).then(function (r) {
      if( r.hasOwnProperty( 'message' ) ) {
        if (r.deleted === true) {
          $('#caldera-forms-label-delete-all-entries').append("<div class='caldera-forms-deleted'>" + r.message + "</div>");
          setTimeout(function () {
            $('.caldera-forms-deleted').remove();
          }, 5000);
          $('#caldera-forms-confirm-delete-all-form-entries').slideToggle("fast");
        } else {
          $('#caldera-forms-label-delete-all-entries').append("<div class='caldera-forms-not-deleted'>" + r.message + "</div>");
          setTimeout(function () {
            $('.caldera-forms-not-deleted').remove();
          }, 5000);
          $('#caldera-forms-confirm-delete-all-form-entries').slideToggle("fast");
        }
      }
      $spinner.css({
        visibility: 'hidden',
        float:'none'
      });

    }).fail(function (r) {
      if( r.responseJSON.hasOwnProperty( 'message' ) ) {
        $('#caldera-forms-label-delete-all-entries').append("<div class='caldera-forms-not-deleted'>" + r.responseJSON.message + "</div>");
        setTimeout(function () {
          $('.caldera-forms-not-deleted').remove();
        }, 5000);
        $('#caldera-forms-confirm-delete-all-form-entries').slideToggle("fast");
      }
      $spinner.css({
        visibility: 'hidden',
        float:'none'
      });
    });

  });

});

/**
 * Makes arbitrary button pulse
 *
 * @since 1.5.0.9
 *
 * @param $btn The button as a jQuery object
 * @constructor
 */
function CalderaFormsButtonPulse( $btn ){

	var pulseEffect,
		pulseLoop,
		stopped = false;

    /**
	 * Animates the pulse effect
	 *
	 * @since 1.5.0.9
     */
	pulseEffect = function() {
        $btn.animate({
            opacity: 0.25
        }, 500 , function() {
            $btn.animate({
                opacity: 1
            }, 500 );
        });

	};

    /**
	 * Starts the pulse effect loop
	 *
	 * @since 1.5.0.9
     */
    this.startPulse = function() {
    	if( false ===  stopped ){
			pulseLoop = setInterval( function(){
				pulseEffect();
			}, 1000 );
		}



	};

    /**
	 * Ends the pulse effect loop
	 *
	 * @since 1.5.0.9
     */
	this.stopPulse = function() {
		stopped = true;
		clearInterval(pulseLoop);

	};

}

!function(a){"use strict";var b=function(a,b){this.init("tooltip",a,b)};b.prototype={constructor:b,init:function(b,c,d){var e,f;this.type=b,this.$element=a(c),this.options=this.getOptions(d),this.enabled=!0,"click"==this.options.trigger?this.$element.on("click."+this.type,this.options.selector,a.proxy(this.toggle,this)):"manual"!=this.options.trigger&&(e="hover"==this.options.trigger?"mouseenter":"focus",f="hover"==this.options.trigger?"mouseleave":"blur",this.$element.on(e+"."+this.type,this.options.selector,a.proxy(this.enter,this)),this.$element.on(f+"."+this.type,this.options.selector,a.proxy(this.leave,this))),this.options.selector?this._options=a.extend({},this.options,{trigger:"manual",selector:""}):this.fixTitle()},getOptions:function(b){return b=a.extend({},a.fn[this.type].defaults,b,this.$element.data()),b.delay&&"number"==typeof b.delay&&(b.delay={show:b.delay,hide:b.delay}),b},enter:function(b){var c=a(b.currentTarget)[this.type](this._options).data(this.type);return c.options.delay&&c.options.delay.show?(clearTimeout(this.timeout),c.hoverState="in",void(this.timeout=setTimeout(function(){"in"==c.hoverState&&c.show()},c.options.delay.show))):c.show()},leave:function(b){var c=a(b.currentTarget)[this.type](this._options).data(this.type);return this.timeout&&clearTimeout(this.timeout),c.options.delay&&c.options.delay.hide?(c.hoverState="out",void(this.timeout=setTimeout(function(){"out"==c.hoverState&&c.hide()},c.options.delay.hide))):c.hide()},show:function(){var a,b,c,d,e,f,g;if(this.hasContent()&&this.enabled){switch(a=this.tip(),this.setContent(),this.options.animation&&a.addClass("fade"),f="function"==typeof this.options.placement?this.options.placement.call(this,a[0],this.$element[0]):this.options.placement,b=/in/.test(f),a.detach().css({top:0,left:0,display:"block"}).insertAfter(this.$element),c=this.getPosition(b),d=a[0].offsetWidth,e=a[0].offsetHeight,b?f.split(" ")[1]:f){case"bottom":g={top:c.top+c.height,left:c.left+c.width/2-d/2};break;case"top":g={top:c.top-e,left:c.left+c.width/2-d/2};break;case"left":g={top:c.top+c.height/2-e/2,left:c.left-d};break;case"right":g={top:c.top+c.height/2-e/2,left:c.left+c.width}}a.offset(g).addClass(f).addClass("in")}},setContent:function(){var a=this.tip(),b=this.getTitle();a.find(".tooltip-inner")[this.options.html?"html":"text"](b),a.removeClass("fade in top bottom left right")},hide:function(){function d(){var b=setTimeout(function(){c.off(a.support.transition.end).detach()},500);c.one(a.support.transition.end,function(){clearTimeout(b),c.detach()})}var c=this.tip();return c.removeClass("in"),a.support.transition&&this.$tip.hasClass("fade")?d():c.detach(),this},fixTitle:function(){var a=this.$element;(a.attr("title")||"string"!=typeof a.attr("data-original-title"))&&a.attr("data-original-title",a.attr("title")||"").attr("title","")},hasContent:function(){return this.getTitle()},getPosition:function(b){return a.extend({},b?{top:0,left:0}:this.$element.offset(),{width:this.$element[0].offsetWidth,height:this.$element[0].offsetHeight})},getTitle:function(){var a,b=this.$element,c=this.options;return a=b.attr("data-original-title")||("function"==typeof c.title?c.title.call(b[0]):c.title)},tip:function(){return this.$tip=this.$tip||a(this.options.template)},validate:function(){this.$element[0].parentNode||(this.hide(),this.$element=null,this.options=null)},enable:function(){this.enabled=!0},disable:function(){this.enabled=!1},toggleEnabled:function(){this.enabled=!this.enabled},toggle:function(b){var c=a(b.currentTarget)[this.type](this._options).data(this.type);c[c.tip().hasClass("in")?"hide":"show"]()},destroy:function(){this.hide().$element.off("."+this.type).removeData(this.type)}};var c=a.fn.tooltip;a.fn.tooltip=function(c){return this.each(function(){var d=a(this),e=d.data("tooltip"),f="object"==typeof c&&c;e||d.data("tooltip",e=new b(this,f)),"string"==typeof c&&e[c]()})},a.fn.tooltip.Constructor=b,a.fn.tooltip.defaults={animation:!0,placement:"top",selector:!1,template:'<div class="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',trigger:"hover",title:"",delay:0,html:!1},a.fn.tooltip.noConflict=function(){return a.fn.tooltip=c,this}}(window.jQuery);