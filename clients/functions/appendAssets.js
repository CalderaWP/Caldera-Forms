const assetsAppended = {
    css: [],
    js: [],
};

/**
 * Append CSS/JS files to DOM so forms look right
 *
 * @since 1.7.0
 * @param {Array} js
 * @param {Array}  css
 */
export function appendAssets(js,css) {
    Object.keys(css).forEach( key => {
        appendAsset('css',css[key],key);
    });
    Object.keys(js).forEach( key => {
        appendAsset('js',js[key],key);
    });}
/**
 * Append CSS or JavaScript as needed if not already done
 *
 * @since 1.7.0
 *
 * @param {String} type
 * @param {String} url
 * @param {String} identifier
 */
function appendAsset(type, url, identifier)
{

    switch( type ){
        case  'css' :
            if ( -1 < assetsAppended.css.indexOf( identifier ) ) {
                const fileref = document.createElement("link");
                fileref.rel = "stylesheet";
                fileref.type = "text/css";
                fileref.href = url;
                fileref.id = identifier;
                document.getElementsByTagName("head")[0].appendChild(fileref);
                assetsAppended.css.push(identifier);

            }

            break;
        case 'js' :

            if ( -1 < assetsAppended.js.indexOf( identifier ) ) {
                const fileref = document.createElement("script");
                fileref.type = "text/javascript";
                fileref.src = url;
                fileref.id = identifier;
                document.getElementsByTagName("body")[0].appendChild(fileref);
                assetsAppended.js.push(identifier);
            }
    }

}