import React from  'react';
import PropTypes from 'prop-types';
import { FormGroup,FormControl,ControlLabel,HelpBlock,Panel,PanelGroup,Checkbox } from 'react-bootstrap';
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
export const FieldPrivacySettings = (props)  => {
        return(
           <section>
               <IsEmailIdentifyingField
                   field={props.field}
                   privacySettings={props.privacySettings}
                   onCheck={props.onCheckIsEmail}
               />
               <IsPiiField
                   field={props.field}
                   privacySettings={props.privacySettings}
                   onCheck={props.onCheckIsPii}
               />
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