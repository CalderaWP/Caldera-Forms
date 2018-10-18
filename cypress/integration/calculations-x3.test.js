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
		visitPage('x3-calculations-create1-5-5-dev1-5-6-2');
	});

	const formId = 'CF59dd60bbe7ab6';
	const mathCalc = 'fld_6083669';
	const roundMathCalc = 'fld_9723683';
	const numberField = 'fld_2950569';


	function testInitialLoad() {
		cfFieldCalcFieldValueIs(mathCalc,'0');
		cfFieldCalcFieldValueIs(roundMathCalc,'0');
	}

	it( 'Has the correct initial load', () => {
		testInitialLoad();
	});
	it( 'Does something else', () => {
		testInitialLoad();
		cfFieldSetValue(numberField, 10);

		cfFieldCalcFieldValueIs(mathCalc,'0.6483608274590866');
		cfFieldCalcFieldValueIs(roundMathCalc,'1');

		cfFieldSetValue(numberField, -351);

		cfFieldCalcFieldValueIs(mathCalc,'1.1577507304420032');
		cfFieldCalcFieldValueIs(roundMathCalc,'1');

	});

});