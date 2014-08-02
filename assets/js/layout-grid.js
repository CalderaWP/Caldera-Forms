var rebuild_field_binding, rebind_field_bindings, current_form_fields = {}, required_errors = {}, add_new_grid_page, add_page_grid, init_magic_tags;

init_magic_tags = function(){
	//init magic tags
	var magicfields = jQuery('.magic-tag-enabled').not('.magic-tag-init-bound');

	magicfields.each(function(k,v){
		var input = jQuery(v);
		
		/*if(input.hasClass('magic-tag-init-bound')){
			return;			
		}*/
		var magictag = jQuery('<span class="icn-code magic-tag-init"></span>'),
			wrapper = jQuery('<span style="position:relative;display:inline-block; width:100%;"></span>');

		if(input.is('input')){
			magictag.css('borderBottom', 'none');
		}
		input.wrap(wrapper);
		magictag.insertAfter(input);
		input.addClass('magic-tag-init-bound');
	});	
}

rebuild_field_binding = function(){

	var fields = jQuery('.caldera-editor-field-config-wrapper');

	// clear list
	current_form_fields = {};
	// set object
	system_values.field = {
		tags	:	{
			text	:	[]
		},
		type	:	"Fields",
		wrap	:	['%','%']
	};

	fields.each(function(fk,fv){
		var field_id = jQuery(fv).prop('id'),
			label = jQuery('#' + field_id + '_lable').val(),
			slug = jQuery('#' + field_id + '_slug').val(),
			type = jQuery('#' + field_id + '_type').val();


		if(typeof system_values.field.tags[type] === 'undefined'){
			system_values.field.tags[type] = [];
		}
		system_values.field.tags[type].push( slug );
		if(type !== 'text'){
			system_values.field.tags.text.push( slug );
		}

		current_form_fields[field_id] = {
			label: label,
			slug: slug,
			type: type
		};

	});
	rebind_field_bindings();

};

rebind_field_bindings = function(){

	//check_required_bindings();
	
	//return;
	var bindings = jQuery('.caldera-field-bind'),
		type_instances,
		processor_li;

	bindings.each(function(k,v){

		var field = jQuery(v),
			current = field.val(),
			default_sel = field.data('default'),
			excludes = field.data('exclude'),
			count = 0,
			wrapper = field.closest('.caldera-editor-processor-config-wrapper'),
			wrapper_id = wrapper.prop('id'),
			valid = '';	

		if(default_sel){			
			current = default_sel;
		}

		if(field.is('select')){
			field.empty();

			var optgroup = jQuery('<optgroup label="Fields">');
			for(var fid in current_form_fields){
				if(field.data('type')){
					if(field.data('type').split(',').indexOf(current_form_fields[fid].type) < 0){
						continue;
					}

				}
				optgroup.append('<option value="' + fid + '"' + ( current === fid ? 'selected="selected"' : '' ) + '>' + current_form_fields[fid].label + ' [' + current_form_fields[fid].slug + ']</option>');
				count += 1;
			}
			optgroup.appendTo(field);
			// system values
			if(count === 0){
				field.empty();
			}
			
			for(var type in system_values){
				type_instances = [];
					
				if(excludes){				
					if( excludes.split(',').indexOf(type) >= 0 ){
						continue;
					}
				}

				if(type !== 'system' && type !== 'variable'){

					var type_instance_confs = jQuery(".processor-" + type);
					
					for(var c = 0; c<type_instance_confs.length; c++){
						if(wrapper_id === type_instance_confs[c].id){
							continue;
						}

						type_instances.push(type_instance_confs[c].id);
						if(type_instance_confs.length > 1){
							if(processor_li = jQuery('li.'+type_instance_confs[c].id + ' .processor-line-number')){
								processor_li.html('[' + ( c + 1 ) + ']');
							}
						}

					}
				}else{					
					type_instances.push('__system__');
				}
				
				var types = [];
				if(field.data('type')){
					types = field.data('type').split(',');
					types.push('vars');
				}else{
					types = ['text','vars'];
				}

				for(var t = 0; t<types.length; t++){
					if(system_values[type].tags[types[t]]){
						
						for( var instance = 0; instance < type_instances.length; instance++){

							// check index order is valid
							if(jQuery('li.'+type_instances[instance]).index() > jQuery('li.'+wrapper_id).index() && type_instances[instance] !== '__system__'){
								if( field.closest('.caldera-editor-processors-panel-wrap').length ){
									valid = ' disabled="disabled"';
								}
							}else{
								valid = '';
							}


							var optgroup = jQuery('<optgroup label="' + system_values[type].type + ( type_instances[instance] !== '__system__' ? ' ' + ( jQuery('li.'+type_instances[instance]).find('.processor-line-number').html() ) : '' ) + '"' + valid + '>');

								//for( var tag in system_values[type].tags){

									for( var i = 0; i < system_values[type].tags[types[t]].length; i++){
										
										var bind_value = system_values[type].tags[types[t]][i];	
										// update labels on multiple
										if(type_instances[instance] !== '__system__'){
											bind_value = bind_value.replace(type ,type_instances[instance]);
										}
										
										optgroup.append('<option value="{' + bind_value + '}"' + ( current === '{'+bind_value+'}' ? 'selected="selected"' : '' ) + valid + '>' + system_values[type].tags[types[t]][i] + '</option>');
										//field.append('<option value="' + bind_value + '"' + ( current === system_values[type].tags[types[t]][i] ? 'selected="selected"' : '' ) + '>' + system_values[type].tags[types[t]][i] + '</option>');

										count += 1;
									}

								//}
							if(optgroup.children().length){
								optgroup.appendTo(field);	
							}							

						}

					}
				}

			}
			if(count === 0){
				field.empty();
				if(field.data('type')){
					field.append('<option value="">No ' + field.data('type').split(',').join(' or ') + ' in form</option>').prop('disabled', true);
					var no_options = true;
				}
			}else{
				field.prop('disabled', false);
			}
			
			if(!field.hasClass('required') && typeof no_options === 'undefined'){
				field.prepend('<option value=""></option>');
			}		
			field.val(current);
		}else{
			// text types
			//console.log(field.val())
		}
	});

	check_required_bindings();
	init_magic_tags();
	jQuery(document).trigger('bound.fields');
};

function setup_field_type(obj){
	
	return {'id' : obj.trigger.prop('id')};
}



function check_required_bindings(){

	var fields = jQuery('.caldera-config-field .required'),
		savebutton = jQuery('.caldera-header-save-button'),
		field_elements = jQuery('.layout-form-field'),
		nav_elements = jQuery('.caldera-processor-nav');
	
	savebutton.prop("disabled", false);

	fields.removeClass('has-error');
	field_elements.removeClass('has-error');
	nav_elements.removeClass('has-error');

	jQuery('.error-tag').remove();
	//reset list 
	required_errors = {};
	
	fields.each(function(k,v){
		var field = jQuery(v),
			panel = field.closest('.caldera-config-editor-panel');

		if(!v.value.length){
			if(!required_errors[panel.prop('id')]){
				required_errors[panel.prop('id')] = 0;
			}

			var is_field = field.closest('.caldera-editor-field-config-wrapper'),
				is_process = field.closest('.caldera-editor-processor-config-wrapper');

			if(is_field.length){
				jQuery('.layout-form-field[data-config="'+is_field.prop('id')+'"]').addClass('has-error');
			}
			if(is_process.length){
				jQuery('.'+is_process.prop('id')).addClass('has-error');
			}
			required_errors[panel.prop('id')] += 1;
			field.addClass('has-error');

			//tab.append('<span class="error-tag">' + required_errors[panel.prop('id')] + '</span>');

		}else{
			//unique
			if( field.hasClass('field-slug') ){
				var slugs = jQuery('.field-slug').not(field);

				for(var s = 0; s < slugs.length; s++){
					if( slugs[s].value === v.value ){
						var field = jQuery(slugs[s]);

						if(!required_errors[panel.prop('id')]){
							required_errors[panel.prop('id')] = 0;
						}
						var is_field = field.closest('.caldera-editor-field-config-wrapper'),
							is_process = field.closest('.caldera-editor-processor-config-wrapper');

						if(is_field.length){
							jQuery('.layout-form-field[data-config="'+is_field.prop('id')+'"]').addClass('has-error');
						}
						if(is_process.length){
							jQuery('.'+is_process.prop('id')).addClass('has-error');
						}
						required_errors[panel.prop('id')] += 1;
						field.addClass('has-error');
						break;
					}
				};				
			}
		}
	});
	
	for(var t in required_errors){
		savebutton.prop("disabled", true);
		jQuery('.caldera-forms-options-form').find('a[href="#' + t + '"]').append('<span class="error-tag">' + required_errors[t] + '</span>');
	}
	
	// check for button and update the processor page.
	if(!jQuery('.preview-caldera-config-group button:submit').length){
		//jQuery('.caldera-editor-processors-panel-wrap').hide();
		jQuery('.mailer-errors').show();
		jQuery('.mailer-control-panel').hide();

	}else{
		//jQuery('.caldera-editor-processors-panel-wrap').show();
		jQuery('.mailer-errors').hide();
		jQuery('.mailer-control-panel').show();
	}

	jQuery('.caldera-conditional-field-set').trigger('change');
}

jQuery(function($) {

	add_new_grid_page = function(obj){
		return { "page_no" : "pg_" + Math.round( Math.random() * 10000000 ) };
	}

	add_page_grid = function(obj){
		//obj.rawData.page_no
		var btn_count = $('.page-toggle').length + 1,
			button = $('<button type="button" data-name="Page ' + btn_count + '" data-page="' + obj.rawData.page_no + '" class="page-toggle button">' + obj.params.trigger.data('addtitle') + ' ' + btn_count + '</button> '),
			option_tab = $('#page-toggles');
		button.appendTo( option_tab );
		option_tab.show();
		buildSortables();
		button.trigger('click');
		if( btn_count === 1){
			option_tab.hide();
		}
		$(document).trigger('add.page');
	}

// bind pages tab
	$(document).on('remove.page add.page load.page', function(e){
		var btn_count = $('.page-toggle').length,
			pages_tab = $('#tab_pages');

		if(btn_count <= 1){
			pages_tab.hide();
		}else{
			pages_tab.show();
		}


	});

	function buildLayoutString(){
		var grid_panels = $('.layout-grid-panel'),
			row_index = 0;

		grid_panels.each(function(pk,pv){
			
			var panel= $(pv),
				capt = panel.find('.layout-structure'),
				rows = panel.find('.row'),
				struct = [];
			
			rows.each(function(k,v){
				var row = $(v),
					cols = row.children().not('.column-merge'),
					rowcols = [];
				row_index += 1;
				cols.each(function(p, c){
					span = $(c).attr('class').split('-');
					rowcols.push(span[2]);
					var fields = $(c).find('.field-location');
					if(fields.length){
						fields.each(function(x,f){
							var field = $(f);
							field.val( row_index + ':' + (p+1) ).removeAttr('disabled');						
						});
					}
					// set name

				});
				struct.push(rowcols.join(':'));
			});
			capt.val(struct.join('|'));
		});
	}

	function insert_new_field(newfield, target, tel){
		var name = "fld_" + Math.round( Math.random() * 10000000 ),
			new_name 	= name,
			field_conf	= $('#field_config_panels'),
			new_conf_templ,
			new_field;

		if(tel){
			var clone = $('#' + tel).clone().wrap('<div>').parent().html().replace( new RegExp(tel,"g") , '{{id}}');
			new_conf_templ = Handlebars.compile( clone );
		}else{
			// field conf template
			new_conf_templ = Handlebars.compile( $('#caldera_field_config_wrapper_templ').html() );
		}
		new_field = {
			"id"	:	new_name,
			"label"	:	'',
			"slug"	:	''
		};

		// pance new conf template
		field_conf.append( new_conf_templ( new_field ) );

		newfield.
		removeClass('button-small').
		removeClass('button').
		removeClass('button-primary').
		removeClass('ui-draggable').
		removeClass('layout-new-form-field').
		addClass('layout-form-field').
		attr('data-config', name);

		newfield.find('.layout_field_name').remove();
		newfield.find('.field-location').prop('name', 'config[layout_grid][fields][' + name + ']');
		newfield.find('.settings-panel').show();
		newfield.appendTo( target );
		buildSortables();
		newfield.find('.icon-edit').trigger('click');

		$('#' + name + '_lable').focus().select();

		if(tel){
			field_conf.find('.field_config_string').val('');
			field_conf.find('.field-label').trigger('change');
		}

		rebuild_field_binding();
		baldrickTriggers();
		$('#' + name).trigger('field.drop');		
		$(document).trigger('field.added');
	}

	function buildSortables(){

		// Sortables
		$('.toggle-options').sortable({
			handle: ".dashicons-sort",
		});


		$( "#grid-pages-panel" ).sortable({
			placeholder: 	"row-drop-helper",
			handle: 		".sort-handle",
			items:			".first-row-level",
			axis: 			"y",
			stop: function(){
				buildLayoutString();
			}
		});		
		$( ".layout-column" ).sortable({
			connectWith: 	".layout-column",
			appendTo: 		"#grid-pages-panel",
			helper: 		"clone",
			items:			".layout-form-field",
			handle:			".drag-handle",
			cursor: 		"move",
			opacity: 		0.7,
			cursorAt: 		{left: 100, top: 15},
			start: function(e,ui){
				ui.helper.css({width: '200px', height: '35px', paddingTop: '20px'});
			},
			stop: function(e,ui){
				ui.item.removeAttr('style');
				buildLayoutString();
			}
		});
		
		// Draggables
		$( "h3 .layout-new-form-field" ).draggable({
			helper: "clone",
			appendTo: "body"
		});
		$('.page-toggle.button').droppable({
			accept: ".layout-form-field",
			over: function(e, ui){
				$(this).trigger('click');
				//buildSortables();
				$( ".layout-column" ).sortable("refresh");
			}
		});
		// Tools Bar Items
		$( ".layout-column" ).droppable({
			greedy: true,
			activeClass: "ui-state-dropper",
			hoverClass: "ui-state-hoverable",
			accept: ".layout-new-form-field",
			drop: function( event, ui ) {
				var newfield= ui.draggable.clone(),
					target = $(this);

				insert_new_field(newfield, target);
			}
		});

		
		buildLayoutString();		
	};
	buildSortables();

	$('#grid-pages-panel').on('click','.column-fieldinsert .dashicons-plus-alt', function(e){
		//newfield-tool
		var target 		= $(this).closest('.column-container'),
			newfield 	= $('#newfield-tool').clone();
		
		insert_new_field(newfield, target);

	});
	$('#grid-pages-panel').on('click','.column-fieldinsert .dashicons-admin-generic', function(e){

	});
	/*
	$('#grid-pages-panel').on('click','.icon-filter', function(e){
		//newfield-tool
		var target 		= $(this).closest('.column-container'),
			newfield 	= $('#newfield-tool').clone();
		
		insert_new_field(newfield, target, $(this).data('id'));

	});*/
	

	$('#grid-pages-panel').on('click','.column-split', function(e){
		var column = $(this).parent().parent(),
			size = column.attr('class').split('-'),
			newcol = $('<div>').insertAfter(column);			

		var left = Math.ceil(size[2]/2),
			right = Math.floor(size[2]/2);
		

		size[2] = left;
		column.attr('class', size.join('-'));
		size[2] = right;
		newcol.addClass(size.join('-')).append('<div class="layout-column column-container">');
		$(this).remove();
		buildSortables();
		
		jQuery('.column-tools').remove();
		jQuery('.column-merge').remove();		
		
	});
	$( "#grid-pages-panel" ).on('click', '.column-remove', function(e){
		var row = $(this).closest('.row'),
			fields = row.find('.layout-form-field'),
			wrap = row.closest('.layout-grid-panel');
		
		//find fields
		if(fields.length){
			if(!confirm($('#row-remove-fields-message').text())){
				return;
			}
			fields.each(function(k,v){
				$('#' + $(v).data('config') ).remove();
			});
		}
		//return;

		row.slideUp(200, function(){
			$(this).remove();
			buildLayoutString();
			rebuild_field_binding();
			if(!wrap.find('.row').length){
				wrap.remove();
				var btn = $('#page-toggles .button-primary'),
					prev = btn.prev(),
					next = btn.next();

				btn.remove();
				if(prev.length){
					prev.trigger('click');
				}else{
					next.trigger('click');
				}
			}
			$(document).trigger('remove.page');
		});

		jQuery('.column-tools').remove();
		jQuery('.column-merge').remove();
		
	});
	
	$( ".caldera-config-editor-main-panel" ).on('click', '.caldera-add-row', function(e){
		e.preventDefault();
		var wrap = $('.page-active');
		if(!wrap.length){
			$('.caldera-add-page').trigger('click');
			return;
		}
		$('.page-active').append('<div class="first-row-level row"><div class="col-xs-12"><div class="layout-column column-container"></div></div></div>');
		buildSortables();
		buildLayoutString();
	});
	
	$( "#grid-pages-panel" ).on('click', '.column-join', function(e){
		
		var column = $(this).parent().parent().parent();
		
		var	prev 		= column.prev(),
			left 		= prev.attr('class').split('-'),
			right 		= column.attr('class').split('-');
		left[2]		= parseFloat(left[2])+parseFloat(right[2]);
		
		
		column.find('.layout-column').contents().appendTo(prev.find('.layout-column'));
		prev.attr('class', left.join('-'));//+' - '+ right);
		column.remove();
		buildLayoutString();
		jQuery('.column-tools').remove();
		jQuery('.column-merge').remove();		
	});	
	
	$('#grid-pages-panel').on('mouseenter','.row', function(e){
		var setrow = jQuery(this);
		jQuery('.column-tools,.column-merge').remove();
		setrow.children().children().first().append('<div class="column-remove column-tools"><i class="icon-remove"></i></div>');
		//setrow.children().children().last().append('<div class="column-sort column-tools"><i class="icon-edit"></i> <i class="dashicons dashicons-menu drag-handle sort-handle"></i> </div>');
		setrow.children().children().last().append('<div class="column-sort column-tools" style="text-align:right;"><i class="dashicons dashicons-menu drag-handle sort-handle"></i></div>');
		
		setrow.children().children().not(':first').prepend('<div class="column-merge"><div class="column-join column-tools"><i class="icon-join"></i></div></div>');
		var single = setrow.parent().parent().parent().width()/12-1;
		setrow.children().children().each(function(k,v){
			var column = $(v)
			var width = column.width()/2-5;
			column.prepend('<div class="column-fieldinsert column-tools"><i class="dashicons dashicons-plus-alt"></i></div>');
			if(!column.parent().hasClass('col-xs-1')){
				column.prepend('<div class="column-split column-tools"><i class="dashicons dashicons-leftright"></i></div>');
				column.find('.column-split').css('left', width);
			}
		});

		jQuery( ".column-merge" ).draggable({
			axis: "x",
			helper: "clone",
			appendTo: setrow,
			grid: [single, 0],
			drag: function(e, ui){
				$(this).addClass('dragging');
				$('.column-tools').remove();
				$('.column-split').remove();				
				var column = $(this).parent().parent(),
					dragged = ui.helper,
					direction = (ui.originalPosition.left > dragged.position().left) ? 'left' : 'right',
					step = 0,
					prev = column.prev(),
					single = Math.round(column.parent().width()/12-10),
					distance = Math.abs(ui.originalPosition.left - dragged.position().left);
					
					column.parent().addClass('sizing');
				
					if(distance >= single){
						var left 		= prev.attr('class').split('-'),
							right 		= column.attr('class').split('-');

						left[2]		= parseFloat(left[2]);
						right[2]	= parseFloat(right[2]);

						if(direction === 'left'){
							left[2]--;
							right[2]++;
							if(left[2] > 0 && left[2] < (left[2]+right[2]) ){
								prev.attr('class', left.join('-'));//+' - '+ right);
								column.attr('class', right.join('-'));//+' - '+ right);
								ui.originalPosition.left = dragged.position().left;
								//$(this).css('margin-left', Math.abs(dragged.position().left) - 12 + 'px');
							}else{
								$(this).draggable( "option", "disabled", true );
							}
						}else{
							left[2]++;
							right[2]--;
							if(right[2] > 0 && right[2] < (right[2]+right[2]) ){
								prev.attr('class', left.join('-'));//+' - '+ right);
								column.attr('class', right.join('-'));//+' - '+ right);
								ui.originalPosition.left = dragged.position().left;
								//$(this).css('margin-left', '-'+Math.abs(dragged.position().left) - 12 + 'px');
							}else{
								$(this).draggable( "option", "disabled", true );
							}

						}
						buildLayoutString();
					}

				
			},
			stop: function(){
				$(this).removeClass('dragging').parent().parent().parent().removeClass('sizing');
			}
		});		
	});
	$('#grid-pages-panel').on('mouseleave','.row', function(e){
		jQuery('.column-tools').remove();
		jQuery('.column-merge').remove();
	});
	
	$('#grid-pages-panel').on('click', '.layout-form-field .icon-remove', function(){
		var clicked = $(this),
			panel = clicked.parent(),
			config = $('#' + panel.data('config'));

		panel.slideUp(100, function(){
			$(this).remove();
		});
		config.slideUp(100, function(){
			$(this).remove();
		});
		//if(!wrap.children().length){
			//wrap.remove();
			
		//}

	});	

	$('#grid-pages-panel').on('click', '.layout-form-field .icon-edit', function(){

		

		var clicked = $(this),
			panel = clicked.parent();

			$('.caldera-editor-field-config-wrapper').hide();

			if(panel.hasClass('field-edit-open')){
				panel.removeClass('field-edit-open');
			}else{
				$('.layout-form-field').removeClass('field-edit-open');
				panel.addClass('field-edit-open');
				$('#' + panel.data('config')).show();
			}

		$(document).trigger('show.' + panel.data('config'));

		$('#' + panel.data('config')).find('.auto-populate-options').trigger('change');
	});
	$('body').on('click', '.layout-modal-edit-closer,.layout-modal-save-action', function(e){
		
		e.preventDefault();
		
		var clicked = $(this),
			panel = $('.layout-form-field.edit-open'),
			modal = clicked.closest('.layout-modal-container');
			settings = modal.find('.settings-panel').first();

			$('.edit-open').removeClass('edit-open');
			settings.appendTo(panel.find('.settings-wrapper')).hide();

			modal.hide();

	});

	// clear params
	$('.layout-editor-body').on('change', '.layout-core-pod-query', function(){
		$(this).parent().find('.settings-panel-row').remove();
		$('.edit-open').find('.drag-handle .set-pod').html(' - ' + $(this).val());
	});
	$('.layout-editor-body').on('click', '.remove-where', function(){
		$(this).closest('.settings-panel-row').remove();
	});
	// load pod fields
	$('.layout-editor-body').on('click', '.use-pod-container', function(){
		var clicked = $(this),
			podselect = clicked.prev(),
			pod	= podselect.val(),		
			container = '';

		if(!pod.length){
			return;
		}

		$('.edit-open').find('.drag-handle .set-pod').html(' - ' + podselect.val());

		clicked.parent().parent().find('.spinner').css('display', 'inline-block');

		var data = {
			'action'	:	'pq_loadpod',
			'pod_reference'	:	{
				'pod'	:	pod
			}
		};

		$.post(ajaxurl, data, function(res){

			clicked.parent().find('.spinner').css('display', 'none');

			var template = $('#where-line-tmpl').html(),
				fields = '',
				container = clicked.closest('.settings-panel').data('container');

				

			for(var i in res){
				fields += '<option value="' + res[i] + '">' + res[i] + '</option>';
			}
			template = template.replace(/{{fields}}/g, fields).replace(/{{container_id}}/g, container);
			
			clicked.parent().append( template );

		});

	});

	// edit row
	$('.caldera-editor-header').on('click', '.column-sort .icon-edit', function(e){

	});
	// bind tray stuff
	$('.layout-editor-body').on('tray_loaded', '.layout-template-tray', function(){
		buildSortables();
	});
	// build panel navigation
	$('.caldera-editor-header').on('click', '.caldera-editor-header-nav a', function(e){
		e.preventDefault();

		var clicked = $(this);

		// remove active tab
		$('.caldera-editor-header-nav li').removeClass('active');

		// hide all tabs
		$('.caldera-editor-body').hide();

		// show new tab
		$( clicked.attr('href') ).show();

		// set active tab
		clicked.parent().addClass('active');

	});

	$('body').on('change', '.required', check_required_bindings);

	// prevent error forms from submiting
	$('body').on('submit', '.caldera-forms-options-form', function(e){
		var errors = $('.required.has-error');
		if(errors.length){
			e.preventDefault();
		}
	});


	//toggle_option_row
	$('.caldera-editor-body').on('click', '.add-toggle-option', function(e){

		var clicked		= $(this);

		if(clicked.data('bulk')){
			$(clicked.data('bulk')).toggle();
			$(clicked.data('bulk')).find('textarea').focus();
			return;
		}

		var	wrapper		= clicked.closest('.caldera-editor-field-config-wrapper'),
			toggle_rows	= wrapper.find('.toggle-options'),
			row			= $('#field-option-row-tmpl').html(),
			template	= Handlebars.compile( row ),
			key			= "opt" + parseInt( ( Math.random() + 1 ) * 0x100000 ),
			config		= {
				_name	:	'config[fields][' + wrapper.prop('id') + '][config]',
				option	: {}
			};

		if(clicked.data('options')){
			var batchinput 	= $(clicked.data('options')),
				batch 		= batchinput.val().split("\n");
			for( var i = 0; i < batch.length; i ++){
				config.option["opt" + parseInt( ( Math.random() + i ) * 0x100000 )] = {
					value	:	batch[i],
					label	:	batch[i],
					default	:	false
				}
			}
			$(clicked.data('options')).parent().hide();
			batchinput.val('');
			toggle_rows.empty();
		}else{
			// add new option
			config.option[key]	=	{
				value	:	'',
				label	:	'',
				default :	false				
			};
		}

		// place new row
		toggle_rows.append( template( config ) );
		wrapper.find('.toggle_show_values').trigger('change');


		$('.toggle-options').sortable({
			handle: ".dashicons-sort"
		});
		if(!batch){
			toggle_rows.find('.toggle_label_field').last().focus();
		}
	});


	// remove an option row
	$('.caldera-editor-body').on('click', '.toggle-remove-option', function(e){
		var triggerfield = $(this).closest('.caldera-editor-field-config-wrapper').find('.field-config').first();
		$(this).parent().remove();
		triggerfield.trigger('change');
	});

	$('.caldera-editor-body').on('click', '.page-toggle', function(e){
		var clicked = $(this),
			wrap = clicked.parent(),
			btns = wrap.find('.button');

		btns.removeClass('button-primary');
		$('.layout-grid-panel').hide().removeClass('page-active');
		$('#' + clicked.data('page')).show().addClass('page-active');
		clicked.addClass('button-primary');
		//reindex
		btns.each(function(k,v){
			$(v).html(wrap.data('title') + ' ' + (k+1) );
		});
		if(btns.length === 1){
			wrap.hide();
		}

	});

	$('.caldera-editor-body').on('blur toggle.values', '.toggle_label_field', function(e){

		var label = $(this),
			value = label.prev();

		if(value.val().length){
			return;
		}

		value.val(label.val());
	});

	// build fild bindings
	rebuild_field_binding();
	$(document).trigger('load.page');
});




Handlebars.registerHelper("_options_config", function() {
	//console.log(this);
});
/*
<div class="caldera-config-group caldera-config-group-full">
	<button class="button block-button add-toggle-option" type="button">Add Option</button>
</div>
<div class="caldera-config-group caldera-config-group-full toggle-options">
	{{#each option}}
	<div class="toggle_option_row">
		<i class="dashicons dashicons-sort" style="padding: 4px 9px;"></i>
		{{#if default}}
			<input type="checkbox" class="toggle_set_default field-config" name="{{../../_name}}[option][{{@key}}][default]" value="1" checked="checked">
		{{else}}			
			<input type="checkbox" class="toggle_set_default field-config" name="{{../../_name}}[option][{{@key}}][default]" value="1">
		{{/if}}
		<input type="text" class="toggle_value_field field-config" name="{{../_name}}[option][{{@key}}][value]" value="{{value}}" placeholder="value">
		<input type="text" class="toggle_label_field field-config" name="{{../_name}}[option][{{@key}}][label]" value="{{label}}" placeholder="label">
		<button class="button button-small toggle-remove-option" type="button"><i class="icn-delete"></i></button>		
	</div>
	{{/each}}
</div>
*/