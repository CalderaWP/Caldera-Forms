/**
 * API config and other stuff passed to jsLand via wp_localize_script
 */
export default {
    cfdotcom: 'object' === typeof CF_CLIPPY ? CF_CLIPPY.cfdotcom : {},
    fallback: 'object' === typeof CF_CLIPPY ? CF_CLIPPY.fallback : {},
    extendTitle:  'object' === typeof CF_CLIPPY ? CF_CLIPPY.extend_title : '',
    noForms:  'object' === typeof CF_CLIPPY ? CF_CLIPPY.noForms : '',
};
