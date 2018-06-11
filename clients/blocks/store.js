import { registerStore, dispatch } from "@wordpress/data";
export const CALDERA_FORMS_STORE_NAME = 'caldera-forms/forms';
import {printedData,cfAdmin} from "../state/api/cfAdmin";
import {requestForm} from "../state/api";
import * as cfFormsState from '@caldera-labs/state';
import {calderaFormsFormState} from "@caldera-labs/state";
import {formsAdminApiClient} from "../state/api/apiClients";

export const DEFAULT_STATE = {
    forms: Array.isArray(printedData.forms)?printedData.forms:[],
    formPreviews: {}
};

//Track requests for previews to prevent multiple while pending
let requestingPreviews = [];
/**
 * Request form preview HTML from server
 *
 * @since 1.6.2
 *
 * @param {Object} state
 * @param {String} formId
 */
export const requestFormPreview = (state,formId) => {
    if( 'false' !== formId && requestingPreviews.includes(formId)){
        return;
    }
    requestingPreviews.push(formId);

	formsAdminApiClient.getFormPreview(formId).then( (r) => {
        dispatch(CALDERA_FORMS_STORE_NAME).addFormPreview(formId, r);
    } );
};


/**
 * Caldera Forms Redux-store
 *
 * @since 1.6.2
 *
 * @type {{reducer: (function(*=, *)), actions: {setForm: (function(*=)), setForms: (function(*=)), addFormPreview: (function(*=, *=))}, selectors: {getForm: (function(*=, *=)), getForms: (function(*)), getFormPreview: (function(*, *=)), getFormPreviews: (function(*))}, resolvers: {getForm: (function(*, *): Promise)}}}
 */
export const STORE = {
    reducer( state = DEFAULT_STATE, action ) {
        return cfFormsState.store.reducers.formsReducer(DEFAULT_STATE,action);
    },
    actions: cfFormsState.store.actions,
    selectors: cfFormsState.store.selectors,
    resolvers: {
        async getForm( state, formId ) {
            const form = await requestForm(formId);
            dispatch( CALDERA_FORMS_STORE_NAME ).setForm( form );
        },
    },

};
