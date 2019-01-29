import {
	visitPage,
	getCfField,
	clearCfField,
	cfFieldIsVisible,
	cfFieldDoesNotExist,
	cfFieldHasValue,
	cfFieldSelectValue,
	cfFieldSetValue,
	cfFieldCheckValue,
	cfFieldIsDisabled,
	cfFieldUnCheckValue,
	cfFieldIsNotDisabled, cfFieldCheckAllValues
} from '../support/util';



/**
 * Tests for when conditions are hide type
 */
describe('Conditionals - disable type - text fields', () => {
	beforeEach(() => {
		visitPage('conditionals-disable-test');
	});

	const disabler = 'fld_9551037';

	const textField = 'fld_3067684';
	const colorField = 'fld_6740475';
	const phoneBetterField = 'fld_4913550';
	const phoneBasicField = 'fld_3414426';
	const numberField = 'fld_8059884';
	const submitButton = 'fld_9529943';
	const emailField = 'fld_7867333';
	const urlField = 'fld_8461580';
	const fields = [
		textField,
		colorField,
		phoneBasicField,
		phoneBetterField,
		numberField,
		submitButton,
		urlField,
		emailField
	];

	it( 'Disables text field',() => {
		cfFieldCheckValue(disabler,'disableText');
		cfFieldIsDisabled(textField);

		cfFieldUnCheckValue(disabler,'disableText');
		cfFieldIsNotDisabled(textField);
		cfFieldSetValue(textField,'Noms');
		cfFieldHasValue(textField,'Noms');

		cfFieldCheckValue(disabler,'disableText');
		cfFieldCheckValue(disabler,'disableNone');
		cfFieldIsNotDisabled(textField);

		cfFieldSetValue(textField,'Noms!');
		cfFieldHasValue(textField,'Noms!');
	});

	it( 'Disables color field',() => {
		cfFieldCheckValue(disabler,'disableColor');
		cfFieldIsDisabled(colorField);

		cfFieldUnCheckValue(disabler,'disableColor');
		cfFieldIsNotDisabled(colorField);
		cfFieldSetValue(colorField,'#FFFFFF');
		cfFieldHasValue(colorField,'#FFFFFF');

		cfFieldCheckValue(disabler,'disableColor');
		cfFieldIsDisabled(colorField);
		cfFieldCheckValue(disabler,'disableNone');
		cfFieldIsNotDisabled(colorField);

		cfFieldSetValue(colorField,'#FFFF00');
		cfFieldHasValue(colorField,'#FFFF00');
	});

	it( 'Disables phone better field',() => {
		cfFieldCheckValue(disabler,'disablePhoneBetter');
		cfFieldIsDisabled(phoneBetterField);

		cfFieldUnCheckValue(disabler,'disablePhoneBetter');
		cfFieldIsNotDisabled(phoneBetterField);
		cfFieldSetValue(phoneBetterField,'(111) 123-4567');
		cfFieldHasValue(phoneBetterField,'(111) 123-4567');

		cfFieldCheckValue(disabler,'disablePhoneBetter');
		cfFieldIsDisabled(phoneBetterField);

		cfFieldCheckValue(disabler,'disableNone');
		cfFieldIsNotDisabled(phoneBetterField);
		cfFieldSetValue(phoneBetterField,'(111) 123-4561');
		cfFieldHasValue(phoneBetterField,'(111) 123-4561');

	});

	it( 'Disables phone basic field',() => {
		cfFieldCheckValue(disabler,'disablePhoneBasic');
		cfFieldIsDisabled(phoneBasicField);

		cfFieldUnCheckValue(disabler,'disablePhoneBasic');
		cfFieldIsNotDisabled(phoneBasicField);

		cfFieldCheckValue(disabler,'disablePhoneBasic');
		cfFieldIsDisabled(phoneBasicField);
		cfFieldCheckValue(disabler,'disableNone');
		cfFieldIsNotDisabled(phoneBasicField);

	});


	it( 'Disables phone basic field',() => {
		cfFieldCheckValue(disabler,'disablePhoneBasic');
		cfFieldIsDisabled(phoneBasicField);

		cfFieldUnCheckValue(disabler,'disablePhoneBasic');
		cfFieldIsNotDisabled(phoneBasicField);

		cfFieldCheckValue(disabler,'disablePhoneBasic');
		cfFieldIsDisabled(phoneBasicField);

		cfFieldCheckValue(disabler,'disableNone');
		cfFieldIsNotDisabled(phoneBasicField);
	});

	it( 'Disables url  field',() => {
		cfFieldCheckValue(disabler,'disableUrl');
		cfFieldIsDisabled(urlField);

		cfFieldUnCheckValue(disabler,'disableUrl');
		cfFieldIsNotDisabled(urlField);

		cfFieldCheckValue(disabler,'disableUrl');
		cfFieldIsDisabled(urlField);

		cfFieldCheckValue(disabler,'disableNone');
		cfFieldIsNotDisabled(urlField);
	});

	it( 'Disables email field',() => {
		cfFieldCheckValue(disabler,'disableEmail');
		cfFieldIsDisabled(emailField);

		cfFieldUnCheckValue(disabler,'disableEmail');
		cfFieldIsNotDisabled(emailField);

		cfFieldCheckValue(disabler,'disableEmail');
		cfFieldIsDisabled(emailField);

		cfFieldCheckValue(disabler,'disableNone');
		cfFieldIsNotDisabled(emailField);
	});

	it( 'Disables none when all are checked, including disable all', () => {
		cfFieldCheckAllValues(disabler);
		fields.forEach( fieldId => {
			cfFieldIsNotDisabled(fieldId);
		});
	});

	it( 'Disables all when all are checked, except disable all', () => {
		cfFieldCheckAllValues(disabler);
		cfFieldUnCheckValue(disabler, 'disableNone')
		fields.forEach( fieldId => {
			cfFieldIsDisabled(fieldId);
		});
	});

});