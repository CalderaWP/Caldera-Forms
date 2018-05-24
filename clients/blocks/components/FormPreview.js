import {CALDERA_FORMS_STORE_NAME} from "../store";
import {appendAssets} from "../../functions/appendAssets";
const { withSelect } = wp.data;
const Spinner = wp.components.Spinner;

function createMarkup(previewHtml) {
    return {__html: previewHtml};
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
    const className = 'caldera-forms-form-preview-' + props.formId;
    if (undefined !== props.preview && props.preview.html ) {

        appendAssets(props.preview.css, props.preview.js);
        return <div className={className} dangerouslySetInnerHTML={createMarkup(props.preview.html)}/>;
    } else {
        return <div className={className}><Spinner /></div>;
    }
};


/**
 * Wrap with data selectors
 */
export const FormPreviewWithSelect = withSelect( (select, ownProps ) => {
    const { getFormPreview,getForm } = select( CALDERA_FORMS_STORE_NAME);
    return {
        form: getForm(ownProps.formId),
        preview: getFormPreview(ownProps.formId),
    };
} )( FormPreview );

