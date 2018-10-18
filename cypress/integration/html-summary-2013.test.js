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
	cfFieldCalcFieldValueIs, getCfFieldSelector,
	cfFieldSummaryContains,
	cfFieldSummaryContainsValues
} from '../support/util';


describe('Name of test', () => {
	beforeEach(() => {
		visitPage('2013-html-summary-create1-5-2-1-dev1-5-7');
	});

	const formId = 'CF59e13e034fc2d';
	const singleLine = 'fld_3068283';
	const dropdown = 'fld_4911252';
	const number1 = 'fld_7259588';
	const number2 = 'fld_7722492';

	const summaryField = 'fld_5594906';


	it( 'Updates summary', () => {
		const selector = `#html-content-${summaryField}_1`;
		cfFieldSummaryContains(`#html-content-${summaryField}_1`, '2' );
		cfFieldSetValue(singleLine, 'Hi Roy' );
		cfFieldSummaryContainsValues(selector, ['2', 'Hi Roy' ]);

		cfFieldSelectValue(dropdown, 'One' );
		cfFieldSummaryContainsValues(selector, ['2', 'Hi Roy', 'One' ]);

		cfFieldSelectValue(dropdown, 'Two' );
		cfFieldSummaryContainsValues(selector, ['2', 'Hi Roy', 'Two' ]);

		cfFieldSetValue(number1, '55' );
		cfFieldSummaryContainsValues(selector, ['2', 'Hi Roy', 'Two', '55' ]);

		cfFieldSetValue(number2, '5' );
		cfFieldSummaryContainsValues(selector, ['5', 'Hi Roy', 'Two', '55' ]);



	});

});