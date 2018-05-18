import React from 'react';
import PropTypes from 'prop-types';
import {FormGroup, Button, ControlLabel, Checkbox, HelpBlock} from 'react-bootstrap';
import {FieldsPrivacySettings} from "./FieldsPrivacySettings";
import remove from 'lodash.remove';

export const FormPrivacySettings = (props) => {
    console.log(props.privacySettings);


    const toggleEnable = () => {
        props.onStateChange({
            ...props.privacySettings,
            privacyExporterEnabled: ! props.privacySettings.privacyExporterEnabled,
        });
    };

    const toggleIsEmail = (fieldId) => {
        let fields = props.emailIdentifyingFields;

        console.log(fields);
        if (!fields.length || !fields.includes(fieldId)) {
            fields.push(fieldId)
        } else {
            fields = remove(fields, (field) => {
                return field.ID === fieldId;
            });
        }
        props.onStateChange({
            ...props.privacySettings,
            emailIdentifyingFields: fields,
        });

    };

    return (
        <div>

            <FormGroup controlId={`caldera-forms-privacy-gdpr-enable-${props.form.ID}`}>
                <ControlLabel>
                    Enable GDPR Exporter For Form
                </ControlLabel>
                <Checkbox
                    onChange={toggleEnable}
                    checked={props.privacySettings.privacyExporterEnabled}
                >
                    Checkbox
                </Checkbox>
                <HelpBlock>If checked data for this form will be added to GDPR personal data requests, and
                    deletes.</HelpBlock>}
            </FormGroup>


            {props.privacyExporterEnabled &&
            <FieldsPrivacySettings
                fields={props.form.fields}
                formId={props.form.ID}
                privacySettings={props.privacySettings}
                onCheckIsEmail={toggleIsEmail}
                onCheckIsPii={() => {
                }}
            />

            }

            <FormGroup
            >
                <Button
                    type="submit"
                    onClick={() => {
                        this.props.onSave(this.state)
                    }}
                >
                    Save
                </Button>
            </FormGroup>
        </div>
    );


};

FormPrivacySettings.propTypes = {
    form: PropTypes.object.isRequired,
    onSave: PropTypes.func.isRequired,
    privacySettings: PropTypes.object.isRequired,
    onStateChange: PropTypes.func.isRequired,
};