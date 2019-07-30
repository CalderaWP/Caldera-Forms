import './index.scss';
import jQuery from 'jquery'
import {Component, render, unmountComponentAtNode,Fragment} from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
import MainDashboard from './MainDashboard/MainDashboard';


class FormsAdminApp extends Component {

    constructor(props) {
        super(props);
        this.state = {
            hide: false,
        };
        this.setWrapperRef = this.setWrapperRef.bind(this);
        this.handleClickOutside = this.handleClickOutside.bind(this);
        this.handleEntryViewerClose = this.handleEntryViewerClose.bind(this);
    }

    /**
     * Bind events
     */
    componentDidMount() {
        document.addEventListener('click', this.handleClickOutside);
        document.getElementById( 'caldera-forms-close-entry-viewer' )
            .addEventListener('click', this.handleEntryViewerClose );
    }

    /**
     * Clean up even bindings
     */
    componentWillUnmount() {
        document.removeEventListener('click', this.handleClickOutside);
        document.getElementById( 'caldera-forms-close-entry-viewer' )
            .removeEventListener('click', this.handleEntryViewerClose );
    }

    /**
     * When close entry viewer clicked, go back to default view
     * @param event
     */
    handleEntryViewerClose(event){
        event.preventDefault();
        //unhide app
        this.setState({hide: false});

        //hide the button to close entry viewer
        jQuery( '.caldera-forms-hide-when-entry-viewer-closed, .form-entries-wrap' )
            .hide()
            .css( 'visibility', 'hidden' )
            .attr( 'aria-hidden', 'true' );

        //Reset right/ left layout with form list
        jQuery( '.form-panel-wrap' )
            .show()
            .css( 'visibility', 'visible' )
            .attr( 'aria-hidden', 'true' );
        jQuery( '.form-admin-page-wrap' )
            .css( 'margin-left', '430px' );

    }


    /**
     * Set the wrapper ref
     */
    setWrapperRef(node) {
        this.wrapperRef = node;
    }

    /**
     * When click outside of component open/close right side if needed
     */
    handleClickOutside(event) {
        //Is click on entry viewer
        if( event.target.classList.contains('cf-entry-viewer-link') ){
            //hide app
            this.setState({hide: true});
            //show entry viewer
            jQuery( '.caldera-forms-hide-when-entry-viewer-closed, .form-entries-wrap' )
                .show()
                .css( 'visibility', 'visible' )
                .attr( 'aria-hidden', 'false' );
            event.preventDefault();
        }

    }

    render() {
        const {props} = this;
        const {hide} = this.state;
        if( hide ){
            return <Fragment />
        }
        return <div ref={this.setWrapperRef}><MainDashboard { ...props } /></div>;
    }

}
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

    render( <FormsAdminApp {...props} />, document.getElementById('caldera-forms-clippy') );

});


