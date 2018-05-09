
//Import WordPress APIs
const { __ } = wp.i18n; // Import __() from wp.i18n
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks

//Setup store;
import {CALDERA_FORMS_STORE_NAME,STORE,SET_CURRENT_FORM_ID,requestFormPreview} from "./store";
const { registerStore, dispatch } = wp.data;
const InspectorControls = wp.blocks.InspectorControls;
const formStore = registerStore(CALDERA_FORMS_STORE_NAME,STORE);
//Import CF components
import {FormChooserWithSelect} from "./components/formChooser";
import {FormPreview,FormPreviewWithSelect} from "./components/FormPreview";

//Create block
registerBlockType( 'calderaforms/cform', {
	title: __( 'Caldera Form', 'caldera-forms' ),
	icon: 'feedback',
	category: 'common',
    attributes: {
        formId: {
            formId: 'string',
            default: 'false',
        }
    },
    edit({ attributes, setAttributes, className, isSelected, id } ) {
        /**
         * Utility function to load preview inside block
         *
         * @since 1.6.2
         *
         * @param {String} formId
         */
        const loadPreview = function (formId) {
            if ('false' !== formId && !formStore.getState().formPreviews.hasOwnProperty(formId)) {
                requestFormPreview(formStore.getState(), formId);
            }
        };

        /**
         * Change handler for when form in block changes
         * 
         * @since 1.6.2
         *
         * @param {String} newFormId
         */
        const setCurrentForm = (newFormId) => {
            setAttributes({formId:newFormId});
            loadPreview(newFormId);
        };

        loadPreview(attributes.formId);
        return (
			<div className={className}>
                <InspectorControls>
                    <FormChooserWithSelect
                        onChange={setCurrentForm}
                        formId={attributes.formId}
                    />
                </InspectorControls>

                {'false' === attributes.formId &&
                    <FormChooserWithSelect
                        onChange={setCurrentForm}
                        formId={attributes.formId}
                    />
                }

                {'false' !== attributes.formId &&
                    <FormPreviewWithSelect
                        formId={attributes.formId}
                    />
                }
            </div>
        );
    },
    save: function( { attributes, className } ) {
       return null;
    },
} );
