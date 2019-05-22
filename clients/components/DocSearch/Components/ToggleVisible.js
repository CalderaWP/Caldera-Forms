import React from 'react';
import {Glyphicon} from 'react-bootstrap';

export class ToggleVisible extends React.Component {

    title() {
        if (this.props.isOpen) {
            return 'Close Search Panel';
        }
        return 'Open Search Panel';
    }


    supportUrl() {
        return `https://calderaforms.com/support?utm_source=search&utm_term=${(this.props.lastParams.categories)}&utm_keyword=${encodeURIComponent(this.props.lastParams.s)}`;
    }


    render() {
        return (
            <button
                aria-expanded={this.props.isOpen}
                className={'cf-doc-search-sidebar-toggle'}
                title={this.title()}
                onClick={this.props.toggleOpen}
            >
                <span className="dashicons dashicons-menu"></span>
                <span
                    className={'screen-reader-text sr-only'}
                >
                    Toggle Menu
                </span>
            </button>
        );


    }
}
