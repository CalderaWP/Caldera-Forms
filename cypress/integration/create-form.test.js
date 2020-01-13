import {
    visitPluginPage,
    login,
    createForm,
} from '../support/util';


/**
 * Can we create a new form?
 */
describe('Create a form', () => {
    beforeEach(() => {
        visitPluginPage('caldera-forms')
    });
    before(() => login());

    it('Opens new form modal', () => {
        cy.get('.cf-new-form-button').click();
        cy.get('form#new_form_baldrickModal').should('be.visible');
    });

    it('Creates a blank form', () => {
        createForm('Some New Form');
        cy.get('.caldera-editor-header-nav li.caldera-element-type-label').should('be.visible');
        cy.get( '.layout-form-field' ).should('have.length', 0);
    });

    it('Creates a From Contact Form', () => {
        createForm('Some Contact Form', false);
        cy.get('.caldera-editor-header-nav li.caldera-element-type-label').should('be.visible');
        cy.get( '.layout-form-field' ).should('have.length', 7);
    });
});