import {CALDERA_FORMS_STORE_NAME} from "../store";
import {appendAssets} from "../../functions/appendAssets";
const { withSelect } = wp.data;
const Spinner = wp.components.Spinner;

function createMarkup(preview) {
    return {__html: preview};
}

/**
 * Render a  form preview
 *
 * @since 1.6.2
 *
 * @param props
 * @return {XML}
 * @constructor
 */
export const FormPreview = (props) => {
    const preview = props.previews[props.formId];
    const className = 'caldera-forms-form-preview-' + props.formId;
    if (undefined !== preview && preview.html ) {
        appendAssets(preview.css, preview.js);
        return <div className={className} dangerouslySetInnerHTML={createMarkup(preview.html)}/>;
    } else {
        return <div className={className}><Spinner /></div>;
    }
};


/**
 * Wrap with data selectors
 */
export const FormPreviewWithSelect = withSelect( (select, ownProps ) => {
    const { getFormPreviews,getForm } = select( CALDERA_FORMS_STORE_NAME);
    return {
        form: getForm(ownProps.formId),
        previews: getFormPreviews(),
    };
} )( FormPreview );

