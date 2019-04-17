//Import WordPress APIs
const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
//Setup store;
import {CALDERA_FORMS_STORE_NAME,STORE,requestFormPreview} from "./store";
const { registerStore,  } = wp.data;
const InspectorControls = wp.editor.InspectorControls;
const {Placeholder} = wp.components;
const formStore = registerStore(CALDERA_FORMS_STORE_NAME,STORE);
import { ServerSideRender } from '@wordpress/components';

//Import CF components
import {FormChooserWithSelect} from "./components/formChooser";
import {LinkToFormEditor} from "./components/linkToFormEditor";
//Create block
registerBlockType( 'calderaforms/cform', {
	title: __( 'Caldera Form' ),
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
         * Change handler for when form in block changes
         *
         * @since 1.6.2
         *
         * @param {String} newFormId
         */
        const setCurrentForm = (newFormId) => {
            setAttributes({formId:newFormId});
        };


        return (
		<div>
			<InspectorControls>
			    <FormChooserWithSelect
				onChange={setCurrentForm}
				formId={attributes.formId}
			    />
				<LinkToFormEditor
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
				<ServerSideRender
					block="calderaforms/cform"
					attributes={ {
						formId:attributes.formId
					} }
				/>
			}
            </div>
        );
    },
    save: function(  ) {
       return null;
    },
} );
