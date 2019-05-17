import './index.scss';
import {createElement, render, unmountComponentAtNode,} from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
domReady(function () {
    const MainDashboard = React.lazy(() => import('./MainDashboard/MainDashboard'));
    const Translate = React.lazy(() => import('./MainDashboard/components/Translate/Translate'));
    render(<React.Suspense fallback={<div>Loading...</div>}>
        <div>
            <MainDashboard/>
        </div>

    </React.Suspense>, document.getElementById('caldera-forms-clippy'));
    jQuery('.cf-entry-viewer-link').on('click', function () {
        unmountComponentAtNode(document.getElementById('caldera-forms-clippy'));
    })

});


