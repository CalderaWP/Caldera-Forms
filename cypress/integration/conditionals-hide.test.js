import {
	visitPage,
	getCfField,
	getCfFieldIdAttr,
	getCfFieldSelector,
	getCfFormIdAttr,
	cfFieldIsVisible,
	cfFieldDoesNotExist,
	cfFieldHasValue,
	cfFieldSelectValue,
	cfFieldSetValue,
	cfFieldCheckValue,
	cfFieldUnCheckValue,
	getCfCheckboxOption, cfFieldOptionIsChecked, cfFieldOptionIsNotChecked, cfFieldOptionIsSelected
} from '../support/util';

const controlField = 'fld_471602';
const controlField2 = 'fld_4533316';
const textField = 'fld_6735870';
const textFieldWithDefault = 'fld_8484460';

const textFieldAsNumber = 'fld_2782690';
const textFieldAsNumberWithDefault = 'fld_9936249';

const button1 = 'fld_8729978';
const button2 = 'fld_8576859';

const hideMaskedInput = 'fld_53474';
const maskedInput = 'fld_7507195';

const formId = 'CF5bc25738c82c2';

/**
 * Tests for when conditions are hide type
 */
describe('Conditionals - hide type - text fields', () => {
	beforeEach(() => {
		visitPage('conditional-hide-test');
	});


	it('Hide and update values of regular text fields', () => {
		//Set a value in text field and then hide text fields
		cfFieldSetValue(textField, 'Mike');
		cfFieldSelectValue(controlField, 'hideText');
		//Check that they are gone
		cfFieldDoesNotExist(textField);
		cfFieldDoesNotExist(textFieldWithDefault);

		//Unhide and check fields exist with the right values
		cfFieldSelectValue(controlField, 'hideNone');
		cfFieldHasValue(textField, 'Mike');
		cfFieldHasValue(textFieldWithDefault, 'Hi Roy');

		//change both fields
		cfFieldSetValue(textField, 'Mike 3@1!');
		cfFieldSetValue(textFieldWithDefault, 'Röy');
		//hide them
		cfFieldSelectValue(controlField, 'hideText');
		//unhide them and check their values are still correct
		cfFieldSelectValue(controlField, 'hideNone');
		cfFieldHasValue(textField, 'Mike 3@1!');
		cfFieldHasValue(textFieldWithDefault, 'Röy');

	});

	it('Hide and update values of number-like text fields', () => {
		//Set a value in text field and then hide text fields
		cfFieldSetValue(textFieldAsNumber, 22);
		cfFieldSelectValue(controlField, 'hideNumber');
		//Check that they are gone
		cfFieldDoesNotExist(textFieldAsNumber);
		cfFieldDoesNotExist(textFieldAsNumberWithDefault);

		//Unhide and check fields exist with the right values
		cfFieldSelectValue(controlField, 'hideNone');
		cfFieldHasValue(textFieldAsNumber, 22);
		cfFieldHasValue(textFieldAsNumberWithDefault, 5);

		//change both fields
		cfFieldSetValue(textFieldAsNumber, 42);
		cfFieldSetValue(textFieldAsNumberWithDefault, -42);
		//hide them
		cfFieldSelectValue(controlField, 'hideNumber');
		//unhide them and check their values are still correct
		cfFieldSelectValue(controlField, 'hideNone');
		cfFieldHasValue(textFieldAsNumber, 42);
		cfFieldHasValue(textFieldAsNumberWithDefault, -42);
	});

	it('Can hide and unhide all', () => {
		//hide all
		cfFieldSelectValue(controlField, 'hideAll');

		//Check that they are gone
		cfFieldDoesNotExist(textField);
		cfFieldDoesNotExist(textFieldWithDefault);
		cfFieldDoesNotExist(textFieldAsNumber);
		cfFieldDoesNotExist(textFieldAsNumberWithDefault);

		//unhide all
		cfFieldSelectValue(controlField, 'hideNone');

		//Check field are not gone
		cfFieldIsVisible(textField);
		cfFieldIsVisible(textFieldWithDefault);
		cfFieldIsVisible(textFieldAsNumber);
		cfFieldIsVisible(textFieldAsNumberWithDefault);
	});

	it('can hide and show based on text value', () => {
		cfFieldSetValue(controlField2, 'Hide 1');
		cfFieldDoesNotExist(button1);
		cfFieldIsVisible(button2);

		cfFieldSetValue(controlField2, 'Hide 2');
		cfFieldIsVisible(button1);
		cfFieldDoesNotExist(button2);

		cfFieldSetValue(controlField2, 'Hi Roy');
		cfFieldIsVisible(button1);
		cfFieldIsVisible(button2);

		cfFieldSetValue(controlField2, 'Hide Both');
		cfFieldDoesNotExist(button1);
		cfFieldDoesNotExist(button2);
	});

	it.skip('can hide masked input and it works right', () => {
		const value = '11-ab-2a';
		cfFieldSetValue(maskedInput, value);

		//Hide it
		cfFieldCheckValue(hideMaskedInput, 'Yes');
		cfFieldDoesNotExist(maskedInput);

		//Show it
		cfFieldCheckValue(hideMaskedInput, 'No');
		cfFieldHasValue(maskedInput, value);

		//Attempt to set an invalid value
		getCfField(maskedInput).type('Roy');
		cfFieldHasValue(maskedInput, value);

		//Set a valid value
		const newValue = '11-ar-3s';
		cfFieldSetValue(maskedInput, newValue)
		cfFieldHasValue(maskedInput, newValue);
		getCfField(maskedInput, '1adadssada1');
		cfFieldHasValue(maskedInput, newValue);

	});
});


describe('state when using hide conditionals', () => {
	beforeEach(() => {
		visitPage('conditional-hide-test');
	});

	it('Loads state object in window scope', () => {
		cy.window().then((theWindow) => {
			assert.isObject(theWindow.cfstate);
			expect(theWindow.cfstate).to.have.property(getCfFormIdAttr(formId));
			const state = theWindow.cfstate[getCfFormIdAttr(formId)];
			assert.isObject(state);
		});
	});


});

describe('Conditionals - hide type - select fields', () => {
	beforeEach(() => {
		visitPage('hide-conditionals-select');
	});

	const formId = 'CF5bc235072e2a8';
	const hideCheckbox = 'fld_7176956';
	const checkbox1 = 'fld_5440278';
	const checkbox2 = 'fld_921826';
	const extraField = 'fld_1034558';

	const hideRadio = 'fld_7631843';
	const radio1 = 'fld_464591';

	const hideOthers = 'fld_1734075';
	const dropDown = 'fld_520708';
	const autoComplete = 'fld_7673890';
	const toggle = 'fld_8736564';
	const stateProvidence = 'fld_5313777';
	const date = 'fld_888212';

	const otherFields = [dropDown,toggle,stateProvidence,date];

	it( 'Hides and shows based on checkbox', () => {
		cfFieldIsVisible(checkbox1);
		cfFieldIsVisible(checkbox2);

		cfFieldCheckValue(hideCheckbox, 'hide1');
		cfFieldDoesNotExist(checkbox1);
		cfFieldIsVisible(checkbox2);

		cfFieldCheckValue(hideCheckbox, 'hide2');
		cfFieldDoesNotExist(checkbox1);
		cfFieldDoesNotExist(checkbox2);

		cfFieldCheckValue(hideCheckbox, 'hideNone');
		cfFieldIsVisible(checkbox1);
		cfFieldIsVisible(checkbox2);

		cfFieldIsVisible(extraField);
		cfFieldCheckValue(checkbox1, 'ck1a');
		cfFieldIsVisible(extraField);


		cfFieldCheckValue(checkbox2, 'ck3b');
		cfFieldDoesNotExist(extraField);

		cfFieldUnCheckValue(checkbox2, 'ck3b');
		cfFieldUnCheckValue(checkbox1, 'ck1a');
		cfFieldIsVisible(extraField);

		cfFieldCheckValue(checkbox2,'ck2b' );
		cfFieldDoesNotExist(extraField);


	});

	it( 'Hides and shows based on radio', () => {
		cfFieldDoesNotExist(radio1);
		cfFieldCheckValue(hideRadio,'No');
		cfFieldIsVisible(radio1);
		cfFieldCheckValue(hideRadio,'Yes');
		cfFieldDoesNotExist(radio1);
	});

	it( 'preserves value of radio field', () => {
		cfFieldCheckValue(hideRadio,'No');

		cfFieldCheckValue(radio1, 'r2b' );
		cfFieldCheckValue(hideRadio,'Yes');
		cfFieldCheckValue(hideRadio,'No');
		cfFieldOptionIsChecked(radio1,'r2b');

	});


	it('Hides/shows checkbox fields and keeps values', () => {

		cfFieldCheckValue(checkbox1, 'ck1b');
		cfFieldCheckValue(hideCheckbox, 'hide1');
		cfFieldUnCheckValue(hideCheckbox, 'hide1');
		cfFieldOptionIsChecked(checkbox1, 'ck1b');
		cfFieldOptionIsNotChecked(checkbox1, 'ck1a');
		cfFieldOptionIsNotChecked(checkbox1, 'ck1c');

		cfFieldCheckValue(checkbox1, 'ck1c');
		cfFieldCheckValue(hideCheckbox, 'hide1');
		cfFieldUnCheckValue(hideCheckbox, 'hide1');
		cfFieldOptionIsNotChecked(checkbox1, 'ck1a');
		cfFieldOptionIsChecked(checkbox1, 'ck1b');
		cfFieldOptionIsChecked(checkbox1, 'ck1c');

	});

	it( 'Hides shows the other fields', () => {
		otherFields.forEach(field => {
			cfFieldDoesNotExist(field)
		});
		cfFieldCheckValue(hideOthers, 'No');
		otherFields.forEach(field => {
			cfFieldIsVisible(field)
		});
	});

	it( 'Keeps dropdown value', () => {
		cfFieldCheckValue(hideOthers, 'No');
		cfFieldSelectValue(dropDown,'s1b');

		cfFieldCheckValue(hideOthers, 'Yes');
		cfFieldDoesNotExist(dropDown);
		cfFieldCheckValue(hideOthers, 'No');
		cfFieldOptionIsSelected(dropDown, 's1b');
	});

	it( 'Keeps stateProvidence value', () => {
		cfFieldCheckValue(hideOthers, 'No');
		cfFieldSelectValue(stateProvidence,'ON');

		cfFieldCheckValue(hideOthers, 'Yes');
		cfFieldDoesNotExist(stateProvidence);
		cfFieldCheckValue(hideOthers, 'No');
		cfFieldOptionIsSelected(stateProvidence, 'ON');
	});

	it( 'Keeps date value', () => {
		cfFieldCheckValue(hideOthers, 'No');
		cfFieldHasValue(date,'2112-12-12');
		cfFieldSetValue(date,'2111-11-11');

		cfFieldCheckValue(hideOthers, 'Yes');
		cfFieldDoesNotExist(date);
		cfFieldCheckValue(hideOthers, 'No');
		cfFieldHasValue(date,'2111-11-11');
	});

});