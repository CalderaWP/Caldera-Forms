import React from 'react';
import PropTypes from 'prop-types';

import { FormGroup,HelpBlock } from 'react-bootstrap';

/**
 * Form selector with no dependency on Gutenberg
 *
 * @since 1.7.0
 *
 * @param props
 * @returns {*}
 * @constructor
 */
export const FormSelectorNoGutenberg = (props) => {
    const idAttr = props.idAttr ? props.idAttr :'caldera-forms-form-chooser';
    return (
        <FormGroup controlId={idAttr}>
            <label
                htmlFor={idAttr}
            >
                Choose Form
            </label>
            <select
                id={idAttr}
                value={props.selected || ''}
                className="select"
                onChange={(e) => {
                    props.onChange(e.target.value)
                }}
            >
                <option value=''></option>
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
            </select>
            <HelpBlock>Choose a form to edit privacy settings for.</HelpBlock>
        </FormGroup>
    )
};

FormSelectorNoGutenberg.propTypes = {
    forms: PropTypes.array.isRequired,
    onChange: PropTypes.func.isRequired,
    selected: PropTypes.string,
    idAttr: PropTypes.string
};