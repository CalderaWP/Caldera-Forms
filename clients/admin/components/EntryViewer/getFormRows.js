import React from 'react';
import {EntryRowActions} from "./EntryRowActions";

/**
 * Get the form entry rows for <EntryViewer>
 * @return {*}
 */
export default function getFormRows(
	entries,
	onEntryAction,
	entryListOnly = true,
	includeEntryActions = true,
) {

	let rows = [];
	Object.keys(entries).forEach(id => {
		const entry = entries[id];
		let thisRow = {
			id: entry.id,
			datestamp: entry.datestamp,
			user: 'object' === typeof entry.user ? entry.user : {},
		};

		if (!entryListOnly) {
			Object.values(entry.fields).forEach(entryField => {
				thisRow[entryField.field_id] = entryField.value;
			});
		}

		if (includeEntryActions) {
			thisRow.entryActions = (
				<EntryRowActions
					onView={() => {
						onEntryAction('view', id);
					}}
					onDelete={() => {
						onEntryAction('view', id);
					}}
					onResend={() => {
						onEntryAction('resend', id )
					}}

				/>
			);
		}


		rows.push(thisRow);

	});
	return rows;


}