import React from 'react';
import PropTypes from 'prop-types';
import {Twemoji} from 'react-emoji-render';
import {DocLinks} from "./DocLinks";
import {ListGroup, ListGroupItem } from 'react-bootstrap'
export const HelpBox = (props) => {
	return (
		<div
			style={{
				backgroundColor: '#fff'
			}}
			className={'col-xs-4'}
		>
			{props.saveButton &&
				<p
					style={{
						margin: 0,
						padding: '.7em 1em',
						borderBottom: '1px solid #eee'
					}}
				>
					{props.saveButton}
				</p>

			}
			<h3>
				<Twemoji text=":volcano: Terms" />
			</h3>
			<ListGroup
				style={{padding:'12px'}}
			>
				<ListGroupItem>
					<Twemoji text=":eyes:"/>Email Identifying Fields: The field(s) of your form that can be used to determine whose data an entry belongs to.
				</ListGroupItem>
				<ListGroupItem>
					<Twemoji text=":eyes:"/>Personally Identifying Fields (PII): The field(s) of your form that contain PII about the person identified in the email identifying field.
				</ListGroupItem>
			</ListGroup>

			<h3>
				<Twemoji text=":volcano: Documentation" />
			</h3>
			<DocLinks/>
		</div>
	);
};

HelpBox.propTypes = {
	saveButton: PropTypes.oneOfType([
		PropTypes.node,
		PropTypes.object
	]),
};