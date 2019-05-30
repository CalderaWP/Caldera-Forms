/**
 * Creates option values for select fields
 *
 * @since 1.8.6
 *
 * @param {String|number|Boolean} value Value of option
 * @param {String|number} label Optional. Label for option
 * @param {Object} other Optional. Additional data to add
 * @return {{value: *, label: *}}
 */
export const optionFactory = (value, label = null, other = {} ) => {
	return {
		...other,
		value,
		label: label ? label : value,
	}
};
