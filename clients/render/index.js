/** globals CF_API_DATA **/
import './index.scss';
import {CalderaFormsRender} from "./components/CalderaFormsRender";

import domReady from '@wordpress/dom-ready';
import {
	hashFile,
	createMediaFromFile,
	captureRenderComponentRef
} from "./util";
import { handleFileUploadResponse, handleFileUploadError, hashAndUpload, processFiles, processFileField, processFormSubmit } from './fileUploads';

domReady(function () {
	jQuery(document).on('cf.form.init', (e, obj) => {
		const {
			state,//CF state instance
			formId, //Form's ID
			idAttr, //Form element id attribute,
			//$form, //Form jQuery object
		} = obj;
		const fieldsToControl = [];
		if( 'object' !== typeof  window.cf2 ){
			window.cf2 = {};
		}


		//Build configurations
		document.querySelectorAll('.cf2-field-wrapper').forEach(function (fieldElement) {
			const fieldIdAttr = fieldElement.getAttribute('data-field-id');

			const formConfig = window.cf2[idAttr];

			let fieldConfig = formConfig.fields.hasOwnProperty(fieldIdAttr) ?
				formConfig.fields[fieldIdAttr]
				: null;

			if ('string' === typeof  fieldConfig) {
				fieldConfig = JSON.parse(fieldConfig);
			}
			if (fieldConfig) {
				fieldsToControl.push(fieldConfig);
				if (fieldConfig.hasOwnProperty('fieldDefault')) {
					state.mutateState(fieldIdAttr, fieldConfig.fieldDefault);

				}
			}
		});



		/**
		 * Flag to indicate if validation is happening or not
		 *
		 * This is controlled outside of React so that CF1 can trigger validation
		 *
		 * @since 1.8.0
		 *
		 * @type {boolean}
		 */
		let shouldBeValidating = false;
		let messages = {};


		jQuery(document).on('cf.ajax.request', (event, obj) => {
			//Compare the event form id with the component form id
			if(!obj.hasOwnProperty('formIdAttr') || obj.formIdAttr !== idAttr){
				return;
			}
			shouldBeValidating = true;
			const values = theComponent.getAllFieldValues();
			const cf2 = window.cf2[obj.formIdAttr];
			if(typeof cf2 !== "undefined" && cf2.length > 0){
				cf2.formIdAttr = obj.formIdAttr;
			}
			const {displayFieldErrors,$notice,$form,fieldsBlocking} = obj;
			if ('object' !== typeof cf2) {
				return;
			}

			cf2.pending = cf2.pending || [];
			cf2.uploadStarted = cf2.uploadStarted || [];
			cf2.uploadCompleted = cf2.uploadCompleted || [];
			cf2.fieldsBlocking = cf2.fieldsBlocking || [];


			if (Object.keys(values).length) {
				Object.keys(values).forEach(fieldId => {
					const field = fieldsToControl.find(field => fieldId === field.fieldId);
					if (field) {
						if ('file' === field.type) {
							const processFunctions = {processFiles, hashAndUpload, hashFile, createMediaFromFile, handleFileUploadResponse, handleFileUploadError, processFormSubmit};
							const processData = {obj, values, field, fieldId, cf2, $form, CF_API_DATA, theComponent, messages};
							processFileField(processData, processFunctions);
						}
					}
				});
			} else {
				if(typeof field !== "undefined"){
					obj.$form.data(field.fieldId, values[field.fieldId]);
				}
			}

		});

		/**
		 * Ref for rendered app
		 *
		 * @see https://reactjs.org/docs/refs-and-the-dom.html
		 *
		 * @since 1.8.0
		 * @type {*}
		 */
		let theComponent = '';
		wp.element.render(
			<CalderaFormsRender
				cfState={state}
				formId={formId}
				formIdAttr={idAttr}
				fieldsToControl={fieldsToControl}
				shouldBeValidating={shouldBeValidating}
				ref={(component) => {
					captureRenderComponentRef(component,idAttr,window);
					theComponent = component
				}}
				messages={messages}
				strings={CF_API_DATA.strings}
			/>,
			document.getElementById(`cf2-${idAttr}`)
		);

	});

});


