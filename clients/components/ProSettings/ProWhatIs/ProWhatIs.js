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
			<p className="pro-title">Caldera Forms Pro is an app + plugins that makes forms easy.</p>
			<h3><Twemoji className="emoji" text=":volcano:"/>Benefits</h3>
			<ul>
				<li key="enhanced-mail-delivery">
					<Twemoji className="emoji" text=":email:"/>
					Enhanced Email Delivery
				</li>
				<li key="form-to-pdf">
					<Twemoji className="emoji" text=":eyes:"/>
					Form To PDF
				</li>
				<li key="priority-support">
					<Twemoji className="emoji" text=":volcano:"/>
					Priority Support
				</li>
				<li key="add-ons">
					<Twemoji className="emoji" text=":plug:"/>
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