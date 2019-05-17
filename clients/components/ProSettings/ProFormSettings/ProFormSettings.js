import React from 'react';
import propTypes from 'prop-types';
import {SettingsGroup} from "../../../../../../Desktop/components/Settings/SettingsGroup";
import configFields, {PRO_FORM_EMAIL_LAYOUT, PRO_FORM_PDF_LAYOUT} from './configFields'
/**
 * Create the ProFormSettings UI
 * @param {Object} props
 * @return {*}
 * @constructor
 */
export class ProFormSettings extends SettingsGroup {

	/**
	 * Get config fields
	 *
	 * Adds pro layouts as options when possible
	 * @return {Array}
	 */
	getConfigFields(){
		let configFields = super.getConfigFields();
		if ( this.props.layouts.length ) {
			configFields.forEach(configField => {
				if ([
					PRO_FORM_EMAIL_LAYOUT,
					PRO_FORM_PDF_LAYOUT
				].includes(configField.id)) {
					configField.options = this.props.layouts;
				}
			})
		}
		return configFields;
	}
};

/**
 * Prop types for the ProFormSettings component
 * @type {{}}
 */
ProFormSettings.propTypes = {
	classNames: propTypes.string,
	layouts: propTypes.array
};

/**
 * Default props for the ProFormSettings component
 * @type {{}}
 */
ProFormSettings.defaultProps = {
	configFields,
	wrapperClass: 'caldera-forms-pro-form-settings',
	layouts: []
};
