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
	const {field, onChange, shouldDisable, shouldShow,hasMessage,isError,message} = props;
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

	const hasCaption = field.hasOwnProperty('caption' ) && field.caption.length;
	const captionId = `${fieldIdAttr}Caption`;
	const ariaAttr = hasCaption ? `aria-describedby="${captionId}"` : '';
	return (

			<div className={'form-group cf2-field-group'}>
				<label
					className={'control-label'}
					htmlFor={fieldIdAttr}
					id={`${fieldIdAttr}Label`}
				>
					{fieldLabel}
				</label>
				<input
					type={type}
					{...propsThatDoNotUseAnEquals}
					{...ariaAttr}
					value={fieldValue}
					className={'form-control'}
					id={fieldIdAttr}
					placeholder={fieldPlaceHolder}
					onChange={onChange}
					data-field={fieldId}
					name={fieldId}
				/>
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
	field: CalderaFormsFieldPropType,
	onChange: PropTypes.func.isRequired,
	shouldShow: PropTypes.bool,
	shouldDisable: PropTypes.bool,
	hasMessage: PropTypes.bool,
	isInvalid: PropTypes.bool,
	message: PropTypes.string
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