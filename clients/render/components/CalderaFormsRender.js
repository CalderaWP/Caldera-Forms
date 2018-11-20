import {Component, Fragment, createContext} from 'react';
import PropTypes from 'prop-types';
import {CalderaFormsFieldPropType, CalderaFormsFieldRender} from "./CalderaFormsFieldRender";
import isEmpty from 'validator/lib/isEmpty';
import {getFieldConfigBy, hashFile} from "../util";

//Collection of change handlers to prevent re-creating
const handlers = {};
//Collection of cfState.events functions that subscribe to changes to prevent re-creating
const stateChangeSubscriptions = {};
const conditionalEventSubscriptions = {};

/**
 * Create a state key for a field's show/hide status
 *
 * This key of CalderaFormsRender.state will indicate if a field should be shown or not, based on conditional logic.
 *
 * @since 1.8.0
 *
 * @param fieldIdAttr
 * @return {string}
 */
export const shouldShowKey = (fieldIdAttr) => {
	return `shouldShow${fieldIdAttr}`;
};

/**
 * Create a state key for a field's disable/enable status
 *
 *  This key of CalderaFormsRender.state will indicate if a field should be disabled or not, based on conditional logic.
 *
 * @since 1.8.0
 *
 * @param fieldIdAttr
 * @return {string}
 */
export const shouldDisableKey = (fieldIdAttr) => {
	return `shouldDisable${fieldIdAttr}`;
};
/**
 * Create a state key for a field's disable/enable status
 *
 * This key of CalderaFormsRender.state will indicate if a field has changed for not.

 * @since 1.8.0
 *
 * @param fieldIdAttr
 * @return {string}
 */
const fieldIsDirtyKey = (fieldIdAttr) => {
	return `isDirty${fieldIdAttr}`;
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
			fieldValues[fieldIsDirtyKey(fieldIdAttr)] = false
		});
		this.state = {
			...fieldValues,
			messages: props.messages || {}
		};
		this.setFieldValue = this.setFieldValue.bind(this);
		this.setFieldShouldShow = this.setFieldShouldShow.bind(this);
		this.setFieldShouldDisable = this.setFieldShouldDisable.bind(this);
		this.subscribe = this.subscribe.bind(this);
		this.getFieldConfig = this.getFieldConfig.bind(this);
		this.addFieldMessage = this.addFieldMessage.bind(this);
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
		if ('file' === this.getFieldConfig(fieldIdAttr).type) {
			return this.state[fieldIdAttr];
		}
		return this.getCfState().getState(fieldIdAttr);
	}

	getFieldValues() {
		const {fieldsToControl} = this.props;
		const pickArray = (array, key) => {
			return array.reduce(
				(accumualtor, item) =>
					accumualtor.concat([item[key]]), []
			);
		};


		const fieldIds = pickArray(fieldsToControl, 'fieldIdAttr');
		const values = {};
		Object.keys(this.state).map(key => {
			if (fieldIds.includes(key)) {
				const fieldId = fieldsToControl.find(field => key === field.fieldIdAttr).fieldId;
				values[fieldId] = this.state[key];
			}
		});
		return values;
	}

	/**
	 * Set a field show or hide
	 *
	 * @since 1.8.0
	 *
	 * @param {String} fieldIdAttr The field's id attribute (not field ID, html id attribute)
	 * @param {boolean} show If field should be shown (true) or hidden (false).
	 */
	setFieldShouldShow(fieldIdAttr, show, fieldValue) {
		const key = shouldShowKey(fieldIdAttr);
		const {state} = this;
		if (state[key] !== show) {
			let update = {
				[key]: show
			};
			if (show) {
				update[fieldIdAttr] = fieldValue;
				this.getCfState().mutateState(fieldIdAttr, fieldValue);

			}

			this.setState({
				[key]: show
			});

		}
	}

	/**
	 * Get the field config, by fieldIdAttr
	 *
	 * @since 1.8.0
	 *
	 * @param {string} fieldIdAttr
	 * @return {*}
	 */
	getFieldConfig(fieldIdAttr) {
		return getFieldConfigBy(this.props.fieldsToControl, 'fieldIdAttr', fieldIdAttr);
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
		if (this.state[key] !== disable) {
			this.setState({
				[key]: disable
			});
		}

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
		const isDirty = newValue !== this.state[fieldIdAttr];
		this.setState(
			{
				[fieldIdAttr]: newValue,
				[fieldIsDirtyKey(fieldIdAttr)]: isDirty
			}
		);
		if (this.state.messages.hasOwnProperty(fieldIdAttr)) {
			this.setState({
				messages: {
					...this.state.messages,
					[fieldIdAttr]: {error: false, message: ''}
				}
			})
		}
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
			switch (this.getFieldConfig(fieldIdAttr).type) {
				case 'file':
					handlers[fieldIdAttr] = (accepted, rejected) => {

            let fieldValue = this.getFieldValue(fieldIdAttr);

						if( Array.isArray(accepted) === true && Array.isArray(rejected) === true ) { // Handle accepted and rejected files

              if(Array.isArray(fieldValue) === false){
                fieldValue = [];
              }

              if( accepted.length > 0 ) {
                accepted.forEach(file => {
                  fieldValue.push(file);
                });
                this.setFieldValue(fieldIdAttr, fieldValue );
              }

              if( rejected.length > 0 ) {
                const rejectedTypes = [];
                rejected.forEach( file => {
                  rejectedTypes.push(file.type);
                })
                const messageText = 'These types of files are not allowed : ' + rejectedTypes;
                this.addFieldMessage(fieldIdAttr, messageText, false )
              }

						} else if ( typeof(accepted) === 'object' && accepted.target.className === "cf2-file-remove" ) { //Remove a File form fieldValue

							const index = fieldValue.indexOf(rejected);
							fieldValue.splice(index, 1);

              this.setFieldValue(fieldIdAttr, fieldValue );
						}


          }
					break;
				default:
					handlers[fieldIdAttr] = (event) => this.setFieldValue(fieldIdAttr, event.target.value);
					break;
			}
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
		const {state, props} = this;
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
					.events().attatchEvent(`cf.conditionals.${conditionalEvent}`,
						(eventData, eventName) => {
							if (formIdAttr === eventData.formIdAttr) {
								const {eventType, fieldIdAttr, fieldValue} = eventData;
								switch (eventType) {
									case 'hide':
										this.setFieldShouldShow(fieldIdAttr, false, fieldValue);
										break;
									case 'show' :
										this.setFieldShouldShow(fieldIdAttr, true, fieldValue);
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

	/**
	 * Check if a field is required
	 *
	 * @since 1.8.0
	 *
	 * @param {string} fieldIdAttr
	 *
	 * @return {boolean}
	 */
	isFieldRequired(fieldIdAttr) {
		const field = this.getFieldConfig(fieldIdAttr);

		return !!field.isRequired;
	}

	/**
	 * Check if a field is valid
	 *
	 * @since 1.8.0
	 *
	 * @param {string} fieldIdAttr
	 *
	 * @return {boolean}
	 */
	isFieldValid(fieldIdAttr) {
		if (!this.isFieldRequired(fieldIdAttr)) {
			return true;
		}
		return !isEmpty(this.state[fieldIdAttr]);
	}

	addFieldMessage(fieldIdAttr, messageText, isError = true) {
		if (!this.getFieldConfig(fieldIdAttr)) {
			return;
		}
		this.setState({
			messages: {
				...this.state.messages,
				[fieldIdAttr]: {
					message: messageText,
					error: isError
				}
			}

		})
	}

  /**
   * Get translatable strings Set in Caldera_Forms_Render_Assets::enqueue_form_assets()
   *
   * @since 1.8.0
   *
   * @return {Object}
   */
  getStrings() {
    return this.props.strings;
  }


	/** @inheritDoc */
	render() {
		const {state, props} = this;
		const {messages} = state;
		const {fieldsToControl, shouldBeValidating} = props;

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

					const isInvalid = shouldBeValidating && !this.isFieldValid(fieldIdAttr);

					this.subscribe(fieldIdAttr);
					const props = {
						field,
						strings: this.getStrings(),
						onChange: this.getHandler(fieldIdAttr),
						shouldShow: state[shouldShowKey(fieldIdAttr)],
						shouldDisable: state[shouldDisableKey(fieldIdAttr)],
					};

					const hasMessage = 'object' === typeof messages && messages.hasOwnProperty(fieldIdAttr);
					return (
						<CalderaFormsFieldRender
							{...props}
							key={outterIdAttr}
							isInvalid={isInvalid}
							getFieldConfig={this.getFieldConfig}
							message={hasMessage ? messages[fieldIdAttr] : {error: false, message: ''}}
							hasMessage={hasMessage}
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
	shouldBeValidating: PropTypes.bool.isRequired,
	messages: PropTypes.object,
	strings: PropTypes.object
};

