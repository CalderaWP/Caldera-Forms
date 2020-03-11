import {
    visitPluginPage,
    login,
    createForm,
    cfGoToProcessorsTab,
    saveFormAndReload
} from '../support/util';


/**
 * Go to the conditional editor for first processor in form
 *
 * @since 1.9.0
 */
function goToFirstProcessorConditionals() {
    cfGoToProcessorsTab();
    cy.get('.active-processors-list').should('have.length', 1);
    cy.get('.caldera-processor-nav').children().first().click();
    cy.get('.toggle_option_tab').children().last().click();
}

describe('Processor conditional logic', () => {
    let formName;

    const makeFormName = (name) => name + +Math.random().toString(36).substring(7);
    beforeEach(() => {
        visitPluginPage('caldera-forms');
    });
    before(() => login());


    /**
     Are we making sure that?
     All of the above changes can be saved, the form editor reloaded, and the settings are the same.
     */
    it.only('We can add new conditional groups to a processor', () => {
        const formName = makeFormName('We can add new conditional groups to a processor');
        createForm(formName, false);
        goToFirstProcessorConditionals();
        //There is the conditional type selector
        cy.get('.caldera-conditionals-usetype:visible').should('have.length', 1);
        cy.get('.caldera-conditionals-usetype:visible').select( 'Use');

        //Add a group
        cy.get( '.add-conditional-group').click();
        cy.get( '.caldera-condition-group' ).should('have.length', 1);
        cy.get( '.caldera-conditional-field-set' ).first().should( 'have.length', 1 );
        //Trigger one change to get options to load
        cy.get( '.caldera-conditional-field-set' ).select( '' );
        //Change to first name NOT lobster
        cy.get( '.caldera-conditional-field-set').first().select( 'First Name [first_name]');
        cy.get( '.compare-type').first().select( 'isnot');
        cy.get( '.caldera-conditional-value-field').first().type( 'Lobster' );

        //Add another group
        cy.get( '.add-conditional-group').click();
        cy.get( '.caldera-condition-group' ).should('have.length', 2);
        //Has settings for two field values
        cy.get( '.caldera-conditional-field-set').should( 'have.length', 2 );
        //Set second to last name endswith Fish
        cy.get( '.caldera-conditional-field-set' ).last().select( '' );
        cy.get( '.caldera-conditional-field-set').last().select( 'Last Name [last_name]');
        cy.get( '.compare-type').last().select( 'endswith');
        cy.get( '.caldera-conditional-value-field').last().type( 'Fish' );

        //Save, reload and check saved values load correctly
        saveFormAndReload();
        goToFirstProcessorConditionals();
        cy.get( '.caldera-conditional-field-set').first().should( 'have.value','fld_8768091');
        cy.get( '.compare-type').first().should( 'have.value', 'isnot');
        cy.get( '.caldera-conditional-value-field').first().should( 'have.value', 'Lobster' );

        cy.get( '.caldera-conditional-field-set').last().should( 'have.value','fld_9970286');
        cy.get( '.compare-type').last().should( 'have.value','endswith');
        cy.get( '.caldera-conditional-value-field').last().should( 'have.value', 'Fish' );

    });

    it('We can remove conditional groups from a processor.', () => {
        const formName = makeFormName('We can remove conditional groups from a processor.');
        createForm(formName, false);
        cfGoToProcessorsTab();
        cy.get('.active-processors-list').should('have.length', 1);
    });

    it('When a processor has conditionals, the saved settings load in the editor correctly.', () => {
        const formName = makeFormName('When a processor has conditionals, the saved settings load in the editor correctly.');
        createForm(formName, false);
        cfGoToProcessorsTab();
        cy.get('.active-processors-list').should('have.length', 1);
    });

    it('When field type changes to field type with options, its options are used as conditional value.', () => {
        const formName = makeFormName('When field type changes to field type with options, its options are used as conditional value.');
        createForm(formName, false);
        cfGoToProcessorsTab();
        cy.get('.active-processors-list').should('have.length', 1);
    });

    it('When options for a field used as conditional value change, conditional editor reflects change', () => {
        const formName = makeFormName('When options for a field used as conditional value change, conditional editor reflects change');
        createForm(formName, false);
        cfGoToProcessorsTab();
        cy.get('.active-processors-list').should('have.length', 1);
    });


});