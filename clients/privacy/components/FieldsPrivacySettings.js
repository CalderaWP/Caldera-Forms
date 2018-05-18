import React from 'react';
import PropTypes from "prop-types";
import {FieldPrivacySettings} from "./FieldPrivacySettings";

export const FieldsPrivacySettings = (props) => {
    return (
        <div>
            {Object.keys(props.fields).map( (fieldId ) => {
                const field = props.fields[fieldId];
                if ( field && field.hasOwnProperty( 'ID' ) ) {
                    const idAttr = `caldera-forms-privacy-field-settings-${field.ID}`;
                    return (
                        <FieldPrivacySettings
                            id={idAttr}
                            key={idAttr}
                            field={field}
                            formId={props.formId}
                            privacySettings={props.privacySettings}
                            onCheckIsEmail={props.onCheckIsEmail}
                            onCheckIsPii={props.onCheckIsPii}
                        />
                    );
                } else {
                    <p>Invalid Field</p>
                }

            })}
        </div>
    )
};

FieldsPrivacySettings.propTypes = {
    fields: PropTypes.object.isRequired,
    formId: PropTypes.string.isRequired,
    privacySettings: PropTypes.object.isRequired,
    onCheckIsEmail: PropTypes.func.isRequired,
    onCheckIsPii: PropTypes.func.isRequired
};