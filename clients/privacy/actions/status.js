import {
    START_SPIN,
    STOP_SPIN,
    CLOSE_STATUS_INDICATOR,
    UPDATE_STATUS_INDICATOR
} from "../reducers/statusStore";


/**
 * Dispatch action to start spinner
 *
 * @since 1.7.0
 *
 * @returns {{type: string}}
 */
export const startSpinner = () => {
    return {
        type: START_SPIN
    }
};

/**
 * Dispatch action to stop spinner
 *
 * @since 1.7.0
 *
 * @param {Number} delay Optional. Number of ms before stopping. Default is 300
 *
 * @returns {{type: string}}
 */
export const stopSpinner = () => {
    return {
        type: STOP_SPIN
    }
};

/**
 * Dispatch action to close (hide) status indicator
 *
 * @since 1.7.0
 *
 * @param {Number} delay Optional. Number of ms before closing. Default is 300

 * @returns {{type: string}}
 */
export const closeStatus = () => {
    return {
        type: CLOSE_STATUS_INDICATOR
    }
};

/**
 * Update the status indicator
 *
 * @since 1.7.0
 *
 * @param {String} message Message to show in status indicator
 * @param {Boolean} success Optional. If true, the default, background is green for success. If false, red for failure.
 * @param {Boolean} show Optional. If true, the default, status indicator will show
 * @returns {{type: string, message: *, show: boolean, success: boolean}}
 */
export const updateStatus = (message, success = true, show = true) => {
    return {
        type: CLOSE_STATUS_INDICATOR,
        message,
        show,
        success
    }
};