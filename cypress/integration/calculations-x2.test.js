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
		visitPage('x2-calculations-create1-5-5-dev1-5-6-2');
	});

	const formId = 'CF59dd5d8e95ffb';
	const totalCalc = 'fld_6617658';
	const option1Select = 'fld_8172473';
	const option2Select = 'fld_8186270';

	function testInitialLoad() {
		cfFieldCalcFieldValueIs(totalCalc, '0.00');

	}

	it( 'Has the correct initial load', () => {
		testInitialLoad();
	});

	it( 'Updates and does math correctly', () => {
		testInitialLoad();

		cfFieldSelectValue(option1Select, '4' );
		cfFieldCalcFieldValueIs(totalCalc, '4.28');

		cfFieldSelectValue(option2Select, 'Two' );
		cfFieldCalcFieldValueIs(totalCalc, '6.42');

		cfFieldSelectValue(option1Select, '3' );
		cfFieldCalcFieldValueIs(totalCalc, '5.35');
	});
});