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

  function handleFileSelect(evt) {
    evt.stopPropagation();
    evt.preventDefault();
    if(evt.dataTransfer){
        var files = evt.dataTransfer.files;
    }else{
        var files = evt.target.files;
    }
    // files is a FileList of File objects. List some properties.
    var output = [];
    // get length
    for (var i = 0; i < files.length ; i++) {
     var id = 'fl' + Math.round(Math.random() * 187465827348977);
      cf_uploader_filelist[ evt.target.id + '_file_' + id ] = {
            file : files[ i ],
            state : 1
        };
    }
    // do preview
    for( var i in cf_uploader_filelist ){
      output.push('<li class="cf-uploader-queue-item ' + i + '">',
                  '<a href="#remove-file" data-file="' + i + '" class="cf-file-remove">&times;</a> <span class="file-name">', cf_uploader_filelist[ i ].file.name, '</span>',
                  '<div class="progress-bar" style="background:#ececec;"><div class="bar" id="progress-file-' + i + '" style="height:2px;width:0%;background:#a3be5f;"></div></div>',                  
                  '<small class="file-type">', cf_uploader_filelist[ i ].file.type || 'n/a', '</small> ',
                  '<small class="file-error"></small>',
                  '<small class="file-size">' + size_format( cf_uploader_filelist[ i ].file.size ) + '</small>',
                  '</li>');
    }
    evt.target.value = null;

    document.getElementById( evt.target.id + '_file_list' ).innerHTML = '<ul>' + output.join('') + '</ul>';
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
        var clicked = $( this );
        delete cf_uploader_filelist[ clicked.data('file') ];
        clicked.parent().remove();
    });    

    $( document ).on('change', '.cf-multi-uploader', function( e ){
        handleFileSelect( e );
    });

    //var dropTarget = document.getElementById( element );
    //dropTarget.addEventListener('dragover', handleDragOver, false);
    //dropTarget.addEventListener('drop', handleFileSelect, false);
    //var selectorButton = document.getElementById( element );
    //selectorButton.addEventListener('change', handleFileSelect, false);


})