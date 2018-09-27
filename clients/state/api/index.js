import {formsAdminApiClient} from "./apiClients";
import {privacySettingsClient} from "./apiClients";

/**
 * Request a form from API
 *
 * @since 1.7.0
 *
 * @param {String} formId
 * @returns {Promise<*>}
 */
export async function requestForm(formId) {
    return await formsAdminApiClient.getForm(formId);
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
	return await privacySettingsClient.getSettings(formId);
};

/**
 * Update a form's privacy settings via API
 *
 * @since 1.7.0
 *
 * @param {Object} settings
 * @param {String} formId
 * @returns {Promise<*>}
 */
export async function requestUpdatePrivacySettings(settings,formId) {
	return await privacySettingsClient.updateSettings(formId,settings);
};

