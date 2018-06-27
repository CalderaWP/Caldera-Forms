import React from 'react';
import { FormGroup,FormControl,ControlLabel,HelpBlock } from 'react-bootstrap';

export function FieldGroup({ id, label, help, ...props }) {
    return (
        <FormGroup controlId={id}>
            <ControlLabel>{label}</ControlLabel>
            <FormControl {...props} />
            {help && <HelpBlock>{help}</HelpBlock>}
        </FormGroup>
    );
}