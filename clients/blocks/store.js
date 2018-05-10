const { apiRequest } = wp;
const { registerStore, dispatch } = wp.data;
export const CALDERA_FORMS_STORE_NAME = 'caldera-forms/forms';
export const SET_FORMS = 'SET_FORMS';
export const SET_FORM = 'SET_FORM';
export const SET_CURRENT_FORM_ID = 'SET_CURRENT_FORM_ID';
export const ADD_FORM_PREVIEW = 'ADD_FORM_PREVIEW';
let printedData = 'object' === typeof  CF_FORMS ? CF_FORMS : [];
let cfAdmin = 'object' === typeof CF_ADMIN ? CF_ADMIN : {};

/**
 * Intial state
 *
 * @since 1.6.2
 *
 * @type {{forms, formPreviews: {}}}
 */
const DEFAULT_STATE = {
    forms: printedData.forms,
    formPreviews :{},
};

/**
 * Check if a form has the provided ID
 *
 * @since 1.6.2
 *
 * @param {Object} form Form config
 * @param {String} formId
 * @return {boolean}
 */
const formHasId = ( form, formId ) => {
    if( 'object' !== typeof  form ){
        return false;
    }
    if( form.hasOwnProperty('ID') ){
        return formId === form.ID;
    }
    if( form.hasOwnProperty('formId') ){
        return formId === form.formId;
    }
    return false;
};

/**
 * Find form in state by Id
 *
 * @since 1.6.2
 *
 * @param {Object} state
 * @param {String} formId
 */
const findFormById = (state, formId) => {
    return state.forms.find(form => {
        return formHasId(form,formId);
    });
};

/**
 * Find form index in state by Id
 *
 * @since 1.6.2
 *
 * @param {Object} state
 * @param {String} formId
 */
const findFormIndexById = (state, formId) => {
    return state.forms.findIndex(form => {
        return formHasId(form,formId);
    });
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
                return {
                    ...state,
                    forms: action.forms
                };
            case ADD_FORM_PREVIEW:
                state.formPreviews[action.formId] = action.html;

                return {
                    ...state,
                    formPreviews:state.formPreviews
                };
            case SET_FORM :
                let forms = state.forms;
                const index = findFormIndexById(state, action.form.ID );
                if(-1 <= index){
                    forms[index] = action.from;
                }else{
                    forms.push(action.form);
                }
                console.log(forms);
                return {
                    ...state,
                    forms: forms
                };

        }

        return state;
    },

    actions: {
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
        addFormPreview(formId,html){
            return {
                type: ADD_FORM_PREVIEW,
                formId: formId,
                html:html
            }
        },
    },
    selectors: {
        getForm( state, formId ) {
            return findFormById(state, formId);
        },
        getForms( state ){
            return state.forms;
        },
        getFormPreview( state,formId ){
            return state.hasOwnProperty( formId )
                ?state.formPreviews[ formId ]
                : '';
        },
        getFormPreviews(state){
            return state.formPreviews;
        }
    },
    resolvers: {
        async getForm( state, formId ) {
            const form = await wp.apiRequest({
                url: `${cfAdmin.api.form}${formId}?preview=false`,
                method: 'GET',
                cache: true

            } );
            dispatch( CALDERA_FORMS_STORE_NAME ).setForm( form );
        },
    },

};
