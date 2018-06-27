import React from 'react';
import { Twemoji } from 'react-emoji-render';
import {ListGroup, ListGroupItem } from 'react-bootstrap'
import PropTypes from 'prop-types';

/**
 * Shows the doc links in the Privacy settings page
 *
 * @since 1.7.2
 *
 * @param props
 * @return {*}
 */
export const DocLinks = (props) => {
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
				<ListGroupItem>
                    {props.children}
                </ListGroupItem>
            </ListGroup>
        </React.Fragment>
    )
};

DocLinks.propTypes = {
	extraLink: PropTypes.string,
	extraLinkText: PropTypes.string
};