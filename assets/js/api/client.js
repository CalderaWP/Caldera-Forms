/**
 * API Client for Caldera Forms API for a single form
 *
 * @since 1.5.0
 *
 * @param routes URLs for endpoints, should have URL for /entries and /forms
 * @param perPage How many items to return for page
 * @param formId Form ID
 * @param nonce WordPress REST API authentication nonce.
 * @param $ jQuery
 *
 * @returns {{getForm: getForm, getEntries: getEntries, paginatedEntryURL: paginatedEntryURL, setPerPage: setPerPage}}
 *
 * @constructor
 */
function CFAPI( routes, perPage, formId, nonce,  $ ) {
    return {
        getForm: function () {
            return $.ajax({
                url: routes.form + formId,
                method: 'GET',
                beforeSend: function ( xhr ) {
                    xhr.setRequestHeader( 'X-WP-Nonce', nonce );
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
                    xhr.setRequestHeader( 'X-WP-Nonce', nonce );
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
                    xhr.setRequestHeader( 'X-WP-Nonce', nonce );
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
