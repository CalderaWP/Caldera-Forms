import React from  'react';
import PropTypes from 'prop-types';
import {FieldPrivacySettings} from "./FieldPrivacySettings";
import { FormGroup,Button,ControlLabel } from 'react-bootstrap';
export  const  FormPrivacySettings = (props) => {
    let hasEmailField = false;
    return (
        <div>
            {Object.keys(props.form.fields).map( (fieldId) => {
                const field = props.form.fields[fieldId];
                if( 'email' !== field.type || 'text' !== field.type ){
                    return;
                }
                hasEmailField = true;
                return (
                    <div
                        key={field.ID}
                    >
                        <FieldPrivacySettings
                            field={field}
                            formId={props.form.ID}
                        />
                    </div>

                )
            })}
            {! hasEmailField &&
                <p>No Email Field In Form</p>
            }
            <FormGroup
            >
                <Button
                    type="submit"
                    onClick={props.onSave}
                >
                    Save
                </Button>
            </FormGroup>
        </div>
    );
};

FormPrivacySettings.propTypes = {
    form: PropTypes.object.isRequired,
    onSave: PropTypes.func.isRequired
};