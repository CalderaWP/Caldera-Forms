import {optionFactory} from "../util/optionFactory";

export const STYLE_FORM = 'form';
export const STYLE_GRID = 'grid';
export const STYLE_ALERT = 'alert';
export const CDN_ENABLE = 'cdn';
const enableOption = optionFactory(
	true,
	'Enable'
);
export default  [
	{
		id: 'form',
		label: 'Form Styles',
		desc: 'Includes Bootstrap 3 styles on the frontend for form fields and buttons',
		type: 'fieldset',
		inputType: 'checkbox',
		path: 'generalSettings.form',
		options: [enableOption]
	},
	{
		id: 'alert',
		label: 'Alert Styles',
		desc: 'Includes Bootstrap 3 styles on the frontend for form alert notices',
		type: 'fieldset',
		inputType: 'checkbox',
		path: 'stylgeneralSettingseIncludes.alert',
		options: [enableOption]
	},
	{
		id: 'grid',
		label: 'Grid',
		desc: 'Includes Bootstrap 3 styles on the frontend for form grid layouts',
		type: 'fieldset',
		inputType: 'checkbox',
		path: 'generalSettings.grid',
		options: [enableOption]
	},{
		id: 'cdn',
		label: 'Enable Free CDN',
		desc: 'Some usage data will be shared with CDN providers',
		type: 'fieldset',
		innerType: 'checkbox',
		path: 'generalSettings.cdnEnable',
		options: [enableOption]
	},

]