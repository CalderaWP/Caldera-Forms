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


describe('1993 Calculations', () => {
	beforeEach(() => {
		visitPage('1993-calculations-created1-5-5-dev1-5-6-2');
	});

	const formId = 'CF59d9652a86fff';
	const calcNumber1 = 'fld_7335878';
	const calcNumber2 = 'fld_5821713';
	const multiplyCalc = 'fld_1467266';
	const number1 = 'fld_4476299';
	const number2 = 'fld_7265648';
	const hidden = 'fld_7400054';

	function testIntialLoad() {
		cfFieldCalcFieldValueIs(calcNumber1, '1');
		cfFieldCalcFieldValueIs(calcNumber2, '0');
		cfFieldCalcFieldValueIs(multiplyCalc, '1');
	}

	it( 'Has the right math on initial load', () => {
		testIntialLoad();

	});

	it( 'Updates after initial load with correct math', () => {
		cfFieldSetValue(number2,  5);
		cfFieldCalcFieldValueIs(calcNumber1, '1');
		cfFieldCalcFieldValueIs(calcNumber2, '5');
		cfFieldCalcFieldValueIs(multiplyCalc, '6');

		cfFieldSetValue(number1,  10);
		cfFieldCalcFieldValueIs(calcNumber1, '10');
		cfFieldCalcFieldValueIs(calcNumber2, '5');
		cfFieldCalcFieldValueIs(multiplyCalc, '51');

		cfFieldSetValue(number2,  -50);
		cfFieldCalcFieldValueIs(calcNumber1, '10');
		cfFieldCalcFieldValueIs(calcNumber2, '-50');
		cfFieldCalcFieldValueIs(multiplyCalc, '-499');
	});

});