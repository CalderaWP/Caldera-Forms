jQuery(function($){


	$('.edit-update-trigger').baldrick({
		method			:	'POST'
	});


	/*
	*	Build the fieltypes config
	*	configs are stored in the .caldera-config-field-setup field within the parent wrapper
	*
	*/
	function build_fieldtype_config(el){

		var select 			= $(el),
			templ			= $('#' + select.val() + '_tmpl').length ? $('#' + select.val() + '_tmpl').html() : $('#noconfig_field_templ').html(),
			parent			= select.closest('.caldera-editor-field-config-wrapper'),
			target			= parent.find('.caldera-config-field-setup'),
			template 		= Handlebars.compile(templ),
			config			= parent.find('.field_config_string').val(),
			current_type	= select.data('type');

			parent.find('.caldera-config-group').show();


			// Be sure to load the fields preset when switching back to the initial field type.
			if(config.length && current_type === select.val() ){
				config = JSON.parse(config);
			}else{
				// default config
				config = fieldtype_defaults[select.val() + '_cfg'];
			}

			// remove not supported stuff
			if(fieldtype_defaults[select.val() + '_nosupport']){
				
				if(fieldtype_defaults[select.val() + '_nosupport'].indexOf('hide_label') >= 0){
					parent.find('.hide-label-field').hide().find('.field-config').prop('checked', false);
				}
				if(fieldtype_defaults[select.val() + '_nosupport'].indexOf('caption') >= 0){
					parent.find('.caption-field').hide().find('.field-config').val('');
				}
				if(fieldtype_defaults[select.val() + '_nosupport'].indexOf('required') >= 0){
					parent.find('.required-field').hide().find('.field-config').prop('checked', false);
				}
				
			}

			// build template
			if(!config){
				config = {};
			}

			config._name = 'config[fields][' + parent.prop('id') + '][config]';


			template = $('<div>').html( template( config ) );

			// send to target
			target.html( template.html() );	

			// check for init function
			if( typeof window[select.val() + '_init'] === 'function' ){
				window[select.val() + '_init'](parent.prop('id'), target);
			}

			build_field_preview(parent.prop('id'));

			// rebind stuff
			rebuild_field_binding();

	}

	function build_field_preview(id){
		var panel 			= $('#' + id),
			select			= panel.find('.caldera-select-field-type'),
			preview_parent	= $('.layout-form-field[data-config="' + id + '"]'),
			preview_target	= preview_parent.find('.field_preview'),
			preview			= $('#preview-' + select.val() + '_tmpl').html(),
			template 		= Handlebars.compile(preview),
			config			= {'id': id},
			data_fields		= panel.find('.field-config'),
			objects			= [];

		data_fields.each(function(k,v){
			var field 		= $(v),
				basename 	= field.prop('name').split('[' + id + ']')[1].substr(1),
				name		= basename.substr(0, basename.length-1).split(']['),
				value 		= ( field.is(':checkbox,:radio') ? field.prop('checked') : field.val() ),
				lineconf 	= {};



			for(var i = name.length-1; i >= 0; i--){
				if(i === name.length-1){
					lineconf[name[i]] = value;
				}else{
					var newobj = lineconf;
					lineconf = {};
					lineconf[name[i]] = newobj;
				}		
			}
			$.extend(true, config, lineconf);
		});
			
		
		preview_target.html( template(config) );
		preview_parent.removeClass('button');

		$('.preview-field-config').prop('disabled', true);
	}

	// build sortables

	// switch active group
	function switch_active_group(id){
		var fields_panel	= $('.caldera-editor-fields-panel'),
			groups_panel	= $('.caldera-editor-groups-panel'),
			group_navs		= $('.caldera-group-nav'),
			group_line		= $('[data-group="'+ id +'"]'),
			group_name		= group_line.find('.caldera-config-group-name'),
			group_slug		= group_line.find('.caldera-config-group-slug'),
			group_desc		= group_line.find('.caldera-config-group-desc'),
			group_admin		= group_line.find('.caldera-config-group-admin'),
			group_name_edit	= $('.active-group-name'),
			group_slug_edit	= $('.active-group-slug'),
			group_desc_edit	= $('.active-group-desc'),
			group_admin_edit= $('.active-group-admin'),
			field_lists		= $('.caldera-editor-fields-list ul'),			
			group_repeat	= group_line.find('.caldera-config-group-repeat'),
			repeat_button	= $('.repeat-config-button'),
			group_settings	= $('.caldera-editor-group-settings'),
			deleter 		= $('.caldera-config-group-remove'),
			group_field_lists;

		// remove any hdden fields
		$('.new-group-input').remove();
		$('.new-field-input').remove();


		// remove current active group
		group_navs.removeClass('active');

		// show fields panel
		fields_panel.show();

		// hide all groups
		field_lists.hide();

		// remove active field
		field_lists.removeClass('active').find('li.active').removeClass('active');
		field_lists.hide();

		// set active group
		group_line.addClass('active');

		// hide delete button or show
		group_field_lists = $('.caldera-editor-fields-list ul.active li');

		if(group_field_lists.length){
			// has fields
			deleter.hide();
		}else{
			deleter.show();
		}


		// hide all field configs
		$('.caldera-editor-field-config-wrapper').hide();

		// show groups fields
		group_line.show();
		
		// set group name edit field
		group_name_edit.val(group_name.val());

		// set group slug edit field
		group_slug_edit.val(group_slug.val());

		// set group slug edit field
		group_desc_edit.val(group_desc.val());

		// set group admin edit field
		if(group_admin.val() === '1'){
			group_admin_edit.prop('checked', true);
		}else{
			group_admin_edit.prop('checked', false);
		}

		


		// is repeatable
		if(group_repeat.val() === '1'){
			repeat_button.addClass('field-edit-open');
		}else{
			repeat_button.removeClass('field-edit-open');
		}


	}


	// Change Field Type
	$('.caldera-editor-body').on('change', '.caldera-select-field-type', function(e){
		// push element to config function
		build_fieldtype_config(this);
	});

	// build group navigation
	$('.caldera-editor-body').on('click', '.caldera-group-nav a', function(e){

		// stop link
		e.preventDefault();

		//switch group
		switch_active_group( $(this).attr('href').substr(1) );

	});

	// build field navigation	
	$('.caldera-editor-body').on('click', '.caldera-editor-fields-list a', function(e){

		// stop link
		e.preventDefault();

		var clicked 		= $(this),
			field_config	= $( clicked.attr('href') );

		// remove any hdden fields
		$('.new-group-input').remove();
		$('.new-field-input').remove();


		// remove active field
		$('.caldera-editor-fields-list li.active').removeClass('active');

		// mark active
		clicked.parent().addClass('active');

		// hide all field configs
		$('.caldera-editor-field-config-wrapper').hide();

		// show field config
		field_config.show();

		//caldera-editor-fields-list

	});

	// build configs on load:
	// allows us to keep changes on reload as not to loose settings on accedental navigation
	$('.caldera-select-field-type').each(function(k,v){
		build_fieldtype_config(v);
	});

	// bind show group config panel
	$('.caldera-editor-body').on('click', '.group-config-button', function(e){
		var clicked = $(this),
			group_settings	= $('.caldera-editor-group-settings'),
			parent = clicked.closest('.caldera-editor-fields-panel'),
			deleter = $('.caldera-config-group-remove');

		// check if children
		if(parent.find('.caldera-field-line').length){
			// has fields
			deleter.hide();
		}else{
			deleter.show();
		}

		if(clicked.hasClass('field-edit-open')){
			// show config
			group_settings.slideUp(100);
			clicked.removeClass('field-edit-open');
		}else{
			// hide config
			group_settings.slideDown(100);
			clicked.addClass('field-edit-open');
		}

	});
	$('.caldera-editor-body').on('keydown', '.field-config', function(e){
		if($(this).is('textarea')){
			return;
		}
		if(e.which === 13){
			e.preventDefault();
		}
	});
	// field label bind
	$('.caldera-editor-body').on('keyup change', '.field-label', function(e){
		var field 		= $(this).closest('.caldera-editor-field-config-wrapper').prop('id');
			field_line	= $('[data-field="' + field + '"]'),
			field_title	= $('#' + field + ' .caldera-editor-field-title, .layout-form-field.field-edit-open .layout_field_name'),
			slug		= $('#' + field + ' .field-slug');

		field_line.find('a').html( '<i class="icn-field"></i> ' + this.value );
		field_title.text( this.value );
		if(e.type === 'change'){
			slug.trigger('change');
		}
		rebuild_field_binding();
	});


	// rename group
	$('.caldera-editor-body').on('keyup blur', '.active-group-name', function(e){
		e.preventDefault();
		var active_group		= $('.caldera-group-nav.active'),
			group				= active_group.data('group'),
			group_name			= active_group.find('.caldera-config-group-name'),
			group_label			= active_group.find('span');

		// check its not blank
		if(e.type === 'focusout' && !this.value.length){
			this.value = 'Group ' + ( parseInt( active_group.index() ) + 1 );
		}


		group_name.val(this.value);		
		group_label.text(this.value);

	});
	// rename group slug
	$('.caldera-editor-body').on('keyup blur', '.active-group-slug', function(e){
		e.preventDefault();

		var active_group		= $('.caldera-group-nav.active'),
			group				= active_group.data('group'),
			group_name			= active_group.find('.caldera-config-group-name').val(),
			group_slug			= active_group.find('.caldera-config-group-slug'),
			group_label			= active_group.find('span'),
			slug_sanitized		= this.value.replace(/[^a-z0-9]/gi, '_').toLowerCase();

		// check its not blank
		if(e.type === 'focusout' && !this.value.length){
			slug_sanitized = group_name.replace(/[^a-z0-9]/gi, '_').toLowerCase();
		}

		group_slug.val(slug_sanitized);
		this.value = slug_sanitized;

	});
	// rename group description
	$('.caldera-editor-body').on('keyup blur', '.active-group-desc', function(e){
		e.preventDefault();

		var active_group		= $('.caldera-group-nav.active'),
			group				= active_group.data('group'),
			group_desc			= active_group.find('.caldera-config-group-desc');

		group_desc.val(this.value);

	});

	// set group admin
	$('.caldera-editor-body').on('change', '.active-group-admin', function(e){
		e.preventDefault();

		var active_group		= $('.caldera-group-nav.active'),
			group				= active_group.data('group'),
			group_name			= active_group.find('.caldera-config-group-name').val(),
			group_admin			= active_group.find('.caldera-config-group-admin'),
			group_label			= active_group.find('span'),
			slug_sanitized		= this.value.replace(/[^a-z0-9]/gi, '_').toLowerCase();

		// check its not blank
		if($(this).prop('checked')){
			group_admin.val(1);
			active_group.addClass('is-admin');
		}else{
			group_admin.val(0);
			active_group.removeClass('is-admin');
		}

	});

	// set repeatable
	$('.caldera-editor-body').on('click', '.repeat-config-button', function(e){
		e.preventDefault();
		var active_group		= $('.caldera-group-nav.active'),
			group				= active_group.data('group'),
			icon				= active_group.find('a .group-type'),
			group_repeat		= active_group.find('.caldera-config-group-repeat'),
			clicked				= $(this);

		if(clicked.hasClass('field-edit-open')){
			// set static
			group_repeat.val('0');
			icon.removeClass('icn-repeat').addClass('icn-folder');
			clicked.removeClass('field-edit-open');
		}else{
			// set repeat
			group_repeat.val('1');
			icon.addClass('icn-repeat').removeClass('icn-folder');
			clicked.addClass('field-edit-open');
		}

	});

	// bind delete field
	$('.caldera-editor-body').on('click', '.delete-field', function(){
		var clicked = $(this),
			field	= clicked.parent().prop('id');

		// remove config
		$('#' + field).remove();

		// remove line
		$('[data-config="' + field + '"]').slideUp(200, function(){
			var line = $(this);

			// remove line 
			line.remove();
			rebuild_field_binding();
		});




	});


	// bind add new group button
	$('.caldera-editor-body').on('click', '.add-new-group,.add-field', function(){

		var clicked		= $(this);

		// remove any hdden fields
		$('.new-group-input').remove();
		$('.new-field-input').remove();

		if( clicked.hasClass( 'add-field' ) ){
			var field_input = $('<input type="text" class="new-field-input block-input">');
			field_input.appendTo( $('.caldera-editor-fields-list ul.active') ).focus();
		}else{
			var group_input = $('<input type="text" class="new-group-input block-input">');
			group_input.appendTo( $('.caldera-editor-groups-panel') ).focus();
		}
		
	});
	
	// dynamic group creation
	$('.caldera-editor-body').on('blur keypress', '.new-group-input', function(e){

		if(e.type === 'keypress'){
			if(e.which === 13){
				e.preventDefault();
			}else{
				return;
			}			
		}
		

		var group_name 	= this.value,
			input		= $(this),
			wrap		= $('.caldera-editor-groups-panel ul'),
			field_list	= $('.caldera-editor-fields-list'),
			new_templ,
			new_group;

		if( !group_name.length ){
			// no name- just remove the input
			input.remove();
		}else{
			new_templ = Handlebars.compile( $('#caldera_group_line_templ').html() );
			new_group = {
				"id"	:	group_name.replace(/[^a-z0-9]/gi, '_').toLowerCase(),
				"name"	:	group_name,
			};

			// place new group line
			wrap.append( new_templ( new_group ) );

			// create field list
			var new_list = $('<ul data-group="' + new_group.id + '">').hide();

			// place list in fields list
			new_list.appendTo( field_list );

			// init sorting
			

			// remove input
			input.remove();

			// swtich to new group
			switch_active_group( new_group.id );
		}

	});

	// dynamic field creation
	$('.caldera-editor-body').on('blur keypress', '.new-field-input', function(e){

		if(e.type === 'keypress'){
			if(e.which === 13){
				e.preventDefault();
			}else{
				return;
			}			
		}
		

		var new_name 	= this.value,
			input		= $(this),
			wrap		= input.parent(),
			field_conf	= $('.caldera-editor-field-config'),
			new_templ,
			new_conf_templ,
			new_field,
			deleter = $('.caldera-config-group-remove');

		if( !new_name.length ){
			// no name- just remove the input
			input.remove();
		}else{
			// hide delete group
			deleter.hide();
			// field line template
			new_templ = Handlebars.compile( $('#caldera_field_line_templ').html() );
			// field conf template
			new_conf_templ = Handlebars.compile( $('#caldera_field_config_wrapper_templ').html() );

			new_field = {
				"id"	:	new_name.replace(/[^a-z0-9]/gi, '_').toLowerCase(),
				"label"	:	new_name,
				"slug"	:	new_name.replace(/[^a-z0-9]/gi, '_').toLowerCase(),
				"group"	:	$('.caldera-group-nav.active').data('group')
			};

			var field = $(new_templ( new_field ));

			// place new field line
			field.appendTo( wrap );
			// pance new conf template
			field_conf.append( new_conf_templ( new_field ) );

			// init sorting
			

			// load field
			field.find('a').trigger('click');

			// remove input
			input.remove();

		}

	});

	// bind slug editing to keep clean
	$('.caldera-editor-body').on('change keyup', '.field-slug', function(e){
		if(this.value.length){
			this.value = this.value.replace(/[^a-z0-9]/gi, '_').toLowerCase();
		}else{
			if(e.type === 'change'){
				this.value = $(this).closest('.caldera-editor-field-config-wrapper').find('.field-label').val().replace(/[^a-z0-9]/gi, '_').toLowerCase();
			}
		}
	});

	// bind add group button
	$('.caldera-editor-body').on('click', '.caldera-add-group', function(e){

		var clicked 	= $(this),
			group		= clicked.data('group'),
			template	= $('#' + group + '_panel_tmpl').html();

		clicked.parent().parent().append(template);

	});
	// bind remove group button
	$('.caldera-editor-body').on('click', '.caldera-config-group-remove', function(e){

		var group = $('.active-group-slug').val();

		$('[data-group="' + group + '"]').hide(0, function(){
			$(this).remove();
			var navs = $('.caldera-group-nav');

			if(navs.length){
				navs.first().find('a').trigger('click');
			}else{
				$('.caldera-editor-fields-panel').hide();
			}
		});

	});

	$('body').on('click', '.set-current-field', function(e){

		e.preventDefault();

		var clicked = $(this);
		$('#' + clicked.data('field') + '_type').val(clicked.data('type')).trigger('change');
		
		$('#' + clicked.data('field') + '_lable').focus()

		$('#field_setup_baldrickModalCloser').trigger('click');


	});

	$('.caldera-editor-body').on('keyup change', '.field-config', function(e){

		var field 	= $(this),
			parent 	= field.closest('.caldera-editor-field-config-wrapper');

		if(parent.length){
			build_field_preview(parent.prop('id'));
		}

	});

	// init sorting
	

	// load fist  group
	$('.caldera-group-nav').first().find('a').trigger('click');


});//









