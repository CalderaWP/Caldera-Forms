import {
    ConditionalEditor,
    SubscribesToFormFields,
    translationStrings,
} from '@calderajs/form-builder';
import React from 'react';
import {render} from '@wordpress/element';
import domReady from '@wordpress/dom-ready';


/**
 * The conditional logic app of today
 * and form builder app of tomorrow
 *
 * @since 1.9.0
 */
const FormBuilder = (props) => {
    const {strings, conditionals} = props;
    return (
        <SubscribesToFormFields
            jQuery={window.jQuery}
            component={({formFields}) => {
                console.log(formFields);
                //Maybe render on a portal?
                //Or will need a different app for Processor Conditionals
                return (
                    <div />
                )
            }}
        />
    );
};

FormBuilder.defaultProps = {
    formFields: []
};


/**
 * Set up app
 */
domReady(function () {
    let form = CF_ADMIN.form;
    if (!form.hasOwnProperty('fields')) {
        form.fields = {};
    }


    let options = {};
    Object.values(form.fields).forEach((field) => {
        if (field.hasOwnProperty('config') && field.config.hasOwnProperty('option')) {
            options[field.ID] = {};
            Object.keys(field.config.option).forEach(optionId => {
                options[field.ID][optionId] = {
                    value: field.config.option[optionId].value,
                    label: field.config.option[optionId].label,
                    id: optionId,
                }
            })
        }
    });

    jQuery('.caldera-editor-body').on(
        'change',
        '.caldera-select-field-type',
        (e) => {
            e.target.value;
        });

    render(
        <SubscribesToFormFields
            jQuery={window.jQuery}
            intitalFields={form.fields}
            component={({formFields}) => {
                console.log(formFields);
                //Maybe render on a portal?
                //Or will need a different app for Processor Conditionals
                return (
                    <div />
                )
            }}
        />,
        document.getElementById('caldera-forms-form-builder')
    );
});
