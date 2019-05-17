import {optionFactory} from "../../../../../../Desktop/components/util/optionFactory";

export const PRO_FORM_SEND_LOCAL = 'SETTINGS/PRO/FORM/SEND_LOCAL';
export const PRO_FORM_EMAIL_LAYOUT = 'SETTINGS/PRO/FORM/EMAIL_LAYOUT';
export const PRO_FORM_PDF_LAYOUT = 'SETTINGS/PRO/FORM/PDF_LAYOUT';
export const PRO_FORM_PDF_ATATCH = 'SETTINGS/PRO/FORM/PDF_ATTACH ';
export const PRO_FORM_PDF_LINK = 'SETTINGS/PRO/FORM/PDF__LINK';

const enableOption = optionFactory(
	true,
	'Enable'
);

const disableOption = optionFactory(
	false,
	'Disable'
);

const enableDiable = [enableOption,disableOption]
export default [
	{
		id: PRO_FORM_SEND_LOCAL,
		label: 'Disable enhanced delivery for this form',
		type: 'checkbox',
		path: 'pro.send_local',
		options: [enableDiable]
	},
	{
		id: PRO_FORM_EMAIL_LAYOUT,
		label: 'Email Layout',
		type: 'dropdown',
		path: 'pro.layout',
		options: []
	},
	{
		id: PRO_FORM_PDF_LAYOUT,
		label: 'PDF Layout',
		type: 'dropdown',
		path: 'pro.pdf_layout',
	},
	{
		id: PRO_FORM_PDF_ATATCH,
		label: 'Attach PDF To Main Mailer',
		type: 'checkbox',
		path: 'pro.attach_pdf',
		options: [enableDiable]
	},
	{
		id: PRO_FORM_PDF_LINK,
		label: 'Add PDF Link',
		type: 'checkbox',
		path: 'pro.pdf_link',
		options: [enableDiable]
	},
]