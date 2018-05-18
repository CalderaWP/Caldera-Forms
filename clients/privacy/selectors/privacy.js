import {findFormById} from "../../state/actions/functions";

export const getFormPrivacySettings = (formId,state) =>{
    return findFormById(state.privacyState, formId)
};