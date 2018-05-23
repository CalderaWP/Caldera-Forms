/** Wrappers around objects added via wp_localize_script() **/

export const printedData = 'object' === typeof  CF_FORMS ? CF_FORMS : [];
export const cfAdmin = 'object' === typeof CF_ADMIN ? CF_ADMIN : {};