import React from 'react';
import PropTypes from 'prop-types';
import {Col,Row} from 'react-bootstrap';
import {IsEmailIdentifyingField} from "./IsEmailIdentifyingField";
import {IsPiiField} from "./IsPiiField";

/**
 * Privacy Settings for one field of a form
 *
 * @since 1.7.0
 *
 * @param props
 * @returns {*}
 * @constructor
 */
export const FieldPrivacySettings = (props) => {
    return (
        <Row
            style={{
                backgroundColor: '#fff',
                margin: '1em',
            }}
        >
            <h4
                style={{
                    margin: 0,
                    padding: '.7em 1em',
                    borderBottom: '1px solid #eee'
                }}
            >
                {props.field.name}
            </h4>
            <Row
                xs={6}
                style={{
                    padding: '12px'
                }}
            >
                <IsPiiField
                    field={props.field}
                    privacySettings={props.privacySettings}
                    onCheck={props.onCheckIsPii}
                />
            </Row>

            <Row
                xs={6}
                style={{
                    padding: '12px'
                }}
            >
                <IsEmailIdentifyingField
                    className={'col-xs-6'}
                    field={props.field}
                    privacySettings={props.privacySettings}
                    onCheck={props.onCheckIsEmail}
                />
            </Row>


        </Row>
    );
};

FieldPrivacySettings.propTypes = {
    field: PropTypes.object.isRequired,
    formId: PropTypes.string.isRequired,
    privacySettings: PropTypes.object.isRequired,
    onCheckIsEmail: PropTypes.func.isRequired,
    onCheckIsPii: PropTypes.func.isRequired
};