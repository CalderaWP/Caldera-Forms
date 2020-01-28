import {
    visitPage,
    getCfField,
    clearCfField,
    cfFieldIsVisible,
    cfFieldDoesNotExist,
    cfFieldHasValue,
    cfFieldSelectValue,
    cfFieldSetValue,
    cfFieldCheckValue,
    cfFieldIsDisabled,
    cfFieldUnCheckValue,
    cfFieldIsNotDisabled,
    cfFieldCheckAllValues,
    cfFieldCalcFieldValueIs,
    visitPluginPage,
    login,
    visitFormEditor,
    cfEditorIsFieldPreviewVisible,
    cfEditorIsFieldPreviewNotVisible,
    cfGoToProcessorsTab, cfAddProcessor, cfFieldClickButton, cfAlertHasText
} from '../support/util';

const submitButtonFieldId = 'fld_173802';
const requiredNumberFieldFormId = 'CF5e304e98685fd';
const requiredNumberFieldPageSlug = 'required-number';
const numberFieldId = 'fld_1621897';


describe( 'Editing forms using entry editor',  () => {
    before(() => login());

    /**
     * Ensures that a number field with the value of 0 can be edited.
     *
     * @see https://github.com/CalderaWP/Caldera-Forms/issues/3024
     */
    it( 'Can edit a required number field whose value is 0', () => {
        //Submit form with value of 0 in field.
        visitPage(requiredNumberFieldPageSlug);
        cfFieldSetValue(numberFieldId,'0');
        cfFieldClickButton(submitButtonFieldId);
        cfAlertHasText( requiredNumberFieldFormId);

        //Open up editor
        visitPluginPage('caldera-forms');
        cy.get( '#form_row_CF5e304e98685fd').trigger('mouseover');
        cy.get( '#form_row_CF5e304e98685fd .cf-entry-viewer-link').should( 'be.visible');
        cy.get( '#form_row_CF5e304e98685fd .cf-entry-viewer-link').click({ force: true });
        cy.get( '.view-entry-btn' ).first().click({force:true});
        cy.get( '.baldrick-modal-footer .button-primary' ).first().click({force:true});

        //Value should be 0. Or if #3024 not fixed, it's empty.
        getCfField(numberFieldId).should( 'have.value', '0');

        //Save, which not possible if field empty due to #3024
        cy.get( '.baldrick-modal-footer .button-primary' ).first().click({force:true});

    });


});