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
		visitPage('hello-world');
	});

	const formId = 'cf111';


	function testInitialLoad() {
		//Define how it loads here
	}

	it( 'Has the correct initial load', () => {
		testInitialLoad();
	});
	it( 'Does something else', () => {
		testInitialLoad();
		//test form
	});

});