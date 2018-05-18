import {combineReducers} from 'redux'
import {
    setForms,
    setForm,
    UNSET_EDIT_FORM
} from '../actions';
import {SET_EDIT_FORM} from "../actions";
import {SET_FORM, SET_FORMS} from "../../state/actions/form";
import {DEFAULT_STATE} from "../../state/actions/form";
import {setFormInState, setFormsInState} from "../../state/actions/mutations";
import {findFormById} from "../../state/actions/functions";
import {privacyState} from "./privacyStore";

const initialState = {
    ...{
        editForm: {},
    },
    ...DEFAULT_STATE
};


/**
 * Reducer for Redux(-like) state managment of form settings
 *
 * @since 1.6.2
 *
 * @param {Object} state
 * @param {Object} action
 * @returns {Object}
 */
const formState = (state = initialState, action) =>
{
    switch (action.type) {
        case SET_FORMS:
            return setFormsInState(state, action);
        case SET_FORM :
            return setFormInState(state, action);
        case SET_EDIT_FORM :
            const editForm = findFormById(state,action.formId);
            return {
                ...state,
                editForm: editForm
            };
        case UNSET_EDIT_FORM :
            return {
                ...state,
                editForm: {}
            };

    }

    return state;
};

/**
 * Combined reducers for privacy screen
 *
 * @since 1.7.0
 *
 * @type {Reducer<any> | Reducer<any, AnyAction>}
 */
const rootReducer = combineReducers({
    formState,
    privacyState
});

export default rootReducer
