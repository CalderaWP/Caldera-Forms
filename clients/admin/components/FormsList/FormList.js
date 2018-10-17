import React from 'react';
import PropTypes from 'prop-types'
import {Form} from "./Form";

export const FormList = (props) => {
	let isAlternate = true;
	const forms = Array.isArray(props.form) ? props.form : Object.values(props.forms);
	return (
		<table className="widefat fixed">
			<thead>
			<tr>
				<th>Form</th>
				<th
					style={{width: '5em', textAlign: 'center'}}>
					Entries
				</th>
			</tr>
			</thead>
			<tbody>

			{

				forms.map(form => {
					isAlternate = ! isAlternate;
					return (
						<Form
							key={form.ID}
							form={form}
							onFormUpdate={props.onFormUpdate}
							openEntryViewerForForm={() => {
								props.openEntryViewerForForm(form.ID);
							}}
							isAlternate={isAlternate}
						/>

					);
				})
			}
			</tbody>
		</table>
	);
};


FormList.propTypes = {
	forms: PropTypes.oneOfType([
		PropTypes.array,
		PropTypes.object,
	]),
	onFormUpdate: PropTypes.func.isRequired,
	openEntryViewerForForm: PropTypes.func.isRequired
};


