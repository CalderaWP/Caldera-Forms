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

    it( 'Opens new form modal', () => {
        cy.get('.cf-new-form-button').click();
        cy.get('form#new_form_baldrickModal').should('be.visible');
    });

    it('Creates a form', () => {
       createForm('Some New Form');
        cy.get('.caldera-editor-header-nav li.caldera-element-type-label').should('be.visible');
    });
});