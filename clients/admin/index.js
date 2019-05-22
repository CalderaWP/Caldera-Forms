import './index.scss';

import {createElement, render, unmountComponentAtNode} from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
import MainDashboard from './MainDashboard/MainDashboard';


/**
 * Controls the right side of the CF admin
 *
 * @since 1.8.6
 */
domReady(function () {
    const isProConnected = 'object' === typeof CF_ADMIN && CF_ADMIN.isProConnected;

    const props = {
        isProConnected,
    };

    render(<MainDashboard { ...props } />, document.getElementById('caldera-forms-clippy'));
    jQuery('.cf-entry-viewer-link').on('click', function () {
        unmountComponentAtNode(document.getElementById('caldera-forms-clippy'));
    })
});


