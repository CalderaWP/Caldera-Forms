const SelectControl = wp.components.SelectControl;
import {CALDERA_FORMS_STORE_NAME} from "../store";
const { __ } = wp.i18n;

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
    const opts = props.forms;
    return (
        <SelectControl
            className={'caldera-forms-form-chooser'}
            label={ __( 'Choose A Form' ) }
            value={ props.formId }
            options={ opts.map( (form) => ( {
                value: getFormId(form),
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







