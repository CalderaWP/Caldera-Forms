import './index.scss';
import React from 'react';
import ReactDOM from "react-dom";
import domReady from '@wordpress/dom-ready';

Object.defineProperty( global.wp, 'element', {
	get: () => React
} );


domReady( () => {
	ReactDOM.render(
		<p>Hi Roy</p>,
		document.getElementById('caldera-forms-admin-client')
	);
} );

