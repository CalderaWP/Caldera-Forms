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
	cfFieldCalcFieldValueIs,
	cfFieldWrapperHasRangeSliderInIt,
	cfStarFieldValueIs,
	cfSetStar,
	cfFieldGetWrapper,
	cfFieldClickButton
} from '../support/util';
import { addQueryArgs } from '@wordpress/url';



describe('Conditionals test hide conditionals of fancy fields on page 2', () => {
	const utmTerms = {
		term:'t1',
		campaign: 'c21',
		medium: 'm22',
		content: 'cc34'
	};
	let queryArgs = {};
	Object.keys(utmTerms).forEach( term => queryArgs[`utm_${term}`] = utmTerms[term]);
	beforeEach(() => {
		visitPage(addQueryArgs('conditionals-fancy-multi-page',queryArgs));
	});

	const formId = 'CF5bcb67b899a38';

	const hideAllCheckbox ='fld_6026662';
	const range = 'fld_8820637';
	const star = 'fld_8552574';
	const utm = 'fld_8899889';
	const consent = 'fld_8097741';

	const nextPageButton = 'fld_2586230';
	const prevPageButton = 'fld_7066233';

	const testUtmTerms = (fieldId) => {
		Object.keys(utmTerms).forEach(term => {
			cy.get(`#${fieldId}_utm_${term}_1`).should('have.value', utmTerms[term]);
		});
	};

	function testAllValues() {
		testUtmTerms(utm);
		cfFieldHasValue(range, '10');
		cfFieldWrapperHasRangeSliderInIt(range);
		cfStarFieldValueIs(star, '5', '7');
		cfFieldGetWrapper(consent).find('a.caldera-forms-consent-field-linked_text').contains('Privacy page');
		cfFieldGetWrapper(consent).find('p.caldera-forms-consent-field-agreement').contains('Agreement');
	}



	it( 'Has the correct initial load', () => {

		cfFieldClickButton(nextPageButton);
		testAllValues();
	});

	it( 'After hide and unhide, looks the same', () => {

		cfFieldCheckValue(hideAllCheckbox,'Yes' );
		cfFieldCheckValue(hideAllCheckbox,'No' );
		cfFieldClickButton(nextPageButton);
		testAllValues();

		cfFieldClickButton(prevPageButton);
		cfFieldCheckValue(hideAllCheckbox,'Yes' );
		cfFieldCheckValue(hideAllCheckbox,'No' );
		cfFieldClickButton(nextPageButton);
		testAllValues();

	});

});