import React from 'react';
import PropTypes from 'prop-types';
import {FormGroup, FormControl, ControlLabel, HelpBlock, Panel, PanelGroup, Checkbox} from 'react-bootstrap';
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
        <section
            style={{
                backgroundColor: '#fff',
                margin: '1em',
            }}
            className={'row'}
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
            <div
                className={'col-xs-6'}
                style={{
                    padding: '12px'
                }}
            >
                <IsPiiField
                    field={props.field}
                    privacySettings={props.privacySettings}
                    onCheck={props.onCheckIsPii}
                />
            </div>

            <div
                className={'col-xs-6'}
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
            </div>


        </section>
    );
};

FieldPrivacySettings.propTypes = {
    field: PropTypes.object.isRequired,
    formId: PropTypes.string.isRequired,
    privacySettings: PropTypes.object.isRequired,
    onCheckIsEmail: PropTypes.func.isRequired,
    onCheckIsPii: PropTypes.func.isRequired
};