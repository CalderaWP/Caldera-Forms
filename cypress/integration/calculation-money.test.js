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
	cfFieldIsNotDisabled, cfFieldCheckAllValues, cfFieldCalcFieldValueIs
} from '../support/util';


describe('Calculations - money style', () => {
	beforeEach(() => {
		visitPage('calculation-money');
	});

	const formId = 'CF5bc25cfa53898';
	const calc1 = 'fld_3231357';
	const calc1Quantity = 'fld_3087149';
	const calc1Option = 'fld_9132898';

	const calc2 = 'fld_8673305';
	const calc2Opt1 = 'fld_7613122';
	const calc2Opt2 = 'fld_6377680';

	const calc3 = 'fld_8223213';
	const calc3opt = 'fld_3489682';

	it( 'Price option + Quantity option', () => {
		cfFieldCalcFieldValueIs(calc1, '5.00' );

		cfFieldSetValue(calc1Quantity, 42 );
		cfFieldCalcFieldValueIs(calc1, '42.00' );

		cfFieldSelectValue(calc1Option,'1');
		cfFieldCalcFieldValueIs(calc1, '4200.00' );

		cfFieldSetValue(calc1Quantity, 41 );
		cfFieldCalcFieldValueIs(calc1, '4100.00' );

	});

	it( 'Radio-based options', () => {
		cfFieldCalcFieldValueIs(calc2, '5.00' );
		cfFieldCheckValue(calc2Opt1, '2' );
		cfFieldCalcFieldValueIs(calc2, '7.00' );
		cfFieldCheckValue(calc2Opt1, '1' );
		cfFieldCalcFieldValueIs(calc2, '6.00' );

		cfFieldCheckValue(calc2Opt2, '2');
		cfFieldCalcFieldValueIs(calc2, '26.00' );

		cfFieldCheckValue(calc2Opt2, '1');
		cfFieldCalcFieldValueIs(calc2, '16.00' );
	});

	it( 'Divides and accounts for hidden field being hidden', () => {
		cfFieldCalcFieldValueIs(calc3,'5.00');
		cfFieldCheckValue(calc2Opt1, '2' );
		cfFieldCalcFieldValueIs(calc3,'6.00');
		cfFieldCheckValue(calc3opt, 'Yes');
		cfFieldCalcFieldValueIs(calc3,'12.00');
		cfFieldUnCheckValue(calc3opt, 'Yes');
		cfFieldCalcFieldValueIs(calc3,'6.00');

	});
});