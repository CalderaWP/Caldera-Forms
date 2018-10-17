const sortBy = require('lodash.sortby');
/**
 * Sort forms by a key of the form's config
 * @param {String} sortFormsBy Key to sort by
 * @param {Object} forms Forms to sort
 */
export const sortFormsBy = (sortFormsBy, forms )  => {
	const sortedArray = sortBy(Object.values(forms), [(form) => { return form[sortFormsBy]; }]);
	let sortedForms = {};
	sortedArray.forEach(sortedForm => {
		sortedForms[sortedForm.ID] = sortedForm;
	});

	return sortedForms;
};