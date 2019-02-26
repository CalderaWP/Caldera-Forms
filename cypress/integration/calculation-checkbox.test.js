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


describe('Calculations - With Checkbox', () => {
	beforeEach(() => {
		visitPage('calculations-checkbox');
	});

	const formId = 'CF5bc26604eaf3c';
	const calc1 = 'fld_3364767';
	const calc2 = 'fld_6418208';
	const calc3 = 'fld_522650';
	const calc4 = 'fld_1629134';
	const checkbox1 = 'fld_3100099';
	const checkbox2 = 'fld_5764774';
	const hideCheck = 'fld_2526988';

	it( 'starts at zero', () => {
		cfFieldCalcFieldValueIs(calc1, '0.00' );
		cfFieldCalcFieldValueIs(calc2, '0' );
		cfFieldCalcFieldValueIs(calc3, '0.00' );
		cfFieldCalcFieldValueIs(calc4, '0' );
	});

	it( 'adds up checkbox', () => {

		cfFieldCheckValue(checkbox1, '1');
		cfFieldCalcFieldValueIs(calc1, '1.00' );
		cfFieldCalcFieldValueIs(calc2, '1' );
		cfFieldCalcFieldValueIs(calc3, '1.00' );
		cfFieldCalcFieldValueIs(calc4, '1' );

		cfFieldCheckValue(checkbox1, '2');
		cfFieldCalcFieldValueIs(calc1, '3.00' );
		cfFieldCalcFieldValueIs(calc2, '3' );
		cfFieldCalcFieldValueIs(calc3, '3.00' );
		cfFieldCalcFieldValueIs(calc4, '3' );

		cfFieldCheckValue(checkbox1, '3');
		cfFieldCalcFieldValueIs(calc1, '6.00' );
		cfFieldCalcFieldValueIs(calc2, '6' );
		cfFieldCalcFieldValueIs(calc3, '6.00' );
		cfFieldCalcFieldValueIs(calc4, '6' );

		cfFieldUnCheckValue(checkbox1, '2');
		cfFieldCalcFieldValueIs(calc1, '4.00' );
		cfFieldCalcFieldValueIs(calc2, '4' );
		cfFieldCalcFieldValueIs(calc3, '4.00' );
		cfFieldCalcFieldValueIs(calc4, '4' );

	});

	it( 'adds up two checkbox fields', () => {

		cfFieldCheckValue(checkbox1, '1');
		cfFieldCalcFieldValueIs(calc1, '1.00' );
		cfFieldCalcFieldValueIs(calc2, '1' );
		cfFieldCalcFieldValueIs(calc3, '1.00' );
		cfFieldCalcFieldValueIs(calc4, '1' );

		cfFieldCheckValue(checkbox2, '10002');
		cfFieldCalcFieldValueIs(calc1, '11.00' );
		cfFieldCalcFieldValueIs(calc2, '11.002' );
		cfFieldCalcFieldValueIs(calc3, '11.00' );
		cfFieldCalcFieldValueIs(calc4, '11.002' );

		cfFieldCheckValue(checkbox2, '10003');
		cfFieldCalcFieldValueIs(calc1, '21.01' );
		cfFieldCalcFieldValueIs(calc2, '21.005000000000003' );
		cfFieldCalcFieldValueIs(calc3, '21.01' );
		cfFieldCalcFieldValueIs(calc4, '21.005000000000003' );


	});

	it( 'Does not add hidden checkbox', () => {
		cfFieldCheckValue(checkbox1, '2');
		cfFieldCheckValue(checkbox2, '10001');
		cfFieldCalcFieldValueIs(calc1, '12.00' );
		cfFieldCalcFieldValueIs(calc2, '12.001' );
		cfFieldCalcFieldValueIs(calc3, '12.00' );
		cfFieldCalcFieldValueIs(calc4, '12.001' );

		cfFieldSelectValue(hideCheck, 'Yes' );
		cfFieldCalcFieldValueIs(calc1, '10.00' );
		cfFieldCalcFieldValueIs(calc2, '10.001' );
		cfFieldCalcFieldValueIs(calc3, '10.00' );
		cfFieldCalcFieldValueIs(calc4, '10.001' );

		cfFieldSelectValue(hideCheck, '' );
		cfFieldCalcFieldValueIs(calc1, '12.00' );
		cfFieldCalcFieldValueIs(calc2, '12.001' );
		cfFieldCalcFieldValueIs(calc3, '12.00' );
		cfFieldCalcFieldValueIs(calc4, '12.001' );

	});
});