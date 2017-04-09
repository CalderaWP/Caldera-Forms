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
        $saveButton = $('.caldera-header-save-button'),
        lastMagicList = {
            system: {},
            notSystem: {}
        },
        lastMagicObj = {
            system: {},
            notSystem: {}
        };

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
     * Get all processors currently in form
     *
     * @todo Use store not DOM
     *
     * @since 1.5.1
     *
     * @returns {Array}
     */
    this.getCurrentProcessors = function() {
        //for now, pull of DOM
        var $el,
            processors = [];
        $( '.caldera-editor-processor-config-wrapper' ).each(function (i,el) {
            $el = $( el );
            processors.push({
                id: $el.attr( 'id' ),
                type: $el.data( 'type' ),
                name: $el.find( 'h3' ).first().html()
            });
        });

        return processors;
    };

    /**
     * Create magic tag list as UL
     *
     * @since 1.5.1
     * 
     * @param includeSystem
     * @returns {*|jQuery|HTMLElement}
     */
    this.magicTagsUl = function ( includeSystem ) {
        var cachedObj,
            cachedList,
            list = optList(true),
            $list = $( '<ul>' ),
            i;
        if( includeSystem ){
            cachedObj = lastMagicObj.system;
            cachedList = lastMagicList.system;
        }else{
            cachedObj = lastMagicObj.notSystem;
            cachedList = lastMagicList.notSystem;
        }
        if( ! emptyObject( cachedObj ) && ! emptyObject( cachedList ) && JSON.stringify(cachedObj) === JSON.stringify(list)  ){
            return cachedList;
        }

        //@todo translations!
        $list.append( '<li class="header">Fields</li>' );
        $.each( list.fields, function (i,field) {
            $list.append( '<li class="tag" data-tag="%'+ field.slug + '%"><strong></strong>%'+ field.slug + '%</li>')
        });

        if ( includeSystem ) {
            $list.append('<li class="header">System Tags</li>');
            $.each( list.system, function (i,tag) {
                $list.append('<li class="tag" data-tag="{' + tag.value + '}"><strong></strong>{' + tag.label + '}</li>');
            });

            $.each( list.processors, function (i,processor) {
                $list.append('<li class="header">' + processor.name + '</li>');
                var tagGroup;
               for( tagGroup in processor.tags ){
                   $.each( processor.tags[ tagGroup ], function (i,tag) {
                       $list.append( '<li class="tag" data-tag="{'+tag +'}"><strong></strong>{'+tag+'} </li>');
                   });
               }
            });


        };

        if( includeSystem ){
            lastMagicObj.system = list;
            lastMagicList.system = $list;
        }else{
            lastMagicObj.notSystem = list;
            lastMagicList.notSystem = $list;
        }

        return $list;


    };


    function setUpOptions($wrapper,fieldId) {
        var opts = self.getStore().getFieldOptions( fieldId );
        if( opts ){
            renderOptions(fieldId );

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

        $wrapper.on('click', '.add-toggle-option', function(e){
            var $clicked		= $(this),
                fieldId = $clicked.data( 'field' );

            if($clicked.data('bulk')){
                $($clicked.data('bulk')).toggle();
                $($clicked.data('bulk')).find('textarea').focus();
                return;
            }

            var            $toggleRows	= $wrapper.find('.toggle-options'),
                template = getOptRowTmpl();

            if($clicked.data('options')){
                var batchinput 	= $($clicked.data('options')),
                    batch 		= batchinput.val().split("\n"),
                    has_vals 	= false;
                for( var i = 0; i < batch.length; i ++){
                    var label = batch[i],
                        val = label,
                        parts = val.split('|');
                    if( parts.length > 1 ){
                        val = parts[0];
                        label = parts[1];
                        has_vals = true;
                    }
                    self.getStore().addFieldOption( fieldId, val, label );
                }
                $($clicked.data('options')).parent().hide();
                batchinput.val('');
                if( true === has_vals ){
                    $wrapper.find('.toggle_show_values').prop( 'checked', true );
                }else{
                    $wrapper.find('.toggle_show_values').prop( 'checked', false );
                }
                $toggleRows.empty();
            }else{
                self.getStore().addFieldOption( fieldId, false, false );
            }
            $('.preset_options').val('');

            renderOptions(fieldId);
            $wrapper.find('.toggle_show_values').trigger('change');


            $('.toggle-options').sortable({
                handle: ".dashicons-sort"
            });
            if(!batch){
                $toggleRows.find('.toggle_label_field').last().focus();
            }
        });
    }

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
            setUpOptions($wrapper,fieldId);
        }else{
            $wrapper.find( '.caldera-config-group-toggle-options' ).remove();
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

        colorFieldsInit( $wrapper );

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

        if( 'calculation' == self.getStore().getFieldType( fieldId ) ){
            calcField(fieldId, $wrapper );
        }

    }

    function colorFieldsInit($wrapper) {
        var colorFields = $wrapper.find( '.color-field' );
        if (colorFields.length) {
            colorFields.wpColorPicker({
                change: function (obj) {

                    var trigger = $(this);


                    if (trigger.data('ev')) {
                        clearTimeout(trigger.data('ev'));
                    }
                    trigger.data('ev', setTimeout(function () {
                        trigger.trigger('record');
                    }, 200));
                    if (trigger.data('target')) {
                        $(trigger.data('target')).css(trigger.data('style'), trigger.val());
                        $(trigger.data('target')).val(trigger.val());
                    }

                }
            });
        }
    }

    /**
     * Setup a calcualation field
     *
     * @since 1.5.1
     *
     * @param fieldId
     * @param $wrapper
     */
    function calcField( fieldId, $wrapper){

        var
            field = self.getStore().getField( fieldId ),
            $addGroupButton = $( '#' + fieldId + '_add_group'),
            $opGroups = $( '#' + fieldId + '_operator_groups' ),
            $fixedButton = $(  '#' + fieldId + '_fixed'),
            $manualButton = $( '#' + fieldId + '_manual' ),
            $autoBox = $( '#' + fieldId + '_autobox' ),
            $manualBox = $( '#' + fieldId + '_manualbox' ),
            $manualFormula = $( '#' + fieldId + '_manual_formula_input' ),
            $separator = $(  '#' + fieldId + '_thousand_separator'),
            formula = self.getStore().getFieldCalcFormula(fieldId ),
            $formular = $( '#' + fieldId + '_formular'),
            $build_formula = $( '#' + fieldId + '_config' );

        if( ! formula ){
            updateFormulaFromAuto();
        }

        if( ! field.config.hasOwnProperty( 'manual' ) || false == field.config.manual ){
            $manualButton.prop( 'checked', false );
        }
        //hide/show manual/auto box on first render
        typeBoxes($manualButton.prop('checked'));

        /**
         * Set formula in in DOM and store
         *
         * @since 1.5.0
         *
         * @param formula
         */
        function updateFormula(formula) {
            self.getStore().updateFieldCalcFormula(fieldId, formula);
            $formular.val(formula);
            var field = self.getStore().getField(fieldId);
            if( ! field.config.hasOwnProperty( 'config' ) ){
                $build_formula.val( JSON.stringify( {group: [] } ) );
            }else{
                $build_formula.val( JSON.stringify( field.config.config ) );

            }

        }

        /**
         * Build formuala form AutoBox (buildFormulaa() ) and update formula in store and DOM (updateFormula() )
         *
         * @since 1.5.1
         */
        function updateFormulaFromAuto() {
            var formula = buildFormuala();
            updateFormula(formula);
        }

        /**
         * Create formula from visual editor
         *
         * @since 1.5.1
         *
         * @returns {*}
         */
        function buildFormuala(){
            var lines = self.getStore().getFieldCalcGroups( fieldId );
            var newFormula = '';
            $.each( lines, function (i,lineGroups) {
                if( lineGroups.hasOwnProperty( 'operator' ) ){
                    newFormula += ' ' + lineGroups.operator + ' ';
                }else{
                    $.each(lineGroups, function (lI, lineGroup) {
                        newFormula += '(';
                        var line;
                        for (var lGI = 0; lGI <= lineGroup.length; lGI++) {
                            line = lineGroup[lGI];
                            if ('object' == typeof line) {

                                newFormula +=  line.operator + line.field;
                            }


                        }
                        newFormula += ')';
                    });
                }

            });
            return newFormula;
        }



        //add new line
        $autoBox.on( 'click', '.calculation-add-line', function () {
            var $this = $(this),
                $newLine = $this.prev().find('.calculation-group-line').last().clone(),
                group = $this.data( 'group' ),
                lineId = $newLine.data( 'line' ) + 1;

            $newLine.appendTo($this.prev());
            var newLine = self.getStore().newFieldCalcGroup( fieldId, group, lineId );
            $newLine.find('select').prepend( '<option />').val('').attr( 'data-group', group ).attr( 'data-line', lineId ).first().focus();
            $newLine.attr( 'data-group', group ).attr( 'data-line', lineId );
            $autoBox.find( '.calculation-operator-line[data-line="'+lineId+'"]' ).show().attr( 'aria-hidden', false );

        });

        //remove line
        $autoBox.on( 'click', '.remove-operator-line', function () {
            var $this = $(this),
                $parent = $this.parent(),
                groupId = $parent.data( 'group' ),
                lineId = $parent.data( 'line' );
            var group = self.getStore().removeFieldCalcLine( fieldId, groupId, lineId );
            //see if we need to remove operator
            if (0 === group.lines.length ) {
                var opGroupId = groupId + 1;
                self.getStore().removeFieldCalcGroup( fieldId, groupId );
                self.getStore().removeFieldCalcGroup( fieldId, opGroupId );
                $( '#op-group-' + opGroupId ).remove();
                $( '#calculation-group-' + groupId ).parent().remove();
            }

            
            $parent.remove();
        });

        //change  operator for line or operator group
        $autoBox.on( 'change', '.calculation-operator', function () {
            var $this = $(this),
                isLine = false,
                lineId = $this.data( 'line' ),
                groupId = $this.data( 'group' ),
                val = $this.val();

            if( $this.hasClass( 'calculation-operator-line'  ) ){
                self.getStore().updateFieldCalcLine( fieldId, groupId, lineId, 'operator', val );
            }else{
                self.getStore().updateFieldCalcOpGroup( fieldId, groupId, val );

            }
            updateFormulaFromAuto();

        });

        //change field for line
        $autoBox.on( 'change', '.calculation-operator-field', function () {
            var $this = $(this),
                groupId = $this.data( 'group'),
                lineId = $this.data( 'line' ),
                val = $this.val(),
                type = 'field';
            if( $this.hasClass( 'calculation-operator-line' ) ){
                type = 'operator';
            }
            self.getStore().updateFieldCalcLine( fieldId, groupId, lineId, type, val );
            updateFormulaFromAuto();
        });

        //add operator group
        $wrapper.on( 'click', '.add-operator-group', function () {
            self.getStore().addFieldCalcGroup( fieldId );
            //re-render (maybe later break up template into parts so this isn't needed
            visualCalcEditor(fieldId, $autoBox );
            updateFormulaFromAuto();
        });

        $fixedButton.on( 'change', function(e){
            var $checked = $(this);
            if($checked.prop('checked')){
                $separator.show().attr( 'aria-hidden', false );
            }else{
                $separator.hide().attr( 'aria-hidden', true );
            }
        });

        /**
         * Change hide/show of visual/manual builder based on conditionals
         *
         * @since 1.5.1
         *
         * @param checked
         */
        function typeBoxes(checked) {
            if (checked) {
                $autoBox.hide().attr( 'aria-hidden', false );
                $manualBox.show();
            } else {
                visualCalcEditor(fieldId, $autoBox);
                $autoBox.show();
                $manualBox.hide().attr( 'aria-hidden', true );
            }
        }

        //when manaul checkbox changes, update hide/show
        $manualButton.on( 'change', function () {
            typeBoxes($manualButton.prop('checked'));
        });

        //when manual formula changes update
        $manualFormula.on( 'change', function () {
            updateFormula( $manualFormula.val() );
        });


    }

    /**
     * Holds visual editor for calculation fields template
     *
     * @since 1.5.1
     */
    var calcTmpl;

    /**
     *
     * @param fieldId
     * @param $autoBox
     * @returns {*}
     */
    function visualCalcEditor(fieldId, $autoBox) {
        if( ! calcTmpl ){
            calcTmpl = Handlebars.compile( document.getElementById( 'calculator-group-tmpl' ).innerHTML );
        }
        var field = self.getStore().getField(fieldId);

        if (field) {
            var rendered = calcTmpl(field.config.config);

        }else{
            return false;
        }

        var list = optList(false);
        var $sel;

        $autoBox.html( rendered );
        $autoBox.find( '.calculation-operator-field' ).each(function (i,sel) {
            $sel = $(sel);
            optListSelect( $sel, list, $sel.data( 'default' ), false, fieldId );
        });

        $autoBox.find( '.calculation-operator-line[data-line="0"]' ).hide().attr( 'aria-hidden', true ).each( function(){
            $(this).find( 'option' ).prop( 'selected', '' );
        });
    }

    /**
     * Creates the option list of fields, system tags
     *
     * @since 1.5.1
     *
     * @TODO lazy-loader/not recreating -- need to have system for emptying when field added/removed first. Also for processors.
     *
     * @param includeSystem
     * @returns {{system: {}, fields: {}, variables: *}}
     */
    function optList(includeSystem){

        var list = {
            system: {},
            fields: {},
            variables: self.variables.getAll(),
            processors : {}
        },
            fields = self.getStore().getFields(),
            i = 0,
            field;

        for( var fieldId in fields ){
            field = self.getStore().getField( fieldId );
            list.fields[i] = {
                value: field.value,
                label: field.label,
                slug: field.slug,
                ID: field.ID
            };
            i++;

        }

        if ( includeSystem ) {
            var si = 0,
                sysTags = system_values.system.tags,
                sysBefore = system_values.system.wrap[0],
                sysAfter = system_values.system.wrap[1];
            for (var tag in sysTags) {
                sysTags[tag].forEach(function (text) {
                    list.system[si] = {
                        value: sysBefore + text + sysAfter,
                        label: sysBefore + text + sysAfter
                    };
                    si++;
                });

            }

            self.getCurrentProcessors().forEach(function (proccesor) {
                if (undefined != typeof  system_values[proccesor.type]) {
                    list.processors[proccesor.type] = {
                        name: proccesor.name,
                        tags: system_values[proccesor.type].tags
                    };
                }
            });
        }

        return list;
    }

    /**
     * Populate a selector setting with all fields and optionally system values
     *
     * @since 1.5.1
     *
     * @param $el Select EL
     * @param list List to parse
     * @param selected Default value
     * @param includeSystem To include system magic tags?
     * @param excludes fieldId as string or array of field IDs and system tags to exclude
     */
    function optListSelect($el, list, selected, includeSystem, excludes ) {
        if( 'string' == typeof  excludes ){
            excludes = [ excludes ];
        }

        $el.append( '<optgroup label="Fields">' );

        var field;
        for ( var i in list.fields ) {
            if( -1 === excludes.indexOf( list.fields[i].value ) ){
                field = self.getStore().getFieldSimple( list.fields[i].ID);
                $el.append($('<option>', {
                    value: field.ID,
                    text: field.label
                }));
            }

        }

        $el.append( '<optgroup label="Variables">' );

        list.variables.forEach(function( variable){
            $el.append($('<option>', {
                value: variable.name,
                text: variable.name
            }));
        });



        if (includeSystem) {
            $el.append( '<optgroup label="System">' );
            for( var sysTag in list.system ){
                if( -1 === excludes.indexOf( list.system[sysTag] ) ) {
                    $el.append($('<option>', {
                        value: list.system[sysTag].value,
                        text: list.system[sysTag].label
                    }));
                }
            }


        }

        $el.val(selected);
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
                config = self.getStore().getField($this.data('field')),
                newType = $this.val(),
                fieldId = $this.data('field'),
                opts = {};


                if( isSelect(self.getStore().getFieldType(fieldId) ) && isSelect( newType )){
                    opts = self.getStore().getFieldOptions(fieldId);
                }
                config = self.getStore().changeFieldType(fieldId, $this.val());
            if (config) {
                if ( ! emptyObject( opts ) ) {
                    config = self.getStore().updateFieldOptions(fieldId, opts);
                }
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
     * Don't call directly, use getOptRowTmpl() which lazy-loads it
     *
     * @since 1.5.1
     */
    var optTmpl;

    /**
     * Get option row template
     *
     * Acts as lazy-loader for compilation
     *
     * @since 1.5.1
     *
     * @returns {*}
     */
    function getOptRowTmpl() {
        if (!optTmpl) {
            optTmpl = Handlebars.compile(document.getElementById('field-option-row-tmpl').innerHTML);
        }

        return optTmpl;
    }

    /**
     * Render options sections
     *
     * @since 1.5.1
     *
     * @param fieldId
     */
    function renderOptions(fieldId ) {
        var optTmpl = getOptRowTmpl();

        var el =  document.getElementById( 'field-options-' + fieldId );
        if( null != el ){
            el.innerHTML = optTmpl(self.getStore().getField( fieldId ));
        }else{
            throw Error( 'Field options wrapper for options not found. field-options-' + fieldId  );
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

    /**
     * Form Variables
     *
     * For now pulls of of DOM.
     *
     * @todo Use form store for variables
     *
     * @since 1.5.1
     *
     * @type {{self: CFFormEditor, findAll: CFFormEditor.variables.findAll, getAll: CFFormEditor.variables.getAll, getType: CFFormEditor.variables.getType, getName: CFFormEditor.variables.getName, getValue: CFFormEditor.variables.getValue}}
     */
    this.variables = {
        self: this,
        /**
         * Collect all form variables in an array
         *
         *
         *
         * @returns {Array}
         */
        getAll: function () {
            var variables = [],
                $variable,
                $variables = this.findAll();
            for (var i = 0; i <= $variables.length; i++) {
                $variable = $($variables[i]);
                variables.push({
                    name: this.getName($variable),
                    type: this.getType($variable),
                    value: this.getValue($variable),
                });
            }
            return variables;
        },
        /**
         * Get all form variables from DOM jQuery
         *
         * @since 1.5.1
         *
         * @returns {*|jQuery|HTMLElement}
         */
        findAll: function () {
            return $( '.cf-variable' );
        },
        /**
         * Get variable type from jQuery
         *
         * @since 1.5.1
         *
         * @param $variable
         * @returns {*}
         */
        getType: function ( $variable) {
            return $variable.find( '.cf-variable-type' ).val();
        },
        /**
         * Get variable name from jQuery
         *
         * @since 1.5.1
         *
         * @param $variable
         * @returns {*}
         */
        getName: function ( $variable ) {
            return $variable.find( '.cf-variable-name' ).val();
        },
        /**
         * Get variable value from jQuery
         *
         * @since 1.5.1
         *
         * @param $variable
         * @returns {*}
         */
        getValue: function ( $variable ) {
            return $variable.find( '.cf-variable-value' ).val();
        }
    };

}

