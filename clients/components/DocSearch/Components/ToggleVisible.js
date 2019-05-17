import React from 'react';
import {Glyphicon} from 'react-bootstrap';

export class ToggleVisible extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            isOpen: this.props.isOpen
        };

        this.text = this.text.bind(this);
        this.title = this.title.bind(this);
        this.icon = this.icon.bind(this);
        this.supportUrl = this.supportUrl.bind(this);
    }

    text() {
        if (this.props.isOpen) {
            return 'Close Search Panel'
        }
        return 'Open Search Panel'
    }

    title() {
        if (this.props.isOpen) {
            return 'Close Search Panel';
        }
        return 'Open Search Panel';
    }

    icon() {
        if (this.props.isOpen) {
            return 'remove-circle';
        }
        return 'search';

    }
    supportUrl(){
        return `https://calderaforms.com/support?utm_source=search&utm_term=${(this.props.lastParams.categories)}&utm_keyword=${encodeURIComponent(this.props.lastParams.s)}`;
    }


    render() {
        return (
            <button
                className={'cf-doc-search-sidebar-toggle'}
                title={this.title()}
                onClick={this.props.toggleOpen}
            >
                <Glyphicon
                    glyph={this.icon()}
                />
                <span
                    className={'description'}
                >
                                {this.text()}
                            </span>
            </button>
            );



    }
}
