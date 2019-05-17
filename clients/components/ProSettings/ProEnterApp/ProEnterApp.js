import React from 'react';
import classNames from 'classnames'

/**
 * Create the global form settings UI
 * @param {Object} props
 * @return {*}
 * @constructor
 */
export const ProEnterApp = (props) => {
	return(
		<div
			className={classNames(props.className,ProEnterApp.classNames.wrapper)}
		>
			ProEnterApp
		</div>
	)
};



/**
 * Class names used in the GlobalForms settings component
 * @type {{wrapper: string}}
 */
ProEnterApp.classNames = {
	wrapper: 'caldera-forms-global-form-settings'
};