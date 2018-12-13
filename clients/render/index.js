/** globals CF_API_DATA **/
import './index.scss';
import {CalderaFormsRender} from "./components/CalderaFormsRender";

import React from 'react';
import ReactDOM from "react-dom";
import domReady from '@wordpress/dom-ready';
import {getFieldConfigBy, hashFile} from "./util";

const CryptoJS = require("crypto-js");
Object.defineProperty(global.wp, 'element', {
	get: () => React
});


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

		const API_FOR_FILES_URL = CF_API_DATA.rest.fileUpload;
		const _wp_nonce = CF_API_DATA.rest.nonce;

		function createMediaFromFile(file, additionalData) {
			// Create upload payload
			const data = new window.FormData();
			data.append('file', file, file.name || file.type.replace('/', '.'));
			data.append('title', file.name ? file.name.replace(/\.[^.]+$/, '') : file.type.replace('/', '.'));
			Object.keys(additionalData)
				.forEach(key => data.append(key, additionalData[key]));

			return fetch(API_FOR_FILES_URL, {
				body: data,
				method: 'POST',
				headers: {
					'X-WP-Nonce': _wp_nonce
				}
			});


		}



		jQuery(document).on('cf.ajax.request', (event, obj) => {
			shouldBeValidating = true;
			const values = theComponent.getFieldValues();
			const cf2 = window.cf2[obj.formIdAttr];
			const {displayFieldErrors,$notice,$form,fieldsBlocking} = obj;
			if ('object' !== typeof cf2) {
				return;
			}

			cf2.pending = cf2.pending || [];
			cf2.uploadStarted = cf2.uploadStarted || [];
			cf2.uploadCompleted = cf2.uploadCompleted || [];
			cf2.fieldsBlocking = cf2.fieldsBlocking || [];
			function removeFromPending(fieldId) {
				const index = cf2.pending.findIndex(item => item === fieldId);
				if (-1 < index) {
					cf2.pending.splice(index, 1);
				}
			}

			function removeFromUploadStarted(fieldId) {
				const index = cf2.uploadStarted.findIndex(item => item === fieldId);
				if (-1 < index) {
					cf2.uploadStarted.splice(index, 1);
				}
			}
			function removeFromBlocking(fieldId) {
				const index = cf2.fieldsBlocking.findIndex(item => item === fieldId);
				if (-1 < index) {
					cf2.fieldsBlocking.splice(index, 1);
				}
			}

			function setBlocking(fieldId){
				removeFromUploadStarted(fieldId);
				removeFromPending(fieldId);
				cf2.fieldsBlocking.push(fieldId);

			}

			/**
			 * Hash a file then upload it
			 *
			 * @since 1.8.0
			 *
			 * @param {File} file File blob
			 * @param {string} verify Nonce token
			 * @param {object} field field config
			 * @param {string} fieldId ID for field
			 */
			function hashAndUpload(file, verify, field, fieldId) {


				if (file instanceof File) {
					hashFile(file, (hash) => {
						createMediaFromFile(file, {
							hashes: [hash],
							verify,
							formId: field.formId,
							fieldId: field.fieldId,
							control: field.control,
							_wp_nonce
						}).then(
							response => response.json()
						).then(
							response => {
								if( 'object' !== typeof  response ){
									removeFromUploadStarted(fieldId);
									removeFromPending(fieldId);
									throw response;
								}
								else if (response.hasOwnProperty('control')) {
									removeFromPending(fieldId);
									removeFromBlocking(fieldId);
									cf2.uploadCompleted.push(fieldId);
									$form.submit();
								}else{
									if( response.hasOwnProperty('message') ){
										messages[field.fieldIdAttr] = {
											error: true,
											message: response.hasOwnProperty('message') ? response.message : 'Invalid'
										};
									}
									removeFromUploadStarted(fieldId);
									removeFromPending(fieldId);
									throw response;
								}


							}
						).catch(
							error => console.log(error)
						);
					})
				}
			}

			if (Object.keys(values).length) {
				Object.keys(values).forEach(fieldId => {
					const field = fieldsToControl.find(field => fieldId === field.fieldId);
					if (field) {
						const {fieldIdAttr} = field;
						if ('file' === field.type) {
							//do not upload after complete
							if ( cf2.uploadCompleted.includes(fieldId)) {
								removeFromPending(fieldId);
								removeFromBlocking(fieldId);
								return;
							}
							//do not start upload if it has started uploading
							if (-1 <= cf2.uploadStarted.indexOf(_fieldId => _fieldId === fieldId )
								&& -1 <= cf2.pending.indexOf(_fieldId => _fieldId === fieldId)
							) {
								cf2.uploadStarted.push(fieldId);
								obj.$form.data(fieldId, field.control);
								cf2.pending.push(fieldId);
								const verify = jQuery(`#_cf_verify_${field.formId}`).val();
								if( '' === values[fieldId] ){
									if( theComponent.isFieldRequired(fieldIdAttr) ){
										theComponent.addFieldMessage( fieldIdAttr, "Field is required" );
										shouldBeValidating = true;
										setBlocking(fieldId);
									}
									removeFromPending(fieldId);
									return;
								}
								removeFromBlocking(fieldId);
								const files = [values[fieldId]];
								files.forEach(file => {
										if( Array.isArray( file ) ){
											file = file[0];
										}
										hashAndUpload(file, verify, field, fieldId);
									}
								);
							}


						}
					}
				});
			} else {
				obj.$form.data(fieldId, values[fieldId]);
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
		ReactDOM.render(
			<CalderaFormsRender
				cfState={state}
				formId={formId}
				formIdAttr={idAttr}
				fieldsToControl={fieldsToControl}
				shouldBeValidating={shouldBeValidating}
				ref={(component) => {
					theComponent = component
				}}
				messages={messages}
				strings={CF_API_DATA.strings}
			/>,
			document.getElementById(`cf2-${idAttr}`)
		);

	});

});


