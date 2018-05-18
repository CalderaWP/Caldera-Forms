import {findFormById} from "../../state/actions/functions";

/**
 * Get all of a form's privacy settings.
 *
 * @since 1.7.0
 *
 * @param {String} formId
 * @param {Object} state
 * @returns {*}
 */
export const getFormPrivacySettings = (formId,state) =>{
    return findFormById(state.privacyState, formId)
};