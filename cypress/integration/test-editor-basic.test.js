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
	visitPluginPage,
	login,
	visitFormEditor,
	cfEditorIsFieldPreviewVisible,
	cfEditorIsFieldPreviewNotVisible,
	cfGoToProcessorsTab, cfAddProcessor
} from '../support/util';

const formId = 'CF5bc64d633d2ea';
const formName = 'Editor Basic Test';

const text1 = 'fld_6548786';
const range2 = 'fld_8586141';



describe( 'Basic editing of form',  () => {
	before(() => login() );
	beforeEach(() => {
		visitFormEditor( formId )
	});

	it( 'Save button is primary, preview is not ', () => {
		cy.get('.caldera-header-save-button').should( 'have.class', 'button-primary');
		cy.get('.caldera-header-preview-button').not( 'have.class', 'button-primary');
	});

	it( 'Has the saved processor', () => {
		//tab_processors
		cy.get( '#tab_processors a' ).click();
		cy.get( '.active-processors-list').children().should('have.length', 1);
		cy.get( '.active-processors-list').children().first().click();
		cy.get( '.processor-form_redirect' ).should('be.visible');
	});

	it( 'Can add a processor', () => {
		cfGoToProcessorsTab();
		const processorType = 'auto_responder';
		cfAddProcessor(processorType);
		cy.get( '.active-processors-list').children().should('have.length', 2);
	});

	it( 'Can add a second condition line in a processors conditional group', () => {
		cfGoToProcessorsTab();
		cy.get('.caldera-processor-nav:first-child').click();
		cy.get('a[href="#fp_6427173_conditions_pane"]').click();
		cy.get('.caldera-editor-processor-config .caldera-conditionals-wrapper > .caldera-condition-group > .caldera-condition-lines .caldera-condition-line').should('have.length', 2);
		cy.get('.caldera-editor-processor-config .caldera-condition-group > button').first().click();
		cy.get('.caldera-editor-processor-config .caldera-conditionals-wrapper > .caldera-condition-group > .caldera-condition-lines .caldera-condition-line').should('have.length', 3);
	});

	it.skip( 'Page nav', () => {
		cy.get('button[data-name="Page 1"]').should( 'have.class', 'button-primary');
		cy.get('button[data-name="Page 2"]').not( 'have.class', 'button-primary');
		cfEditorIsFieldPreviewVisible(text1);
		cfEditorIsFieldPreviewNotVisible(range2);

		cy.get('button[data-name="Page 2"]').click();
		cy.get('button[data-name="Page 2"]').should( 'have.class', 'button-primary');
		cy.get('button[data-name="Page 1"]').not( 'have.class', 'button-primary');
		cfEditorIsFieldPreviewVisible(range2);
		cfEditorIsFieldPreviewNotVisible(text1);
	});

	it( 'Has the right name', () => {
		cy.get('.caldera-element-type-label').contains(formName);
	});

	it( 'Has the variables still saved', () => {
		cy.get( '#tab_variables a' ).click();
		cy.get('#variable_entry_list').children().should('have.length', 3);
	});

	it( 'can add a  variables', () => {
		cy.get( '#tab_variables a' ).click();
		cy.get( 'a.add-new-h2.caldera-panel-action.caldera-add-variable').click();
		cy.get('#variable_entry_list').children().should('have.length', 4);

	});


});

describe('Form exists in admin', () => {
	beforeEach(() => {
		visitPluginPage( 'caldera-forms' )
	});


	it( 'Form is in list', () => {
		cy.get( '#form_row_CF5bc64d633d2ea').contains(formName);
	});

});