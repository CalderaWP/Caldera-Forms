import {actionFunctions} from "../../state/actions/form";


export const setForm = (form, formId ) => {
    return actionFunctions.setForm(form,formId);
};

export const setForms = (forms) => {
    return actionFunctions.setForms(forms);
};


