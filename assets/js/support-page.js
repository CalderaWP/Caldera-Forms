jQuery( document ).ready( function ( $ ) {
    'use strict';

    var hash = getHash();
    window.addEventListener("hashchange", hashChange, false);
    function hashChange(){
        hash = getHash();
        updateActive( hash );
        updateVisible( hash );
    };

    function updateActive( tab ){
        $(".caldera-forms-toolbar-link:not(#support-nav-" + tab + ")" ).removeClass( 'active' );
        $( '#support-nav-' + tab ).addClass( 'active' );
    };

    function updateVisible( tab ){
        $(".support-panel-wrap:not(#panel-support-" + tab + ")" ).attr( 'aria-hidden', 'true' ).css( 'visibility', 'hidden' ).hide();
        $( '#panel-support-' + tab ).attr( 'aria-hidden', 'false' ).css( 'visibility', 'visible' ).show();

    };

    function getHash(){
        hash = window.location.hash.substr(1);
        if( ! hash ){
            hash = 'info';
            window.location.hash = hash;
        }
        return hash;
    }

    updateActive( hash );
    updateVisible( hash );


} );
