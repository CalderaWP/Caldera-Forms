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
