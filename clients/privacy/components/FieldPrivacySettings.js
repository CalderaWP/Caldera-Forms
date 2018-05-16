import React from  'react';
import PropTypes from 'prop-types';
import { FormGroup,FormControl,ControlLabel,HelpBlock,Panel,PanelGroup,Checkbox } from 'react-bootstrap';

export  class FieldPrivacySettings extends React.Component {

    constructor(props){
        super(props);
        this.state = {
            isIdentifying: false
        };
        this.handleCheck = this.handleCheck.bind(this);
    }

    handleCheck(){
        this.setState( { isIdentifying: ! this.state.isIdentifying } );
    };
    render(){
        return(
            <PanelGroup>
                <Panel onClick={(e) => {}}>
                    <Panel.Body>
                        <Panel.header>
                            <p>{field.label}</p>
                        </Panel.header>
                        <FormGroup controlId={`caldera-forms-privacy-is-email-identifier-field-${this.props.field.ID}`}>
                            <ControlLabel>
                                Submitter Email
                            </ControlLabel>
                            <Checkbox
                                onChange={this.handleCheck}
                                checked={this.state.isIdentifying}
                            >
                                Checkbox
                            </Checkbox>
                            <HelpBlock>Can this field be used to identify form submitter by email?</HelpBlock>}
                        </FormGroup>
                    </Panel.Body>
                    <Panel.Footer>Panel footer</Panel.Footer>
                </Panel>
            </PanelGroup>
        )
    }


}

FieldPrivacySettings.propTypes = {
    field: PropTypes.object.isRequired,
    formId: PropTypes.string.isRequired
};