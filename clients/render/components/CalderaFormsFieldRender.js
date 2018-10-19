import {Component, Fragment} from 'react';
import PropTypes from 'prop-types';
import {RenderComponentViaPortal} from "./RenderComponentViaPortal";

/**
 * Render a Caldera Forms v2 field via a RenderComponentViaPortal to an element inside a Caldera Forms v1 field
 *
 * @since 1.8.0
 *
 * @param props
 * @return {*}
 * @constructor
 */
export const CalderaFormsFieldRender = (props) => {
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
		<Fragment>
			<RenderComponentViaPortal
				domNode={document.getElementById(outterIdAttr)}
				key={outterIdAttr}
			>
				<div>
					<label
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
			</RenderComponentViaPortal>
		</Fragment>

	);

}

/**
 * Prop Type describing a Caldera Forms field
 *
 * @since 1.8.0
 */
export const CalderaFormsFieldPropType = PropTypes.shape({
	type: PropTypes.string.isRequired,
	outterIdAttr: PropTypes.string.isRequired,
	fieldId: PropTypes.string.isRequired,
	fieldIdAttr: PropTypes.string.isRequired,
	fieldLabel: PropTypes.string.isRequired,
	fieldCaption: PropTypes.string.isRequired,
	required: PropTypes.bool,
	fieldPlaceHolder: PropTypes.string.isRequired,
	fieldDefault: PropTypes.string,
	fieldValue: PropTypes.oneOfType(
		[
			PropTypes.string,
			PropTypes.number,
			PropTypes.array,
		]
	)
});

/**
 * Prop Type definitions for CalderaFormsFieldRender component
 *
 * @since 1.8.0
 *
 * @type {{field: *, onChange: (e|*), shouldShow: *, shouldDisable: *}}
 */
CalderaFormsFieldRender.propTypes = {
	field: CalderaFormsFieldPropType,
	onChange: PropTypes.func.isRequired,
	shouldShow: PropTypes.bool,
	shouldDisable: PropTypes.bool
};

/**
 * Default props for the CalderaFormsFieldRender component
 *
 * @since 1.8.0
 *
 * @type {{shouldShow: boolean, shouldDisable: boolean, fieldValue: string}}
 */
CalderaFormsFieldRender.defaultProps = {
	shouldShow: true,
	shouldDisable: false,
	fieldValue: ''
};