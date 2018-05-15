import {cfAdmin} from "./cfAdmin";

export async function requestForm(formId) {
    const form = await wp.apiRequest({
        url: `${cfAdmin.api.form}${formId}?preview=false`,
        method: 'GET',
        cache: true

    });
    return form;
};