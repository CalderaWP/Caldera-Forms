/** Wrappers around objects added via wp_localize_script() **/

/**
 * Should be an array of forms
 * @type {Array}
 */
export const printedData = 'object' === typeof  window.CF_FORMS ? window.CF_FORMS : [];

const _cfAdmin = 'object' === typeof CF_ADMIN ? CF_ADMIN : {};

/**
 * Creates the config object we expect CF_ADMIn to have
 *
 * @since 1.7.2
 *
 * @param _cfAdmin
 * @returns {*}
 */
export function createCFadminConfig(_cfAdmin) {
    return Object.assign(
        {},
        {
            api: {
                root: '',
                form: '',
                entries: '',
                entrySettings: '',
                nonce: ''
            },
            adminAjax: '',
            dateFormat: 'F j, Y g:i a',
            rest: {
                root: '',
                nonce: '',
            }
        },
        _cfAdmin
    );
}

/**
 * Should be API settings
 * @type {{} & {api: {root: string, form: string, entries: string, entrySettings: string, nonce: string}, adminAjax: string, dateFormat: string, rest: {root: string, nonce: string}}}
 */
export const cfAdmin = createCFadminConfig(_cfAdmin);