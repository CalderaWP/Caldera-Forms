import React from  'react';
import PropTypes from 'prop-types';
import { FormGroup,FormLabel,Form,FormCheck } from 'react-bootstrap';
const{Text} = Form;
/**
 * Determine if field is a PII field
 *
 * @since 1.7.0
 *
 * @param {Object} field
 * @param {Object} privacySettings
 * @returns {*}
 */
function fieldIsPii(field,privacySettings ) {
    return privacySettings.piiFields.length && privacySettings.piiFields.includes(field.ID);
}

/**
 * Setting to determine if this is a PII field
 *
 * @since 1.7.0
 *
 * @param props
 * @returns {*}
 * @constructor
 */
export const IsPiiField = (props) => {
    const idAttr  = `caldera-forms-privacy-gdpr-is-pii-field-${props.field.ID}`;
    return (
        <FormGroup>
            <FormLabel
               htmlFor={idAttr}
            >
               Personally Identifying Field?
            </FormLabel>

            <Form.Check
                type={'checkbox'}
                label={'Yes'}
                onChange={() => {
                    props.onCheck(props.field.ID)
                }}
                checked={fieldIsPii(props.field, props.privacySettings)}
            />

            <Text
                className={'screen-reader-text'}
            >
                Does field contain personally identifying data?
            </Text>
        </FormGroup>
    );

};

IsPiiField.propTypes = {
    field: PropTypes.object.isRequired,
    privacySettings: PropTypes.object.isRequired,
    onCheck: PropTypes.func.isRequired,
};