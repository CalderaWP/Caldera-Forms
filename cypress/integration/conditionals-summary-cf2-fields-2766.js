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
	cfFieldCalcFieldValueIs, cfFieldSummaryContainsValues
} from '../support/util';


describe('conditionals and summary cf2 fields 2766', () => {
	beforeEach(() => {
		visitPage('conditionals-cf2-fields-2766');
	});

	const formId = 'CF5bc8e4db50622';
	const cf2Text1 = 'fld_5843941';
	const cf1Text = 'fld_6540633';
	const summary = 'fld_4065869';
	const cf2FileField = 'fld_9226671';
	const cf2Text2 = 'fld_7276122';
	const submit = 'fld_6660137';
	function checkSummary(fieldValues){
		return cfFieldSummaryContainsValues(
			'#html-content-fld_4065869_1',
			fieldValues
		)
	}
	const hideSelect = 'fld_990986';

	it( 'Has the correct initial load', () => {
		cfFieldHasValue(cf2Text1, 'new default' );
		cfFieldHasValue(cf2Text2, '' );
		cfFieldHasValue(cf1Text, 'old default' );

		checkSummary([ 'new default', 'old default'] )
	});
	it( 'Change in text cf2 text fields updates summary', () => {
		cfFieldSetValue(cf2Text2, 'Moon People');
		checkSummary([ 'new default', 'old default', 'Moon People'] );
		cfFieldSetValue(cf2Text1, 'Mars People');
		checkSummary([ 'Mars People', 'old default', 'Moon People'] );
	});

	it( 'Brings back default value for the cf2 text field after being conditionally hidden and then shown again', () => {
		cfFieldSelectValue(hideSelect, 'Yes' );
		cfFieldSelectValue(hideSelect, 'No' );
		checkSummary([ 'new default', 'old default'] );

	});

	it( 'Brings back updated value for the cf2 text field after being conditionally hidden and then shown again', () => {
		cfFieldSetValue(cf2Text1, 'Moon People');
		cfFieldSetValue(cf2Text2, 'Mars People');


		cfFieldSelectValue(hideSelect, 'Yes' );
		cfFieldSelectValue(hideSelect, 'No' );
		checkSummary([ 'Moon People', 'Mars People'] );

	});

});