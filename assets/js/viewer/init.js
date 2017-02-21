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
