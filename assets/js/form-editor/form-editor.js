/**
 * Form editor system
 *
 * @since 1.5.1
 *
 * @param editorConfig
 * @param $ jQuery
 * @constructor
 */
function CFFormEditor( editorConfig, $ ){

    var editorAPI,
        store,
        self = this,
        $coreForm,
        fieldConfigs = {},
        currentFromFields = {},
        compiledTemplates = {},
        $editorBody = $('.caldera-editor-body'),
        $saveButton = $('.caldera-header-save-button');

    /**
     * Initialize editor
     *
     * @since 1.5.1
     */
    this.init = function () {
        setUpClickHandlers();
        $coreForm = $( 'form#' + editorConfig.formId );
        editorAPI = new CFFormEditorAPI(
            editorConfig.api, editorConfig.formId, editorConfig.nonce, $
        );
        $.when( editorAPI.getForm() ).then( function (r,z) {
            store = new CFFormEditStore( r );
            self.createFieldPreviews();
            var firstField = $('.layout-grid-panel .icon-edit').first().data( 'field' );
            renderFieldConfig(getFieldConfigWrapper(firstField), self.getStore().getField( firstField ) );
        });
    };

    /**
     * Create field previews
     *
     * @since 1.5.1
     */
    this.createFieldPreviews = function () {
      $.each( this.getStore().getFields(), function(fieldId, field){
            self.buildFieldPreview(fieldId);
      });
    };

    /**
     * Get the form store
     *
     * @since 1.5.1
     *
     * @returns {CFFormEditStore}
     */
    this.getStore = function () {
        return store;
    };

    /**
     * Enable submit button
     *
     * @since 1.5.1
     */
    this.enableSubmit = function () {
        $saveButton.prop('disabled', false);
    };

    /**
     * Disable submit button
     *
     * @since 1.5.1
     */
    this.disableSubmit = function () {
        $saveButton.prop('disabled', true);
    };

    /**
     * Render a field config panel
     *
     * @since 1.5.1
     *
     *
     * @param $wrapper
     * @param fieldConfig
     */
    function renderFieldConfig( $wrapper, fieldConfig ) {
        var target			= $wrapper.find('.caldera-config-field-setup'),
            fieldType = fieldConfig.type,
            fieldId = fieldConfig.ID,
            template 		= getCompiledTemplate( fieldType );
        fieldConfig._id = fieldId;
        fieldConfig._name = 'config[fields][' + fieldId + '][config]';


        template = $('<div>').html( template( fieldConfig ) );

        // send to target
        target.html( template.html() );

        // check for init function
        if( typeof window[fieldType + '_init'] === 'function' ){
            window[fieldType + '_init']( fieldId, target);
        }

        // remove not supported stuff
        var noSupportKey = fieldType + '_nosupport';
        if(fieldtype_defaults[noSupportKey]){

            if(fieldtype_defaults[noSupportKey].indexOf('hide_label') >= 0){
                $wrapper.find('.hide-label-field').hide().find('.field-config').prop('checked', false);
            }
            if(fieldtype_defaults[noSupportKey].indexOf('caption') >= 0){
                $wrapper.find('.caption-field').hide().find('.field-config').val('');
            }
            if(fieldtype_defaults[noSupportKey].indexOf('required') >= 0){
                $wrapper.find('.required-field').hide().find('.field-config').prop('checked', false);
            }
            if(fieldtype_defaults[noSupportKey].indexOf('custom_class') >= 0){
                $wrapper.find('.customclass-field').hide().find('.field-config').val('');
            }
            if(fieldtype_defaults[noSupportKey].indexOf('entry_list') >= 0){
                $wrapper.find('.entrylist-field').hide().find('.field-config').prop('checked', false);
            }
        }

    }

    /**
     * Create a field type config
     *
     * @since 1.5.1
     *
     * @param el
     */
    this.buildFieldTypeConfig = function(el){
        var select 			= $(el);
        var fieldId = select.data( 'field' );

        var fieldType = select.val(),
            $wrapper		= select.closest('.caldera-editor-field-config-wrapper'),
            target			= $wrapper.find('.caldera-config-field-setup'),
            template 		= getCompiledTemplate( fieldType ),
            config			= store.getField(fieldId),
            current_type	= select.data('type'),
            newField = false;

        $wrapper.find('.caldera-config-group').show();

        select.addClass('field-initialized');

        // Be sure to load the fields preset when switching back to the initial field type.
        if(config.length && current_type === select.val() ){
           // config = JSON.parse(config);
        }else{
            // default config
            newField = true;
            config = fieldtype_defaults[select.val() + '_cfg'];
        }

        // build template
        if(!config){
            newField = true;
        }

        if( newField ){
            config = store.addField(fieldId,fieldType);
        }

        renderFieldConfig( $wrapper, config );

        // seup options
        $wrapper.find('.toggle_show_values').trigger('change');

        if( !$('.caldera-select-field-type').not('.field-initialized').length){

            // build previews
            if(! $coreForm.hasClass('builder-loaded')){

                var fields = $('.caldera-select-field-type.field-initialized');
                for( var f = 0; f < fields.length; f++){
                    self.buildFieldPreview( $(fields[f]).data('field') );
                }
                $coreForm.addClass('builder-loaded');
            }else{
                self.buildFieldPreview( select.data('field') );
            }

            self.enableSubmit();
            rebuild_field_binding();
            baldrickTriggers();
        }

        if( $('.color-field').length ){
            $('.color-field').wpColorPicker({
                change: function(obj){

                    var trigger = $(this);


                    if( trigger.data('ev') ){
                        clearTimeout( trigger.data('ev') );
                    }
                    trigger.data('ev', setTimeout( function(){
                        trigger.trigger('record');
                    },200) );
                    if( trigger.data('target') ){
                        $( trigger.data('target') ).css( trigger.data('style'), trigger.val() );
                        $( trigger.data('target') ).val( trigger.val() );
                    }

                }
            });
        }
    };

    /**
     * Render the field preview
     *
     * @since 1.5.1
     *
     * @param fieldId
     * @param config
     */
    function renderFieldPreview( fieldId, config) {
        var
            type = self.getStore().getFieldType(fieldId),
            $preview_parent	= $('.layout-form-field[data-config="' + fieldId + '"]'),
            preview_target	= $preview_parent.find('.field_preview'),
            preview			= $('#preview-' + type + '_tmpl').html(),
            template 		= getCompiledTemplate( 'preview-' + type );
        preview_target.html(template(config));
        $preview_parent.removeClass('button');
        $preview_parent.find( ':input').prop( 'disabled', true );
        self.enableSubmit();
    }

    /**
     * Get jQuery object for config wrapper (the element wrapping field settings)
     *
     * @since 1.5.1
     *
     * @param fieldId
     * @returns {*|jQuery|HTMLElement}
     */
    function getFieldConfigWrapper( fieldId ){
        return $( '#' + fieldId )
    }

    /**
     * Build field preview
     *
     * @since 1.5.1
     *
     * @param fieldId
     */
    this.buildFieldPreview = function(fieldId){
        var config = self.getStore().getField(fieldId);
        renderFieldPreview( fieldId,config );
    };

    /**
     * Setup click handlers for editor
     *
     * @since 1.5.1
     */
    function setUpClickHandlers() {
        // Change Field Type
        $editorBody.on('change', '.caldera-select-field-type', function(e){
            self.buildFieldTypeConfig(this);
        });

        //Change to settings
        $editorBody.on('change record', '.field-config', function(e){

            var $editField 	= $(this),
                $parent 	= $editField.closest('.caldera-editor-field-config-wrapper'),
                fieldId = $parent.prop('id'),
                editType = $editField.data( 'config-type' ),
                newVal,
                updated;
                if( 'checkbox' == $editField.attr( 'type' ) ){
                    newVal = $editField.prop( 'checked' );
                }else{
                    newVal = $editField.val();
                }
                updated = store.updateField( fieldId, editType, newVal );

            if( updated ){
                renderFieldPreview( fieldId, updated );
            }

        });

        //Open field settings
        $( document ).on('click', '.layout-form-field .icon-edit', function(){
            var $clicked = $(this),
                $panel 	= $clicked.parent(),
                type 	= $('#' + $panel.data('config') +'_type').val();

            if ( 'object' == typeof store ) {
                var config = $panel.data('config');
                if ('string' == typeof config) {
                    var $wrapper = getFieldConfigWrapper( config );
                    config = store.getField(config);
                    renderFieldConfig( $wrapper, config );
                }

            }

            $('.caldera-editor-field-config-wrapper').hide();

            if($panel.hasClass('field-edit-open')){
                $panel.removeClass('field-edit-open');
            }else{
                $('.layout-form-field').removeClass('field-edit-open');
                $panel.addClass('field-edit-open');
                $('#' + $panel.data('config')).show();
            }

            $(document).trigger('show.' + $panel.data('config'));
            $(document).trigger('show.fieldedit');

            if( type === 'radio' || type === 'checkbox' || type === 'dropdown' || type === 'toggle_switch' ){
                $('#' + $panel.data('config') + '_auto').trigger('change');
            }
        });

        //Field type change
        $editorBody.on( 'change record', '.caldera-select-field-type', function () {
            var $this = $(this),
                newType = $this.val(),
                fieldId = $this.data( 'field' ),
                updated = self.getStore().changeFieldType( fieldId, $this.val() );
            if (updated) {
                renderFieldConfig($this.parent(), updated);
                renderFieldPreview(fieldId, updated);
            }
        });

    }

    /**
     * Pre compile all Handlears templates
     *
     * @since 1.5.1
     */
    function preCompileTemplates(){
        var pretemplates = jQuery('.cf-editor-template');
        for( var t = 0; t < pretemplates.length; t++){
            compiledTemplates[pretemplates[t].id] = Handlebars.compile( pretemplates[t].innerHTML );
        }

    }


    /**
     * Get a compiled Handlebars template or the fallback template
     * @param template
     * @returns {*}
     */
    function getCompiledTemplate( template ) {
        if ( emptyObject( compiledTemplates) ) {
            preCompileTemplates();
        }

        if (has( compiledTemplates, template + '_tmpl')) {
            return compiledTemplates[template + '_tmpl'];
        } else {
            return compiledTemplates.noconfig_field_templ;
        }

    }

    /**
     * Check if object has a key
     *
     * @since 1.5.1
     *
     * @param object
     * @param key
     * @returns {boolean}
     */
    function has(object, key) {
        return object ? hasOwnProperty.call(object, key) : false;
    }

    /**
     * Check if is empty object
     *
     * @since 1.5.1
     *
     * @param obj
     * @returns {boolean}
    */
    function emptyObject(obj) {
        return Object.keys(obj).length === 0 && obj.constructor === Object;
    }

}