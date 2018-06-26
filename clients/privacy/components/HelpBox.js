import React from 'react';
import PropTypes from 'prop-types';
import { Twemoji } from 'react-emoji-render';
import {Panel, ListGroup, ListGroupItem } from 'react-bootstrap'
import {DocLinks} from "./DocLinks";
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
            <h3>
                <Twemoji text=":volcano: FAQ" />
            </h3>
           <DocLinks />


            <h3>
                <Twemoji text=":volcano: Documentation" />
            </h3>
            <HelpBox />
        </div>
    )
};

HelpBox.propTypes = {
    saveButton: PropTypes.object.isRequired
};