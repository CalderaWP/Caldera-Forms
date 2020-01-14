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


/**
 * Can we create a clone a form?
 */
describe.only('Clone a form', () => {
    beforeEach(() => {
        visitPluginPage('caldera-forms')
    });
    before(() => login());

    it( 'Shows clone option when hovered', () => {
        cy.get( '#form_row_CF59ce6f1747efb').trigger('mouseover');
        cy.get( '.clone-form-CF59ce6f1747efb' ).should( 'be.visible')

        cy.get( '#form_row_CF5e1dd76c0a484').trigger('mouseover');
        cy.get( '.clone-form-CF5e1dd76c0a484' ).should( 'be.visible');
    });
    it( 'Clones a form', () => {
        cy.get( '#form_row_CF5e1dd6a3eec18').trigger('mouseover');
        cy.get( '.clone-form-CF5e1dd6a3eec18' ).click({ force: true });
        cy.get( '#new_clone_baldrickModalLable' ).should( 'be.visible' );
        cy.get( '#cf-clone-form-name' ).clear().type( 'Radio Clone' );
        cy.get( '#new_clone_baldrickModalFooter button' ).click();
        cy.get('.caldera-editor-header-nav li.caldera-element-type-label').should('be.visible');
        cy.get('.caldera-editor-header-nav li.caldera-element-type-label').contains( 'Radio Clone');
        cy.get( '.layout-form-field' ).should('have.length', 1);

    });

});