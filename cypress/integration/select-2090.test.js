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
	cfFieldCalcFieldValueIs, cfFieldHasOptions
} from '../support/util';


describe('2090 SELECT CREATED@1.5.7.1 DEV@1.5.8', () => {
	beforeEach(() => {
		visitPage('2090-select-created1-5-7-1-dev1-5-8');
	});

	const formId = 'CF5a1163356fe7c';
	const select1 = 'fld_7960462';
	const select2 = 'fld_6647575';


	function testInitialLoad() {
		//Define how it loads here
	}

	it( 'Has the right number of options', () => {
		cfFieldHasOptions(select1,3);
		cfFieldHasOptions(select2,3);
	});
	it( 'Has the right default values', () => {
		cfFieldHasValue(select1,'0');
		cfFieldHasValue(select2,'1');
	});

});