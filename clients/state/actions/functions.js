/**
 * Check if a form has the provided ID
 *
 * @since 1.6.2
 *
 * @param {Object} form Form config
 * @param {String} formId
 * @return {boolean}
 */
export const formHasId = ( form, formId ) => {
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
export const findFormById = (state, formId,) => {
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
export const findFormIndexById = (state, formId) => {
    return state.forms.findIndex(form => {
        return formHasId(form,formId);
    });
};

