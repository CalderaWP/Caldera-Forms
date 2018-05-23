import {printedData} from "../api/cfAdmin";

export const SET_FORMS = 'SET_FORMS';
export const SET_FORM = 'SET_FORM';
export const SET_CURRENT_FORM_ID = 'SET_CURRENT_FORM_ID';
export const ADD_FORM_PREVIEW = 'ADD_FORM_PREVIEW';



/**
 * Intial state
 *
 * @since 1.6.2
 *
 * @type {{forms, formPreviews: {}}}
 */
export const DEFAULT_STATE = {
    forms: printedData.forms,
    formPreviews :{},
};


/**
 * Shared redux(-like) action callbacks
 *
 * @type {{setForm(*=): *, setForms(*=): *, addFormPreview(*=, *=): *}}
 */
export const actionFunctions = {
    setForm(form){
        return {
            type: SET_FORM,
            form: form
        }
    },
    setForms( forms ) {
        return {
            type: SET_FORMS,
            forms:forms
        };
    },
    addFormPreview(formId,preview){
        return {
            type: ADD_FORM_PREVIEW,
            formId: formId,
            preview:preview
        }
    },
};