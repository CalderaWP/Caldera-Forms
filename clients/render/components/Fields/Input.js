import React,{ Fragment} from 'react';
import {CalderaFormsFieldGroup} from "../CalderaFormsFieldGroup";
import {CalderaFormsFieldPropType} from "../CalderaFormsFieldRender";
import PropTypes from 'prop-types';

/**
 * Input component
 *
 * @since 1.8.0
 *
 * @param props
 * @return {*}
 * @constructor
 */
export const Input = (props) => {
	const{shouldDisable,field,describedById,onChange} = props;
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
		isInvalid
	} = field;


	const propsThatDoNotUseAnEquals = {};
	if (shouldDisable) {
		propsThatDoNotUseAnEquals.disabled = true;
	} else {
		propsThatDoNotUseAnEquals.disabled = false;
	}
	if (required) {
		propsThatDoNotUseAnEquals.required = true;
	} else {
		propsThatDoNotUseAnEquals.required = false;
	}

	return(
		<input
			type={type}
			{...propsThatDoNotUseAnEquals}
			aria-describedby={describedById}
			value={fieldValue}
			className={'cf2-text form-control'}
			id={fieldIdAttr}
			placeholder={fieldPlaceHolder}
			onChange={onChange}
			data-field={fieldId}
			data-type={'cf2-text'}
			name={fieldId}
		/>
	)
};

/**
 * Prop definitions for Input component
 *
 * @since 1.8.0
 *
 * @type {{field: *, onChange: (e|*), shouldDisable: *, isInvalid: *, describedById: *}}
 */
Input.propTypes = {
	field: PropTypes.shape(CalderaFormsFieldPropType),
	onChange: PropTypes.func.isRequired,
	shouldDisable: PropTypes.bool,
	isInvalid: PropTypes.bool,
	describedById: PropTypes.string,
};

/**
 * Default props for Input component
 *
 * @since 1.8.0
 *
 * @type {{ariaAttr: string, isInvalid: boolean}}
 */
Input.defaultProps = {
	ariaAttr: '',
	isInvalid:false
};
