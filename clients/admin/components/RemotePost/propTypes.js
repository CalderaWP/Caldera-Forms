import PropTypes from 'prop-types'

/**
 * Prop type definitions for the RemotePost component
 *
 * @type {{form: shim, formId: (boolean|*), onFormUpdate: (boolean|*)}}
 */
export const postPropTypes = {
	post: PropTypes.shape({
		id: PropTypes.oneOfType([
			PropTypes.string,
			PropTypes.number
		]).isRequired,
		title: PropTypes.shape({
			rendered: PropTypes.string.isRequired,
		}),
		content: PropTypes.shape({
			rendered: PropTypes.string.isRequired,
		}),
	}).isRequired
};

