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
