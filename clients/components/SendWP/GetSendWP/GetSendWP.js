import React from 'react';
import classNames from 'classnames';
import { __ } from "@wordpress/i18n";

/**
 * Create the UI for SendWP information and, link and Remote install
 * @param {Object} props
 * @return {*}
 * @constructor
 */
export const GetSendWP = (props) => {	


	return(
		<div className={classNames(props.className,GetSendWP.classNames.wrapper)} style={{margin:'20px'}}>
			<div className="caldera-forms-clippy-zone-inner-wrap" style={{background: 'white'}}>
				<div className="caldera-forms-clippy"
					style={{textAlign:'center',backgroundColor:'white',padding:'20px'}}>
					<h2>
						{__( 'Getting WordPress email into an inbox just got a lot easier!', 'caldera-forms' )}
					</h2>
					<p>
						{__(
							'SendWP makes getting emails delivered as simple as a few clicks. So you can relax, knowing those important emails are being delivered on time.',
							'caldera-forms'
						)}
					</p>
					<a href="https://sendwp.com?utm_source=Caldera+Forms+Plugin&utm_medium=Forms_Email+Settings&utm_campaign=SendWP+banner+ad"
						target="_blank" className="bt-btn btn btn-green" style={{width: '80%',margin: 'auto', paddingTop: '12px' }}>
						{__( 'Learn More', 'caldera-forms' )}
					</a>
				</div>
			</div>
		</div>
	)
};

/**
 * Class names used in the GlobalForms settings component
 * @type {{wrapper: string}}
 */
GetSendWP.classNames = {
	wrapper: 'caldera-forms-global-form-settings'
};