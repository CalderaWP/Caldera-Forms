import {setFormInState} from "../../state/actions/mutations";
import {SET_FORM_PRIVACY_SETTINGS} from "../actions";


/**
 * Reducer for privacy settings redux(-like) store
 *
 * @since 1.7.0
 *
 * @param {Object} state
 * @param {Object} action
 * @returns {*}
 */
export const privacyState = (state = {
    forms:[],
    formPreviews: {}
}, action ) => {
    switch (action.type){
        case SET_FORM_PRIVACY_SETTINGS :
            return setFormInState(state, action);
        default:
            return state;
    }
};