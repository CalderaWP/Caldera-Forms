import {CalderaFormsFieldGroup, Fragment} from "../CalderaFormsFieldGroup";
import {CalderaFormsFieldPropType} from "../CalderaFormsFieldRender";
import PropTypes from 'prop-types';


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
Input.propTypes = {
	field: PropTypes.shape(CalderaFormsFieldPropType),
	onChange: PropTypes.func.isRequired,
	shouldDisable: PropTypes.bool,
	isInvalid: PropTypes.bool,
	describedById: PropTypes.string,
};

Input.defaultProps = {
	ariaAttr: '',
	isInvalid:false
}