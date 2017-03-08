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
     * @returns CFFormEditStore
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

    this.saveForms = function () {
        return {
            before: function (el, e) {
                e.preventDefault();

                if (!check_required_bindings()) {
                    return false;
                }

                $('#save_indicator').addClass('loading');
                if (typeof tinyMCE !== 'undefined') {
                    tinyMCE.triggerSave();
                }

                var data_fields = $('.caldera-forms-options-form').formJSON();
                if (data_fields.conditions) {
                    data_fields.config.conditional_groups = {conditions: data_fields.conditions};
                }
                $(el).data('cf_edit_nonce', data_fields.cf_edit_nonce);
                $(el).data('_wp_http_referer', data_fields._wp_http_referer);
                $(el).data('sender', 'ajax');

                //this would let us get fields from store, but not ready for that yet.
                //data_fields.config.fields = self.getStore().getFields();
                $(el).data('config', JSON.stringify(data_fields.config));

                return true;
            },
            callback: function (obj) {
                if (false === obj.data) {
                    var $notice = $('.updated_notice_box');

                    $notice.stop().animate({top: 0}, 200, function () {
                        setTimeout(function () {
                            $notice.stop().animate({top: -75}, 200);
                        }, 2000);
                    });
                }
            },
            complete: function (obj) {
                $('.wrapper-instance-pane .field-config').prop('disabled', false);
            }
        }
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

        if( self.isStoreReady() && isSelect( fieldType )  ){
            var opts = self.getStore().getFieldOptions( fieldId );
            if( opts ){
                renderOptions(fieldId, opts );

                //the rest of this conditional is why I wish I was using Vue or something :(

                //Prevent no default and a default from being checked
                $wrapper.find( '.toggle_set_default' ).not( '.no-default' ).on( 'change', function () {
                    $wrapper.find( '.no-default' ).prop( 'checked', false );
                    $wrapper.find( '.toggle_set_default' ).prop( 'checked', false );
                    $(this).prop( 'checked', true );
                });

                $wrapper.find( '.no-default' ).on( 'change', function () {
                    //nice vintage vibe here
                    if( $(this).is(':checked') ){
                        $wrapper.find( '.toggle_set_default' ).not( '.no-default' ).prop( 'checked', false );
                    }
                });

                //this hack to prevent showing values when not needed, sucks.
                var showValues = true;
                $.each( opts, function (i,v) {
                    if ( ! v.value ) {
                        showValues = false;
                        return false;
                    }

                } );

                if( showValues ){
                    $wrapper.find( '.toggle_show_values' ).prop( 'checked', true ).trigger( 'change' );
                }else{
                    $wrapper.find( '.toggle_show_values' ).prop( 'checked', false ).trigger( 'change' );
                }

                var fieldDefault = self.getStore().getFieldOptionDefault( fieldId );
                if( fieldDefault  ){
                    $( '#value-default-' + fieldDefault ).prop( 'checked', true );
                    $wrapper.find( '.no-default' ).prop( 'checked', false );

                }else{
                    $wrapper.find( '.no-default' ).prop( 'checked', true );
                }
            }
        }

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

        if ( fieldConfig.hasOwnProperty( 'config' ) ) {
            var checkboxes = $wrapper.find('input:checkbox');
            if (checkboxes.length) {
                fieldConfig = self.getStore().getField(fieldId);
                var configType, $check;
                $.each(checkboxes, function (i, v) {
                    $check = $(v);
                    configType = $check.data('config-type');
                    if (configType) {
                        if (fieldConfig.config.hasOwnProperty(configType) && false == fieldConfig.config[configType]) {
                            //don't check
                        } else {
                            $check.prop('checked', true);
                        }
                    }

                });
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

        // setup options
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
     * Check if store is ready
     *
     * @since 1.5.1
     *
     * @returns {boolean}
     */
    this.isStoreReady = function () {
        if ( 'object' == typeof store ){
            return true;
        }
        return false;
    };

    /**
     * Check if we should treat a type as a select
     *
     * @since 1.5.1
     *
     * @param type
     * @returns {boolean}
     */
    function isSelect(type) {
        if (-1 < [
            'color_picker',
            'filtered_select2',
            'radio',
            'dropdown',
            'checkbox'
        ].indexOf(type)) {
            return true;
        }

    }


    /**
     * Render the field preview
     *
     * @since 1.5.1
     *
     * @param fieldId
     * @param config
     */
    function renderFieldPreview( fieldId, config) {
        if( emptyObject( config ) ){
            config = self.getStore().getField( fieldId );
        }

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

    function deleteField( fieldId ) {
        // remove config
        $('#' + field).remove();
        // remove options
        $('option[value="' + field + '"]').remove();
        $('[data-bind="' + field + '"]').remove();

        // remove field
        delete current_form_fields[field];
        self.getStore().deleteField(fieldId);

        $('[data-config="' + field + '"]').slideUp(200, function(){
            var line = $(this);
            // remove line
            line.remove();
            rebuild_field_binding();
            $(document).trigger('field.removed');
        });


    }



    /**
     * Setup click handlers for editor
     *
     * @since 1.5.1
     */
    function setUpClickHandlers() {
        //Save form
        $saveButton.baldrick({
            method: 'POST',
            request: 'admin.php?page=caldera-forms',
            before: self.saveForms().before,
            callback: self.saveForms().callback,
            complete: self.saveForms().complete,
        });

        // Change Field Type
        $editorBody.on('change', '.caldera-select-field-type', function (e) {
            if (!isSelect(self.getStore().getFieldType($(this).data('field')))) {
                self.buildFieldTypeConfig(this);
            }

        });

        //Change to settings
        $editorBody.on('change record', '.field-config', function (e) {
            if (!self.isStoreReady()) {
                return;
            }

            var $editField = $(this),
                $parent = $editField.closest('.caldera-editor-field-config-wrapper'),
                fieldId = $parent.prop('id'),
                editType = $editField.data('config-type'),
                newVal,
                updated;
            if (!editType) {
                return;
            } else if ('option-value' == editType || 'option-label' == editType || 'option-default' == editType) {
                editType = editType.replace('option-', '');
                if ('default' !== editType) {
                    newVal = $editField.val();
                } else {
                    newVal = $editField.prop('checked')
                }
                updated = self.getStore().updateFieldOption(fieldId, editType, $editField.data('option'), newVal);


            } else {
                if ('checkbox' == $editField.attr('type')) {
                    newVal = $editField.prop('checked');
                } else {
                    newVal = $editField.val();
                }

                updated = self.getStore().updateField(fieldId, editType, newVal);

            }

            if (updated) {
                renderFieldPreview(fieldId, updated);
            }

        });

        //Open field settings
        $(document).on('click', '.layout-form-field .icon-edit', function () {
            var $clicked = $(this);
            if ($clicked.hasClass('caldera-select-field-type')) {
                return;
            }
            var
                $panel = $clicked.parent(),
                type = $('#' + $panel.data('config') + '_type').val();

            if (self.isStoreReady()) {
                var config = $panel.data('config');
                if ('string' == typeof config) {
                    var $wrapper = getFieldConfigWrapper(config);
                    config = store.getField(config);
                    renderFieldConfig($wrapper, config);
                }

            }

            $('.caldera-editor-field-config-wrapper').hide();

            if ($panel.hasClass('field-edit-open')) {
                $panel.removeClass('field-edit-open');
            } else {
                $('.layout-form-field').removeClass('field-edit-open');
                $panel.addClass('field-edit-open');
                $('#' + $panel.data('config')).show();
            }

            $(document).trigger('show.' + $panel.data('config'));
            $(document).trigger('show.fieldedit');

            if (type === 'radio' || type === 'checkbox' || type === 'dropdown' || type === 'toggle_switch') {
                $('#' + $panel.data('config') + '_auto').trigger('change');
            }
        });

        //Field type change
        $editorBody.on('change record', '.caldera-select-field-type', function () {
            if (!self.isStoreReady()) {
                return;
            }

            var $this = $(this),
                field = self.getStore().getField($this.data('field')),
                newType = $this.val(),
                fieldId = $this.data('field'),
                opts = self.getStore().getFieldOptions(fieldId),
                config = self.getStore().changeFieldType(fieldId, $this.val());
            if (config) {
                config = self.getStore().updateFieldOptions(fieldId, opts);
                renderFieldConfig($this.parent(), config);
                renderFieldPreview(fieldId, config);
            }
        });


        // remove an option row
        $('.caldera-editor-body').on('click', '.toggle-remove-option', function (e) {
            var $this = $(this);
            var $triggerfield = $(this).closest('.caldera-editor-field-config-wrapper').find('.field-config').first();
            var fieldId = $triggerfield.val();
            self.getStore().removeFieldOption(fieldId, $this.data('option'));
            $this.parent().remove();
            $triggerfield.trigger('change');
            $(document).trigger('option.remove');
            renderFieldPreview(fieldId, {});
        });

        //delete field
        $editorBody.on('click', '.delete-field', function () {
            var clicked = $(this),
                field = clicked.closest('.caldera-editor-field-config-wrapper').prop('id');

            if (!confirm(clicked.data('confirm'))) {
                return;
            }
            deleteField(fieldId);
        });


    }


    /**
     * Get a compiled Handlebars template or the fallback template
     *
     * @since 1.5.1
     *
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
     * Holds the compiled template for options sections
     *
     * @since 1.5.1
     */
    var optTmpl;

    /**
     * Render options sections
     *
     * @since 1.5.1
     *
     * @param fieldId
     * @param options
     */
    function renderOptions(fieldId, options ) {
        if( ! optTmpl ){
            optTmpl = Handlebars.compile( document.getElementById( 'field-option-row-tmpl' ).innerHTML );
        }

        if ( ! options.hasOwnProperty( 'option' ) ) {
            options = {option: options};
        }


        var el =  document.getElementById( 'field-options-' + fieldId );
        if( null != el ){
            el.innerHTML = optTmpl(self.getStore().getField( fieldId ));
        }else{
            throw Error( 'Field options wrapper for options not found.' );
        }

    }

    /**
     * Pre compile all Handlebars templates
     *
     * @since 1.5.1
     */
    function preCompileTemplates(){
        var pretemplates = $('.cf-editor-template');
        for( var t = 0; t < pretemplates.length; t++){
            compiledTemplates[pretemplates[t].id] = Handlebars.compile( pretemplates[t].innerHTML );
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