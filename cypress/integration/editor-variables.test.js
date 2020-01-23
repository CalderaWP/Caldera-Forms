import {
	login,
	visitFormEditor,
    saveFormAndReload,
    cfGoToVariablesTab,
    cfAddVariable,
    cfRemoveVariable
} from '../support/util';

const formId = 'CF5e2997843a875';
const formName = 'Variables Test';

describe( 'Check variables',  () => {

    before(() => login() );
	beforeEach(() => {
		visitFormEditor( formId )
    });
    
    it( 'Create a variable', () => {

        cfGoToVariablesTab();
        cfAddVariable();
        saveFormAndReload();
        cy.get('#variable_entry_list').children().should('have.length', 1);
        cy.expect('#variable_entry_list').to.not.be.empty;
    });

    it( 'Adds the variable to magic tags list', () => {

        cy.get('#fld_29462editor').click();
        cy.get('.magic-tags-autocomplete ul li.header').contains('Variables');
        saveFormAndReload();
        cy.get('#fld_29462editor').click();
        cy.get('.magic-tags-autocomplete ul li.header').contains('Variables');
	});
    
    it( 'Remove a variable', () => {

        cfGoToVariablesTab();
        cfRemoveVariable();
        saveFormAndReload();
        cy.get('#variable_entry_list').children().should('have.length', 0);
    });

});

