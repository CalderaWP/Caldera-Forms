import {
    visitPluginPage,
    login,
} from '../support/util';

/**
 * Can we create a new form?
 */
describe('Create a form', () => {
    beforeEach(() => {
        visitPluginPage('caldera-forms')
    });
    before(() => login());
    it('Creates a form', () => {
        cy.get('.cf-new-form-button').click();
        cy.get('form#new_form_baldrickModal').should('be.visible');
        cy.get('.cf-form-template').last().click();
        cy.get('.new-form-name').type('Test Form');
        cy.get('.cf-create-form-button').click();
        cy.get('.caldera-editor-header-nav li.caldera-element-type-label').should('be.visible');
    });
});