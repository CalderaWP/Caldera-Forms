import {
    ConditionalEditor,
    SubscribesToFormFields,
    translationStrings,
} from '@calderajs/form-builder';
import React from 'react';
import {render} from '@wordpress/element';
import domReady from '@wordpress/dom-ready';

/**
 * Watches for changes to field options and provides to child
 *
 * @todo Move to form-builder lib?
 */
const SubscribesToFieldOptions = (props) => {
    const {jQuery} = props;
    const [fieldOptions, setFieldOptions] = React.useState(props.options || {});
    React.useEffect(() => {
        let isSubscribed = true;
        jQuery(document).on('change record click', '.field-options .option-setting', function () {
            let update = Object.assign({}, fieldOptions);
            console.log(jQuery(this));
            let key = '';
            if (jQuery(this).hasClass('toggle_value_field')) {
                key = 'value'
            } else if (jQuery(this).hasClass('toggle_label_field')) {
                key = 'label'
            } else {
                return;
            }
            const option = jQuery(this).data('opt');
            const fieldId = jQuery(this).closest('.field-options').data('field');
            if (!update.hasOwnProperty(fieldId)) {
                update[fieldId] = {};
            }
            update[fieldId][option] = {
                ...update[fieldId].hasOwnProperty(option) ? update[fieldId][option] : {},
                [key]: jQuery(this)[0].value
            };
            setFieldOptions(update);
        });
        return () => {
            isSubscribed = false;
        }
    }, [jQuery]);
    return props.component({fieldOptions, setFieldOptions})
};

/**
 * The conditional logic app of today
 * and form builder app of tomorrow
 *
 * @since 1.9.0
 */
const FormBuilder = (props) => {
    const {strings, conditionals} = props;
    return (
        <SubscribesToFieldOptions
            options={props.fieldOptions}
            jQuery={window.jQuery}
            component={({fieldOptions}) => {
                return (
                    <SubscribesToFormFields
                        jQuery={window.jQuery}
                        component={({formFields}) => {
                            const fields = formFields.map(field => {
                                if (fieldOptions.hasOwnProperty(field.ID)) {
                                    return {
                                        ...field,
                                        config: {
                                            option: fieldOptions[field.ID]
                                        }
                                    }
                                } else {
                                    return field;
                                }

                            });
                            //Maybe render on a portal?
                            //Or will need a different app for Processor Conditionals
                            return (
                                <ConditionalEditor formFields={fields} conditionals={conditionals} strings={strings}/>
                            )
                        }}
                    />
                )
            }}/>
    )
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

    /**
     * Load conditional editor when clicking on conditions tab
     * @type {boolean}
     */
    let isLoaded = false;
    document.getElementById('tab_conditions').addEventListener("click", () => {
        if (!isLoaded) {
            isLoaded = true;
            //Maybe render on hidden element and then use portals for editor.
            render(
                <FormBuilder conditionals={[]} strings={translationStrings} fieldOptions={options}/>,
                document.getElementById('caldera-forms-conditions-panel')
            );
            //Yah seriously, use portals
        }
    });

});
