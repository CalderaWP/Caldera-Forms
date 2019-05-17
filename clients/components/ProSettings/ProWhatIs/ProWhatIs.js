import React from 'react';
import propTypes from 'prop-types';
import classNames from 'classnames'
import {Twemoji} from 'react-emoji-render';

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
			<h3><Twemoji text=":volcano:"/>Benefits</h3>
			<ul>
				<li>
					<Twemoji text=":email:"/>
					Enhanced Email Delivery
				</li>
				<li>
					<Twemoji text=":eyes:"/>
					Form To PDF
				</li>
				<li>
					<Twemoji text=":volcano:"/>
					Priority Support
				</li>
				<li>
					<Twemoji text=":plug:"/>
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