
import React from 'react';
import ReactDOM from "react-dom";
import domReady from '@wordpress/dom-ready';

Object.defineProperty( global.wp, 'element', {
	get: () => React
} );




import {CalderaFormsRender} from "./components/CalderaFormsRender";
domReady( function() {
	jQuery( document ).on( 'cf.form.init', (e, obj ) => {
		const {
			state,//CF state instance
			formId, //Form's ID
			idAttr //Form element id attribute
		} = obj;
		const fieldsToControl = [];


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

		ReactDOM.render( <CalderaFormsRender
			cfState={state}
			formId={formId}
			formIdAttr={idAttr}
			fieldsToControl={fieldsToControl}
		/>, document.getElementById(`cf2-${idAttr}`))
	});
} );