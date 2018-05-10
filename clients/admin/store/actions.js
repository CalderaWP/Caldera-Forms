
import config from  './util/wpConfig';

export const ACTIONS = {
    getContentBoxData(context){
        const get = ( url ) => {
            return jQuery.get( url, {
                crossDomain: true
            } ).done( function(r){
                return r;
            }).fail( function(){
                return false;
            });
        };

        const pickRandomProperty =(obj) => {
            var result;
            var count = 0;
            for (var prop in obj)
                if (Math.random() < 1/++count)
                    result = prop;
            return result;
        };

        return new Promise((resolve, reject) => {
            jQuery.when(
                get(config.cfdotcom.api.important),
                get(config.cfdotcom.api.product)
            ).then(function (dImportant, dProduct) {
                context.commit('contentBoxData', {
                	importantDocs: dImportant[0],
					products: dProduct[0],
                    product: pickRandomProperty(dProduct[0])
				});
                context.commit( 'contentExtendTitle', config.extendTitle)


            });
        })
    },
};
