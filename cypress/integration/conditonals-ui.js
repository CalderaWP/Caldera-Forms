import {
    visitPluginPage,
    login,
    createForm,
    saveFormAndReload
} from '../support/util';

/**
 * Click on tab for form layout
 *
 * @since 1.8.10
 */
const clickLayoutTab = () => {
    cy.get('#tab_layout a').click();
};

/**
 * Click on tab for conditional logic editor
 *
 * @since 1.8.10
 */
const clickConditionalsTab = () => {
    cy.get('#tab_conditions a').click();
};

/**
 * Add a conditional and set its name and type
 *
 * @since 1.8.10
 *
 * @param name
 * @param type
 */
const createConditional = (name, type) => {
    cy.get('#new-conditional').click();
    cy.get('.condition-new-group-name').should('be.visible');
    cy.get('.condition-new-group-name').type(name).blur();
    cy.get('.condition-group-type').should('be.visible');
    cy.get('.condition-group-type').select(type);
};



describe('Using fields with conditionals', () => {
    let formName;
    beforeEach(() => {
        formName = 'Contact ' + Math.random().toString(36).substring(7);
        visitPluginPage('caldera-forms');
    });
    before(() => login());

    const cloneRadioForm = () => {
        cy.get( '#form_row_CF5e1dd6a3eec18').trigger('mouseover');
        cy.get( '.clone-form-CF5e1dd6a3eec18' ).click({ force: true });
        cy.get( '#new_clone_baldrickModalLable' ).should( 'be.visible' );
        cy.get( '#cf-clone-form-name' ).clear().type( 'Radio Clone' );
        cy.get( '#new_clone_baldrickModalFooter button' ).click();
    };


    it.only('Sets field for conditional', () => {
        createForm('Sets field for conditional', false);
        cy.get( '.layout-form-field' ).should('have.length', 7);
        clickConditionalsTab();
        createConditional('c1', 'hide');
        cy.get('.condition-group-add-lines').click();
        cy.get('.condition-group-add-line').click();
        cy.get('.condition-line-field').last().select('fld_9970286');
        cy.get('.condition-line-field').first().select('fld_8768091');

        saveFormAndReload();
        clickConditionalsTab();
        cy.get('.caldera-condition-nav' ).first().find( 'a' ).click();

        cy.get('.condition-line-field').last().should('have.value', 'fld_9970286');
        cy.get('.condition-line-field').first().should('have.value','fld_8768091');
    });


    it('Changes field being edited', () => {
        createForm('Changes field being edited', false);
        cy.get('.caldera-editor-header-nav li.caldera-element-type-label').should('be.visible');

        clickConditionalsTab();
        createConditional('c1', 'hide');
        cy.get('.condition-group-add-lines').click();
        cy.get('.condition-group-add-line').click();
        cy.get('.condition-line-field').first().select('fld_8768091');


        saveFormAndReload();

        //Make sure conditionals are still set right
        clickConditionalsTab();
        cy.get('.caldera-condition-nav' ).first().find( 'a' ).click();
        cy.get('.condition-line-field').first().should('have.value', 'fld_8768091');

    });

    it('Knows the labels of fields after they update', () => {
        createForm('Knows the labels of fields after they update', false);
        clickConditionalsTab();

        //create conditional using field header
        createConditional('c1', 'hide');
        cy.get('.condition-group-add-lines').click();
        cy.get('.condition-group-add-line').click();

        cy.get('.condition-line-field').first().select('header [header]');
        cy.get('.condition-line-field').should('have.value', 'fld_29462');

        //Go to layout tab and change field label
        clickLayoutTab();
        cy.get( '#fld_29462_lable' ).clear().type( 'Paste' ).blur();


        clickConditionalsTab();
        cy.get('.condition-line-field').should('have.value', 'fld_29462');
        cy.get('.condition-line-field').first().select('Paste [header]');
        cy.get('.condition-line-field').should('have.value', 'fld_29462');

        saveFormAndReload();

        //Make sure conditionals are still set right
        clickConditionalsTab();
        cy.get('.caldera-condition-nav' ).first().find( 'a' ).click();
        cy.get('.condition-line-field').should('have.value', 'fld_29462');
    });

    it( 'Knows the slug of the field after it updates', () => {
        createForm('Knows the slug of the field after it updates', false);

        clickConditionalsTab();

        //create conditional using field header
        createConditional('Slug Test', 'hide');
        cy.get('.condition-group-add-lines').click();
        cy.get('.condition-group-add-line').click();

        cy.get('.condition-line-field').first().select('header [header]');
        cy.get('.condition-line-field').should('have.value', 'fld_29462');

        //Go to layout tab and change field label
        clickLayoutTab();
        cy.get( '#fld_29462_slug' ).clear().type( 'Paste' ).blur();
        clickConditionalsTab();

        cy.get('.condition-line-field').should('have.value', 'fld_29462');

        cy.get('.condition-line-field').first().select('header [paste]');
        cy.get('.condition-line-field').should('have.value', 'fld_29462');
    });

    it( 'Uses a radio fields options for conditional logic values', () => {
        cloneRadioForm();
        clickConditionalsTab();

        createConditional('c1', 'hide');
        cy.get('.condition-group-add-lines').click();
        cy.get( '.condition-line-field').first( ).select('fld_6733423');
        //Using select in query tests that it's a select option, not a text input now.
        cy.get('.caldera-conditional-field-value select' ).select( 'Two' );
        cy.get('.caldera-conditional-field-value select' ).select( 'One' );
    });

    it( 'Updates the conditional settings based on field type change', () => {
        cloneRadioForm();
        clickConditionalsTab();


        createConditional('Radio-Based', 'hide');
        cy.get('.condition-group-add-lines').click();
        cy.get( '.condition-line-field').first( ).select('fld_6733423');
        cy.get('.caldera-conditional-field-value select' ).select( 'Two' );

        clickLayoutTab();
        cy.get( '#fld_6733423_type' ).select( 'Number' );

        clickConditionalsTab();
        //Test it changed back to an input
        cy.get('.caldera-conditional-field-value input' ).should( 'be.visible' );

    });

    it( 'Updates options for a conditional based on a radio, when radio gets more options', () => {
        cloneRadioForm();

        clickConditionalsTab();
        createConditional('Radio-Based Group', 'hide');
        cy.get('.condition-group-add-lines').click();
        cy.get( '.condition-line-field').first().select('fld_6733423');
        //It has two options
        cy.get('.caldera-conditional-field-value select' ).select( 'Two' );
        cy.get('.caldera-conditional-field-value select' ).select( 'One' );

        //Add another option
        clickLayoutTab();
        cy.get( '#fld_6733423 .add-option' ).click();
        cy.get( '#fld_6733423 .toggle_label_field').last().clear().type( 'Three' ).blur();
        cy.get( '#fld_6733423 .toggle_value_field').last().clear().type( '3' ).blur();

        clickConditionalsTab();

        //It has three options
        cy.get('.caldera-conditional-field-value select' ).select( 'Three' );
        cy.get('.caldera-conditional-field-value select' ).select( 'Two' );
        cy.get('.caldera-conditional-field-value select' ).select( 'One' );
    });
});


describe('Conditional Logic Editor', () => {
    let formName;
    beforeEach(() => {
        formName = Math.random().toString(36).substring(7);
        visitPluginPage('caldera-forms');
        createForm(formName)
    });
    before(() => login());
    it('Can open and close conditionals editor', () => {
        //hidden by default
        cy.get('#new-conditional').should('be.hidden');

        //Show it
        clickConditionalsTab();
        cy.get('#new-conditional').should('be.visible');

    });

    it('Can add conditional and set type', () => {
        clickConditionalsTab();
        cy.get('#new-conditional').click();
        cy.get('.condition-new-group-name').should('be.visible');
        cy.get('.condition-new-group-name').type('Condition 1').blur();
        cy.get('.condition-group-type').should('be.visible');
        cy.get('.condition-group-type').select('hide');
        cy.get('.condition-group-type').select('disable');
        cy.get('.condition-group-type').select('show');
    });

    it('Can add two conditionals', () => {
        clickConditionalsTab();
        cy.get('#new-conditional').click();
        cy.get('.condition-new-group-name').should('be.visible');
        cy.get('.condition-new-group-name').type('Hide').blur();
        cy.get('.condition-group-type').select('hide');


        cy.get('#new-conditional').click();
        cy.get('.condition-new-group-name').should('be.visible');
        cy.get('.condition-new-group-name').type('Disable').blur();
        cy.get('.condition-group-type').select('disable');

        cy.get('.caldera-condition-nav' ).first().find( 'a' ).click();
        cy.get('.condition-group-name' ).should( 'have.value', 'Hide' );
        cy.get('.condition-group-type').should( 'have.value', 'hide' );

        cy.get('.caldera-condition-nav' ).last().find( 'a' ).click();
        cy.get('.condition-group-name' ).should( 'have.value', 'Disable' );
        cy.get('.condition-group-type').should( 'have.value', 'disable' );

    });

    it('Can add conditional lines', () => {
        clickConditionalsTab();
        createConditional('c1', 'hide');
        cy.get('.condition-group-add-lines').click();
        cy.get('.caldera-condition-lines').should('have.length', 1);
        cy.get('.condition-group-add-lines').click();
        cy.get('.caldera-condition-lines').should('have.length', 2);

    });

    it('Can add and remove lines from a group of lines', () => {
        clickConditionalsTab();
        createConditional('c1', 'hide');
        //Add a group and add a line to it
        cy.get('.condition-group-add-lines').click();
        cy.get('.caldera-condition-line').should('have.length', 1);
        cy.get('.condition-group-add-line').click();
        cy.get('.caldera-condition-line').should('have.length', 2);

        //Remove a line
        cy.get('.caldera-condition-line-remove').first().click();
        cy.get('.caldera-condition-line').should('have.length', 1);

        cy.get('.condition-group-add-line').click();
        cy.get('.condition-group-add-line').click();
        cy.get('.condition-group-add-line').click();
        cy.get('.caldera-condition-line').should('have.length', 4);

    });

    it('Changes compare type', () => {
        clickConditionalsTab();
        createConditional('c1', 'hide');
        cy.get('.condition-group-add-lines').click();
        cy.get('.condition-group-add-line').click();
        cy.get('.condition-line-compare').first().select('isnot');

        ['contains', 'is', 'isnot', 'startswith', 'endswith', 'smaller', 'greater'].forEach(function (conditionType) {
            cy.get('.condition-line-compare').last().select(conditionType);
            cy.get('.condition-line-compare').last().should('have.value', conditionType);
            cy.get('.condition-line-compare').first().should('have.value', 'isnot');
        });

    });

});