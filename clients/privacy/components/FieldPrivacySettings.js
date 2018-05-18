import React from  'react';
import PropTypes from 'prop-types';
import { FormGroup,FormControl,ControlLabel,HelpBlock,Panel,PanelGroup,Checkbox } from 'react-bootstrap';
import {IsEmailIdentifyingField} from "./IsEmailIdentifyingField";
import {IsPiiField} from "./IsPiiField";

export const FieldPrivacySettings = (props)  => {


        return(
           <section>
               <p>{props.field.name}</p>
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