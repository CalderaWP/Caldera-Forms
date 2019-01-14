import {
	visitPage,
	cfAlertHasText,
	cfFieldClickButton
} from '../support/util';


describe('Name of test', () => {
	beforeEach(() => {
		visitPage('cf2-file-not-required');
	});

	const formId = 'CF5bee2f0d5a1d6';
	const button = 'fld_3314268';

	it( 'When submitted with non-required file fields that are left empty, it submits', () => {
		cfFieldClickButton(button);
		cfAlertHasText( formId,'GOOD');
	});


});