export const isObject = function (value) {
	/**@TODO npm in just this function from lodash, beacuse the fact that I copypasted 3 lines of code is THE WORST THING EVER! https://github.com/lodash/lodash/blob/master/isObject.js **/
	const type = typeof value;
	return value != null && (type == 'object' || type == 'function');
	//Look - I added semicolons
};

export const hasProp = function (maybeObj, prop) {
	return isObject(maybeObj) && objHasProp(maybeObj,prop);
};

export const objHasProp = function(obj,prop) {
	return Object.prototype.hasOwnProperty.call(obj, prop);
};

export const findForm = function(state,formId){
	return state.forms.find(form =>
		form.form_id === formId
	);
};

export const findFormOffset = function(state,formId){
	return state.forms.findIndex(form => form.form_id === formId);
};
