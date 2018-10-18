
import React from 'react';
import ReactDOM from "react-dom";
import domReady from '@wordpress/dom-ready';

Object.defineProperty( global.wp, 'element', {
	get: () => React
} );




import {CalderaFormsRender} from "./components/CalderaFormsRender";
domReady( function() {
	jQuery( document ).on( 'cf.form.init', (e, obj ) => {
		const {state,formId,idAttr} = obj;
		const fieldsToControl = [
			{
				type: 'text2',
				outterIdAttr:  'cf2-fld_5843941',
				fieldId: 'fld_5843941',
				fieldIdAttr: 'fld_5843941_1',
				fieldLabel: "Text Field",
				fieldCaption: 'This field is not required',
				required: false,
				fieldPlaceHolder: 'Placeholder',
			}
		]
		ReactDOM.render( <CalderaFormsRender
			cfState={state}
			formId={formId}
			fieldsToControl={fieldsToControl}
		/>, document.getElementById(`cf2-${idAttr}`))
	});
} );