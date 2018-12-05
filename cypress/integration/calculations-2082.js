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


describe('2082 CALCULATIONS CREATED@1.5.7.1 DEV@1.5.8', () => {
	beforeEach(() => {
		visitPage('2082-calculations-created1-5-7-1-dev1-5-8');
	});

	const formId = 'CF5a3193046927c';
	const select = 'fld_5132042';
	const number = 'fld_60384';
	const calc = 'fld_9859374';


	function testInitialLoad() {
		cfFieldCalcFieldValueIs(calc,'20');
		cfFieldHasValue(select,'7');
		cfFieldHasValue(number,'13');
	}

	it( 'Has the correct initial load', () => {
		testInitialLoad();
	});
	it( 'Does math based on select field without calc value set', () => {
		testInitialLoad();
		cfFieldSelectValue(select,'5');
		cfFieldCalcFieldValueIs(calc,'18')
	});

});