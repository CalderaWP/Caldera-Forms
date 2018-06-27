export const SET_FORMS = 'SET_FORMS';
export const SET_FORM = 'SET_FORM';
export const SET_CURRENT_FORM_ID = 'SET_CURRENT_FORM_ID';
export const ADD_FORM_PREVIEW = 'ADD_FORM_PREVIEW';

import * as cfFormsState from '@caldera-labs/state';


/**
 * Shared redux(-like) action callbacks
 *
 * @type {{setForm(*=): *, setForms(*=): *, addFormPreview(*=, *=): *}}
 */
export const actionFunctions = cfFormsState.store.actions;