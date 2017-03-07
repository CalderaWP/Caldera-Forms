/*! GENERATED SOURCE FILE caldera-forms - v1.5.1-b-1 - 2017-03-06 *//**
 * API Client for Caldera Forms API for a single form
 *
 * @since 1.5.0
 *
 * @param routes URLs for endpoints, should have URL for /entries and /forms
 * @param perPage How many items to return for page
 * @param formId Form ID
 * @param tokens Either WordPress REST API authentication nonce as string, or object with index nonce and token (token is Caldera Forms Entry Token)
 * @param $ jQuery
 *
 * @returns {{getForm: getForm, getEntries: getEntries, paginatedEntryURL: paginatedEntryURL, setPerPage: setPerPage}}
 *
 * @constructor
 */
function CFAPI( routes, perPage, formId, tokens,  $ ) {
    var nonce, token;
    if( 'object' == typeof  tokens ){
        nonce = typeof  tokens.nonce == 'string' ?  tokens.nonce : false;
        token = typeof  tokens.nonce == 'string' ?  tokens.token : false;
    }else{
        nonce = tokens;
    }

    function addHeaders( xhr ){
        xhr.setRequestHeader( 'X-CF-ENTRY-TOKEN', token );
        xhr.setRequestHeader( 'X-WP-Nonce', nonce );
    }

    return {
        getForm: function () {
            return $.ajax({
                url: routes.form + formId,
                method: 'GET',
                beforeSend: function ( xhr ) {
                    addHeaders( xhr );
                }
            }).success(function (r) {
                return r;
            }).error(function (r) {
                console.log(r);
            });
        },
        getEntries: function ( page ) {
            return $.ajax({
                url: this.paginatedEntryURL(formId, page, perPage ),
                method: 'GET',
                beforeSend: function ( xhr ) {
                    addHeaders( xhr );
                }
            } ).success(function (r) {
                return r;
            }).error(function (r) {
                console.log(r);
            });
        },
        paginatedEntryURL: function (formId, page ) {
            var params = $.param({
                page: page,
                per_page: perPage
            });

            return routes.entries + formId + '?' + params
        },
        setPerPage : function( newPerPage ) {
            perPage = newPerPage;
        },
        getPerPage :function () {
            return perPage;
        },
        savePerPage: function(){
            return $.ajax({
                url: routes.entrySettings,
                method: 'POST',
                dataType: 'json',
                beforeSend: function ( xhr ) {
                    addHeaders( xhr );
                },
                data:{
                    per_page: perPage
                }
            }).success( function( r ){
                return r.per_page;
            }).error( function( r ){
                console.log(r);
            })

        }
    }
}


/**
 * Form API for use in editor
 *
 * @since 1.5.1
 *
 * @param routes
 * @param formId
 * @param nonce
 * @param $
 * @returns {{getForm: getForm, saveForm: saveForm}}
 * @constructor
 */
function CFFormEditorAPI( routes, formId, nonce, $ ) {
    function addHeaders(xhr) {
        xhr.setRequestHeader('X-WP-Nonce', nonce);
    }
    return {
        getForm: function () {
            return $.ajax({
                url: routes.form + formId + '?full=true',
                method: 'GET',
                beforeSend: function (xhr) {
                    addHeaders(xhr);
                }
            }).success(function (r) {
                return r;
            }).error(function (r) {
                console.log(r);
            });
        },
        saveForm: function () {
            //placeholder
        }
    }
}
/**
 * A factory for a form state containers
 *
 * Can be used constructed with CFAPI() or other data. Designed to provide data to our VueJS entry viewer, but is framework agnostic.
 *
 * @since 1.50
 *
 * @param formId The ID of the form
 * @param allFields All fields of this form
 * @param listFields The fields of this form with "Show In Entry List" checked
 *
 * @returns {{state: {formId: *, allFields: *, listFields: *}, setFormId: setFormId, setAllFields: setAllFields, setListFields: setListFields, getAllFields: getAllFields}}
 *
 * @constructor
 */
function CFFormStoreFactory( formId, allFields, listFields ){
    return {
        state: {
            formId: formId,
            allFields: allFields,
            listFields: listFields
        },
        setFormId: function(newValue) {
            this.state.formId = newValue
        },
        setAllFields: function(newValue) {
            this.state.allFields = newValue
        },
        setListFields: function(newValue) {
            this.state.listFields = newValue
        },
        getAllFields: function () {
            return this.state.allFields;
        }
    };
}

/**
 * A factory for creating a state container for a paginated collection of entries
 *
 * Can be used constructed with CFAPI() or other data. Designed to provide data to our VueJS entry viewer, but is framework agnostic.
 *
 * @since 1.50
 *
 * @param formId The ID of form entries are from
 * @param entries The entry collection
 *
 * @returns {{state: {formId: *, entries: *, total: number, totalPages: number}, setEntries: setEntries, setTotal: setTotal, setTotalPages: setTotalPages, getTotalPages: getTotalPages, getEntry: getEntry, getFieldFromEntry: getFieldFromEntry}}
 *
 * @constructor
 */
function CFEntriesStoreFactory( formId, entries ){
    return {
        state: {
            formId: formId,
            entries: entries,
            total: 0,
            totalPages: 0,
            page: 0
        },
        setEntries: function (entries) {
            this.state.entries = entries;
        },
        setTotal: function( total ){
            this.state.total = total;
        },
        getTotal: function(){
            return this.state.total;
        },
        setTotalPages: function( totalPages ){
            this.state.totalPages = totalPages;
        },
        getTotalPages: function(){
            return this.state.totalPages;
        },
        setPage: function( page ){
            this.state.page = page;
        },
        getPage: function(){
            return this.state.page;
        },
        getEntry :function( id ){
            if( 'object' == typeof this.state.entries[id] ){
                return this.state.entries[id];
            }
            return false;
        },
        getFieldFromEntry: function( entry, fieldId ){
            if( 'object' == typeof entry.fields[fieldId ]) {
                return entry.fields[fieldId];
            }
            return false;
        }
    }
}

/**
 * A factory for creating a form store for use in form  editor
 *
 * @since 1.5.1
 *
 * @param form
 * @returns {{getFields: getFields, getField: getField, getFieldType: getFieldType, addField: addField, updateField: updateField, getConditionals: getConditionals, getConditional: getConditional, getProcessors: getProcessors, getProcessor: getProcessor}}
 * @constructor
 */
function CFFormEditStore( form ) {
    var fieldKeys = [
        'ID',
        'type',
        'label',
        'slug',
        'config',
        'caption',
        'custom_class',
        'default',
        'conditions',
        'hide_label'
    ];

    function fieldFactory (fieldId, type) {
        var field = {
            ID: fieldId,
            type: type,
            config: {},
            hide_label: false,
        };

        fieldKeys.forEach(function (index) {
            if( ! field.hasOwnProperty( index ) ){
                field[index] = '';
            }
        });

        return field;
    }

    /**
     * Create a new option
     *
     * @sine 1.5.1
     *
     * @returns {{}}
     */
    function optionFactory() {
        return {
            label: '',
            value: '',
            default : false
        };

    }

    function has(object,key) {
        return object ? hasOwnProperty.call(object, key) : false;
    }

    function emptyObject( object ) {
        return Object.keys(object).length === 0 && object.constructor === Object;

    }

    /**
     * Set a field config
     *
     * form.fields shouldn't be changed anywhere else. This is the one place to do so it can emit an event, when we get to that sort of thing.
     *
     * @since 1.5.1
     *
     *
     * @param fieldId Field ID
     * @param config New field config
     */
    function setField(fieldId, config){
        form.fields[fieldId] = config;
    }


    return {
        /**
         * Get all fields of form
         *
         * @returns {*}
         */
        getFields : function(){
            return form.fields;
        },
        /**
         * Get a field of a form
         *
         *  @since 1.5.1
         *
         * @param fieldId
         * @returns {*}
         */
        getField : function ( fieldId ) {
            if( form.fields.hasOwnProperty( fieldId )  ){
                return form.fields[fieldId];
            }

            return {}
        },
        /**
         * Get field type by field ID
         *
         *  @since 1.5.1
         *
         * @param fieldId
         * @returns {*}
         */
        getFieldType: function ( fieldId ) {
            var field = this.getField(fieldId);
            if( ! emptyObject( field ) ){
                return field.type;
            }
            return false;
        },
        /**
         * Add a field to collection
         *
         *  @since 1.5.1
         *
         * @param fieldId
         * @param fieldType
         * @returns {*|{}}
         */
        addField : function (fieldId,fieldType) {
            setField(fieldId, fieldFactory(fieldId,fieldType ) );
            return this.getField(fieldId);
        },
        /**
         * Update field in collection
         *
         *  @since 1.5.1
         *
         * @param fieldId
         * @param key
         * @param data
         * @returns {*}
         */
        updateField: function (fieldId, key, data ) {
            var field = this.getField(fieldId);
            if( ! emptyObject(field) && undefined != key  ){
                if( -1 < fieldKeys.indexOf( key ) ){
                    field[key] = data;
                    setField( fieldId, field );
                    return this.getField(fieldId);
                }else if( 'placeholder' == key || 'default' == key ){
                    field.config[ key ] = data;
                    setField( fieldId, field );
                    return this.getField(fieldId);
                }else if( 'option-value' == key || 'option-value' == key || 'option' == key  ) {
                    throw new Error( 'Invalid field key to update. Use this.UpdateFieldOptions' );
                }else{
                    throw new Error( 'Invalid field key to update. Not supported.' );
                }

            }
            return false;
        },
        /**
         * Add an option label or value to the
         * @param fieldId
         * @param type
         * @param opt
         * @param value
         * @returns {*}
         */
        updateFieldOption: function (fieldId, type, opt, value ) {
            var field = this.getField(fieldId);
            if( ! emptyObject(field) && ( 'value' == type || 'label' == type || 'default' == type ) ){
                if( ! field.config.hasOwnProperty( 'option' ) ){
                    field.config[ 'option' ] = {};
                }
                if( ! field.config.option.hasOwnProperty( opt ) ){
                    field.config.option[ opt ] = optionFactory( opt );
                }
                field.config.option[opt][type] = value;
                setField( fieldId, field );
                return this.getField(fieldId);
            }

            return false;
        },
        getFieldOptions: function (fieldId ) {
            var field = this.getField(fieldId);
            if( ! emptyObject(field) && field.hasOwnProperty( 'config' ) && field.config.hasOwnProperty( 'option' ) ){
                return field.config.option;
            }

            return false;
        },
        /**
         * Change a field's type
         *
         * @since 1.5.1
         *
         * @param fieldId
         * @param newType
         * @returns {*}
         */
        changeFieldType: function (fieldId, newType ) {
            var field = this.getField( fieldId );
            if( ! emptyObject( field ) ){
                field.type = newType;
                setField( fieldId, field );
                return this.getField(fieldId);
            }

            return false;
        },
        /**
         * Get conditional groups of form
         *
         * @since 1.5.1
         *
         * @returns {*}
         */
        getConditionals : function () {
            return form.conditional_groups.conditions;
        },
        /**
         * Get a conditional group by ID
         *
         *  @since 1.5.1
         *
         * @param id
         * @returns {*}
         */
        getConditional : function ( id ) {
            if( has( form.conditional_groups.conditions, id ) ){
                return form.conditional_groups.conditions[id];
            }
            return {}
        },
        /**
         * Get processors of form
         *
         * @since 1.5.1
         *
         * @returns {*}
         */
        getProcessors : function() {
            return form.processors;
        },
        /**
         * Get a form processor
         *
         * @since 1.5.1
         *
         * @param id
         * @returns {*}
         */
        getProcessor: function ( id ) {
            if( has( form.processors, id )){
                return form.processors[id];
            }
            return {}
        }

    }

}


/**
 * A VueJS-powered entry viewer for Caldera Forms
 *
 * @since 1.5.0
 *
 * @param formId Form ID
 * @param formStore Form data store, should be created with CFFormStoreFactory()
 * @param entryStore Entry data store, should be created with CFEntriesStoreFactory()
 * @param api API instance for this form. Should be instance of CFAPI
 * @param config Configuration. Probably CF_ENTRY_VIEWER_2_CONFIG, but you can add your own if you like.
 *
 * @returns {*}
 *
 * @constructor
 */
function CFEntryViewer2( formId, formStore, entryStore, api, config ){
    var $singleEntryZone = jQuery( document.getElementById( config.targets.entry ) );

    return new Vue({
        data: function() {
            return {
                form: formStore.state,
                entries: entryStore.state,
                page: 1,
                perPage: api.getPerPage(),
                totalPages: entryStore.getTotalPages(),
                singleEntry: {},
                currentView: 'empty'
            }
        },
        el: '#caldera-forms-entries',
        components : {
            'single-entry' : {
                template: '#' + config.targets.entries,
                data: function () {
                    return {
                        singleEntryFields: Object,
                        singleEntry: Object
                    }
                }
            },

        },
        mounted: function () {
            this.paginationButtons();
        },
        methods:{
            paginationButtons: function(){
                var $el = jQuery( this.$el );
                var $next = $el.find( '.caldera-forms-entry-viewer-next-button' ),
                    $prev = $el.find( '.caldera-forms-entry-viewer-prev-button' );


                if( this.page >= this.totalPages ){
                    $next.prop( 'disabled', true ).attr( 'aria-disabled', true );
                }else{
                    $next.prop( 'disabled', false ).attr( 'aria-disabled', false );
                }

                if( this.page == 1 ){
                    $prev.prop( 'disabled', true ).attr( 'aria-disabled', true );
                }else{
                    $prev.prop( 'disabled', false ).attr( 'aria-disabled', false )

                }
            },
            nextPage: function(){
                var self = this;
                this.$set( this, 'page', this.page + 1 );
                jQuery.when( api.getEntries( self.page ) ).then( function(d){
                    entryStore.setEntries(d);
                    self.$set( self, 'entries', entryStore.state );
                    self.paginationButtons();
                }, function(){
                    self.notAllowed();
                });

            },
            prevPage: function(){
                if( 0 >= this.page - 1 ){
                    return false;
                }
                var self = this;
                this.$set( this, 'page', this.page - 1 );
                jQuery.when( api.getEntries( self.page ) ).then( function(d){
                    entryStore.setEntries(d);
                    self.$set( self, 'entries', entryStore.state );
                    self.paginationButtons();
                }, function(){
                    self.notAllowed();
                });

            },
            entryHasField: function( fieldId, entryId ){
                var entry = entryStore.getEntry( entryId );
                if ( false !== entry ) {
                    return entryStore.getFieldFromEntry(entry, fieldId);
                } else {
                    return false;
                }
            },
            updatePerPage: function(){
                var self = this;
                api.setPerPage( this.perPage );
                jQuery.when( api.getEntries( self.page ) ).then( function(d){
                    entryStore.setEntries(d);
                    self.$set( self, 'entries', entryStore.state );
                });
                api.savePerPage( this.perPage );
            },
            fieldValue: function( fieldId, entry ){
                if( 'string' == typeof  entry[ fieldId ] ){
                    return entry[ fieldId ];
                }else if( 'object' == typeof entry[ 'fields' ][ fieldId ] ){
                    return entry[ 'fields' ][ fieldId ].value;
                }else{
                    return '';
                }

            },
            showSingle: function( entryId ){
                var $modal,
                    entry = entryStore.getEntry( entryId ),
                    fields = formStore.getAllFields();
                var single = Vue.extend({
                    template: '#' + config.templates.entry,
                    data: function(){
                        return {
                            fields: fields,
                            entry: entry
                        }
                    },
                    methods: {
                        fieldValue: function (fieldId) {
                            if ('string' == typeof  entry[fieldId]) {
                                return entry[fieldId];
                            } else if ('object' == typeof entry['fields'][fieldId]) {
                                return entry['fields'][fieldId].value;
                            } else {
                                return '';
                            }
                        },
                        close: function () {
                            $singleEntryZone.empty();
                            $modal.destroy();
                        }
                    },
                });

                $singleEntryZone.empty();
                var newDiv = document.createElement("div");
                jQuery( newDiv ).attr( 'id', config.targets.entry + '-' + entryId );
                jQuery( newDiv ).appendTo( $singleEntryZone );
                new single().$mount('#' + config.targets.entry + '-' + entryId );
                $modal = jQuery('[data-remodal-id=' + entryId +']').remodal();
                $modal.open();

            },
            notAllowed: function (r) {
                if ( 'object' != typeof  r && 404 != r.status ) {
                    $singleEntryZone.remove();
                    jQuery('#caldera-forms-entries-nav').remove();
                    jQuery(document.getElementById('caldera-forms-entries')).html('<div class="alert alert-warning">' + config.strings.not_allowed + '</div>');
                }
            }
        }
    });
}

/**
 * Set up the entry viewer
 *
 * @since 1.5.0
 */
jQuery( document ).ready( function ($) {
    if( 'object' == typeof CF_ENTRY_VIEWER_2_CONFIG ){

        var formId = CF_ENTRY_VIEWER_2_CONFIG.formId;

        var tokens = {
            //REST API Nonce
            nonce: CF_ENTRY_VIEWER_2_CONFIG.api.nonce,
            //Special token for entry viewer
            token: CF_ENTRY_VIEWER_2_CONFIG.api.token
        };

        var api = new CFAPI( CF_ENTRY_VIEWER_2_CONFIG.api, CF_ENTRY_VIEWER_2_CONFIG.perPage, formId, tokens, $ );
        $.when( api.getForm(), api.getEntries(1) ).then( function( d1, d2 ){
            var form = d1[0];

            var entries = d2[0];
            var formStore = new CFFormStoreFactory( formId, form.field_details.order, form.field_details.entry_list );
            var entriesStore = new CFEntriesStoreFactory( formId, entries );
            entriesStore.setPage(1);
            if( null != d2[2].getResponseHeader( 'X-CF-API-TOTAL-PAGES')  ){
                entriesStore.setTotalPages(d2[2].getResponseHeader( 'X-CF-API-TOTAL-PAGES' ) );
            }
            if( null != d2[2].getResponseHeader( 'X-CF-API-TOTAL' ) ){
                entriesStore.setTotal( d2[2].getResponseHeader( 'X-CF-API-TOTAL' ) );
            }
            var viewer = new CFEntryViewer2( formId, formStore, entriesStore, api, CF_ENTRY_VIEWER_2_CONFIG );

        }, function(r){
            var entriesId = typeof CF_ENTRY_VIEWER_2_CONFIG.targets == 'object' && typeof CF_ENTRY_VIEWER_2_CONFIG.targets.entries == 'string' ? CF_ENTRY_VIEWER_2_CONFIG.targets.entries : 'caldera-forms-entries';
            var navId = typeof CF_ENTRY_VIEWER_2_CONFIG.targets == 'object' && typeof CF_ENTRY_VIEWER_2_CONFIG.targets.nav == 'string' ? CF_ENTRY_VIEWER_2_CONFIG.targets.nav : 'caldera-forms-entries-nav';
            jQuery('#' + navId).remove();
            if ( 'object' == typeof r && 404 == r.status  ) {
                jQuery('#' + entriesId).html('<div class="alert alert-error">' + CF_ENTRY_VIEWER_2_CONFIG.strings.no_entries + '</div>');
            }else{
                jQuery('#' + entriesId).html('<div class="alert alert-error">' + CF_ENTRY_VIEWER_2_CONFIG.strings.not_allowed + '</div>');

            }
        });

    }

});
