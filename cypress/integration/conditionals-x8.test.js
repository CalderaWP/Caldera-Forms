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
	cfFieldCalcFieldValueIs, cfFieldIsNotVisible, cfFieldClickButton
} from '../support/util';


describe('X8 Conditionals Hide create@1.5.5', () => {
	beforeEach(() => {
		visitPage('x8-conditionals-hide-create1-5-5');
	});

	const formId = 'CF59f533d0a5e0b';
	const dropdown1 = 'fld_5216203';
	const number1 = 'fld_1705245';

	const nextButton = 'fld_7100783';
	const prevButton = 'fld_5619974';

	const dropdown2 = 'fld_313720';
	const number2 = 'fld_8846716';

	function testInitialLoad() {
		getCfField(dropdown1).contains('Hide Number' );

		cfFieldDoesNotExist(number1);
		cfFieldIsVisible(nextButton);
		cfFieldIsNotVisible(prevButton);
		cfFieldIsNotVisible(dropdown2);
		cfFieldDoesNotExist(number2);
	}


	it( 'Updates conditionals across pages', () => {
		testInitialLoad();
		cfFieldSelectValue(dropdown1, 'No');
		cfFieldIsVisible(number1);
		cfFieldIsNotVisible(number2);

		cfFieldSetValue(number1,5);
		cfFieldIsVisible(dropdown1);

		cfFieldSetValue(number1,6);
		cfFieldDoesNotExist(dropdown1);

		cfFieldClickButton(nextButton);
		cfFieldIsVisible(number2);
		cfFieldIsNotVisible(number1);
		cfFieldDoesNotExist(dropdown2);

		cfFieldClickButton(prevButton);
		cfFieldDoesNotExist(dropdown1);

		cfFieldSetValue(number1,4);
		cfFieldIsVisible(dropdown1);

		cfFieldClickButton(nextButton);
		cfFieldIsVisible(number2);
		cfFieldIsVisible(dropdown2);

		cfFieldSelectValue(dropdown2, 'Hide Number' );
		cfFieldDoesNotExist(number2);

		cfFieldSelectValue(dropdown2, 'No' );
		cfFieldIsVisible(number2);

		cfFieldSetValue(number2,10);
		cfFieldDoesNotExist(dropdown2);

	});

	it( 'Has the correct initial load', () => {
		testInitialLoad();
	});

});