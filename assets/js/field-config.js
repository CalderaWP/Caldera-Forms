/**
 * Dynamic Field Configuration
 *
 * @since 1.5.0
 *
 * @param configs
 * @param $form
 * @param $
 * @constructor
 */
 function Caldera_Forms_Field_Config( configs, $form, $ ){
     var self = this;

     var fields = {};

     var formInstance = $form.data( 'instance' );

     var $submits = $form.find(':submit, .cf-page-btn-next' );

     /**
      * Start system
      *
      * @since 1.5.0
      */
     this.init = function(){
         $.each( configs, function( i, config ){
             fields[ config.id ] = self[config.type]( config );
         } );
     };

     /**
      * Validation handler for adding/removing errors for field types
      *
      * @since 1.5.0
      *
      * @param valid
      * @param $field
      * @param message
      * @param extraClass
      * @returns {boolean}
      */
     function handleValidationMarkup( valid, $field, message, extraClass ){
         var $parent = $field.parent().parent();
         $parent.removeClass( 'has-error' );
         $parent.find( '.help-block' ).remove();
         if( ! valid ){
             $parent.addClass( 'has-error' ).append( '<span id="cf-error-'+ $field.attr('id') +'" class="help-block ' + extraClass +'">' + message  + '</span>' );
             if ( $field.prop( 'required' ) ) {
                 disableAdvance();
             }
             $field.addClass( 'parsely-error' );
             return false;
         }else{
             $parent.removeClass( 'has-error' );
             allowAdvance();
             return true;
         }
     }

     /**
      * Utility method for preventing advance (next page/submit)
      *
      * @since 1.5.0
      */
     function disableAdvance(){
         $submits.prop( 'disabled',true).attr( 'aria-disabled', true  );
     }

     /**
      * Utility method for allowing advance (next page/submit)
      *
      * @since 1.5.0
      */
     function allowAdvance(){
         $submits.prop( 'disabled',false).attr( 'aria-disabled', false  );
     }


     /**
      * Handler for button fields
      *
      * @since 1.5.0
      *
      * @param field
      */
     this.button = function( field ){
         var field_id  = field.id;
         $(document).on('click dblclick', '#' + field_id, function( e ){
             $('#' + field_id + '_btn').val( e.type ).trigger('change');
         });
     };


     /**
      * Handler for HTML fields (and summary fields since this.summary is alias of this.html)
      *
      * @since 1.5.0
      *
      * @param fieldConfig
      */
     this.html = function ( fieldConfig ) {
         if( false == fieldConfig.sync ){
             return;
         }

         var templates = {},
             list = fieldConfig.binds,
			 theBindFields = [];

         /**
          * The actual template system for HTML/summary fields
          *
          * @since 1.5.0
          */
         function templateSystem() {
             if( undefined == templates[ fieldConfig.tmplId ] ){
                 templates[ fieldConfig.tmplId ] = $( document.getElementById( fieldConfig.tmplId ) ).html()
             }


             var template = templates[ fieldConfig.tmplId ],
                 $target = $( document.getElementById( fieldConfig.contentId ) );

             for (var i = 0; i < list.length; i++) {

				 var $field = $form.find('[data-field="' + list[i] + '"]'),
                     value = [];
				 if( ! $field.length ){
				 	$field = $form.find('[data-field="' + list[i] + '_' + formInstance + '"]');
				 }
				 if( $field.length ){
					 theBindFields.push( $field );
				 }

				 for (var f = 0; f < $field.length; f++) {
                     if ($($field[f]).is(':radio,:checkbox')) {
                         if (!$($field[f]).prop('checked')) {
                             continue;
                         }
                     }
                     if ($($field[f]).is('input:file')) {
                         var file_parts = $field[f].value.split('\\');
                         value.push(file_parts[file_parts.length - 1]);
                     } else {
                         if ($field[f].value) {
                             value.push($field[f].value);
                         }
                     }
                 }

                 value = value.join(', ');
                 if( 'string' === typeof  value ){
                     value = value.replace(/(?:\r\n|\r|\n)/g, '<br />');
                 }

                 template = template.replace(new RegExp("\{\{" + list[i] + "\}\}", "g"), value );
             }

             $target.html(template).trigger('change');

         }

         /**
          * On change/keyup events of fields that are used by this field.
          *
          * @since 1.5.0.7 -based on legacy code
          */
         function bindFields() {
			 theBindFields.forEach( function ($field) {
				 $field.on('click keyup change', templateSystem);
             });
         }

         /**
          * Rebind on conditional and page nav
          */
         $(document).on('cf.pagenav cf.add cf.disable', function () {
             bindFields();
         });

		 templateSystem();

		 bindFields();


     };

     /**
      * Handler to summary fields
      *
      * A copy of handler for HTML fields
      *
      * @since 1.5.0
      *
      * @type {any}
      */
     this.summary = this.html;

    var rangeSliders = {};

     /**
      * Handler for range slider fields
      *
      * @since 1.5.0
      *
      * @param field
      */
     this.range_slider = function( field ){
         var $el = $(document.getElementById(field.id));

         function init() {
             if ('object' != rangeSliders[field.id]) {
                 rangeSliders[field.id] = {
                     value: field.default,
                     init: {},
					 inited : false
                 };
             }

             var init = {
				 onSlide: function (position, value) {
					 if (!$el.is(':visible') || ! rangeSliders[field.id].inited ) {
						 return;
					 }
					 rangeSliders[field.id].value = value;
					 value = value.toFixed(field.value);
					 $('#' + field.id + '_value').html(value);
				 },
                 onInit: function () {
					 if (!$el.is(':visible')) {
						 return;
					 }
					 rangeSliders[field.id].inited = true;
                     this.value = rangeSliders[field.id].value;
                     $el.parent().find('.rangeslider').css('backgroundColor', field.trackcolor);
                     $el.parent().find('.rangeslider__fill').css('backgroundColor', field.color);
                     $el.parent().find('.rangeslider__handle').css('backgroundColor', field.handle).css('borderColor', field.handleborder);
                 },
                 polyfill: false
             };

             $el.rangeslider(init);
             rangeSliders[field.id].init = init;


         }


         $el.on('change', function () {
             $('#' + field.id + '_value').html(this.value);
			 rangeSliders[field.id].value = this.value;
         }).css("width", "100%");


         $(document).on('cf.pagenav cf.add cf.disable cf.modal', function () {
             var el = document.getElementById(field.id);
             if (null != el) {
                 var $el = $(el),
					 doChange = false,
                     val = $el.val();
				 if (!$el.is(':visible')) {
					 return;
				 }

				 if( rangeSliders[field.id].inited ){
				 	doChange = true;
				 }

				 $el.rangeslider('destroy');
				 $el.rangeslider(rangeSliders[field.id].init);
				 if ( doChange ) {
					 $el.val(val).change();
				 }else{
					 $el.val(field.default).change();
				 }

             }
         });

		 init();


     };

     /**
      * Handler for star ratings fields
      *
      * @since 1.5.0
      *
      * @param field
      */
     this.star_rating = function( field ){

         var score = field.options.score;
         var $el = $( document.getElementById( field.starFieldId ) );
         var $input = $( document.getElementById( field.id ) );
         var init =  function(){
             var options = field.options;

             options[ 'click' ] = function(){
                 score = $el.raty('score');
                 $el.trigger( 'change' );
             };
             $el.raty(
                 options
             );


             $el.raty('score', score );
         };

         init();
         var updating = false;
        jQuery( document ).on('cf.add', function(){

            if( false === updating ){
                updating = true;
                if( $el.length ){
                    $el.raty( 'destroy' );
                    init();
                }
                setTimeout(function(){
                    updating = false
                }, 500 );
            }



        } );
     };

     /**
      * Handler for new toggle swich fields
      *
      * @since 1.5.0
      *
      * @param field
      */
     this.toggle_switch = function( field ) {
         $( document ).on('reset', '#' + field.id, function(e){
             $.each( field.options, function( i, option ){
                 $( document.getElemenetById( option ) ).removeClass( field.selectedClassName ).addClass( field.defaultClassName );
             });
             $( document.getElementById( field.id )).prop('checked','');
         } );
     };

     /**
      * Handler for new phone fields
      *
      * @since 1.5.0
      *
      * @param field
      */
     this.phone_better = function( field ){

         var $field = $( document.getElementById( field.id ) );


         var reset = function(){
             var error = document.getElementById( 'cf-error-'+ field.id );
             if(  null != error ){
                 error.remove();
             }
         };

         var validation = function () {
             reset();
             var valid;
             if ($.trim($field.val())) {
                 if ($field.intlTelInput("isValidNumber")) {
                     valid = true;
                 } else {
                     valid = false;
                 }
             }

             var message;
             var errorCode = $field.intlTelInput("getValidationError");
             if (0 == errorCode) {
                 valid = true;
                 message = '';
             } else {
                 if ('undefined' != field.messages[errorCode]) {
                     message = field.messages[errorCode]
                 } else {
                     message = field.messages.generic;
                 }
             }


             handleValidationMarkup(valid, $field, message, 'help-block-phone_better');
             return valid;
         };

         var init = function() {
             if( ! $field.length ){
                 $field = $( document.getElementById( field.id ) );
             }

             $field.intlTelInput( field.options );
             $field.on( 'keyup change', reset );

             $field.blur(function() {
                 reset();
                 validation();
             });

             $field.on( 'change', validation );
             $form.on( 'submit', function(){
                 validation();
             })

         };

         $(document).on('cf.pagenav cf.add cf.disable cf.modal', init );

         init();



     };

     /**
      * Handler for WYSIWYG fields
      *
      * @since 1.5.0
      *
      * @param field
      */
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

     /**
      * Handler for credit card fields
      *
      * @since 1.5.0
      *
      * @param fieldConfig
      */
     this.credit_card_number = function( fieldConfig ){
         var $field = $( document.getElementById( fieldConfig.id ) );

         if( false != fieldConfig.exp || false != fieldConfig.cvc ){
             setupLink();
         }

         if( $field.length ){
             $field.payment('formatCardNumber');
             $field.blur( function(){
                 var val =  $field.val();
                 var valid = $.payment.validateCardNumber( val );
                 var type = $.payment.cardType(val);
                 handleValidationMarkup( valid, $field, fieldConfig.invalid, 'help-block-credit_card_number help-block-credit_card' );
                 if( valid ){
                     setImage( type );
                 }
             })
         }

         /**
          * Link fields in credit card group
          *
          * @since 1.5.0
          *
          */
         function setupLink(){
             disableAdvance();
             var $cvcField = $( document.getElementById( fieldConfig.cvc ) ),
                 $expField = $( document.getElementById( fieldConfig.exp ) );
             $cvcField.blur( function(){
                 if ( $cvcField.val() ) {
                     self.creditCardUtil.validateCVC($field, $cvcField);
                 }
                 if ( $expField.val() ) {
                     self.creditCardUtil.validateExp($expField);
                 }
             });
         }

         /**
          * If possible change the icon in the credit card input
          *
          * @since 1.5.0
          *
          * @param type
          */
         function setImage( type ){
             var iconTypes = {
                 0: 'amex',
                 1: 'discover',
                 2: 'visa',
                 3: 'discover',
                 4: 'mastercard'
             };
             var icon = 'credit-card.svg';
             $.each( iconTypes, function( i, card ){
                if( 0 === type.indexOf( card ) ){
                    icon = 'cc-' + card + '.svg';
                    return false;
                }
             });

             $field.css( 'background', 'url("' + fieldConfig.imgPath + icon + '")' );
             
         }

     };

     /**
      * Handler for credit card expiration fields
      *
      * @since 1.5.0
      *
      * @param fieldConfig
      */
     this.credit_card_exp = function ( fieldConfig ) {
         var $field = $( document.getElementById( fieldConfig.id ) );
         if( $field.length ){
             $field.payment('formatCardExpiry');
             $field.blur( function () {
                 var valid = self.creditCardUtil.validateExp( $field );
                 handleValidationMarkup( valid, $field, fieldConfig.invalid, 'help-block-credit_card_exp help-block-credit_card' );
             });
         }
     };

     /**
      * Handler for credit card secret code fields
      *
      * @since 1.5.0
      *
      * @param fieldConfig
      */
     this.credit_card_cvc = function ( fieldConfig ) {
         var $field = $( document.getElementById( fieldConfig.id ) );
         if( $field.length ){
             $field.payment('formatCardCVC');
             if( false !== fieldConfig.ccField ) {
                 var $ccField = $( document.getElementById( fieldConfig.ccField ) );
                 $field.blur( function () {
                     var valid = self.creditCardUtil.validateExp( $ccField, $field);
                     handleValidationMarkup(valid, $field, fieldConfig.invalid, 'help-block-credit_card_cvc help-block-credit_card');
                 });
             }

         }
     };

     /**
      * Validators for credit card CVC and expirations
      *
      * @since 1.5.0
      *
      * @type {{validateCVC: Caldera_Forms_Field_Config.creditCardUtil.validateCVC, validateExp: Caldera_Forms_Field_Config.creditCardUtil.validateExp}}
      */
     this.creditCardUtil = {
         validateCVC: function( $ccField, $cvcField ){
             var val =  $cvcField.val();
             var cardValid = $.payment.validateCardNumber( $ccField.val() );
             var valid = false;
             if ( cardValid ) {
                 var type = $.payment.cardType( $ccField.val() );
                 valid = $.payment.validateCardCVC( val, type)
             }

             return valid;
         },
         validateExp: function ($expField) {
             var val = $expField.val().split('/');
             if (  val && 2 == val.length ) {
                 return $.payment.validateCardExpiry(val[0].trim(), val[1].trim());
             }
         }

     };
     
     this.color_picker = function ( fieldConfig ) {
         $( document.getElementById( fieldConfig.id ) ).miniColors( fieldConfig.settings );
         $(document).on('cf.pagenav cf.add cf.disable cf.modal', function () {
             $(document.getElementById(fieldConfig.id)).miniColors(fieldConfig.settings);
         });
     };



 }

