import {Component} from 'react';
import PropTypes from 'prop-types';


export const ClassicEntryViewer = (props) => (
	<div>
		<button onClick={props.onClose}>Close</button>
		<div>I am the entry viewer for {props.form.name}</div>
	</div>
);
