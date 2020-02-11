import {
    visitPage,
    cfFieldIsNotVisible,
	cfFieldIsVisible,
	cfFieldSetValue,
	cfFieldCheckValue,
    cfFieldCheckAllValues,
    cfFieldCalcFieldValueIs
} from '../support/util';

describe('Calculation value is showed when unhiding the calculation field', () => {

	before(() => {
		visitPage('calculation-triggered-when-unhiding-calculation-field');
    });

    it( 'Check that calculation field is hidden', () => {
		cfFieldIsNotVisible('fld_5714523');
    });

    it( 'Set the number field a value and check the checkbox', () => {
        cfFieldSetValue('fld_241369', '5');
        cfFieldCheckAllValues('fld_7013805');
    });

    it( 'Check that calculation field is visible and has the value of the number field', () => {
        cfFieldIsVisible('fld_5714523_1-wrap');
        cfFieldCalcFieldValueIs('fld_5714523', '5');
	});
    
});

describe('Calculation value is showed the calculation field wasn\'t hidden by default but was hidden and unhidden', () => {

	before(() => {
		visitPage('calculation-showing-by-default-hidden-unhidden-displays-result');
    });

    it( 'Check that calculation field is visible and has defaut value 5', () => {
        cfFieldIsVisible('fld_5714523_1');
        cfFieldCalcFieldValueIs('fld_5714523', '5');
    });

    it( 'Check the checkbox to hide the calculation field', () => {
        cfFieldCheckAllValues('fld_7013805');
		cfFieldIsNotVisible('fld_5714523');
    });

    it( 'Set the number field a value and uncheck the checkbox to show the calculation field', () => {
        cfFieldSetValue('fld_241369', '5');
        cfFieldCheckAllValues('fld_7013805');
    });

    it( 'Check that calculation field is visible and has the value of the calculation ( defaut 5 + number field 5 )', () => {
        cfFieldIsVisible('fld_5714523');
        cfFieldCalcFieldValueIs('fld_5714523', '10');
	});
    
});