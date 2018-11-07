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
	cfFieldIsNotDisabled,
	cfFieldCheckAllValues,
	cfFieldClickButton, cfFieldGetWrapper
} from '../support/util';


describe('Name of test', () => {
	beforeEach(() => {
		visitPage('date-validation-2747');
	});

	const formId = 'CF5be31da4beff7';

	const textField = 'fld_225467';
	const dateField = 'fld_8112155';
	const button = 'fld_2324958';



	it( 'Leaving it empty triggers validation error', () => {
		cfFieldClickButton(button);
		cfFieldSetValue(textField, 'I am a sandwich' );//set text field valid
		cfFieldGetWrapper(dateField).find( '.parsley-required').contains( 'This value is required.');
		cfFieldGetWrapper(dateField).find( '.help-block').children().should('have.length', 1);
	});


	it( 'We can clear the validation error', () => {
		cfFieldClickButton(button);
		cfFieldGetWrapper(dateField).find( '.help-block').children().should('have.length', 1);
		getCfField(dateField).type( '2018-11-06');
		//cfFieldGetWrapper(dateField).find( '.help-block').children().should('have.length', 0);
		cfFieldClickButton(button);


	});

});