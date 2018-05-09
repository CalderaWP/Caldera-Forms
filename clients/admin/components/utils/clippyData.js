
export function clippyData($) {
    $.when(
        get(config.cfdotcom.api.important),
        get(config.cfdotcom.api.product)
    ).then(function (dImportant, dProduct) {
        var importantDocs = dImportant[0],
            products = dProduct[0],
            product = products[pickRandomProperty(products)],
            showExtend = 0 < Object.keys(products).length,
            showDocs = 0 < Object.keys(importantDocs).length;
    });

    function get( url ) {
        return $.get( url, {
            crossDomain: true
        } ).done( function(r){
            return r;
        }).fail( function(){
            return false;
        });
    }

    function pickRandomProperty(obj) {
        var result;
        var count = 0;
        for (var prop in obj)
            if (Math.random() < 1/++count)
                result = prop;
        return result;
    }
}