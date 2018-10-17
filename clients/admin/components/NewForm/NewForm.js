import React from 'react';
import {factories} from '@caldera-labs/components';
import PropTypes from 'prop-types'

const {fieldFactory} = factories;


const fieldConfigs = [
	{
		id: 'newFormName',
		label: 'Name',
		type: 'text',
	},
	{
		id: 'newFormTemplate',
		type: 'dropdown',
		label: 'Template',
		options: [

		],
		conditionals: [
			(values) => {
				return 'templates' === values.newFormCreateFrom;
			}
		]
	},
	{
		'id': 'newFormCreateFrom',
		'type': 'dropdown',
		'label': 'Create Form Using',
		options: [
			{
				value: 'templates',
				label: 'From Template'
			},
			{
				value: 'clone',
				label: 'Clone Existing'
			}
		],
	},
	{
		id: 'newFormSubmitButton',
		label: 'Create Form',
		type: 'button',
		inputType: 'submit',
		value: 'Submit',
	},
];


/**
 * The form for creating a new form
 */
export class NewForm extends React.PureComponent {
	/**
	 * Create CreateFormSlot component
	 * @param {Object} props
	 */
	constructor(props){
		super(props);
		this.state = {
			newFormName: '',
			newFormTemplate: '',
			newFormCreateFrom: 'templates',
		};
		this.getFieldComponents = this.getFieldComponents.bind(this);
	}

	/**
	 * Create the field components for CreateFormSlot
	 */
	getFieldComponents(){
		let fields = {};
		const {props} = this;
		const {fieldConfigs,templates} = props;
		fieldConfigs.forEach( fieldConfig => {
			const {id} = fieldConfig;
			if ( 'newFormSubmitButton' !== id ) {
				fieldConfig.onValueChange = (newValue) => {
					let update = {};
					update[id] = newValue;
					this.setState(update);
				};
				fieldConfig.value = this.state[id];
				if( 'newFormTemplate' === id ){
					const opts=  [];
					Object.keys(templates).forEach( templatesKey => {
						opts.push({
							value:templatesKey,
							label: templates[templatesKey]
						})
					});

					fieldConfig.options = opts;
					fieldConfig.onValueChange = (event) => {
						this.setState({newFormTemplate:event.target.value});
					};
				}
			}else{
				fieldConfig.onClick = () => {
					this.props.onCreate({
						...this.state
					});
				}
			}
			fields[id] = fieldFactory(fieldConfig);
		});
		return fields;

	}

	/**
	 * Render CreateFormSlot component
	 * @return {*}
	 */
	render(){
		const components = this.getFieldComponents();
		return (
			<div>
				{this.props.fieldConfigs.map(fieldConfig => {
					const {id} = fieldConfig;
					return React.createElement(
						React.Fragment,
						{
							key: `newForm-${id}`,
						},
						components[id]
					);
				})}
			</div>
		)
	}


};

/**
 * prop definitions for CreateFormSlot component.
 *
 * @type {{fieldConfigs: shim, onCreate: *, templates: shim}}
 */
NewForm.propTypes = {
	fieldConfigs: PropTypes.array,
	onCreate: PropTypes.func.isRequired,
	templates: PropTypes.array,
	forms: PropTypes.object
};

/**
 * Default props for CreateFormSlot component
 *
 * @type {{fieldConfigs: *[]}}
 */
NewForm.defaultProps = {
	fieldConfigs,
	forms: {}
};
