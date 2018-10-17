import React from 'react';
import {Spinner} from '@wordpress/components'
import {Admin} from '@caldera-labs/components'
import PropTypes from 'prop-types';

export const TopNav = (props) => {

	const {onClickSettings, onClickNewForm,mainStatus} = props;
	return (
		<React.Fragment>
			<Admin.CalderaHeader>
				<li>
					<button
						className={'button button-primary'}
						onClick={onClickNewForm}
					>
						New Form
					</button>
				</li>
				<li>
					<button
						className={'button button-secondary'}
						onClick={onClickSettings}
					>
						Settings
					</button>
				</li>
				{mainStatus.loading &&
				<li>
					<div className="spinner" />

				</li>
				}
				{mainStatus.show &&
				<li>
					<Admin.StatusIndicator
						message={mainStatus.message}
						show={mainStatus.show}
						success={mainStatus.success}
					/>
				</li>
				}
			</Admin.CalderaHeader>


		</React.Fragment>
	)
};

TopNav.propTypes = {
	onClickNewForm: PropTypes.func.isRequired,
	onClickSettings: PropTypes.func.isRequired,
	mainStatus: PropTypes.shape({
		loading: PropTypes.bool.isRequired,
		show: PropTypes.bool.isRequired,
		success: PropTypes.bool,
		message: PropTypes.string,
	}).isRequired
}