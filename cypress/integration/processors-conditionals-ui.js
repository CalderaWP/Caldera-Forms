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
        cy.get('.caldera-conditionals-usetype:visible').select( 'Use');
        addConditionalGroup();

        cy.get( '.condition-line-field').first().select( 'header [header]');
        cy.get( '.condition-line-compare').first().select( 'isnot');
        cy.get( '.caldera-conditional-field-value-input').first().type( 'Lobster' );

        //Add another group
        addConditionalGroup();
        expectNumberOfConditionalGroups(2);
        //Has settings for two field values
        cy.get( '.condition-line-field').should( 'have.length', 2 );
        cy.get( '.condition-line-field').last().select( 'Last Name [last_name]');
        cy.get( '.condition-line-compare').last().select( 'endswith');
        cy.get( '.caldera-conditional-field-value-input').last().type( 'Fish' );


        //Save, reload and check saved values load correctly
        saveFormAndReload();
        goToFirstProcessorConditionals();
        cy.get( '.condition-line-field').first().should( 'have.value','fld_29462');
        cy.get( '.condition-line-compare').first().should( 'have.value', 'isnot');
        cy.get( '.caldera-conditional-field-value-input').first().should( 'have.value', 'Lobster' );

        cy.get( '.condition-line-field').last().should( 'have.value','fld_9970286');
        cy.get( '.condition-line-compare').last().should( 'have.value','endswith');
        cy.get( '.caldera-conditional-field-value-input').last().should( 'have.value', 'Fish' );

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
        cy.get( '.condition-line-remove').first().click();
        expectNumberOfConditionalGroups(0);

        //Add two and remove one
        addConditionalGroup();
        addConditionalGroup();
        expectNumberOfConditionalGroups(2);
        cy.get( '.condition-line-remove').last().click();
        expectNumberOfConditionalGroups(1);

    });

    it('When field type changes to field type with options, its options are used as conditional value.', () => {
        const formName = makeFormName('field type changes');
        createForm(formName, false);
        goToFirstProcessorConditionals();
        cy.get('.caldera-conditionals-usetype:visible').select( 'Use');
        addConditionalGroup();

        cy.get( '.condition-line-field').first().select( 'header [header]');
        cy.get( '.condition-line-compare').first().select( 'isnot');
        cy.get( '.caldera-conditional-field-value-input').first().type( 'Lobster' );

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
        cy.get( '.condition-line-field').first().should( 'have.value','fld_29462');
        cy.get( '.condition-line-compare').first().should( 'have.value', 'isnot');
        //It SHOULD have lost value-field, can we change it?
        cy.get( '.caldera-conditional-field-value select').first().should( 'have.value', '' );
        cy.get( '.caldera-conditional-field-value select').select( 'Pants' );
        cy.get( '.caldera-conditional-field-value select option:selected').should( 'have.text', 'Pants' );

        //Save, reload and check saved values load correctly
        saveFormAndReload();
        goToFirstProcessorConditionals();
        cy.get( '.condition-line-field').first().should( 'have.value','fld_29462');
        cy.get( '.condition-line-compare').first().should( 'have.value', 'isnot');
        cy.get( '.caldera-conditional-field-value select option:selected').should( 'have.text', 'Pants' );

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
        cy.get( '.condition-line-field').first().select( 'header [header]');
        cy.get( '.condition-line-compare').first().select( 'isnot');
        cy.get( '.caldera-conditional-field-value select').select( 'Jumpy' );
        cy.get( '.caldera-conditional-field-value select').find(':selected').contains( 'Jumpy' );

        //add another option
        cfGoToLayoutTab();
        cy.get( '.add-toggle-option.add-option').click();
        cy.get( '.toggle_label_field').last().type( 'Legs');

        //Can we use new option?
        goToFirstProcessorConditionals();
        cy.get( '.caldera-conditional-field-value select').find(':selected').contains( 'Jumpy' );
        cy.get( '.caldera-conditional-field-value select').select( 'Legs' );
        cy.get( '.caldera-conditional-field-value select').find(':selected').contains( 'Legs' );

        //Save, reload and check saved values load correctly
        saveFormAndReload();
        goToFirstProcessorConditionals();
        cy.get( '.condition-line-field').first().should( 'have.value','fld_29462');
        cy.get( '.caldera-conditional-field-value select').find(':selected').contains( 'Legs' );

    });


});