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
	cfFieldCalcFieldValueIs, cfFieldSummaryContainsValues
} from '../support/util';


describe('2063.1 CONDITIONALS CREATE@1.6.2.2 DEV@1.5.7.1', () => {
	beforeEach(() => {
		visitPage('2063-1-conditionals-create1-6-2-2-dev1-5-7-1');
	});

	const formId = 'CF5a0479e6025a2';
	const number1 = 'fld_6564773';
	const number2 = 'fld_8938360';
	const calc = 'fld_1741771';
	const summary = 'fld_6634118';

	const hideNumber1Select = 'fld_6496833';
	const hideNumber2Select = 'fld_5119751';

	const summaryContains = (containsValues) => {
		return cfFieldSummaryContainsValues('#html-content-fld_6634118_1', containsValues);
	};

	function testInitialLoad() {
		cfFieldHasValue(number1,'5');
		cfFieldHasValue(number2,'10');
		summaryContains([
			'1','5','10'
		]);
	}

	it( 'Has the correct initial load', () => {
		testInitialLoad();
	});
	it( 'Does not include hidden', () => {
		testInitialLoad();
		cfFieldSelectValue(hideNumber1Select,'Yes');
		cfFieldCalcFieldValueIs(calc,'10');
		cfFieldSelectValue(hideNumber1Select,'Yes');
		cfFieldCalcFieldValueIs(calc,'10');

		cfFieldSelectValue(hideNumber2Select,'Yes');
		cfFieldCalcFieldValueIs(calc,'0');

		cfFieldSelectValue(hideNumber1Select,'');
		cfFieldCalcFieldValueIs(calc,'5');


	});

});