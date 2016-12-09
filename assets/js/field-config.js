
 /** Dynamic Field Configuration **/
 function Caldera_Forms_Field_Config( configs, $form, $ ){
     var self = this;

     this.init = function(){
         $.each( configs, function( i, config ){
             self[config.type]( config );
         } );
     };

     this.calculation = function( field ){

     };

     this.button = function( field ){
         var field_id  = field.id;
         $(document).on('click dblclick', '#' + field_id, function( e ){
             $('#' + field_id + '_btn').val( e.type ).trigger('change');
         });
     };

     this.gravatar = function( field ){

     };

     this.html = function ( fieldConfig ) {

         function templateSystem() {
             var template = $( document.getElementById( fieldConfig.tmplId ) ).html(),
                 $target = $( document.getElementById( fieldConfig.contentId ) ),
                 list = fieldConfig.binds;
             for (var i = 0; i < list.length; i++) {

                 var field = $('[data-field="' + list[i] + '"]'),
                     value = [];
                 for (var f = 0; f < field.length; f++) {
                     if ($(field[f]).is(':radio,:checkbox')) {
                         if (!$(field[f]).prop('checked')) {
                             continue;
                         }
                     }
                     if ($(field[f]).is('input:file')) {
                         var file_parts = field[f].value.split('\\');
                         value.push(file_parts[file_parts.length - 1]);
                     } else {
                         if (field[f].value) {
                             value.push(field[f].value);
                         }
                     }
                 }

                 template = template.replace(new RegExp("\{\{" + list[i] + "\}\}", "g"), value.join(', '));
             }

             $target.html(template).trigger('change');

         }


         $.each( fieldConfig.bindFields, function( i, id ){
             $( document.getElementById( id ) ).on( 'change keyup', templateSystem );
         });

         templateSystem();


     };

     this.range_slider = function( field ){
         var $el = $(document.getElementById(field.id));
         function init() {

             $el.rangeslider({
                 onSlide: function (position, value) {
                     value = value.toFixed(field.value);
                 },
                 onInit: function () {
                     $el.parent().find('.rangeslider').css('backgroundColor', field.trackcolor );
                     $el.parent().find('.rangeslider__fill').css('backgroundColor',  field.color );
                     $el.parent().find('.rangeslider__handle').css('backgroundColor', field.handle ).css('borderColor', field.handleborder );
                 },
                 polyfill: false
             });
         }


         $el.on('change', function(){
             $('#' + field.id + '_value').html(this.value);
         }).css("width", "100%");


         $(document).on('cf.pagenav cf.add cf.disable cf.modal', function(){
             init();
         });

         init();

     };

     this.select2 = function( field ){

     };

     this.star_rating = function( field ){
         var $el = $( document.getElementById( field.id ) );
         function init(){
             var options = field.options;
             options[ 'click' ] = function(){
                 $el.trigger( 'change' );
             };
             $el.raty(
                 options
             );
         }
         init();
         jQuery( document ).on('cf.add', init );
     };

     this.toggle_switch = function( field ) {
         $( document ).on('reset', '#' + field.id, function(e){
             $.each( field.options, function( i, option ){
                 $( document.getElemenetById( option ) ).removeClass( field.selectedClassName ).addClass( field.defaultClassName );
             });
             $( document.getElementById( field.id )).prop('checked','');
         } );
     };


     this.phone_better = function( field ){

         var $field = $( document.getElementById( field.id ) );
         var $parent = $field.parent().parent();

         var validatorName = 'phone_better_validator-' + field.validator;
         $( document ).on( 'cf.fieldsInit', function(e){
             cf_validate_form.parsely.addValidator( field.validator,
                 function (v) {
                     return valid();

                 }, 32 )
                 .addMessage('en', 'myvalidator', 'my validator failed');
         } );

         $field.intlTelInput( field.options );
         $form.on( 'submit', function(e){
             validation();
         });

         $field.on( 'change', function(){
             validation();
         });

         var validation = function(){
             var error = document.getElementById( 'cf-error-'+ field.id );
             if(  null != error ){
                 error.remove();
             }

             if( valid() ){
                 $parent.addClass( 'has-error' ).append( '<span id="cf-error-'+ field.id +'" class="help-block help-block-phone_better">' + field.options.invalid  + '</span>' );
                 return false;
             }else{
                 $parent.removeClass( 'has-error' );
                 return true;
             }

         };

         function valid(){
             return false == $field.intlTelInput("isValidNumber") && 0 != $field.intlTelInput("getValidationError");
         }
     };

     this.wysiwyg = function( field ){

         var actual_field = document.getElementById( field.id );
         if( null != actual_field ){
             var $field = $( actual_field );
             $field.trumbowyg(field.options);
             var $editor = $field.parent().find( '.trumbowyg-editor');

             $editor.html( $field.html() );
             $editor.bind('input propertychange', function(){
                 $field.html( $editor.html() );
             });
         }

     };
 }