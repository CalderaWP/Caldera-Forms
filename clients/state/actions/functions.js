import * as cfFormsState from '@caldera-labs/state';

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
    return cfFormsState.util.formHasId(form,formId);
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
    return cfFormsState.util.findFormById(state.forms,formId);
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
    return cfFormsState.util.findFormIndexById(state.forms,formId);
};

