import React from 'react';
import propTypes from 'prop-types';
import classNames from 'classnames'

/**
 * Create the ProWhatIs UI
 * @param {Object} props
 * @return {*}
 * @constructor
 */
export const ProWhatIs = (props) => {
	return(
		<div
			className={classNames(props.className,ProWhatIs.classNames.wrapper)}
		>
			<p>Caldera Forms Pro is an app + plugin that makes forms easy.</p>
			<h3>Benefits</h3>
			<ul>
				<li>
					Enhanced Email Delivery
				</li>
				<li>
					Form To PDF
				</li>
				<li>
					Priority Support
				</li>
				<li>
					Add-ons Included in Yearly Plans
				</li>
			</ul>
		</div>
	)
};

/**
 * Prop types for the ProWhatIs component
 * @type {{}}
 */
ProWhatIs.propTypes = {
	classNames: propTypes.string
};


/**
 * Class names used in the ProWhatIs component
 * @type {{wrapper: string}}
 */
ProWhatIs.classNames = {
	wrapper: 'caldera-forms-pro-what-is'
}