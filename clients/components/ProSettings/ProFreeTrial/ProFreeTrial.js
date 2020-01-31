import React from 'react';
import propTypes from 'prop-types';
import classNames from 'classnames';
import {Button} from 'react';

/**
 * Create the ProFreeTrial UI
 * @param {Object} props
 * @return {*}
 * @constructor
 */
export const ProFreeTrial = (props) => {
	const documentationHref = `https://calderaforms.com/doc/caldera-forms-pro-getting-started/?utm_source=wp-admin&utm_campaign=pro-screen&utm_term=not-connected`;
	const trialHref = `https://calderaforms.com/checkout?edd_action=add_to_cart&download_id=64101&edd_options[price_id]=1?utm_source=wp-admin&utm_campaign=pro-screen&utm_term=not-connected`;
	return(
		<div
			className={classNames(props.className,ProFreeTrial.classNames.wrapper)}
		>
			<p>Ready to try Caldera Forms Pro? Plans start at just 14.99/ month with a 7 day free trial.</p>
			<div>
				<a
					href={documentationHref}
					target={'_blank'}

					className={'button'}
				>
					Documentation
				</a>
				<a
					target={'_blank'}
					href={trialHref}
					className={'button button-primary'}
				>
					Start Free Trial
				</a>
			</div>
		</div>
	)
};

/**
 * Prop types for the ProFreeTrial component
 * @type {{}}
 */
ProFreeTrial.propTypes = {
	classNames: propTypes.string
};


/**
 * Class names used in ProFreeTrial component
 * @type {{wrapper: string}}
 */
ProFreeTrial.classNames = {
	wrapper: 'caldera-forms-pro-free-trial'
}
