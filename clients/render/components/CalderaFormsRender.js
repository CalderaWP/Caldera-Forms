import {Component, Fragment} from 'react';
import PropTypes from 'prop-types';
import {CalderaFormsFieldPropType, CalderaFormsFieldRender} from "./CalderaFormsFieldRender";

//Collection of change handlers to prevent re-creating
const handlers = {};
//Collection of cfState.events functions that subscribe to changes to prevent re-creating
const subscribed = {};

/**
 * Handles rendering fo Caldera Forms v2 fields inside of a Caldera Form v1
 *
 * @since 1.8.0
 */
export class CalderaFormsRender extends Component {

	/**
	 * Create CalderaFormsRender component
	 *
	 * @since 1.8.0
	 *
	 * @param props
	 */
	constructor(props) {
		super(props);
		const fieldValues = {};
		props.fieldsToControl.forEach(field => {
			const {
				type,
				fieldId,
				fieldDefault,
				fieldIdAttr
			} = field;
			fieldValues[fieldIdAttr]=fieldDefault
		});
		this.state = {
			...fieldValues
		};
		this.setFieldValue = this.setFieldValue.bind(this);
	}

	/**
	 * Get instance of CF State
	 *
	 * @since 1.8.0
	 *
	 * @return {Object}
	 */
	getCfState() {
		return this.props.cfState;

	}

	/**
	 * Get the current value of a field from CF State
	 *
	 * @since 1.8.0
	 *
	 * @param {String} fieldIdAttr The field's id attribute (not field ID, html id attribute)
	 * @return {*}
	 */
	getFieldValue(fieldIdAttr) {
		return this.getCfState().getState(fieldIdAttr);
	}

	/**
	 * Set a field's value
	 *
	 * NOTE: Set 3rd arg true when updating internally. Set 3rd arg false when reciving update from CFState
	 *
	 * @since 1.8.0
	 *
	 * @param {String} fieldIdAttr The field's id attribute (not field ID, html id attribute)
	 * @param {String|Number|null|boolean|Array} newValue
	 * @param {boolean} bubbleUp Optional. If true, the default, the new value is dispatched to CFState. If false it is not.
	 */
	setFieldValue(fieldIdAttr, newValue,bubbleUp = true) {
		this.setState(
			{fieldIdAttr:newValue}
		);
		if( bubbleUp) {
			this.getCfState().mutateState(fieldIdAttr, newValue);

		}
	}

	/**
	 * Get (or create) change handler for field
	 *
	 * @since 1.8.0
	 *
	 * @param {String} fieldIdAttr The field's id attribute (not field ID, html id attribute)
	 * @return {*}
	 */
	getHandler(fieldIdAttr) {
		if (!handlers.hasOwnProperty(fieldIdAttr)) {
			handlers[fieldIdAttr] = (event) => this.setFieldValue(fieldIdAttr, event.target.value);
		}
		return handlers[fieldIdAttr];
	}

	/**
	 * Subscribe to changes in CF State
	 *
	 * @since 1.8.0
	 *
	 * @param {String} fieldIdAttr The field's id attribute (not field ID, html id attribute)
	 */
	subscribe(fieldIdAttr) {
		if (!subscribed.hasOwnProperty(fieldIdAttr)) {
			subscribed[fieldIdAttr] = this.getCfState()
				.events()
				.subscribe(fieldIdAttr, (newValue, fieldIdAttr) => this.setFieldValue(fieldIdAttr, newValue, false ) )
		}
	}

	/** @inheritDoc */
	render() {
		const {fieldsToControl} = this.props;
		return (
			<Fragment>
				{fieldsToControl.map(field => {
					const {
						type,
						outterIdAttr,
						fieldId,
						fieldLabel,
						fieldCaption,
						required,
						fieldPlaceHolder,
						fieldDefault,
						fieldIdAttr
					} = field;

					field = {
						...field,
						fieldValue: this.getFieldValue(fieldIdAttr)
					};

					this.subscribe(fieldIdAttr);
					const props = {
						field,
						onChange: this.getHandler(fieldIdAttr)
					};

					return (
						<CalderaFormsFieldRender {...props} key={outterIdAttr}/>
					);
				})}

			</Fragment>
		)

	}
}


/**
 * Default props for the CalderaFormsRender component
 *
 * @since 1.8.0
 *
 * @type {{cfState: (e|*), formId: (e|*), fieldsToControl: *}}
 */
CalderaFormsRender.propTypes = {
	cfState: PropTypes.object.isRequired,
	formId: PropTypes.string.isRequired,
	fieldsToControl: PropTypes.arrayOf(
		CalderaFormsFieldPropType
	),

};

