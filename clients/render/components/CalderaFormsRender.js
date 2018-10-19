import {Component, Fragment, createContext} from 'react';
import PropTypes from 'prop-types';
import {CalderaFormsFieldPropType, CalderaFormsFieldRender} from "./CalderaFormsFieldRender";
import isEmpty from 'validator/lib/isEmpty';

//Collection of change handlers to prevent re-creating
const handlers = {};
//Collection of cfState.events functions that subscribe to changes to prevent re-creating
const stateChangeSubscriptions = {};
const conditionalEventSubscriptions = {};


const shouldShowKey = (fieldIdAttr) => {
	return `shouldShow${fieldIdAttr}`;
};

const shouldDisableKey = (fieldIdAttr) => {
	return `shouldDisable${fieldIdAttr}`;
};

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
			fieldValues[fieldIdAttr] = fieldDefault;
			fieldValues[shouldShowKey(fieldIdAttr)] = field.hasOwnProperty('shouldShow') && false === field.shouldShow ? false : true;
			fieldValues[shouldDisableKey(fieldIdAttr)] = field.hasOwnProperty('shouldDisable') && true === field.shouldDisable ? true : false;
		});
		this.state = fieldValues;
		this.setFieldValue = this.setFieldValue.bind(this);
		this.setFieldShouldShow = this.setFieldShouldShow.bind(this);
		this.setFieldShouldDisable = this.setFieldShouldDisable.bind(this);
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
	 * Set a field show or hide
	 *
	 * @since 1.8.0
	 *
	 * @param {String} fieldIdAttr The field's id attribute (not field ID, html id attribute)
	 * @param {boolean} show If field should be shown (true) or hidden (false).
	 */
	setFieldShouldShow(fieldIdAttr, show) {
		const key = shouldShowKey(fieldIdAttr);
		this.setState({
			[key]: show
		});
	}

	/**
	 * Set a field  disabled or enabled
	 *
	 * @since 1.8.0
	 *
	 * @param {String} fieldIdAttr The field's id attribute (not field ID, html id attribute)
	 * @param {boolean} disable If field should be enabled (true) or disabled (false).
	 */
	setFieldShouldDisable(fieldIdAttr, disable) {
		const key = shouldDisableKey(fieldIdAttr);
		this.setState({
			[key]: disable
		});
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
	setFieldValue(fieldIdAttr, newValue, bubbleUp = true) {
		this.setState(
			{fieldIdAttr: newValue}
		);
		if (bubbleUp) {
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
		if (!stateChangeSubscriptions.hasOwnProperty(fieldIdAttr)) {
			stateChangeSubscriptions[fieldIdAttr] = this.getCfState()
				.events()
				.subscribe(fieldIdAttr, (newValue, fieldIdAttr) => this.setFieldValue(fieldIdAttr, newValue, false))
		}
		const conditionalEvents = [
			'show',
			'hide',
			'enable',
			'disable',
		];
		const {formId, formIdAttr} = this.props;
		if (!conditionalEventSubscriptions.hasOwnProperty(fieldIdAttr)) {
			conditionalEventSubscriptions[fieldIdAttr] = {}
		}
		conditionalEvents.forEach(conditionalEvent => {
			if (!conditionalEventSubscriptions[fieldIdAttr].hasOwnProperty(conditionalEvent)) {
				conditionalEventSubscriptions[fieldIdAttr][conditionalEvent] = this.getCfState()
					.events().
					attatchEvent(`cf.conditionals.${conditionalEvent}`,
						(eventData, eventName) => {
							if (formIdAttr === eventData.formIdAttr) {
								const {eventType, fieldIdAttr} = eventData;
								switch (eventType) {
									case 'hide':
										this.setFieldShouldShow(fieldIdAttr, false);
										break;
									case 'show' :
										this.setFieldShouldShow(fieldIdAttr, true);
										break;
									case 'enable':
										this.setFieldShouldDisable(fieldIdAttr, false);
										break;
									case 'disable':
										this.setFieldShouldDisable(fieldIdAttr, true);
										break;
									default:
										break;
								}
							}
					});
			}
		});
	}

	isFieldRequired(fieldIdAttr){
		const field = this.props.fieldsToControl
			.find( field => fieldIdAttr === field.fieldIdAttr );
		return field.hasOwnProperty('required' ) && true === field.required ? true : false;
	}

	isFieldValid(fieldIdAttr){
		return this.isFieldRequired(fieldIdAttr) && ! isEmpty(this.state[fieldIdAttr]);
	}

	/** @inheritDoc */
	render() {
		const {state, props} = this;
		const {fieldsToControl,shouldBeValidating} = props;

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

					const isInvalid = shouldBeValidating && ! this.isFieldValid(fieldIdAttr);

					this.subscribe(fieldIdAttr);
					const props = {
						field,
						onChange: this.getHandler(fieldIdAttr),
						shouldShow: state[shouldShowKey(fieldIdAttr)],
						shouldDisable: state[shouldDisableKey(fieldIdAttr)],
					};

					return (
						<CalderaFormsFieldRender
							{...props}
							key={outterIdAttr}
							isInvalid={isInvalid}
						/>
					);
				})}

			</Fragment>
		);

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
	formIdAttr: PropTypes.string.isRequired,
	shouldBeValidating: PropTypes.bool.isRequired
};

