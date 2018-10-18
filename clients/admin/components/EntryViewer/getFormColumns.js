
/**
 * Get column headers for <EntryViewer>
 *
 * @return {({id: string, label: string}|{id: string, label: string})[]}
 */
export function getFormColumns(form, entryListOnly, includeEntryActions   = true )  {
	const {entry_list, order} = form.field_details;
	let columns = Object.values(entry_list);
	if (false === entryListOnly) {
		Object.values(order).forEach(orderedField => {
			columns.push(orderedField);
		});
	}

	if (includeEntryActions) {
		columns.push({
			name: "Actions",
			id: 'entryActions',
			key: 'entryActions',
		});
	}

	columns.forEach(column => {
		if (!column.hasOwnProperty('key')) {
			column.key = column.id;
		}
		column.key = column.key;
		column.name = column.hasOwnProperty('name') ? column.name : column.label;
	});

	return columns;

}