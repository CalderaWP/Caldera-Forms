import {Component, Fragment} from 'react';
import PropTypes from 'prop-types';
import {CalderaFormsFieldPropType} from "./CalderaFormsFieldRender";

/**
 * Render a Caldera Forms v2 field
 *
 * @since 1.8.0
 *
 * @param props
 * @return {*}
 * @constructor
 */
export const CalderaFormsFieldGroup = (props) => {
	const {field, onChange, shouldDisable, shouldShow} = props;
	const {
		type,
		outterIdAttr,
		fieldId,
		fieldLabel,
		fieldCaption,
		required,
		fieldPlaceHolder,
		fieldDefault,
		fieldValue,
		fieldIdAttr,
	} = field;

	if (!shouldShow) {
		return <Fragment/>;
	}
	return (

			<div className={'form-group cf2-field-group'}>
				<label
					className={'control-label'}
					htmlFor={fieldIdAttr}
				>
					{fieldLabel}
				</label>
				<input
					type={type}
					disabled={shouldDisable}
					value={fieldValue}
					required={required}
					className={'form-control'}
					id={fieldIdAttr}
					placeholder={fieldPlaceHolder}
					onChange={onChange}
				/>
			</div>

	);

}



/**
 * Prop Type definitions for CalderaFormsFieldGroup component
 *
 * @since 1.8.0
 *
 * @type {{field: *, onChange: (e|*), shouldShow: *, shouldDisable: *}}
 */
CalderaFormsFieldGroup.propTypes = {
	field: CalderaFormsFieldPropType,
	onChange: PropTypes.func.isRequired,
	shouldShow: PropTypes.bool,
	shouldDisable: PropTypes.bool
};

/**
 * Default props for the CalderaFormsFieldGroup component
 *
 * @since 1.8.0
 *
 * @type {{shouldShow: boolean, shouldDisable: boolean, fieldValue: string}}
 */
CalderaFormsFieldGroup.defaultProps = {
	shouldShow: true,
	shouldDisable: false,
	fieldValue: ''
};