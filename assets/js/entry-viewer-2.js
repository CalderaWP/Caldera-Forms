/*! GENERATED SOURCE FILE Caldera-Forms - v1.5.0-b-1 - 2016-12-16 *//**
 * API Client for Caldera Forms API for a single form
 *
 * @since 1.5.0
 *
 * @param routes URLs for endpoints, should have URL for /entries and /forms
 * @param perPage How many items to return for page
 * @param formId Form ID
 * @param $ jQuery
 *
 * @returns {{getForm: getForm, getEntries: getEntries, paginatedEntryURL: paginatedEntryURL, setPerPage: setPerPage}}
 *
 * @constructor
 */
function CFAPI( routes, perPage,formId, $ ) {
    return {
        getForm: function () {
            return $.get(routes.form + formId).success(function (r) {
                return r;
            }).error(function (r) {
                console.log(r);
            });
        },
        getEntries: function ( page ) {
            return $.get(this.paginatedEntryURL(formId, page, perPage ) ).success(function (r) {
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
            totalPages: 0
        },
        setEntries: function (entries) {
            this.state.entries = entries;
        },
        setTotal: function( total ){
            this.state.total = total;
        },
        setTotalPages: function( totalPages ){
            this.state.totalPages = totalPages;
        },
        getTotalPages: function(){
            return this.state.totalPages;
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
                perPage: 2,
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
            var self = this;
            var $el = jQuery( self.$el );
            var $next = $el.find( '.caldera-forms-entry-viewer-next-button' ),
                $prev = $el.find( '.caldera-forms-entry-viewer-prev-button' );


            if( self.page >= this.totalPages ){
                $next.prop( 'disabled', true );
            }

            if( self.page = 1 ){
                $prev.prop( 'disabled', true );
            }

        },
        methods:{
            nextPage: function(){
                var self = this;
                this.$set( this, 'page', this.page + 1 );
                jQuery.when( api.getEntries( self.page ) ).then( function(d){

                    entryStore.setEntries(d);
                    self.$set( self, 'entries', entryStore.state );
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
                console.log( this.perPage );
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
        var api = new CFAPI( CF_ENTRY_VIEWER_2_CONFIG.api, CF_ENTRY_VIEWER_2_CONFIG.perPage, formId, $ );
        $.when( api.getForm(), api.getEntries(1) ).then( function( d1, d2 ){
            var form = d1[0];

            var entries = d2[0];
            var formStore = new CFFormStoreFactory( formId, form.field_details.order, form.field_details.entry_list );
            var entriesStore = new CFEntriesStoreFactory( formId, entries );
            if( null != d2[2].getResponseHeader( 'X-CF-API-TOTAL-PAGES')  ){
                entriesStore.setTotalPages = d2[2].getResponseHeader( 'X-CF-API-TOTAL-PAGES' );
            }
            if( null != d2[2].getResponseHeader( 'X-CF-API-TOTAL' ) ){
                entriesStore.setTotalPages = d2[2].getResponseHeader( 'X-CF-API-TOTAL' );
            }
            var viewer = new CFEntryViewer2( formId, formStore, entriesStore, api, CF_ENTRY_VIEWER_2_CONFIG );

        });
    }

});
