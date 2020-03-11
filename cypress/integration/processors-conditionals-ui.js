import {
    visitPluginPage,
    login,
    createForm,
    cfGoToProcessorsTab,
    cfGoToLayoutTab,
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

function addConditionalGroup() {
    cy.get('.add-conditional-group').click();
}

function expectNumberOfConditionalGroups(groups) {
    cy.get('.caldera-condition-group').should('have.length', groups);
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
    it('We can add new conditional groups to a processor', () => {
        const formName = makeFormName('add new conditional');
        createForm(formName, false);
        goToFirstProcessorConditionals();
        //There is the conditional type selector
        cy.get('.caldera-conditionals-usetype:visible').should('have.length', 1);
        cy.get('.caldera-conditionals-usetype:visible').select( 'Use');

        //Add a group
        addConditionalGroup();
        expectNumberOfConditionalGroups(1);
        cy.get( '.caldera-conditional-field-set' ).first().should( 'have.length', 1 );
        //Trigger one change to get options to load
        cy.get( '.caldera-conditional-field-set' ).select( '' );
        //Change to first name NOT lobster
        cy.get( '.caldera-conditional-field-set').first().select( 'First Name [first_name]');
        cy.get( '.compare-type').first().select( 'isnot');
        cy.get( '.caldera-conditional-value-field').first().type( 'Lobster' );

        //Add another group
        addConditionalGroup();
        expectNumberOfConditionalGroups(2);
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
        const formName = makeFormName('remove conditional groups');
        createForm(formName, false);
        goToFirstProcessorConditionals();
        cy.get('.caldera-conditionals-usetype:visible').select( 'Use');
        //Add a group
        addConditionalGroup();
        expectNumberOfConditionalGroups(1);

        //Remove group
        cy.get( '.remove-conditional-line').first().click();
        expectNumberOfConditionalGroups(0);

        //Add two and remove one
        addConditionalGroup();
        addConditionalGroup();
        expectNumberOfConditionalGroups(2);
        cy.get( '.remove-conditional-line').last().click();
        expectNumberOfConditionalGroups(1);

    });

    it('When field type changes to field type with options, its options are used as conditional value.', () => {
        const formName = makeFormName('field type changes');
        createForm(formName, false);
        goToFirstProcessorConditionals();
        cy.get('.caldera-conditionals-usetype:visible').select( 'Use');
        addConditionalGroup();
        cy.get( '.caldera-conditional-field-set' ).select( '' );
        cy.get( '.caldera-conditional-field-set').first().select( 'header [header]');
        cy.get( '.compare-type').first().select( 'isnot');
        cy.get( '.caldera-conditional-value-field').first().type( 'Lobster' );

        //Change first field type to radio
        cfGoToLayoutTab();
        cy.get( '.caldera-select-field-type').first().select( "Radio");
        //Add two options
        cy.get( '.add-toggle-option.add-option').click();
        cy.get( '.add-toggle-option.add-option').click();
        cy.get( '.toggle_label_field:visible').should( 'have.length', 2);
        cy.get( '.toggle_label_field').first().type( 'Arms');
        cy.get( '.toggle_label_field').last().type( 'Pants');

        cfGoToProcessorsTab();
        cy.get('.active-processors-list').should('have.length', 1);
        //First two settings should be the same.
        cy.get( '.caldera-conditional-field-set').first().should( 'have.value','fld_29462');
        cy.get( '.compare-type').first().should( 'have.value', 'isnot');
        //It SHOULD have lost value-field, can we change it?
        cy.get( '.caldera-conditional-value-field').first().should( 'have.value', '' );
        cy.get( '.caldera-conditional-value-field').select( 'Pants' );

        //Save, reload and check saved values load correctly
        saveFormAndReload();
        goToFirstProcessorConditionals();
        cy.get( '.caldera-conditional-field-set').first().should( 'have.value','fld_29462');
        cy.get( '.compare-type').first().should( 'have.value', 'isnot');
        cy.get( '.caldera-conditional-value-field').first().should( 'have.value', 'Pants' );

    });

    it('When options for a field used as conditional value change, conditional editor reflects change', () => {
        const formName = makeFormName('Options change');
        createForm(formName, false);
        cy.get( '.caldera-select-field-type').first().select( "Checkbox");
        cy.get( '.add-toggle-option.add-option').click();
        cy.get( '.toggle_label_field').first().type( 'Jumpy');
        goToFirstProcessorConditionals();
        cy.get('.caldera-conditionals-usetype:visible').select( 'Use');
        addConditionalGroup();
        cy.get( '.caldera-conditional-field-set' ).select( '' );
        cy.get( '.caldera-conditional-field-set').first().select( 'header [header]');
        cy.get( '.compare-type').first().select( 'isnot');
        cy.get( '.caldera-conditional-value-field').select( 'Jumpy' );
        cy.get( '.caldera-conditional-value-field').find(':selected').contains( 'Jumpy' );

        //add another option
        cfGoToLayoutTab();
        cy.get( '.add-toggle-option.add-option').click();
        cy.get( '.toggle_label_field').last().type( 'Legs');

        //Can we use new option?
        goToFirstProcessorConditionals();
        cy.get( '.caldera-conditional-value-field').find(':selected').contains( 'Jumpy' );
        cy.get( '.caldera-conditional-value-field').select( 'Legs' );
        cy.get( '.caldera-conditional-value-field').find(':selected').contains( 'Legs' );

        //Save, reload and check saved values load correctly
        saveFormAndReload();
        goToFirstProcessorConditionals();
        cy.get( '.caldera-conditional-field-set').first().should( 'have.value','fld_29462');
        cy.get( '.caldera-conditional-value-field').find(':selected').contains( 'Legs' );
        
    });


});