import React from 'react';
import PropTypes from 'prop-types';

import { FormGroup,FormControl,ControlLabel,HelpBlock } from 'react-bootstrap';


export const FormSelectorNoGutenberg = (props) => {
    return (
        <FormGroup controlId="formControlsSelect">
            <ControlLabel>Select</ControlLabel>
            <FormControl
                componentClass="select"
                onChange={(e) => {
                    props.onChange(e.target.value)
                }}
            >
                {Object.keys(props.forms).map( (formsIndex) => {
                    const form = props.forms[formsIndex];
                    return (
                        <option
                            key={form.ID}
                            value={form.ID}
                        >
                            {form.name}
                        </option>
                    )
                })}
            </FormControl>
            <HelpBlock>Choose a form to edit privacy settings for.</HelpBlock>
        </FormGroup>
    )
};

FormSelectorNoGutenberg.propTypes = {
    forms: PropTypes.array.isRequired,
    onChange: PropTypes.func,
};