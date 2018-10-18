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
	cfFieldCalcFieldValueIs
} from '../support/util';


describe('Name of test', () => {
	beforeEach(() => {
		visitPage('x10-conditionals-hide-single-page-create1-5-5');
	});

	const formId = 'CF59f54cb2b84bb';
	const number1 = 'fld_1705245';
	const text1 = 'fld_6561874';
	const text2 = 'fld_6501179';
	const text3 = 'fld_4732555';
	const email = 'fld_1059582';
	const dropdown = 'fld_5216203';

	function testInitialLoad() {
		//Define how it loads here
	}

	it( 'Hides based on dropdown', () => {
		cfFieldSelectValue(dropdown, 'Show Email');
		cfFieldIsVisible(email);
	});

	it( 'hides if number is less than ', () => {
		cfFieldSetValue(number1,4);
		cfFieldDoesNotExist(dropdown);


	});

	it( 'hides if number is', () => {
		cfFieldSetValue(number1,10);
		cfFieldDoesNotExist(dropdown);
		cfFieldSetValue(number1,6);
		cfFieldIsVisible(dropdown);

	});

	it( 'Hides if text starts with', () => {
		cfFieldSetValue(text1, 'hats' );
		cfFieldDoesNotExist(text2);
		cfFieldSetValue(text1, 'hat' );
		cfFieldIsVisible(text2);
		cfFieldSetValue(text1, 'hatsa' );
		cfFieldDoesNotExist(text2);

	});

	it.skip( 'Hides if text ends with', () => {
		cfFieldSetValue(text1, 'asasddshats' );
		cfFieldDoesNotExist(text3);
		cfFieldSetValue(text1, 'hata' );
		cfFieldIsVisible(text2);
		cfFieldSetValue(text1, ' hats' );
		cfFieldDoesNotExist(text2);
	});


});