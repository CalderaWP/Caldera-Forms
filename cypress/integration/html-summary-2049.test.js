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


describe('2049 HTML/SUMMARY CREATE@1.5.7 DEV@1.5.7', () => {
	beforeEach(() => {
		visitPage('2049-html-summary-create1-5-7-dev1-5-7');
	});

	const formId = 'CF59fb854b2f05f';
	const dropdown1 = 'fld_30547';
	const dropdownSync = 'fld_7212795';
	const autocomplete = 'fld_9766093';
	const autoCompleteSync = 'fld_3490198';
	const summary = 'fld_6130370';


	const summaryContainsValues = (containsValues) => {
		cfFieldSummaryContainsValues('#html-content-fld_6130370_1', containsValues );
	};

	function testInitialLoad() {
		cfFieldHasValue(dropdown1,'B');
		cfFieldHasValue(dropdownSync,'B');

		cfFieldHasValue(autocomplete,'D');
		cfFieldHasValue(autoCompleteSync,'D');
		summaryContainsValues([
			'B', 'B', 'D', 'D'
		]);
	}

	it( 'Has the right initial load', () => {
		testInitialLoad();
	});
	it.skip( 'Stays in sync from auto-complete field to text field', () => {
		testInitialLoad();
		cfFieldSelectValue(dropdown1,'A');
		summaryContainsValues([
			'A', 'A', 'D', 'D'
		]);
	});
	it.skip( 'Breaks sync correctly from auto-complete field to text field', () => {
		testInitialLoad();
		cfFieldSetValue(autocomplete,'C');
		cfFieldSetValue(autoCompleteSync, 'Boom' );
		summaryContainsValues([
			'A', 'A', 'C', 'Boom'
		]);
		cfFieldSelectValue(autocomplete,'D');
		cfFieldHasValue(autoCompleteSync, 'Boom' );
		summaryContainsValues([
			'B', 'A', 'D', 'Boom'
		]);
	});

	it( 'Stays in sync from dropdown field to text field', () => {
		testInitialLoad();
		cfFieldSelectValue(dropdown1,'A');
		summaryContainsValues([
			'A', 'A', 'D', 'D'
		]);
	});
	it( 'Breaks sync correctly from dropdown field to text field', () => {
		testInitialLoad();
		cfFieldSelectValue(dropdown1,'A');
		cfFieldSetValue(dropdownSync, 'Boom' );
		summaryContainsValues([
			'A', 'Boom', 'D', 'D'
		]);
		cfFieldSelectValue(dropdown1,'B');
		cfFieldHasValue(dropdownSync, 'Boom' );
		summaryContainsValues([
			'B', 'Boom', 'D', 'D'
		]);
	});

});