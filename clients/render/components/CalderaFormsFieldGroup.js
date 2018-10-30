import {Component, Fragment} from 'react';
import PropTypes from 'prop-types';
import {CalderaFormsFieldPropType} from "./CalderaFormsFieldRender";
import {Input} from "./Fields/Input";
import {FileInput} from "./Fields/FileInput";


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
	const {field, onChange, shouldDisable, shouldShow,hasMessage,isError,message,getFieldConfig} = props;
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

	if (!shouldShow) {
		return <Fragment/>;
	}


	const hasCaption = field.hasOwnProperty('caption' ) && field.caption.length;
	const captionId = `${fieldIdAttr}Caption`;

	const Inside = () =>{
		switch (type) {
			case 'file':
				const fileProps = FileInput.fieldConfigToProps(getFieldConfig(fieldIdAttr));
				return<FileInput
					onChange={onChange}
					field={field}
					shouldDisable={shouldDisable}
					isInvalid={isInvalid}
					describedById={captionId}
					message={fileProps.message}
					style={fileProps.style}
					inputProps={fileProps.inputProps}
					className={'cf2-file form-control'}
				/>
			case'text':
			default:
				return<Input
					field={field}
					onChange={onChange}
					shouldDisable={shouldDisable}
					isInvalid={isInvalid}
					describedById={captionId}
				/>

		}
	}
	return (

			<div className={'form-group cf2-field-group'}>
				<label
					className={'control-label'}
					htmlFor={fieldIdAttr}
					id={`${fieldIdAttr}Label`}
				>
					{fieldLabel}
				</label>
				<Inside/>

				{hasCaption &&
					<span
						id={captionId}
						className={'help-block'}
					>
							{field.caption}
						</span>
				}
				{isInvalid &&
					<span
						className="help-block caldera_ajax_error_block filled"
						aria-live="polite"
					>
						<span className="parsley-required">{message}</span>
					</span>
				}

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
	field: PropTypes.shape(CalderaFormsFieldPropType),
	onChange: PropTypes.func.isRequired,
	shouldShow: PropTypes.bool,
	shouldDisable: PropTypes.bool,
	hasMessage: PropTypes.bool,
	isInvalid: PropTypes.bool,
	message: PropTypes.string,
	getFieldConfig: PropTypes.func.isRequired
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
	fieldValue: '',
	isInvalid: false,
	message: 'Field is required'
};

