
//Import WordPress APIs
const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
//Setup store;
import {CALDERA_FORMS_STORE_NAME,STORE,SET_CURRENT_FORM_ID,requestFormPreview} from "./store";
const { registerStore, dispatch } = wp.data;
const InspectorControls = wp.blocks.InspectorControls;
const Placeholder = wp.components.Placeholder;
const formStore = registerStore(CALDERA_FORMS_STORE_NAME,STORE);
//Import CF components
import {FormChooserWithSelect} from "./components/formChooser";
import {FormPreviewWithSelect} from "./components/FormPreview";
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

        //Preload preview
        if( 'false' !== attributes.formId ){
            loadPreview(attributes.formId);
        }
        return (
			<div>
                <InspectorControls>
                    <FormChooserWithSelect
                        onChange={setCurrentForm}
                        formId={attributes.formId}
                    />
                </InspectorControls>

                {'false' === attributes.formId &&
                    <Placeholder
                        className={ 'caldera-forms-form-chooser-placeholder' }
                        label={ 'Caldera Form' } >
                        <FormChooserWithSelect
                            onChange={setCurrentForm}
                            formId={attributes.formId}
                        />

                    </Placeholder>
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
