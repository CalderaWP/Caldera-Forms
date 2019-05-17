import querystring from 'querystring';

/**
 * Utility function for creating links with utms based on last search.
 * @param lastParams
 * @param link
 * @returns {string}
 */
const url =function (lastParams,link) {
    let query = {
        utm_source: 'search'
    };

    if( lastParams.categories){
        query.utm_term = lastParams.categories;
    }

    if( lastParams.search && undefined !== lastParams.search ){
        query.utm_keyword = lastParams.search;
    }

    return `${link}?${querystring.stringify(query)}`

};

export  default url;