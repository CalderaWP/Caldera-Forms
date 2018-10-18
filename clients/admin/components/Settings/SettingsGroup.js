import React from 'react';
import PropTypes from 'prop-types';
import {object, pick} from "dot-object";
import {RenderGroup} from '@caldera-labs/components';
import {Button} from '@wordpress/components';
import classNames from 'classnames'
export class SettingsGroup extends React.PureComponent{

	constructor(props) {
		super(props);
		this.state = {
			[props.settingsKey]: props.settings
		};
		this.onSettingsChange = this.onSettingsChange.bind(this);
		this.getConfigFields = this.getConfigFields.bind(this);
		this.onSettingsSave = this.onSettingsSave.bind(this);
		this.wrapperClass = this.wrapperClass.bind(this);

	}

	/**
	 * Save the settings
	 */
	onSettingsSave() {
		this.props.onSettingsSave(this.state[this.props.settingsKey]);
	}

	/**
	 * Update internal state when settings change
	 *
	 * @param {Object} update
	 */
	onSettingsChange(update) {
		this.setState(
			{
				[this.props.settingsKey]:update
		});
	}

	/**
	 * Prepare config fields
	 *
	 * @return {Array}
	 */
	getConfigFields() {
		let currentConfigFields = this.props.configFields;
		currentConfigFields.forEach(configField => {
			const {path} = configField;
			if( undefined !== path ){
				configField.value = pick(
					path,
					this.state
				);
				configField.onValueChange = (newValue) => {
					const update = {
						...this.state,
						[path]:newValue
					};
					this.onSettingsChange(object(update));
				};
			}





		});
		return currentConfigFields;
	};

	/**
	 * Get the class for the outer element
	 * @return {String}
	 */
	wrapperClass(){
		return this.props.wrapperClass;
	}


	render(){
		if( Array.isArray( this.props.configFields ) ) {
			return(
				<div
					className={classNames(
						this.wrapperClass(),
						this.props.classNames
					)}
				>
					<RenderGroup
						configFields={this.getConfigFields()}
					/>
					<Button
						onClick={this.props.onSettingsSave}
					>
						{this.props.saveButtonText}
					</Button>
				</div>
			)
		}
		return <div>!</div>

	}

}

/**
 * Prop types for the SettingsGroup component
 * @type {{}}
 */
SettingsGroup.propTypes = {
	classNames: PropTypes.string,
	settings: PropTypes.object.isRequired,
	settingsKey: PropTypes.string.isRequired,
	onSettingsSave: PropTypes.func.isRequired,
	configFields: PropTypes.array,
	saveButtonText: PropTypes.string,
};

SettingsGroup.defaultProps= {
	saveButtonText: 'Save'
}