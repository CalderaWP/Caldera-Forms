import {cfAdmin} from "./cfAdmin";

/**
 * Request a form from API
 *
 * @since 1.7.0
 *
 * @param {String} formId
 * @returns {Promise<*>}
 */
export async function requestForm(formId) {
    const form = await wp.apiRequest({
        url: `${cfAdmin.api.form}${formId}?preview=false`,
        method: 'GET',
        cache: true

    });
    return form;
};

/**
 * Request a form's privacy settings from API
 *
 * @since 1.7.0
 *
 * @param {String} formId
 * @returns {Promise<*>}
 */
export async function requestPrivacySettings(formId) {
    const form = await wp.apiRequest({
        url: `${cfAdmin.api.form}${formId}?privacy=true`,
        method: 'GET',
        cache: true

    });
    return form;
};

/**
 * Update a form's privacy settings via API
 *
 * @since 1.7.0
 *
 * @param {String} formId
 * @param {Object} settings
 * @returns {Promise<*>}
 */
export async function requestUpdatePrivacySettings(formId,settings) {
    const form = await wp.apiRequest({
        url: `${cfAdmin.api.form}${formId}?privacy=true`,
        method: 'POST',
        data: settings,
    });
    return form;
};

