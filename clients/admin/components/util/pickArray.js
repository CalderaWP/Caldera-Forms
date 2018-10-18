export const pickArray = (array, key) => {
	return array.reduce(
		(accumualtor, item) =>
			accumualtor.concat([item[key]]), []
	);
}