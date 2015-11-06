/* formjson module */
(function($){
  
$.fn.formJSON = function(){
    var form = $(this);

    var fields       = form.find('[name]'),
        json         = {},
        arraynames   = {};
    for( var v = 0; v < fields.length; v++){
      var field     = $( fields[v] ),
        name    = field.prop('name').replace(/\]/gi,'').split('['),
        value     = field.val(),
        lineconf  = {};

        if( field.is(':radio') || field.is(':checkbox') ){
          if( !field.is(':checked') ){
            continue;
          }
        }

      for(var i = name.length-1; i >= 0; i--){
        var nestname = name[i];
        if( typeof nestname === 'undefined' ){
          nestname = '';
        }
        if(nestname.length === 0){
          lineconf = [];
          if( typeof arraynames[name[i-1]] === 'undefined'){
            arraynames[name[i-1]] = 0;
          }else{
            arraynames[name[i-1]] += 1;
          }
          nestname = arraynames[name[i-1]];
        }
        if(i === name.length-1){
          if( value ){
            if( value === 'true' ){
              value = true;
            }else if( value === 'false' ){
              value = false;
            }else if( !isNaN( parseFloat( value ) ) && parseFloat( value ).toString() === value ){
              value = parseFloat( value );
            }else if( typeof value === 'string' && ( value.substr(0,1) === '{' || value.substr(0,1) === '[' ) ){
              try {
                value = JSON.parse( value );

              } catch (e) {
                //console.log( e );
              }
            }else if( typeof value === 'object' && value.length && field.is('select') ){
              var new_val = {};
              for( var i = 0; i < value.length; i++ ){
                new_val[ 'n' + i ] = value[ i ];
              }

              value = new_val;
            }
          }
          lineconf[nestname] = value;
        }else{
          var newobj = lineconf;
          lineconf = {};
          lineconf[nestname] = newobj;
        }   
      }
      $.extend(true, json, lineconf);
    };

    return json;
  }

  /* new button handler */
  $('.caldera_forms_form').on('click','.cf-form-trigger', function( ev ){
    var clicked = $(this);
        form = clicked.closest('form.caldera_forms_form'),
        form_id = form.prop('id'),
        calderaforms = window[ form_id ],
        data = form.formJSON(),
        fields = {},
        target = clicked.data('target');

        for( var field in calderaforms ){
          fields[ calderaforms[ field ].slug ] = data[ field ];
        }

        // check target
        if( typeof window[ target ] === 'function' ){
          window[ target ]( fields, ev );
        }else{
          
          try {
            var elements = $( target );
          } catch (err) {}
          
          if( elements && elements.length ){
            fields = JSON.stringify( fields ); // make into string
            var inputTypes = ['textarea','text','hidden'];
            elements.each( function(k,v){
              if( this.type && inputTypes.indexOf( this.type ) >= 0 ){
                // form fields
                $(this).val( fields ).trigger('change');
              }else{
                this.innerHTML = fields;
              }
            });
          }else{
            // assume a URL
            $.post( target, fields );
          }
        }
  });
  
  /* setup modals system */
  if( cfModals ){

    var head = $('head'),
        body = $('body');

    for( var style in cfModals.style ){
      if( ! $('#cf-' + style + '-styles-css').length ){
        head.append('<link id="cf-' + style + '-styles-css" rel="stylesheet" type="text/css" href="' + cfModals.style[ style ] + '">');
      }
    }
    for( var script in cfModals.script ){
      if( ! $('script[src^="' + cfModals.script[ script ] + '"]').length ){
        body.append('<script src="' + cfModals.script[ script ] + '" type="text/javascript">');
      }
    }

    $( document ).on( 'click', '.caldera-forms-modal', function(){
      var trigger = $( this );
      if( !trigger.data('form') ){return;}
      var modal = trigger.calderaModal({
        modal : trigger.data('form'),
        width: 100,
        height: 100,
        content : function(){
          $.get( '/cf-api/' + trigger.data('form') + '/', function(res){
            var modalWrapper = $('#' + trigger.data('form') + '_calderaModalContent');
            modal.config.width = trigger.data('width') ? trigger.data('width') : 500;
            modal.resize();
            modalWrapper.html( res );
            resBaldrickTriggers();

            $(document).on('cf.modal cf.remove cf.add cf.submission cf.pagenav cf.error', function(){                            
              if( trigger.data('height') ){
                modal.config.hegiht = trigger.data('height')
              }else{
                modal.config.height = modalWrapper.outerHeight() + modal.config.padding;
              }
              modal.resize();
            });
            $(document).trigger('cf.modal');
          } );
          return '<div class="caldera-grid cf_processing" style="width: 75px; height: 75px;"></div>';
        }
      });



    });

  }

})(jQuery);