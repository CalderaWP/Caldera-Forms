import {
    visitPage,
    cfFieldIsNotVisible,
	cfFieldIsVisible,
	cfFieldSetValue,
	cfFieldCheckValue,
    cfFieldCheckAllValues
} from '../support/util';

describe('Calculation value is showed when unhiding the calculation field', () => {

	beforeEach(() => {
		visitPage('calculation-triggered-when-unhiding-calculation-field');
    });

    it( 'Check that calculation field is hidden', () => {
		cfFieldIsNotVisible('fld_5714523_1-wrap');
    });

    it( 'Set the number field a value and check the checkbox', () => {
        cfFieldSetValue('fld_241369_1', '5');
        cfFieldCheckAllValues('fld_7013805_1-wrap');
    });

    it( 'Check that calculation field is visible and has the value of the number field', () => {
        cfFieldIsVisible('fld_5714523_1-wrap');
        cfFieldCheckValue('fld_5714523_1', '5');
	});
    
});
