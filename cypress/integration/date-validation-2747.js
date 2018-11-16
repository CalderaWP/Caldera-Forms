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


describe('Date Picker validation tests', () => {

  Cypress.on('uncaught:exception', (err, runnable) => {
    // returning false here prevents Cypress from
    // failing the test
    return false
  })

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
		getCfField(dateField).click();
    cy.get('div.cfdatepicker div.cfdatepicker-days table.table-condensed tbody tr td.day').first().click();
    cy.get('.entry-content').click();
		cfFieldGetWrapper(dateField).find( '.help-block').click();
    cfFieldGetWrapper(dateField).find('input').should('have.class', 'parsley-success');
    cfFieldGetWrapper(dateField).find( '.help-block').should('not.be.visible');
    cfFieldGetWrapper(dateField).find('input').should('not.have.class', 'parsley-required');
		cfFieldClickButton(button);


	});

});