import {
    actionFunctions,
    SET_FORM
} from "../../state/actions/form";
export const SET_EDIT_FORM = 'SET_EDIT_FORM';
export const UNSET_EDIT_FORM = 'UNSET_EDIT_FORM';

/**
 * Set a form in state.
 *
 * Designed for use with "form" reducer
 *
 * @since 1.7.0
 *
 * @param {Object}  forms
 * @param {String} formId
 * @returns {*|{type, form}}
 */
export const setForm = (form, formId ) => {
    return actionFunctions.setForm(form, formId);
}/**
 *
 * Designed for use with "form" reducer
 *
 * @since 1.7.0
 *
 * @param {String} formId
 * @returns {{type: string, formId: *}}
 */
export const setEditForm = (formId ) => {
    return {
        type: SET_EDIT_FORM,
        formId: formId
    }
};

/**
 * Unset the form currently being edited.
 *
 * Designed for use with "form" reducer
 *
 * @since 1.7.0
 *
 * @returns {{type: string}}
 */
export const unsetEditForm = () => {
    return {
        type: UNSET_EDIT_FORM,
    }
};


/**
 *
 * @param {Object}  forms
 * @returns {*|{type, forms}}
 */
export const setForms = (forms) => {
    return actionFunctions.setForms(forms);
};


export const SET_FORM_PRIVACY_SETTINGS = 'SET_FORM_PRIVACY_SETTINGS';
export const setFormPrivacyForm = (settings) => {
    return {
        type: SET_FORM_PRIVACY_SETTINGS,
        form: settings
    }
};

