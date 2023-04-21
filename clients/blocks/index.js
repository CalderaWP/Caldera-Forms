//Import WordPress APIs
const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
//Setup store;
import {CALDERA_FORMS_STORE_NAME,STORE,requestFormPreview} from "./store";
import ServerSideRender from '@wordpress/server-side-render';

const { registerStore,  } = wp.data;
const InspectorControls = wp.editor.InspectorControls;
const {Placeholder} = wp.components;
const formStore = registerStore(CALDERA_FORMS_STORE_NAME,STORE);

//Import CF components
import {FormChooserWithSelect} from "./components/FormChooser";
import {LinkToFormEditor} from "./components/LinkToFormEditor";

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

	edit: ({ attributes, setAttributes, className, isSelected, id }) => {

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
					<LinkToFormEditor formId={attributes.formId} />
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
						attributes={{
							formId:attributes.formId
						}}
					/>
				}
			</div>
		)
	},

	save: () => {
		return null;
	},

});
