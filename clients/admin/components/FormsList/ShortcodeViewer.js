import React from 'react';
import propTypes from 'prop-types'

/**
 * Show shortcode viewer UI
 * @param props
 * @return {*}
 * @constructor
 */
export const ShortcodeViewer = (props) => {
	if (true === props.show) {
		return (
			<span>

				<span>
					[caldera_forms="{props.formId}"]
				</span>
				<button
					className="button cf-form-shortcode-preview-button"
					onClick={props.onButtonClick}
				>
				Close
			</button>
			</span>

		)
	}
	return (
		<button
			className="button cf-form-shortcode-preview-button"
			onClick={props.onButtonClick}
		>
			Get Shortcode
		</button>
	)
}

ShortcodeViewer.propTypes = {
	formId: propTypes.string.isRequired,
	onButtonClick: propTypes.func.isRequired,
	show: propTypes.bool
};

ShortcodeViewer.defaultProps = {
	show: false
}



