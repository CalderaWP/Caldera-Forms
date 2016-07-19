<script type="text/javascript">
var init_cf_baldrick;
function cf_set_limits( el ){
	jQuery( el ).data('perpage', jQuery('#cf-entries-list-items').val() );
}
function cf_refresh_view(obj){
	
	jQuery('.entry_count_' + obj.params.trigger.data('form')).html(obj.rawData.total);
	jQuery('.status_toggles[data-status="trash"] .current-status-count').html(obj.rawData.trash);
	jQuery('.status_toggles[data-status="active"] .current-status-count').html(obj.rawData.total);
	if(obj.rawData.undo === obj.params.trigger.data('panel')){
		obj.params.trigger.closest('tr').addClass('cf-deleted-row');
	}else{
		obj.params.trigger.closest('tr').removeClass('cf-deleted-row');
	}
	obj.params.trigger.data('do', obj.rawData.undo).html(obj.rawData.undo_text).removeClass('disabled');
}

function setup_pagination(obj){

	var total			= obj.rawData.total,
		trash			= obj.rawData.trash,
		active			= obj.rawData.active,		
		toggles			= jQuery('.status_toggles'),
		exporter		= jQuery('.caldera-entry-exporter'),
		tense			= ( total === 1 ? ' <?php _e('item'); ?>' : ' <?php _e('items'); ?>' ),
		pages			= obj.rawData.pages,
		current			= obj.rawData.current_page,
		form			= obj.params.trigger.data('form'),
		status			= obj.params.trigger.data('status'),
		pagenav			= jQuery('.caldera-table-nav'),
		page_links		= pagenav.find('.pagination-links'),
		entries_total	= pagenav.find('.displaying-num'),
		pages_total		= pagenav.find('.total-pages'),
		current_display	= pagenav.find('.current-page'),
		first_page		= pagenav.find('.first-page'),
		prev_page		= pagenav.find('.prev-page'),
		next_page		= pagenav.find('.next-page'),
		last_page		= pagenav.find('.last-page'),
		form_trigger	= jQuery('.form_entry_row.highlight').find('.form-entry-trigger'),
		bulk_actions	= jQuery('#cf_bulk_action'),
		bulk_template	= jQuery('#bulk-actions-'+status+'-tmpl').html(),
		entry_count		= jQuery('.entry_count_' + form);

	obj.params.trigger.data('page', current);
	form_trigger.data('status', status);
	bulk_actions.html(bulk_template);

	toggles.removeClass('button-primary').removeClass('disabled');
	toggles.filter('[data-status="'+status+'"]').addClass('button-primary');
	toggles.each(function(k,v){
		var el = jQuery(v);
		if(typeof obj.rawData[el.data('status')] === 'number'){
			if(obj.rawData[el.data('status')] > 0){
				el.find('.current-status-count').html(obj.rawData[el.data('status')]);
			}else{
				el.find('.current-status-count').html('');
			}
		}
	});
	// update count
	entry_count.html(active); 
	//bulk-actions-active-tmpl

	// add form id to toggles
	toggles.data('form', form)
	pagenav.data('total', pages);

	if(pages <= 1){
		page_links.hide();
	}else{
		page_links.show();		
	}
	exporter.find('.caldera-forms-entry-exporter').attr('href', 'admin.php?page=caldera-forms&export=' + form);
	exporter.show();
	pagenav.show();
	page_links.find('a').removeClass('disabled');

	// setup values
	page_links.data('total', total);
	entries_total.html(total + tense);
	pages_total.html(pages);
	current_display.val(current);

	if(current === 1){
		first_page.addClass('disabled');
		prev_page.addClass('disabled');
	}else if(current === pages){
		last_page.addClass('disabled');
		next_page.addClass('disabled');		
	}

	jQuery( 'html, body').animate({
		scrollTop: 0
	}, 250 );


	init_cf_baldrick();
	jQuery( window ).trigger('resize');
}

jQuery(function($){

	init_cf_baldrick = function(){
		$('.cfajax-trigger').baldrick({
			before			: function(el, ev){

				var form	=	$(el),
					buttons = 	form.find(':submit');
				ev.preventDefault();
				if( form.is( 'form' ) ){
					
					var validate = form.parsley({
						errorsWrapper : '<span class="help-block caldera_ajax_error_block"></span>',
						errorTemplate : '<span></span>'
					});

					if( !validate.isValid() ){
						$(window).trigger('resize');
						return false;
					}
				}
			},
			callback : function( obj ){
				var form;
				if( obj.params.trigger.is( 'form' ) ){
					form = obj.params.trigger;
				}else{
					form = obj.params.target.find( 'form.caldera_forms_form' );
				}
				if( form.length ){
					var validate = form.parsley({
						errorsWrapper : '<span class="help-block caldera_ajax_error_block"></span>',
						errorTemplate : '<span></span>'
					});
				}
				calders_forms_init_conditions();
			}
		});	

		window.Parsley.on('field:validated', function() {
			setTimeout( function(){ $(window).trigger('resize') }, 10 );
		});
	}
	
	function do_page_navigate(el){
	
		var clicked 		= $(el);

		if(clicked.hasClass('disabled')){
			return;
		}

		var	form_trigger	= $('.form_entry_row.highlight').find('.form-entry-trigger'),
			current			= parseInt(form_trigger.data('page')),
			pagenav			= jQuery('.caldera-table-nav'),
			page_links		= pagenav.find('.pagination-links'),
			total			= parseInt(pagenav.data('total'));

		

		if(clicked.data('page') === 'first'){
			form_trigger.data('page', 1).trigger('click');
		}else if(clicked.data('page') === 'prev'){
			var next = current - 1;
			form_trigger.data('page', next).trigger('click');
		}else if(clicked.data('page') === 'next'){
			var next = current + 1;
			form_trigger.data('page', next).trigger('click');
		}else if(clicked.data('page') === 'last'){
			form_trigger.data('page', total).trigger('click');
		}else{
			form_trigger.data('page', clicked.val()).trigger('click');
		}
	}

	$('body').on('change','.current-page', function(e){
		do_page_navigate(this);
	});
	$('body').on('click','.pagination-links a', function(e){
		e.preventDefault();
		do_page_navigate(this);
	});
	$( document ).on('cf.add cf.remove cf.enable cf.disable', function(){
		$(window).trigger('resize');
	});
});
</script>
