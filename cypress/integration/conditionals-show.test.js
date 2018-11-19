import {
	visitPage,
	getCfField,
	clearCfField,
	cfFieldIsVisible,
	cfFieldDoesNotExist,
	cfFieldHasValue,
	cfFieldSelectValue,
	cfFieldSetValue,
	cfFieldCheckValue, cfFieldUnCheckValue, cfFieldOptionIsChecked, cfFieldOptionIsSelected
} from '../support/util';

describe('Conditionals - show type - select fields', () => {
	beforeEach(() => {
		visitPage('show-conditionals-select');
	});

	const formId = 'CF5bc235072e2a8';
	const showCheckbox = 'fld_7176956';
	const checkbox1 = 'fld_5440278';
	const checkbox2 = 'fld_921826';
	const extraField = 'fld_1034558';

	const showRadio = 'fld_7631843';
	const radio1 = 'fld_464591';

	const showOthers = 'fld_1734075';
	const dropDown = 'fld_520708';
	const stateProvidence = 'fld_5313777';
	const date = 'fld_888212';



	it( 'Hides and shows based on checkbox', () => {
		cfFieldDoesNotExist(checkbox1);
		cfFieldDoesNotExist(checkbox2);

		cfFieldCheckValue(showCheckbox,'show1');
		cfFieldIsVisible(checkbox1);
		cfFieldUnCheckValue(checkbox1, 'ck1c' );//uncheck default
		cfFieldDoesNotExist(checkbox2);

		cfFieldCheckValue(showCheckbox,'show2');
		cfFieldIsVisible(checkbox1);
		cfFieldIsVisible(checkbox2);

		cfFieldUnCheckValue(showCheckbox, 'show2' );
		cfFieldDoesNotExist(checkbox2);

		cfFieldUnCheckValue(showCheckbox, 'show1' );
		cfFieldDoesNotExist(checkbox1);

		cfFieldCheckValue(showCheckbox, 'showBoth');
		cfFieldIsVisible(checkbox1);
		cfFieldIsVisible(checkbox2);

		cfFieldCheckValue(checkbox1,'ck1a');
		cfFieldCheckValue(checkbox2,'ck3b');
		cfFieldIsVisible(extraField);

		cfFieldUnCheckValue(checkbox1,'ck1a');
		cfFieldUnCheckValue(checkbox2,'ck3b');
		cfFieldDoesNotExist(extraField);

		cfFieldCheckValue(checkbox2,'ck2b');
		cfFieldIsVisible(extraField);

	});

	it( 'hides and shows based on radio field', () => {
		cfFieldIsVisible(radio1);

		cfFieldCheckValue(showRadio,'No');
		cfFieldDoesNotExist(radio1);

		cfFieldCheckValue(showRadio,'Yes');
		cfFieldIsVisible(radio1);

	});

	it( 'Preserves values of checkboxes', () => {
		cfFieldCheckValue(showCheckbox,'show1');
		cfFieldOptionIsChecked(checkbox1, 'ck1c');
		cfFieldCheckValue(checkbox1, 'ck1a');

		cfFieldUnCheckValue(showCheckbox,'show1');
		cfFieldCheckValue(showRadio,'Yes' );
		cfFieldCheckValue(showCheckbox,'show1');
		cfFieldOptionIsChecked(checkbox1, 'ck1c');
		cfFieldOptionIsChecked(checkbox1, 'ck1a');

	});

	it( 'Preserves values of radios', () => {

		cfFieldCheckValue(showRadio,'Yes' );
		cfFieldOptionIsChecked(radio1, 'r1b');
		cfFieldCheckValue(radio1, 'r1c');
		cfFieldCheckValue(showRadio,'No' );
		cfFieldCheckValue(showRadio,'Yes' );
		cfFieldOptionIsChecked(radio1,'r1c');

	});

	it( 'Preserves values of dropdown', () => {
		cfFieldCheckValue(showOthers,'Yes' );
		cfFieldOptionIsSelected(dropDown, 's1b');

		cfFieldSelectValue(dropDown, 's1c' );

		cfFieldCheckValue(showOthers,'No' );
		cfFieldCheckValue(showRadio,'Yes' );
		cfFieldCheckValue(showOthers,'Yes' );

		cfFieldOptionIsSelected(dropDown,'s1c');
	});

	it( 'preserves value of date', () => {
		cfFieldCheckValue(showOthers, 'Yes' );
		cfFieldSetValue(date, '2019-01-01' );
		cfFieldCheckValue(showOthers, 'No' );
		cfFieldDoesNotExist(date);
		cfFieldCheckValue(showOthers, 'Yes' );
		cfFieldHasValue(date, '2019-01-01');

	});

	it( 'preserves value of state/Providence field', () => {
		cfFieldCheckValue(showOthers, 'Yes' );
		cfFieldSelectValue(stateProvidence, 'PA' );
		cfFieldCheckValue(showOthers, 'No' );
		cfFieldDoesNotExist(date);
		cfFieldCheckValue(showOthers, 'Yes' );
		cfFieldHasValue(stateProvidence, 'PA');

	});
});

describe('Conditionals - show type - text fields', () => {
	beforeEach(() => {
		visitPage('conditional-show-test');
	});

	const controlField = 'fld_471602';
	const controlField2 = 'fld_4533316';
	const textField = 'fld_6735870';
	const textFieldWithDefault = 'fld_8484460';

	const textFieldAsNumber = 'fld_2782690';
	const textFieldAsNumberWithDefault = 'fld_9936249';

	const button1 = 'fld_8729978';
	const button2 = 'fld_8576859';

	const showMaskedInput = 'fld_53474';
	const maskedInput = 'fld_7507195';


	it( 'Does not show the fields that are not shown by default', () => {
		cfFieldDoesNotExist(textField);
		cfFieldDoesNotExist(textFieldWithDefault);

		cfFieldDoesNotExist(textFieldAsNumber);
		cfFieldDoesNotExist(textFieldAsNumberWithDefault);

		cfFieldDoesNotExist(maskedInput);
	});



	it('Show and update values of regular text fields', () => {
		cfFieldSelectValue(controlField,'showText');
		cfFieldIsVisible(textField);
		cfFieldIsVisible(textFieldWithDefault);

		cfFieldDoesNotExist(textFieldAsNumber);
		cfFieldDoesNotExist(textFieldAsNumberWithDefault);


		const newValue = '! R%oœnom s 8 oõeê';
		cfFieldSetValue(textField,newValue);
		cfFieldSelectValue(controlField,'showNone');
		cfFieldSelectValue(controlField,'showText');
		cfFieldHasValue(textField,newValue);

	});

	it('Show and update values of number-like text fields', () => {
		cfFieldSelectValue(controlField,'showNumber');
		cfFieldIsVisible(textFieldAsNumber);
		cfFieldIsVisible(textFieldAsNumberWithDefault);

		cfFieldDoesNotExist(textField);
		cfFieldDoesNotExist(textFieldWithDefault);

		const newValue = -42;
		cfFieldSetValue(textFieldAsNumber,newValue);
		cfFieldSelectValue(controlField,'showNone');
		cfFieldSelectValue(controlField,'showNumber');
		cfFieldHasValue(textFieldAsNumber,newValue);
		cfFieldHasValue(textFieldAsNumberWithDefault,5);

	});


	it.skip( 'can show masked input and it works right', () => {
		const value = '11-ab-2a';
		cfFieldCheckValue(showMaskedInput, 'Yes');
		cfFieldIsVisible(maskedInput);
		cfFieldSetValue(maskedInput, value);

		//Hide it
		cfFieldCheckValue(showMaskedInput, 'No' );
		cfFieldDoesNotExist(maskedInput);

		//Show it
		cfFieldCheckValue(showMaskedInput, 'Yes' );
		cfFieldHasValue(maskedInput,value);

		//Attempt to set an invalid value
		getCfField(maskedInput).type( 'Roy' );
		cfFieldHasValue(maskedInput,value);

		//Set a valid value
		const newValue ='11-ar-3s';
		cfFieldSetValue(maskedInput,newValue);
		cfFieldHasValue(maskedInput,newValue);
		//set an invalid value
		getCfField(maskedInput,'1adadssada1');
		//still has good value
		cfFieldHasValue(maskedInput,newValue);
		//hide and show again to make sure it still has valid value
		cfFieldCheckValue(showMaskedInput, 'No' );
		cfFieldCheckValue(showMaskedInput, 'Yes' );
		cfFieldHasValue(maskedInput,newValue);
	});

});