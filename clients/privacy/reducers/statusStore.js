
import {DEFAULT_STATE} from "../../state/actions/form";
export const START_SPIN = 'START_SPIN';
export const STOP_SPIN = 'STOP_SPIN';
export const CLOSE_STATUS_INDICATOR = 'CLOSE_STATUS_INDICATOR';
export const UPDATE_STATUS_INDICATOR = 'UPDATE_STATUS_INDICATOR';

/**
 * Reducer for redux(-like) store managing spinner and success
 *
 * @since 1.7.0
 *
 * @param {Object} state
 * @param {Object} action
 * @returns {*}
 */
export const statusState = (state = {
    show:false,
    message: '',
    success: true,
    spin: false,
}, action ) => {
    switch (action.type){
        case START_SPIN :
            return {
                ...state,
                spin: true
            };
        case STOP_SPIN : {
            return {
                ...state,
                spin: false
            };
        }
        case CLOSE_STATUS_INDICATOR: {
            return {
                ...state,
                show: false
            };
        }
        case UPDATE_STATUS_INDICATOR: {
            return {
                ...state,
                show:action.show,
                message: action.message,
                success: action.success,
            };
        }
        default:
            return state;
    }

};