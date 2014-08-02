jQuery(function($){



	function build_sortables(){
		// set sortable groups
		$( ".caldera-editor-processors-panel ul" ).sortable({
			update: function(){
				rebuild_field_binding();
			}
		});

	}

	// set active processor editor
	$('body').on('click', '.caldera-processor-nav a', function(e){

		e.preventDefault();

		var clicked = $(this);

		$('.caldera-processor-nav').removeClass('active');
		$('.caldera-editor-processor-config-wrapper').hide();
		$( clicked.attr('href') ).show();
		clicked.parent().addClass('active');

	});

	$('body').on('click', '.add-new-processor', function(e){
		
		var clicked = $(this),
			new_conf_templ = Handlebars.compile( $('#processor-wrapper-tmpl').html() );
			wrap = $('.active-processors-list'),
			process_conf = $('.caldera-editor-processor-config'),
			processid = Math.round(Math.random() * 100000000);

		new_templ = Handlebars.compile( $('#processor-line-tmpl').html() );
		new_proc = {
			"id"	:	"fp_" + processid,
			"type"	:	clicked.data('type')
		};

		// place new group line
		wrap.append( new_templ( new_proc ) );

		// place config
		process_conf.append( new_conf_templ( new_proc ) );

		// reset sortable		
		$('#form_processor_baldrickModalCloser').trigger('click');
		$('.caldera-processor-nav a').last().trigger('click');
		$('#fp_' + processid + '_type').val(clicked.data('type')).trigger('change');
		build_sortables();
		//rebuild_field_binding();

		baldrickTriggers();
	});

	// remove processor	
	$('body').on('click', '.delete-processor', function(e){
		
		var clicked = $(this),
			parent = clicked.closest('.caldera-editor-processor-config-wrapper'),
			type = parent.data('type');

		if(!confirm(clicked.data('confirm'))){
			return;
		}

		$('.' + parent.prop('id')).remove();
		parent.remove();

		$('.caldera-processor-nav a').first().trigger('click');

		//check_required_bindings();
		rebuild_field_binding();

	});

	// set title & config of selected processor	
	$('body').on('change', '.caldera-select-processor-type', function(e){
		var selected = $(this),
			parent = selected.closest('.caldera-editor-processor-config-wrapper'),
			title = selected.find('option[value="'+selected.val()+'"]').text(),
			title_line = parent.find('.caldera-editor-processor-title'),
			activeline = $('.caldera-processor-nav.active a');

		if(title === ''){
			title = title_line.data('title');
		}

		title_line.html( title );
		activeline.html( title + ' <span class="processor-line-number"></span>' ).parent().addClass( 'processor_type_' + selected.val() );

		// get config
		build_processor_config(this);

		//check_required_bindings();
		rebuild_field_binding();

	});


	// build processor type config
	function build_processor_config(el){

		var select 			= $(el),
			templ			= $('#' + select.val() + '-tmpl').length ? $('#' + select.val() + '-tmpl').html() : '',
			parent			= select.closest('.caldera-editor-processor-config-wrapper'),
			target			= parent.find('.caldera-config-processor-setup'),
			template 		= Handlebars.compile(templ),
			config			= parent.find('.processor_config_string').val(),
			current_type	= select.data('type');

			// Be sure to load the processors preset when switching back to the initial processor type.
			if(config.length && current_type === select.val() ){
				config = JSON.parse(config);
			}else{
				// default config
				config = processor_defaults[select.val() + '_cfg'];
			}

			// build template
			if(!config){
				config = {};
			}

			config._id = parent.prop('id');
			config._name = 'config[processors][' + parent.prop('id') + '][config]';


			

			template = $('<div>').html( template( config ) );

			// send to target
			target.html( template.html() );	

			// check for init function
			if( typeof window[select.val() + '_init'] === 'function' ){
				window[select.val() + '_init'](parent.prop('id'), target);
			}

		// check if conditions are allowed			
		if(parent.find('.no-conditions').length){
			// conditions are not supported - remove them
			parent.find('.toggle_option_tab').remove();
		}


		rebuild_field_binding();
		baldrickTriggers();
	}

	// build configs on load:
	// allows us to keep changes on reload as not to loose settings on accedental navigation
	
	rebuild_field_binding();

	$('.caldera-select-processor-type').each(function(k,v){
		build_processor_config(v);
	});


	build_sortables();
});//


// field binding helper
Handlebars.registerHelper('_field', function(args) {

	var config = this,required="", is_array = "", exclude="";

	var default_val = this[args.hash.slug] ? ' data-default="' + this[args.hash.slug] + '"' : '';

	if(args.hash.required){
		required = " required";
	}
	if(args.hash.exclude){
		exclude = 'data-exclude="'+args.hash.exclude+'"';
	}
	if(args.hash.array){
		is_array = "[]";
		if(args.hash.array !== 'true'){
			default_val = 'value="' + args.hash.array + '"';
		}
	}

	out = '<select ' + ( args.hash.type ? 'data-type="' + args.hash.type + '"' : '' ) + default_val +' ' + exclude + ' name="' + this._name + '[' + args.hash.slug + ']' + is_array + '" id="' + this._id + '_' + args.hash.slug + '" class="block-input field-config caldera-field-bind' + required + '">';
	//out = '<input type="text" ' + ( args.hash.type ? 'data-type="' + args.hash.type + '"' : '' ) + default_val +' ' + exclude + ' name="' + this._name + '[' + args.hash.slug + ']' + is_array + '" id="' + this._id + '_' + args.hash.slug + '" class="block-input field-config magic-tag-enabled caldera-field-bind' + required + '">';
	
	if(!args.hash.required){
		out += '<option value=""></option>';
	}

	for(var fid in current_form_fields){
		
		var sel = '';
		
		if(args.hash.type){
			if(current_form_fields[fid].type !== args.hash.type){
				continue;
			}
		}

		if(config[args.hash.slug]){
			if(config[args.hash.slug] === fid){
				sel = ' selected="selected"';
			}
		}
		

		out += '<option value="' + fid + '"' + sel + '>' + current_form_fields[fid].label + ' [' + current_form_fields[fid].slug + ']</option>';
	};

	out += '</select>';
	if(args.hash.required){
		out += '<input class="field-config" name="' + this._name + '[_required_bounds][]" type="hidden" value="' + args.hash.slug + '">';
	}
	return out;
});

Handlebars.registerHelper('console', function(context, options) {
	console.log(this);
});




