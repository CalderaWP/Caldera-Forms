import React from  'react';
import PropTypes from 'prop-types';
import { FormGroup,FormControl,ControlLabel,HelpBlock,Panel,PanelGroup,Checkbox } from 'react-bootstrap';

/**
 * Determine if field is a email identifying field
 *
 * @since 1.7.0
 *
 * @param {Object} field
 * @param {Object} privacySettings
 * @returns {*}
 */
function fieldIsEmailIdentifying(field,privacySettings ){
    return privacySettings.emailIdentifyingFields.length && privacySettings.emailIdentifyingFields.includes(field.ID);
}

/**
 * Setting for a form being an email identifying field
 *
 * @since 1.7.0
 *
 * @param props
 * @returns {*}
 * @constructor
 */
export const IsEmailIdentifyingField = (props)  => {
    const idAttr  = `caldera-forms-privacy-gdpr-is-email-identifiying-${props.field.ID}`;
    if( 'email' === props.field.type || 'text' === props.field.text ){
        return (
            <FormGroup
                controlId={idAttr}
            >
                <ControlLabel
                    controlId={idAttr}
                >
                    Email Identifying Field?
                </ControlLabel>
                <Checkbox
                    controlId={idAttr}
                    onChange={() => {
                            props.onCheck(props.field.ID)
                        }
                    }
                    checked={fieldIsEmailIdentifying(props.field,props.privacySettings)}
                >
                    <span style={{
                        marginLeft: '12px'
                    }}>
                        Enable
                    </span>
                </Checkbox>
                <HelpBlock>Can this field be used to determine whose data an entry belongs to?</HelpBlock>}
            </FormGroup>
        )
    }
    return (
        <div
            id={idAttr}
        >
            Not an email or text field
        </div>
    )

};

IsEmailIdentifyingField.propTypes=  {
    field: PropTypes.object.isRequired,
    privacySettings: PropTypes.object.isRequired,
    onCheck: PropTypes.func.isRequired,
};