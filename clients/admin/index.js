import './index.scss';
import React from 'react';
import ReactDOM from "react-dom";
import domReady from '@wordpress/dom-ready';
import {CalderaAdmin} from "./CalderaAdmin";

Object.defineProperty( global.wp, 'element', {
	get: () => React
} );

domReady( () => {
	let forms = CF_ADMIN.forms;
	if( 'string' === typeof  forms ){
		forms = JSON.parse(forms);
	}

	let templates = CF_ADMIN.templates;
	ReactDOM.render(
		<CalderaAdmin forms={Object.values(forms)} templates={templates}/>,
		document.getElementById('caldera-forms-admin-client')
	);
} );

