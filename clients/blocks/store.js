const { apiRequest } = wp;
const { registerStore, dispatch } = wp.data;
export const CALDERA_FORMS_STORE_NAME = 'caldera-forms/forms';
import {SET_FORM} from "../state/actions/form";
import {SET_FORMS} from "../state/actions/form";
import {ADD_FORM_PREVIEW} from "../state/actions/form";
import {setFormInState,setFormsInState} from "../state/actions/mutations";
import {findFormById} from "../state/actions/functions";
import {printedData,cfAdmin} from "../state/api/cfAdmin";
import {requestForm} from "../state/api";

import * as cfFormsState from '@caldera-labs/state';


import {calderaFormsFormState} from "@caldera-labs/state";

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

    wp.apiRequest({
        url: `${cfAdmin.api.form}${formId}?preview=true`,
        method: 'GET',
        cache: true

    }).then( (r) => {
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
        switch ( action.type ) {
            case SET_FORMS:
                return setFormsInState(state, action);
            case ADD_FORM_PREVIEW:
                state.formPreviews[action.formId] = action.preview;
                return {
                    ...state,
                    formPreviews:state.formPreviews
                };
            case SET_FORM :
                return setFormInState(state, action);

        }

        return state;
    },
    actions: cfFormsState.store.actions,
    selectors: {
        getForm( state, formId ) {
            return findFormById(state, formId);
        },
        getForms( state ){
            return state.forms;
        },
        getFormPreview( state,formId ){
            return state.formPreviews.hasOwnProperty( formId )
                ?state.formPreviews[ formId ]
                : '';

        },
        getFormPreviews(state){
            return state.formPreviews;
        }
    },
    resolvers: {
        async getForm( state, formId ) {
            const form = await requestForm(formId);
            dispatch( CALDERA_FORMS_STORE_NAME ).setForm( form );
        },
    },

};
