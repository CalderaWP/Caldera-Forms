import {combineReducers} from 'redux'
import {
    setForms,
    setForm,
} from '../actions';
import {ADD_FORM_PREVIEW, SET_FORM, SET_FORMS} from "../../state/actions/form";
import {DEFAULT_STATE} from "../../state/actions/form";
import {setFormInState, setFormsInState} from "../../state/actions/mutations";

/**
 * Simple form state b
 * @param state
 * @param action
 * @returns {*}
 */
const formState = (state = DEFAULT_STATE, action) =>
{
    switch (action.type) {
        case SET_FORMS:
            return setFormsInState(state, action);
        case SET_FORM :
            return setFormInState(state, action);

    }

    return state;
}

const rootReducer = combineReducers({
    formState
});

export default rootReducer
