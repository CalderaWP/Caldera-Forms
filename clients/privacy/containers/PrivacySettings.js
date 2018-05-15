import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';


export const PrivacySettings = (props) => {
    console.log(props);
    return (
        <div>
            <p>{props.forms[0].name}</p>
        </div>

    )
};

PrivacySettings._propTypes = {
    forms: PropTypes.array
};

const mapStateToProps = state => {
    return {
        forms : state.formState.forms,
        roy: 'hi'
    }
};


export const PrivacySettingsWrapped = connect(
    mapStateToProps,
)(PrivacySettings);