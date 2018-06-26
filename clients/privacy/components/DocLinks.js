import React from 'react';
import { Twemoji } from 'react-emoji-render';
import {ListGroup, ListGroupItem } from 'react-bootstrap'
export const DocLinks = () => {
    return (
        <React.Fragment>
            <ListGroup
                style={{padding:'12px'}}
            >
                <ListGroupItem>
                    <Twemoji text=":eyes:" />
                    <a href="https://calderaforms.com/doc/setting-caldera-forms-gdpr-data-requests/?utm_source=wp-admin&utm_campaign=privacy-settings">
                        Setting Up Caldera Forms For GDPR Requests
                    </a>
                </ListGroupItem>
                <ListGroupItem>
                    <Twemoji text=":eyes:" />
                    <a href="https://calderaforms.com/gdpr/?utm_source=wp-admin&utm_campaign=privacy-settings">
                       All Caldera Forms GDPR Compliance Tools
                    </a>
                </ListGroupItem>
            </ListGroup>
        </React.Fragment>
    )
};

