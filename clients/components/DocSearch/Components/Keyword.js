import React from 'react';
import { FormGroup,FormControl } from 'react-bootstrap';

export  class  Keyword extends React.Component {
    render() {
        return (
            <FormGroup controlId="keyword-search">

                <h3>Keyword Search</h3>
                <FormControl
                    type="text"
                    value={this.props.value}
                    placeholder="Enter text"
                    onChange={this.props.change}
                />
            </FormGroup>
        );
    }
}

