/**
 * Given array of objects, return an array with only one specific key of each item in array
 *
 * @since 1.8.6
 *
 * @param array
 * @param key
 * @returns {*}
 */
export const pickArray = (array, key) => {
	return array.reduce(
		(accumualtor, item) =>
			accumualtor.concat([item[key]]), []
	);
};
