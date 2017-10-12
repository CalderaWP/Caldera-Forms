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

              } catch (e) {}
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
      if( ! $('script[src^="' + cfModals.script[ script ] + '"]').length && null !== cfModals.script[ script ] ){
        body.append('<script src="' + cfModals.script[ script ] + '" type="text/javascript">');
      }
    }

    function cf_modals_load_form( form_modal ){

      if( !form_modal.data('form') ){return;}

      var url = '/cf-api/' + form_modal.data('form') + '/';
      if( form_modal.data('entry') ){
        url += form_modal.data('entry') + '/';
      } 
      // set form instance count
      url += '?cf_instance=' + ( $('.caldera_forms_form.' + form_modal.data('form') ).length + 1 );
      if( form_modal.data('width') ){
        form_modal.css({ width: form_modal.data('width') } );
      }else{
        form_modal.css({ width: false } );
      }    
      $.get( url, function( data ){
                    
        $('#modal-' + form_modal.data('form') + '-content').html( data );
        resBaldrickTriggers();
        if(typeof caldera_conditionals !== 'undefined'){
          calders_forms_init_conditions();
        }        

      } );
    }

    // place in modal templates
    $('.caldera-forms-modal').each( function(){
      var form_modal = $(this),
          form_id = form_modal.data('form');
          var entry = '';
          if( form_modal.data('entry') ){
            entry += 'data-entry="' + form_modal.data('entry') + '"';
          }
          if( form_modal.data('width') ){
            entry += 'data-width="' + form_modal.data('width') + '"';
          }          
          $('body').append('<div class="remodal" data-form="' + form_id + '" ' + entry + ' data-remodal-id="modal-' + form_id + '" id="modal-' + form_id + '"><button data-remodal-action="close" class="remodal-close"></button><div class="modal-content" id="modal-' + form_id + '-content"><span class="caldera-grid cf_processing cf_modal"></span></div></div>');
          cf_modals_load_form( $('#modal-' + form_id) );

    });

    $(document).on('opened', '.remodal', function () {
      $(document).trigger('cf.modal', $( this ) );
    });

    $(document).on('cf.submission', function (e, obj ) {      
      setTimeout( function(){
        var inst = $('[data-remodal-id="modal-' + obj.data.form_id + '"]').remodal();
        cf_modals_load_form( inst.$modal );
        if( inst.getState() === 'opened' ){
          inst.close();
        }
      }, 1500 );
    });    

  }

})(jQuery);