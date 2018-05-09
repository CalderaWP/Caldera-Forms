const SelectControl = wp.components.SelectControl;
import {CALDERA_FORMS_STORE_NAME} from "../store";
const { __ } = wp.i18n;

/**
 * Basic component to choose forms with
 *
 * @param props
 * @return {XML}
 * @constructor
 */
export const FormChooser = (props) => {
    return (
        <SelectControl
            className={'caldera-forms-form-chooser'}
            label={ __( 'Form' ) }
            value={ props.formId }
            options={ props.forms.map( (form) => ( {
                value: form.formId,
                label: form.name,
            } ) ) }
            onChange={ (newValue) => {props.onChange(newValue)} }
        />
    )
};

//Import wp.data's HOC
const { withSelect } = wp.data;

/**
 * Form chooser wrapped in form selector
 */
export const FormChooserWithSelect = withSelect( (select, ownProps ) => {
    const { getForms } = select( CALDERA_FORMS_STORE_NAME);
    return {
        forms: getForms()
    };
} )( FormChooser );







