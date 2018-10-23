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
	cfFieldCalcFieldValueIs, cfFieldSummaryContains, cfFieldSummaryContainsValues
} from '../support/util';


describe('Name of test', () => {
	beforeEach(() => {
		visitPage('2014-html-summary-create1-5-5-dev1-5-7');
	});

	const formId = 'CF59e14e252a2c8';

	const check1 = 'fld_17743';
	const check2 = 'fld_5391964';
	const check3 = 'fld_6022334';
	const summary = 'fld_5594906';

	const summarySelector = '#html-content-fld_5594906_1';


	function testInitialLoad() {
		cfFieldSummaryContains(summarySelector, 'Three');
	}
	it('Has the correct initial load', () => {
		testInitialLoad();
	});

	it('Updates values', () => {
		testInitialLoad();
		cfFieldCheckValue(check1, 'One' );
		cfFieldSummaryContainsValues(summarySelector, [
			'Three', 'One'
		]);

		cfFieldCheckValue(check3, 'Four' );
		cfFieldSummaryContainsValues(summarySelector, [
			'Three', 'One', 'Four'
		]);

		cfFieldCheckValue(check3, 'Five' );
		cfFieldSummaryContainsValues(summarySelector, [
			'Three', 'One', 'Four, Five'
		]);

		cfFieldUnCheckValue(check3, 'Four' );
		cfFieldSummaryContainsValues(summarySelector, [
			'Three', 'One', 'Five'
		]);

		cfFieldCheckValue(check1, 'Two' );
		cfFieldSummaryContainsValues(summarySelector, [
			'Three', 'One, Two', 'Five'
		]);

		cfFieldCheckValue(check3, 'Four' );
		cfFieldSummaryContainsValues(summarySelector, [
			'Three', 'One, Two', 'Four, Five'
		]);


	});



});