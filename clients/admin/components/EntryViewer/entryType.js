import PropTypes from  'prop-types';

export default {
	fields: PropTypes.arrayOf(PropTypes.shape({
		slug: PropTypes.string,
		value: PropTypes.oneOfType([
			PropTypes.string,
			PropTypes.number,
			PropTypes.array,
		]),
		id: PropTypes.oneOfType([
			PropTypes.string,
			PropTypes.number,
		]).isRequired,
	})).isRequired,
	user: PropTypes.shape({
		name: PropTypes.string,
		avatar: PropTypes.string,
		email: PropTypes.string,
		ID: PropTypes.oneOfType([
			PropTypes.string,
			PropTypes.number,
		]),
	}),
	id: PropTypes.oneOfType([
		PropTypes.string,
		PropTypes.number,
	]),
}