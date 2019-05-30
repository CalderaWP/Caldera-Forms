import React from 'react';
import { FormGroup,FormControl } from 'react-bootstrap';

export  class  Keyword extends React.Component {
    render() {
        return (
            <FormGroup controlId="keyword-search">

                <h4>Keywords</h4>
                <FormControl
                    type="text"
                    value={this.props.value}
                    placeholder="Enter keyword"
                    onChange={this.props.change}
                />
            </FormGroup>
        );
    }
}

