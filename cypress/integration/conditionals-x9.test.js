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
	cfFieldCalcFieldValueIs, cfFieldClickButton, cfFieldIsNotVisible
} from '../support/util';


describe('X9 CONDITIONALS SHOW CREATE@1.5.5', () => {
	beforeEach(() => {
		visitPage('x9-conditionals-show-create1-5-5');
	});

	const formId = 'CF59f542781fc44';

	const text1 = 'fld_2960467';
	const number1 = 'fld_7869772';
	const text2 = 'fld_1932889';
	const number2 = 'fld_8556025';

	const showTextCheckbox = 'fld_5262727';
	const showNumbersRadio = 'fld_2047794';

	const nextButton = 'fld_6983702';
	const prevButton = 'fld_5419649';

	it( 'Hides and shows based on a radio across pages', () => {
		cfFieldDoesNotExist(number1);
		cfFieldDoesNotExist(number2);

		cfFieldClickButton(nextButton );
		cfFieldCheckValue(showNumbersRadio, 'Yes');
		cfFieldIsVisible(number2);

		cfFieldClickButton(prevButton);
		cfFieldIsVisible(number1);
	});

	it( 'Hides and shows based on a checkbox across pages', () => {
		cfFieldIsVisible(text1);
		cfFieldIsNotVisible(text2);

		cfFieldUnCheckValue(showTextCheckbox, 'Yes');
		cfFieldDoesNotExist(text1);

		cfFieldClickButton(nextButton);
		cfFieldDoesNotExist(text2);

	});

});