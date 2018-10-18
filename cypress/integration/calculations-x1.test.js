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
		visitPage('x1-calculations-create1-5-5-dev1-5-6-2');
	});

	const formId = 'CF59dd15667a03b';
	const totalCalc = 'fld_8997460';
	const option1checkbox = 'fld_3993413';
	const option2Select = 'fld_5161425';

	function testInitialLoad() {
		cfFieldCalcFieldValueIs(totalCalc, '25.00');

	}

	it( 'Has the correct initial load', () => {
		testInitialLoad();
	});

	it( 'Updates and does math correctly', () => {
		testInitialLoad();

		cfFieldCheckValue(option1checkbox, 'Yes' );
		cfFieldCalcFieldValueIs(totalCalc, '35.00');

		cfFieldSelectValue(option2Select, 'Big' );
		cfFieldCalcFieldValueIs(totalCalc, '40.00');

		cfFieldSelectValue(option2Select, 'Small' );
		cfFieldCalcFieldValueIs(totalCalc, '36.00');

		cfFieldUnCheckValue(option1checkbox, 'Yes' );
		cfFieldCalcFieldValueIs(totalCalc, '26.00');


	});

});