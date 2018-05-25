import React from 'react';
import PropTypes from 'prop-types';
import {Button} from 'react-bootstrap';


export const HelpBox = (props) => {
    return (
        <div
            style={{
                backgroundColor: '#fff'
            }}
            className={'col-xs-4'}
        >
            <p
                style={{
                    margin: 0,
                    padding: '.7em 1em',
                    borderBottom: '1px solid #eee'
                }}
            >
                {props.saveButton}
            </p>
            <ul
                style={{padding:'12px'}}
            >
                <li>Email Identifying Fields: The field(s) of your form that can be used to determine whose data an entry belongs to.</li>
                <li>Personally Identifying Fields (fields): The field(s) of your form that contain PII about the person identified in the email identifying field.</li>
                <li><a href={'https://calderaforms.com/gdpr?utm_campaign=wp-admin'}>Learn More Here</a></li>
            </ul>
        </div>
    )
}

HelpBox.propType = {
    saveButton: function (props, propName, componentName) {
        if(!props[propName] || typeof(props[propName].render) != 'function') {
            return new Error(`${propName}.render must be a function!`);
        }
    },
}