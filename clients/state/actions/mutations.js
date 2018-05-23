import {findFormIndexById} from "./functions";

/**
 * Add one form to state
 *
 * @since 1.7.0
 *
 * @param {Object} state
 * @param {Object} action
 *
 * @returns {{forms: *}}
 */
export function setFormInState(state, action) {
    let forms = state.forms;
    const index = findFormIndexById(state, action.form.ID);
    if (-1 <= index) {
        forms.splice(index, 1, action.form);
    } else {
        forms.push(action.form);
    }
    return {
        ...state,
        forms: forms
    };
};

/**
 * Add forms to State
 *
 * @since 1.7.0
 *
 * @param {Object} state
 * @param {Object} action
 * @returns {{forms: *}}
 */
export function setFormsInState(state, action) {
    return {
        ...state,
        forms: action.forms
    };
};

