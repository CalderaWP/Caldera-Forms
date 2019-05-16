import './index.scss';
import {createElement, render, unmountComponentAtNode} from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
import {Panel, PanelBody, PanelRow} from '@wordpress/components';

const MyPanel = () => (
    <Panel header="Using Caldera Forms">
        <PanelBody
            title="Getting Started"
            icon="editor-help"
            initialOpen={true}
        >
            <PanelRow>
                My Panel Inputs and Labels
            </PanelRow>
        </PanelBody>

        <PanelBody
            title="Go Pro"
            icon="thumbs-up"
            initialOpen={true}
        >
            <PanelRow>
                My Panel Inputs and Labels
            </PanelRow>
        </PanelBody>

        <PanelBody
            title="Translations"
            icon="translation"
            initialOpen={true}
        >
            <iframe src={"https://calderaforms.com/translate/"} lazy="true"/>
        </PanelBody>


    </Panel>
);


domReady(function () {
    render(<MyPanel/>, document.getElementById('caldera-forms-clippy'));

    jQuery('.cf-entry-viewer-link').on('click', function () {
        unmountComponentAtNode(document.getElementById('caldera-forms-clippy'));
    })

});


