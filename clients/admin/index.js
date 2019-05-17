import './index.scss';
import {createElement, render, unmountComponentAtNode,} from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
domReady(function () {
    const MainDashboard = React.lazy(() => import('./MainDashboard/MainDashboard'));
    const Translate = React.lazy(() => import('./MainDashboard/components/Translate/Translate'));
    const App = () => (
        <React.Suspense fallback={<div>Loading...</div>}>
            <div>
                <MainDashboard/>
            </div>

        </React.Suspense>
    );
    render(<App/>, document.getElementById('caldera-forms-clippy'));
    jQuery('.cf-entry-viewer-link').on('click', function () {
        unmountComponentAtNode(document.getElementById('caldera-forms-clippy'));
        jQuery( '#form-entries-viewer' ).show().css( {visibility:'visible'});
        jQuery('.form-panel-wrap').show().css( {visibility:'visible'});

    });
    
    jQuery( '#cf-close-entry-viewer').on( 'click', function () {
        jQuery( '#form-entries-viewer' ).hide().css( {visibility:'hidden'});
        render(<App/>, document.getElementById('caldera-forms-clippy'));

    })

});


