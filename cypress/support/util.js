/**
 * Get site details
 * @type {any}
 */
export const site = Cypress.env('wp_site');
export const {url, user, pass} = site;

/**
 * Login to the site
 *
 * @since unknown
 */
export const login = () => {
	cy.visit(url + '/wp-login.php');
	cy.wait(250);
	cy.get('#user_login').clear().type(user);
	cy.get('#user_pass').clear().type(pass);
	cy.get('#wp-submit').click();
};

/**
 * Activate a plugin
 * @param {string} pluginSlug
 */
export const activatePlugin = (pluginSlug) => {
	cy.visit(url + '/wp-admin/plugins.php');
	const selector = 'tr[data-slug="' + pluginSlug + '"] .activate a';
	if (Cypress.$(selector).length > 0) {
		cy.get(selector).click();
	};
};

function pluginUrl(pluginSlug) {
	return `${url}/wp-admin/admin.php?page=${pluginSlug}`;
}

/**
 * Go to a plugin page
 * @param {string} pluginSlug
 */
export const visitPluginPage = (pluginSlug) => {
	cy.visit(pluginUrl(pluginSlug));
};
export const visitFormEditor = (formId) => {
	cy.visit(`${pluginUrl('caldera-forms')}&edit=${formId}`)
}
export const visitPage = (pageSlug) => {
	cy.visit(`${url}/${pageSlug}`);
};

/**
 * Get the selector for a Caldera Forms Form by ID
 *
 * @param {String} formId CF Form ID, not ID attribute
 * @return {String}
 */
export const getCfFormSelector = (formId) => {
	return `[data-cf-form-id="${formId}"]`;
};

/**
 * Get a Caldera Forms forms by ID
 *
 * @param {String} formId CF Form ID, not ID attribute
 * @return {Cypress.Chainable<JQuery<HTMLElement>>}
 */
export const getCfForm = (formId) => {
	return cy.get(getCfFormSelector(formId));
};

/**
 * Get a Caldera Forms field by ID
 *
 * @param {String} fieldId CF Field ID, not ID attribute
 * @return {Cypress.Chainable<JQuery<HTMLElement>>}
 */
export const getCfField = (fieldId) => {
	return cy.get(getCfFieldSelector(fieldId));
};

/**
 * Get the selector for a Caldera Forms field by ID
 *
 * @param {String} fieldId CF Field ID, not ID attribute
 * @return {String}
 */
export const getCfFieldSelector = (fieldId) => {
	return `[data-field="${fieldId}"]`;
};

/**
 * Clear value of Caldera Forms field by ID
 *
 * @param {String} fieldId CF Field ID, not ID attribute
 * @return {Cypress.Chainable<JQuery<HTMLElement>>}
 */
export const clearCfField = (fieldId) => {
	return getCfField(fieldId).clear();
};

/**
 * Check if Caldera Forms field is visible by ID
 *
 * Use: Check if field was unhidden by conditional logic.

 * @param {String} fieldId CF Field ID, not ID attribute
 * @return {Cypress.Chainable<JQuery<HTMLElement>>}
 */
export const cfFieldIsVisible = (fieldId) => {
	return getCfField(fieldId).should('be.visible');
};

/**
 * Check if Caldera Forms form contains a type of field
 *

 * @param {String} formId CF FormID, not ID attribute
 * @param {String} type Type of field we want to check
 * @return {Cypress.Chainable<JQuery<HTMLElement>>}
 */
export const cfFormHasFieldType = (formId, type) => {
	return getCfForm(formId).find( `[type=${type}]` );
};

/**
 * Check if Caldera Forms field is NOT visible by ID
 *
 * Use: Check if field is on a different page AND not hidden by conditional logic

 * @param {String} fieldId CF Field ID, not ID attribute
 * @return {Cypress.Chainable<JQuery<HTMLElement>>}
 */
export const cfFieldIsNotVisible = (fieldId) => {
	return getCfField(fieldId).not('be.visible');
};

/**v
 * Check if Caldera Forms field does NOT exist on DOM by field ID
 *
 * Use: Check if field was hidden by conditional logic.
 *
 * @param {String} fieldId CF Field ID, not ID attribute
 * @return {Cypress.Chainable<JQuery<HTMLElement>>}
 */
export const cfFieldDoesNotExist = (fieldId) => {
	return getCfField(fieldId).should('not.exist');
};

/**
 * Check if a Caldera Forms field exists and has a value, by field ID
 * @param {String} fieldId CF Field ID, not ID attribute
 * @param {String|Number} value Value to assert. Evaluated as string (numbers will be cast to string)
 * @return {Cypress.Chainable<JQuery<HTMLElement>>}
 */
export const cfFieldHasValue = (fieldId, value) => {
	if ('number' === typeof  value) {
		value = value.toString(10);
	}
	return getCfField(fieldId).should('have.value', value);
};

/**
 * Select an option of a Caldera Forms select field, by field ID
 * @param {String} fieldId CF Field ID, not ID attribute
 * @param {String} newValue Value to set
 * @return {Cypress.Chainable<JQuery<HTMLElement>>}
 */
export const cfFieldSelectValue = (fieldId, newValue) => {
	return getCfField(fieldId).select(newValue);
};


export const cfFieldIsValueSelected = (fieldId,value) => {
	return cfFieldOptionIsSelected(fieldId,value);
};


/**
 * Set new value for Caldera Forms text-like field, by field ID
 *
 * Note: clears field first
 *
 * @param {String} fieldId CF Field ID, not ID attribute
 * @param {String} newValue Value to set
 * @return {Cypress.Chainable<JQuery<HTMLElement>>}
 */
export const cfFieldSetValue = (fieldId, newValue) => {
	return clearCfField(fieldId).type(newValue);
};

/**
 * Check value for Caldera Forms radio/checkbox, by field ID
 **
 * @param {String} fieldId CF Field ID, not ID attribute
 * @param {String} valueToCheck Value to set
 * @return {Cypress.Chainable<JQuery<HTMLElement>>}
 */
export const cfFieldCheckValue = (fieldId, valueToCheck) => {
	return getCfField(fieldId).check(valueToCheck);
};

/**
 * Check all values for Caldera Forms radio/checkbox, by field ID
 * @param {String} fieldId CF Field ID, not ID attribute
 * @return {Cypress.Chainable<JQuery<HTMLElement>>}
 */
export const cfFieldCheckAllValues = (fieldId) => {
	return getCfField(fieldId).check();
};

/**
 * UnCheck value for Caldera Forms radio/checkbox, by field ID
 *
 * Note: clears field first
 *
 * @param {String} fieldId CF Field ID, not ID attribute
 * @param {String} valueToCheck Value to set
 * @return {Cypress.Chainable<JQuery<HTMLElement>>}
 */
export const cfFieldUnCheckValue = (fieldId, valueToCheck) => {
	return getCfField(fieldId).uncheck(valueToCheck);
};

/**
 * Check if a Caldera Forms field is disabled, by field ID
 *
 * Use: Check if a field is disabled by conditional
 *
 * @param {String} fieldId CF Field ID, not ID attribute
 * @return {Cypress.Chainable<JQuery<HTMLElement>>}
 */
export const cfFieldIsDisabled = (fieldId) => {
	return getCfField(fieldId).should('be.disabled');
};

/**
 * Check if a Caldera Forms field is NOT disabled, by field ID
 *
 * Use: Check if a field is disabled by conditional
 *
 * @param {String} fieldId CF Field ID, not ID attribute
 * @return {Cypress.Chainable<JQuery<HTMLElement>>}
 */
export const cfFieldIsNotDisabled = (fieldId) => {
	return getCfField(fieldId).not('be.disabled');
};

/**
 * Get the field ID attribute for a Caldera Forms field by field ID
 *
 * @param {String} fieldId CF Field ID, not ID attribute
 * @param {Number} formCount Optional. Form count, default is 1
 * @return {string}
 */
export const getCfFieldIdAttr = (fieldId, formCount = 1) => {
	return `${fieldId}_${formCount}`;
};

/**
 * Get the field ID attribute for a Caldera Form by form ID
 *
 * @param {String} formId CF Form ID, not ID attribute
 * @param {Number} formCount Optional. Form count, default is 1
 * @return {string}
 */
export const getCfFormIdAttr = (formId, formCount = 1) => {
	return `${formId}_${formCount}`;
};

/**
 *
 * @param {String} fieldId CF Field ID, not ID attribute
 * @param value
 */
export const cfFieldIsChecked = (fieldId, value ) => {
	expect(getCfField(fieldId)).not.to.be.checked
};

/**
 * Get a checkbox option for a Caldera Forms checkbox field, by field Id and option value.
 * @param {String} fieldId CF Field ID, not ID attribute
 * @param optionValue
 * @return {Cypress.Chainable<JQuery<HTMLElement>>}
 */
export const getCfCheckboxOption =(fieldId, optionValue) => {
	return cy.get(`input[data-field="${fieldId}"][value="${optionValue}"]`);
};

/**
 *
 * @param {String} fieldId CF Field ID, not ID attribute
 * @param optionValue
 * @return {Cypress.Chainable<JQuery<HTMLElement>>}
 */
export const cfFieldOptionIsChecked = (fieldId, optionValue ) => {
	return getCfCheckboxOption(fieldId,optionValue).should('be.checked');
};

export const cfFieldOptionIsNotChecked = (fieldId, optionValue ) => {
	return getCfCheckboxOption(fieldId,optionValue).not('be.checked');
};

/**
 *
 * @param {String} fieldId CF Field ID, not ID attribute
 * @param value
 * @return {Cypress.Chainable<JQuery<HTMLElement>>}
 */
export const cfFieldOptionIsSelected = (fieldId, value ) => {

	return getCfField(fieldId).should('have.value', value);
};

/**
 *
 * @param {String} fieldId CF Field ID, not ID attribute
 * @param value
 * @return {Cypress.Chainable<JQuery<HTMLElement>>}
 */
export const cfFieldCalcFieldValueIs = (fieldId, value) => {
	return 	cy.get( `[data-calc-field="${fieldId}`).should('have.value', value);
};

/**
 * Check that a summary field contains a value
 *
 * @param {String} fieldSelector Selector for field
 * @param {mixed} contains Value to check for
 * @return {Cypress.Chainable<JQuery<HTMLElement>>}
 */
export const cfFieldSummaryContains = (fieldSelector,contains) => {
	return cy
		.get( fieldSelector )
		.find( '.caldera-forms-summary-value' )
		.contains(contains);
};

/**
 * Check that a summary field contains values
 *
 * @param {String} fieldSelector Selector for field
 * @param {array} containsValues Values to check for
 * @param fieldSelector
 * @param containsValues
 */
export const cfFieldSummaryContainsValues = (fieldSelector,containsValues) => {
	containsValues.forEach( value => {
		cfFieldSummaryContains(fieldSelector,value);
	})
};

/**
 * Click a button by field Id
 *
 * @param {String} fieldId CF Field ID, not ID attribute
 * @return {Cypress.Chainable<JQuery<HTMLElement>>}
 */
export const cfFieldClickButton = (fieldId) => {
	return cy.get(`.btn${getCfFieldSelector(fieldId)}`).click();
};

export const cfFieldGetWrapper = (fieldId ) => {
	return cy.get(`div[data-field-wrapper="${fieldId}"]` );
};

export const cfAlertHasText = (formId, text = 'Form has been successfully submitted. Thank you.' ) => {
	return cy.get( `.caldera-grid[data-cf-form-id="${formId}"] .alert` ).contains(text );
};

/**
 * Test how many options a select field should have
 *
 * @param {String} fieldId CF Field ID, not ID attribute
 * @param {number} number Number of options select field should have
 * @return {Chai.Assertion}
 */
export const cfFieldHasOptions = (fieldId, number ) => {
	return expect(Cypress.$(`${getCfFieldSelector(fieldId)}`).find( 'option' ).length ).equals(number);
};

export const cfEditorGetFieldPreview = (fieldId) => {
	return cy.get( 'div[data-config="fld_8586141"]' );
};

export const cfEditorIsFieldPreviewVisible = (fieldId ) => {
	return cfEditorGetFieldPreview(fieldId).should('be.visible');
};

export const cfEditorIsFieldPreviewNotVisible = (fieldId ) => {
	return cfEditorGetFieldPreview(fieldId).not('be.visible');
};
export const cfAddProcessor = (processorType) =>{
	cy.get( '.new-processor-button' ).click();
	cy.get(`.add-new-processor[data-type="${processorType}"]`).click();
}

export const cfGoToProcessorsTab=() => {
	cy.get('#tab_processors a').click();
};

/**
 * Check that a range slider field exists and has the extra stuff the jQuery plugin adds
 * @param fieldId
 */
export const cfFieldWrapperHasRangeSliderInIt = (fieldId) => {

	 cfFieldGetWrapper(fieldId).children().find( '.rangeslider__fill' );
	 cfFieldGetWrapper(fieldId).children().find( '.rangeslider__handle' );

};

/**
 *
 * @param fieldId
 * @param {Number}starValue Which star to click on
 */
export const cfSetStar = (fieldId,starValue) =>{
	cfFieldGetWrapper(fieldId).find( `f.raty-star-on[title="${starValue}"]` ).trigger( 'click' );
};


export const cfStarFieldValueIs = (fieldId, starValue,maxStars)=> {
	cfFieldHasValue(fieldId,starValue);
	for(let i=1; i <= maxStars; i++ ){
		const className = i <= starValue ? 'raty-star-on' : 'raty-star-off';
		cfFieldGetWrapper(fieldId).find( `f.${className}[title="${i}"]` ).should( 'have.class',className );
	};
};

export const cfDropMultipleFiles = (fieldId, filesPaths, filesTypes)=> {

	let dropEvent = [];
	filesPaths.forEach( file => {

		//Set File Type
		const fileExt = file.substr(file.length - 3);
		const fileType = '';
		/*if( fileExt === 'jpg' ){
			fileType = filesTypes.jpg;
		} else if ( fileExt === 'png' ) {
			fileType = filesTypes.png;
		}*/
		//Push to DropEvent array
		cy.fixture(file).then((picture) => {
			return Cypress.Blob.base64StringToBlob(picture, filesTypes.jpg).then((blob) => {
				dropEvent.push(blob);
			});
		});

	});


	return cfGetFileDropzone(fieldId).trigger('drop', dropEvent);

};

export const cfDropSingleFile = (fieldId, filesPaths, filesTypes)=> {

	const dropEvent = [];
	cy.fixture(filesPaths[0]).then((picture) => {
		return Cypress.Blob.base64StringToBlob(picture, filesTypes.jpg).then((blob) => {
			dropEvent.push(blob);
		});
	});

	//Cypress.$('div[data-field=' + fieldId + ']').find( '.cf2-field.cf2-file .cf2-dropzone' ).remove();
	return cfGetFileDropzone(fieldId).trigger('drop', dropEvent);

};

export const cfGetFileDropzone = ( fieldId => {
	return cy.get('div[data-field=' + fieldId + ']').find( '.cf2-dropzone button' );
});

/**
 * Create a form using UI
 *
 *
 * @since 1.8.0
 *
 * @param name What to name new form
 */
export const createForm = (name,blankForm = true) => {
	visitPluginPage('caldera-forms');
	cy.get('.cf-new-form-button').click();
	cy.get('form#new_form_baldrickModal').should('be.visible');
	if( blankForm ){
		cy.get('.cf-form-template').last().click();
	}else{
		cy.get('.cf-form-template').first().click();
	}

	cy.get('.new-form-name').type(name);
	cy.get('.cf-create-form-button').click();
};

/**
 * Save form and refresh page
 *
 * @since 1.8.10
 */
export const saveFormAndReload = ()  => {
	cy.get('.caldera-header-save-button').click();
	cy.reload();
};

/**
 * Variables functions
 * 
 * @since 1.8.10
 */
export const cfGoToVariablesTab = () => {
	cy.get('#tab_variables a').click();
};
export const cfAddVariable = () =>{
	cy.get('.caldera-add-variable').click();
	cy.get('.set-system-variable').type('variable_name');
	cy.get('.var-value').click();
	cy.get('.var-value').type('variable_value');
}
export const cfRemoveVariable = () => {
	cy.get( '.remove-this-variable' ).each(($el) => {
		cy.wrap($el).click()
	})
}
