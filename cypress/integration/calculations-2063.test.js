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


describe('2063 CALCULATIONS CREATE@X DEV@1.5.7.1', () => {
	beforeEach(() => {
		visitPage('2063-calculations-createx-dev1-5-7-1');
	});

	const formId = 'CF5a047196d398f';
	const number1 = 'fld_6564773';
	const number2 = 'fld_8938360';
	const calc = 'fld_1741771';

	function testInitialLoad() {
		cfFieldHasValue(number1,'5');
		cfFieldHasValue(number2,'10');
		cfFieldCalcFieldValueIs(calc,'15');
	}

	it( 'Has the correct initial load', () => {
		testInitialLoad();
	});
	it( 'Does math', () => {
		testInitialLoad();
		cfFieldSetValue(number2, '25');
		cfFieldCalcFieldValueIs(calc,'30');

		cfFieldSetValue(number1,'-50');
		cfFieldCalcFieldValueIs(calc,'-25');

	});

});