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


describe('X11 Conditionals Show Single Page create@1.5.5', () => {
	beforeEach(() => {
		visitPage('x11-conditionals-show-single-page-create1-5-5');
	});

	const formId = 'CF59f5446e46a71';
	const text1 = 'fld_2960467';
	const number1 = 'fld_7869772';
	const showTextCheckbox = 'fld_5262727';
	const showNumbersRadio = 'fld_6778974';


	it( 'Hides and shows a number field based on a radio and brings back its value', () =>{
		cfFieldDoesNotExist(number1);
		cfFieldCheckValue(showNumbersRadio, 'Yes' );
		cfFieldIsVisible(number1);

		cfFieldSetValue(number1, '101' );
		cfFieldCheckValue(showNumbersRadio, 'No' );
		cfFieldDoesNotExist(number1);
		cfFieldCheckValue(showNumbersRadio, 'Yes' );
		cfFieldHasValue(number1,'101');

		cfFieldSetValue(number1,-100.01);
		cfFieldCheckValue(showNumbersRadio, 'No' );
		cfFieldDoesNotExist(number1);
		cfFieldCheckValue(showNumbersRadio, 'Yes' );
		cfFieldHasValue(number1,'-100.01');
	});


	it( 'Hides and shows a text field based on a checkbox and brings back its value', () => {
		cfFieldIsVisible(text1);
		const value = 'hī R&öy!';
		cfFieldSetValue(text1,value);
		cfFieldUnCheckValue(showTextCheckbox, 'Yes' );
		cfFieldDoesNotExist(text1);

		cfFieldCheckValue(showTextCheckbox, 'Yes' );
		cfFieldHasValue(text1,value);
	});

});