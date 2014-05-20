jQuery('document').ready(function($){

	//toggle_option_row

	$('.caldera-editor-body').on('click', '.add-toggle-option', function(e){

		var clicked		= $(this),
			wrapper		= clicked.closest('.caldera-editor-field-config-wrapper'),
			toggle_rows	= wrapper.find('.toggle-options'),
			row			= $('<div>').html( $('#toggle_switch_tmpl').html() ).find('.toggle-options').html(),
			template	= Handlebars.compile( row ),
			key			= "opt" + parseInt( ( Math.random() + 1 ) * 0x100000 ),
			config		= {
				_name	:	'config[fields][' + wrapper.prop('id') + '][config]',
				option	: {}
			};

			console.log(row);

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


	});

	// remove an option row
	$('.caldera-editor-body').on('click', '.toggle-remove-option', function(e){
		var triggerfield = $(this).closest('.caldera-editor-field-config-wrapper').find('.field-config').first();
		$(this).parent().remove();
		triggerfield.trigger('change');
		

	});


	// set default option
	$('.caldera-editor-body').on('change', '.toggle_set_default', function(e){

		var option 	= $(this),
			checked	= option.prop('checked');

		if(checked){
			option.closest('.caldera-config-field-setup').find('.toggle_set_default').prop('checked', false);
			option.prop('checked', true);
		}

	});

	$('.toggle-options').sortable({
		handle: ".dashicons-sort"
	});

});


function toggle_switch_init(id, target){

	jQuery('.toggle-options').sortable({
		handle: ".dashicons-sort"
	});
	
}