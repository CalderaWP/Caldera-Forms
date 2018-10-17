import PropTypes from 'prop-types';

const stringOrNumber = PropTypes.oneOfType([
	PropTypes.string,
	PropTypes.number,
]);
export const idType =
	stringOrNumber;

export const nameType = PropTypes.string.isRequired;

export default {
	ID: idType,
	name: nameType,
	fields: PropTypes.shape({
			ID: stringOrNumber,
			name: PropTypes.string,
			type: PropTypes.string
	}),
	emailIdentifyingFields: PropTypes.array,
	piiFields: PropTypes.array,
	privacyExporterEnabled: PropTypes.bool,
	field_details: PropTypes.shape(
		{
			order: PropTypes.shape(
				{
					id: idType,
					label: PropTypes.string
				}
			),
			entry_list: PropTypes.shape(
				{
					//id: idType,
					label: PropTypes.string
				}
			)
		}
	),
	mailer: PropTypes.shape(
		{
			active: PropTypes.bool
		}
	),
	entries: PropTypes.shape(
		{
			count: stringOrNumber
		}
	)
}