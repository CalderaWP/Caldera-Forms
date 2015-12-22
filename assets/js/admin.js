var baldrickTriggers, loop_loader;

jQuery(document).ready(function($){
	
	// admin stuff!
	// Baldrick Bindings
	baldrickTriggers = function(){
		$('.ajax-trigger').baldrick({
			request			:	ajaxurl,
			method			:	'POST',
			before			:	function( el, e ){
				var clicked = $( el );
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


});
