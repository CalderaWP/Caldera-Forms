const SelectControl = wp.components.SelectControl;
import {CALDERA_FORMS_STORE_NAME} from "../store";
import { __ } from '@wordpress/i18n';
//Import wp.data's HOC
import { withSelect } from "@wordpress/data";

/**
 * Get ID of form
 *
 * @since 1.6.2
 *
 * @param {Object} form Form config
 * @return {*}
 */
const getFormId = (form) => {
    if( 'object' !== typeof  form ){
        return '';
    }
    return form.hasOwnProperty('formId' ) ? form.formId : form.ID;
};

/**
 * Basic component to choose forms with
 *
 * @param props
 * @return {XML}
 * @constructor
 */
export const FormChooser = (props) => {
	const {forms,formId} = props;
    const opts = ! Array.isArray(forms) ? Object.values(forms) : forms;

    const value = formId && forms.hasOwnProperty(formId) ? formId : null;
    if( ! value ){
		opts.unshift({
			value: null,
			label: ''
		});
	}

    return (
        <SelectControl
            className={'caldera-forms-form-chooser'}
            label={ __( 'Choose A Form' ) }
            value={ value }
            options={ opts.map( (form) => ( {
                value: getFormId(form),
                label: form.name,
            } ) ) }
            onChange={ (newValue) => {props.onChange(newValue)} }
        />
    );
};

/**
 * Form chooser wrapped in form selector
 */
export const FormChooserWithSelect = withSelect( (select, ownProps ) => {
    const { getForms } = select( CALDERA_FORMS_STORE_NAME);
    return {
        forms: getForms()
    };
} )( FormChooser );







