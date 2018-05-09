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
    const id = 'caldera-forms-form-preview';
    if (undefined !== props.preview && props.preview.html ) {
        appendAssets(props.preview.css, props.preview.js);
        return <div id={id} dangerouslySetInnerHTML={createMarkup(props.preview.html)}/>;
    } else {
        return <div id={id}><Spinner /></div>;
    }
};


/**
 * Wrap with data selectors
 */
export const FormPreviewWithSelect = withSelect( (select, ownProps ) => {
    const { getFormPreview } = select( CALDERA_FORMS_STORE_NAME);
    return {
        preview: getFormPreview(ownProps.formId),
    };
} )( FormPreview );

