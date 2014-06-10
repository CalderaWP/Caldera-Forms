var rebuild_field_binding, rebind_field_bindings, current_form_fields = {}, required_errors = {};

rebuild_field_binding = function(){

	var fields = jQuery('.caldera-editor-field-config-wrapper');
	
	// clear list
	current_form_fields = {};

	fields.each(function(fk,fv){
		var field_id = jQuery(fv).prop('id'),
			label = jQuery('#' + field_id + '_lable').val(),
			slug = jQuery('#' + field_id + '_slug').val(),
			type = jQuery('#' + field_id + '_type').val();

		current_form_fields[field_id] = {
			label: label,
			slug: slug,
			type: type
		};

	});

	rebind_field_bindings();

};

rebind_field_bindings = function(){

	var bindings = jQuery('.caldera-processor-field-bind');

	bindings.each(function(k,v){

		var field = jQuery(v),
			current = field.val(),
			default_sel = field.data('default'),
			count = 0;


		if(default_sel){
			current = default_sel;
		}

		field.empty();

		if(!field.hasClass('required')){
			field.append('<option value=""></option>');
		}
		for(var fid in current_form_fields){
			if(field.data('type')){
				if(field.data('type').split(',').indexOf(current_form_fields[fid].type) < 0){
					continue;
				}

			}
			field.append('<option value="' + fid + '"' + ( current === fid ? 'selected="selected"' : '' ) + '>' + current_form_fields[fid].label + ' [' + current_form_fields[fid].slug + ']</option>');
			count += 1;
		}

		if(count === 0){
			field.empty().append('<option value="">No ' + field.data('type').split(',').join(' or ') + ' in form</option>').prop('disabled', true);
		}else{
			field.prop('disabled', false);
		}


	});

	check_required_bindings();
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
		if(!v.value.length){
			var field = jQuery(v),
				panel = field.closest('.caldera-config-editor-panel'),
				is_field = field.closest('.caldera-editor-field-config-wrapper'),
				is_process = field.closest('.caldera-editor-processor-config-wrapper');

			if(!required_errors[panel.prop('id')]){
				required_errors[panel.prop('id')] = 0;
			}
			if(is_field.length){
				jQuery('.layout-form-field[data-config="'+is_field.prop('id')+'"]').addClass('has-error');
			}
			if(is_process.length){
				jQuery('.'+is_process.prop('id')).addClass('has-error');
			}
			required_errors[panel.prop('id')] += 1;
			field.addClass('has-error');

			//tab.append('<span class="error-tag">' + required_errors[panel.prop('id')] + '</span>');

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


	function buildLayoutString(){
		var capt = $('.layout-structure'),
			grid = $('.layout-grid'),
			rows = grid.find('.row'),
			struct = [];
		rows.each(function(k,v){
			var row = $(v),
				cols = row.children().not('.column-merge'),
				rowcols = [];
			
			cols.each(function(p, c){
				span = $(c).attr('class').split('-');
				rowcols.push(span[2]);
				var fields = $(c).find('.field-location');
				if(fields.length){
					fields.each(function(x,f){
						var field = $(f);
						field.val( (k+1) + ':' + (p+1) ).removeAttr('disabled');						
					});
				}
				// set name

			});
			struct.push(rowcols.join(':'));
		});
		capt.val(struct.join('|'));
	}

	function insert_new_field(newfield, target){
		var name = "fld_" + Math.round( Math.random() * 10000000 ),
			new_name 	= name,
			field_conf	= $('#field_config_panels'),
			new_conf_templ,
			new_field;


		// field conf template
		new_conf_templ = Handlebars.compile( $('#caldera_field_config_wrapper_templ').html() );

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

		

		rebuild_field_binding();
		baldrickTriggers();
		$('#' + name).trigger('field.drop');		
	}

	function buildSortables(){

		// Sortables
		$('.toggle-options').sortable({
			handle: ".dashicons-sort",
		});


		$( ".layout-grid-panel" ).sortable({
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
			helper: 		"clone",
			items:			".layout-form-field",
			handle:			".drag-handle",
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

	$('.layout-grid-panel').on('click','.column-fieldinsert', function(e){
		//newfield-tool
		var target 		= $(this).closest('.column-container'),
			newfield 	= $('#newfield-tool').clone();
		
		insert_new_field(newfield, target);

	});
	

	$('.layout-grid-panel').on('click','.column-split', function(e){
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
	$( ".layout-grid-panel" ).on('click', '.column-remove', function(e){
		var row = $(this).parent().parent().parent(),
			fields = row.find('.layout-form-field');
		
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
		});
		jQuery('.column-tools').remove();
		jQuery('.column-merge').remove();
		
	});
	
	$( ".caldera-config-editor-main-panel" ).on('click', '.caldera-add-row', function(e){
		e.preventDefault();
		$('.layout-grid').append('<div class="first-row-level row"><div class="col-xs-12"><div class="layout-column column-container"></div></div></div>');
		buildSortables();
		buildLayoutString();
	});
	
	$( ".layout-grid-panel" ).on('click', '.column-join', function(e){
		
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
	
	$('.layout-grid-panel').on('mouseenter','.row', function(e){
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
	$('.layout-grid').on('mouseleave','.row', function(e){
		jQuery('.column-tools').remove();
		jQuery('.column-merge').remove();
	});
	
	$('.layout-grid').on('click', '.layout-form-field .icon-remove', function(){
		var clicked = $(this),
			panel = clicked.parent(),
			config = $('#' + panel.data('config'));

		panel.slideUp(100, function(){
			$(this).remove();
		});
		config.slideUp(100, function(){
			$(this).remove();
		});

	});	

	$('.layout-grid').on('click', '.layout-form-field .icon-edit', function(){

		

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

		var clicked		= $(this),
			wrapper		= clicked.closest('.caldera-editor-field-config-wrapper'),
			toggle_rows	= wrapper.find('.toggle-options'),
			row			= $('#field-option-row-tmpl').html(),
			template	= Handlebars.compile( row ),
			key			= "opt" + parseInt( ( Math.random() + 1 ) * 0x100000 ),
			config		= {
				_name	:	'config[fields][' + wrapper.prop('id') + '][config]',
				option	: {}
			};

			// add new option
			config.option[key]	=	{				
				value	:	'',
				label	:	'',
				default :	false				
			};


			// place new row
			toggle_rows.append( template( config ) );



			$('.toggle-options').sortable({
				handle: ".dashicons-sort"
			});

			toggle_rows.find('.toggle_value_field').last().focus();


	});

	// remove an option row
	$('.caldera-editor-body').on('click', '.toggle-remove-option', function(e){
		var triggerfield = $(this).closest('.caldera-editor-field-config-wrapper').find('.field-config').first();
		$(this).parent().remove();
		triggerfield.trigger('change');
	});

	$('.caldera-editor-body').on('blur', '.toggle_value_field', function(e){
		var value = $(this),
			label = value.next();

		if(label.val().length){
			return;
		}

		label.val(value.val());
	});

	// build fild bindings
	rebuild_field_binding();

});



Handlebars.registerHelper("is_single", function(value, options) {
	if(Object.keys(value).length !== 1){
		return false;
	}else{
		return options.fn(this);
	}
});
Handlebars.registerHelper("script", function(options) {
	if(options.hash.src){
		return '<script type="text/javascript" src="' + options.fn(this) + '"></script>';
	}else{
		return '<script type="text/javascript">' + options.fn(this) + '</script>';
	}
});
Handlebars.registerHelper("is", function(value, options) {
	//console.log(value);
	if(options.hash.value === '@key'){
		options.hash.value = options.data.key;
	}
	if(options.hash.value === value){
		return options.fn(this);
	}else{
		return false;
	}
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