import {
    visitPage,
    cfFieldSetValue,
    cfFieldClickButton,
    cfAlertHasText,
	login,
    visitPluginPage
} from '../support/util';


/**
 * Tests for main Caldera Forms page
 */
describe('Caldera Forms admin main page', () => {

	it('Submits a form', () => {
        visitPage('Test resend button');
        cfFieldSetValue('fld_8768091', 'First name value');
        cfFieldSetValue('fld_9970286', 'Last name value');
        cfFieldSetValue('fld_6009157', 'email@address.ext');
        cfFieldSetValue('fld_7683514', 'Comments / Questions value');
        cfFieldClickButton('fld_7908577');
        cy.wait(250);
		cfAlertHasText('CF5e32fc7f39dc8');
    });

	it('Resend the entry saved', () => {
        login();
        visitPluginPage('caldera-forms');
        cy.get('#form_row_CF5e32fc7f39dc8').trigger('mouseover');
        cy.get('.cf-entry-viewer-link').should('be.visible');
        cy.get('.cf-entry-viewer-link[data-Form="CF5e32fc7f39dc8"]').click({force:true});
        cy.wait(250);
        cy.get('#caldera-forms-admin-page-right .form-entries-wrap #entry_row_1 a').click({force:true});
        cy.wait(250);
        cy.get('.caldera-forms-toolbar-item.success').should('be.visible');;
        //cy.get('.caldera-forms-toolbar-item.success').contain('Message Resent');
	});

	
});