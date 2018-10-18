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
	cfFieldCalcFieldValueIs, cfFieldClickButton, cfFieldGetWrapper, cfAlertHasText
} from '../support/util';


describe('Name of test', () => {
	beforeEach(() => {
		visitPage('2107-credit-card-created-dev1-5-8');
	});

	const formId = 'CF5a1de64c110ae';
	const nextButton = 'fld_9594894';
	const cc2 = 'fld_8591050';
	const exp = 'fld_6124340';
	const cvc = 'fld_4200620';
	const submitButton = 'fld_9177438';




	it( 'CC fields validation works on page 2', () => {
		cfFieldClickButton(nextButton);
		cfFieldClickButton(submitButton);
		cfFieldGetWrapper(cc2).find( '.parsley-required').contains( 'This value is required.');
	});

	it( 'CC fields submit when has expiration and ccv', () => {
		//4242 4242 4242 4242
		cfFieldClickButton(nextButton);
		cfFieldClickButton(submitButton);
		cfFieldSetValue(cc2,'4242 4242 4242 4242' );
		cfFieldSetValue(cc2,'4242 4242 4242 4242' );
		cfFieldSetValue(exp,'11/28' );
		cfFieldSetValue(cvc,'412' );
		cfFieldClickButton(submitButton);
		cfAlertHasText(formId);


	})
});