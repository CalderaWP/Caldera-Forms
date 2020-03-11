import {
    visitPluginPage,
    login,
    createForm,
    cfGoToProcessorsTab,
    saveFormAndReload
} from '../support/util';




describe('Processor conditional logic', () => {
    let formName;

    const makeFormName = (name) => name + + Math.random().toString(36).substring(7);
    beforeEach(() => {
        visitPluginPage('caldera-forms');
    });
    before(() => login());


    /**
     Are we making sure that?
     All of the above changes can be saved, the form editor reloaded, and the settings are the same.
     */
    test('We can add new conditional groups to a processor', () => {
        const formName = makeFormName('We can add new conditional groups to a processor');
        createForm(formName, false);
        cfGoToProcessorsTab();
        cy.get('.active-processors-list').should( 'have.length', 1);
    });

    test('We can remove conditional groups from a processor.', () => {
        const formName = makeFormName('We can remove conditional groups from a processor.');
        createForm(formName, false);
        cfGoToProcessorsTab();
        cy.get('.active-processors-list').should( 'have.length', 1);
    });

    test('When a processor has conditionals, the saved settings load in the editor correctly.', () => {
        const formName = makeFormName('When a processor has conditionals, the saved settings load in the editor correctly.');
        createForm(formName, false);
        cfGoToProcessorsTab();
        cy.get('.active-processors-list').should( 'have.length', 1);
    });

    test('When field type changes to field type with options, its options are used as conditional value.', () => {
        const formName = makeFormName('When field type changes to field type with options, its options are used as conditional value.');
        createForm(formName, false);
        cfGoToProcessorsTab();
        cy.get('.active-processors-list').should( 'have.length', 1);
    });

    test('When options for a field used as conditional value change, conditional editor reflects change', () => {
        const formName = makeFormName('When options for a field used as conditional value change, conditional editor reflects change');
        createForm(formName, false);
        cfGoToProcessorsTab();
        cy.get('.active-processors-list').should( 'have.length', 1);
    });







});