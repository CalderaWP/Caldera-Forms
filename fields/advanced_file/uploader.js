var cf_uploader_filelist = {};
function size_format(bytes) {
	var converted = false;
	quant = [
	{
		unit: 'TB', 
		mag: 1099511627776
	},

	{
		unit: 'GB', 
		mag: 1073741824
	},

	{
		unit: 'MB', 
		mag: 1048576
	},

	{
		unit: 'kB', 
		mag: 1024
	},

	{
		unit: 'B ', 
		mag: 1
	}
	];
	quant.forEach(function(v){
		if (parseFloat(bytes) >= v.mag && converted == false){
			converted = bytes/v.mag;
			if( bytes > 1048576 ){
				converted = converted.toFixed(2);
			}else{
				converted = Math.round( converted );
			}
			converted = converted +' '+v.unit;
		}
	});
	return converted;
}

  function handleFileSelect( evt, config ) {
	evt.stopPropagation();
	evt.preventDefault();
	if(evt.dataTransfer){
		var files = evt.dataTransfer.files;
	}else{
		var files = evt.target.files;
	}
	// files is a FileList of File objects. List some properties.
	var output = [], validator = 'valid';
	// get length
	for (var i = 0; i < files.length ; i++) {
	 var id = 'fl' + Math.round(Math.random() * 187465827348977),
		state = 1,
		error = '';
		if( config.allowed.length ){
			if( config.allowed.indexOf( files[ i ].type ) < 0 ){
				state = 0;
				error = config.notices.invalid_filetype;
			}
		}
		if( config.max_size ){
			if( files[ i ].size > config.max_size ){
				state = 0;
				error = config.notices.file_exceeds_size_limit;
			}
		}
		if( ! files[ i ].size ){
			state = 0;
			error = config.notices.zero_byte_file;
		}

	  cf_uploader_filelist[ evt.target.id + '_file_' + id ] = {
			file : files[ i ],
			state : state,
			field : config.id,
			message : error
		};
	}
	// do preview
	for( var i in cf_uploader_filelist ){
		if( cf_uploader_filelist[i].field !== config.id ){ continue; }
		var state_class = '',
		error_message = '';
		if( cf_uploader_filelist[ i ].state === 0 ){
			state_class = 'has-error';
	  	}      

	  output.push('<li class="cf-uploader-queue-item ' + i + ' ' + state_class + '">',
				  '<a href="#remove-file" data-file="' + i + '" class="cf-file-remove">&times;</a> <span class="file-name">', cf_uploader_filelist[ i ].file.name, '</span>&nbsp;',
				  '<div class="progress-bar" style="background:#ececec;"><div class="bar" id="progress-file-' + i + '" style="height:2px;width:0%;background:#a3be5f;"></div></div>',                  
				  '<small class="file-type">', cf_uploader_filelist[ i ].file.type || 'n/a', '</small> ',
				  '<small class="file-size">' + size_format( cf_uploader_filelist[ i ].file.size ) + '</small>',
				  '<small class="file-error">' + cf_uploader_filelist[ i ].message + '</small>',
				  '</li>');
		if( cf_uploader_filelist[ i ].message.length ){
			validator = cf_uploader_filelist[ i ].message;
		}
	}
	evt.target.value = null;

	document.getElementById( evt.target.id + '_file_list' ).innerHTML = '<ul class="cf-adv-preview-list">' + output.join('') + '</ul>';

	jQuery( '#' + evt.target.id + '_validator' ).val( validator );
  }

  function handleDragOver(evt) {
	evt.stopPropagation();
	evt.preventDefault();
	evt.dataTransfer.dropEffect = 'copy'; // Explicitly show this is a copy.
  }

function handleDragOver(event) {
	event.stopPropagation();
	event.preventDefault();
	event.dataTransfer.dropEffect = 'copy';
}

jQuery( function( $ ){
	$( document ).on('click', '.cf-uploader-trigger', function(){
		var clicked = $(this);
		$( '#' + clicked.data('parent') ).trigger('click');
	});
	$('.cf-multi-uploader').hide();
	$( document ).on('click', '.cf-file-remove', function( e ){
		e.preventDefault();
		var clicked = $( this ),
			list = clicked.closest('.cf-adv-preview-list'),
			field = clicked.closest('.cf-multi-uploader-list').data('field'),
			field_id = clicked.closest('.cf-multi-uploader-list').data('id'),
			validator = $('#' + field_id + '_validator');

		validator.val('');

		$('[data-parent="' + field + '"]').show();
		delete cf_uploader_filelist[ clicked.data('file') ];
		clicked.closest('.cf-multi-uploader-list').parent().find('.cf-uploader-trigger').show();
		clicked.parent().remove();
		if( ! list.children().length ){
			list.remove();
		}

		for( var fid in cf_uploader_filelist ){
			if( cf_uploader_filelist[ fid ].field === field_id && cf_uploader_filelist[ fid ].message.length ){
				validator.val( cf_uploader_filelist[ fid ].message );
			}
		}

	});    

	$( document ).on('change', '.cf-multi-uploader', function( e ){
		var field = $(this),
			config = field.data('config');
		config.id = field.prop('id');
		if( !field.prop( 'multiple' ) ){
			if ('object' != typeof  cf_uploader_filelist) {
				cf_uploader_filelist = {};
			}
			field.parent().find('.cf-uploader-trigger').hide();
		}
		handleFileSelect( e, config );
	});

	window.Parsley
	.addValidator('fileType', {
	requirementType: 'string',
	validateString: function( value, requirement ) {
	  if( value === 'valid' ){
	  	return true;
	  }
	  return false;
	}
	});

})