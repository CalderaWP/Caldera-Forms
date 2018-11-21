import {
	visitPage,
	cfAlertHasText,
	cfFieldClickButton
} from '../support/util';


describe('Name of test', () => {
	beforeEach(() => {
		visitPage('cf2-file-2-not-required');
	});

	const formId = 'CF5bee3162ab0b2';
	const button = 'fld_7332208';

	it( 'When submitted with two non-required file field, left empty it submits', () => {
		cfFieldClickButton(button);
		cfAlertHasText( formId,'GOOD');
	});


});