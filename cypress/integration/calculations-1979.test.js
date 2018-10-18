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
		visitPage('1979-calculations-created1-5-5-dev1-5-6-2');
	});

	const formId = 'CF59ce6f1747efb';
	const totalCalc = 'fld_7896676';
	const discountCalc = 'fld_1734684';
	const grandTotalCalc = 'fld_6532733';
	const selectionsCheckbox = 'fld_9272690';


	function testInitialLoad() {
		cfFieldCalcFieldValueIs(discountCalc, '0.00');
		cfFieldCalcFieldValueIs(totalCalc, '100.00');
		cfFieldCalcFieldValueIs(grandTotalCalc, '100.00');
	}

	it( 'Has the correct initial load', () => {
		testInitialLoad();
	});

	it( 'Updates and does math correctly', () => {
		testInitialLoad();
		cfFieldCheckValue(selectionsCheckbox, '200' );
		cfFieldCalcFieldValueIs(discountCalc, '10.00');
		cfFieldCalcFieldValueIs(grandTotalCalc, '290.00');

		cfFieldCheckValue(selectionsCheckbox, '300' );
		cfFieldCalcFieldValueIs(discountCalc, '30.00');
		cfFieldCalcFieldValueIs(grandTotalCalc, '570.00');

		cfFieldUnCheckValue(selectionsCheckbox, '200' );
		cfFieldCalcFieldValueIs(discountCalc, '0.00');
		cfFieldCalcFieldValueIs(grandTotalCalc, '400.00');
	});

});