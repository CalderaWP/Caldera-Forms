const { apiRequest } = wp;
const { registerStore, dispatch } = wp.data;
export const CALDERA_FORMS_STORE_NAME = 'caldera-forms/forms';
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
 * @type {{forms, formPreviews: {}, currentFormId: string}}
 */
const DEFAULT_STATE = {
    forms: printedData.forms,
    formPreviews :{},
    currentFormId: ''
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
    return state.forms.map(form => {
        return formId === form.ID
    });
};

//Track requests for previews to prevent multiple while pending
let requestingPreviews = [];
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

export const STORE = {
    reducer( state = DEFAULT_STATE, action ) {
        switch ( action.type ) {
            case SET_FORM:
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
            case  SET_CURRENT_FORM_ID:
                return{
                    ...state,
                    currentFormId:action.formId
                };


        }

        return state;
    },

    actions: {
        setForms( forms ) {
            return {
                type: SET_FORM,
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
        setCurrentFormId(formId) {
            return {
                type: SET_CURRENT_FORM_ID,
                formId: formId,
            }
        }
    },
    selectors: {
        getForm( state, formId ) {
            return findFormById(state, formId);
        },
        getForms( state ){
            return state.forms;
        },
        getCurrentForm( state ){
            return state.currentFormId
        },
        getFormPreview( state,formId ){
            console.log(formId);
            return state.formPreviews[ formId ];
        }
    },

};
