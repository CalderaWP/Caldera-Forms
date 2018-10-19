import {CalderaFormsRender} from "./components/CalderaFormsRender";

import React from 'react';
import ReactDOM from "react-dom";
import domReady from '@wordpress/dom-ready';

Object.defineProperty( global.wp, 'element', {
	get: () => React
} );




domReady( function() {
	jQuery( document ).on( 'cf.form.init', (e, obj ) => {
		const {
			state,//CF state instance
			formId, //Form's ID
			idAttr, //Form element id attribute,
			//$form, //Form jQuery object
		} = obj;
		const fieldsToControl = [];

		//Build configurations
		document.querySelectorAll('.cf2-field-wrapper' ).forEach(function(fieldElement) {
			const fieldIdAttr = fieldElement.getAttribute('data-field-id');

			const formConfig = window.cf2[idAttr];

			let fieldConfig = formConfig.fields.hasOwnProperty(fieldIdAttr) ?
				formConfig.fields[fieldIdAttr]
				: null;

			if( 'string' === typeof  fieldConfig ){
				fieldConfig = JSON.parse(fieldConfig);
			}
			if( fieldConfig ){
				fieldsToControl.push(fieldConfig);
				if(fieldConfig.hasOwnProperty('fieldDefault' ) ){
					state.mutateState(fieldIdAttr,fieldConfig.fieldDefault);

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

		//When form submits, push values onto form object before it is serialized
		jQuery(document).on( 'cf.form.submit', (event,obj) => {
			const values = theComponent.getFieldValues();
			if( Object.keys(values).length){
				Object.keys(values).forEach(fieldId => {
					obj.$form.data(fieldId, values[fieldId]);
				});
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
			ref={(component) => {theComponent = component}} />,
			document.getElementById(`cf2-${idAttr}`)
		);


	});
} );