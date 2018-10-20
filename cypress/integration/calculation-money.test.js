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
		//cost calc1Quantity is x10 by default
		cfFieldCalcFieldValueIs(calc1, '50.00' );

		cfFieldSetValue(calc1Quantity, 42 );
		cfFieldCalcFieldValueIs(calc1, '420.00' );

		cfFieldSelectValue(calc1Option,'1');//cost is now calc1Quantityx100
		cfFieldCalcFieldValueIs(calc1, '4200.00' );

		cfFieldSelectValue(calc1Option,'2');//cost is now calc1Quantityx10

		cfFieldSetValue(calc1Quantity, 41 );
		cfFieldCalcFieldValueIs(calc1, '410.00' );

	});

	it( 'Radio-based options', () => {
		cfFieldCalcFieldValueIs(calc2, '50.00' );
		cfFieldCheckValue(calc2Opt1, '2' );
		cfFieldCalcFieldValueIs(calc2, '52.00' );
		cfFieldCheckValue(calc2Opt1, '1' );
		cfFieldCalcFieldValueIs(calc2, '51.00' );

		cfFieldCheckValue(calc2Opt2, '2');
		cfFieldCalcFieldValueIs(calc2, '71.00' );

		cfFieldCheckValue(calc2Opt2, '1');
		cfFieldCalcFieldValueIs(calc2, '61.00' );
	});

	it( 'Divides and accounts for hidden field being hidden', () => {
		cfFieldCalcFieldValueIs(calc3,'50.00');
		cfFieldCheckValue(calc2Opt1, '2' );
		cfFieldCalcFieldValueIs(calc3,'51.00');
		cfFieldCheckValue(calc3opt, 'Yes'); //remove dividing by one via hidden field
		cfFieldCalcFieldValueIs(calc3,'102.00');


	});
});