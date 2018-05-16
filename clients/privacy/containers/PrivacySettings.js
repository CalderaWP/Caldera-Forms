import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux'
import {
    setForm,
    setEditForm,
    unsetEditForm
} from "../actions";
import {requestForm} from "../../state/api";
import {FormPrivacySettings} from "../components/FormPrivacySettings";
import {FormSelectorNoGutenberg} from "../../components/FormSelectorNoGutenberg";

export const PrivacySettings = (props) => {
    const onChange = (newFormsId) => {
        const formRequest = requestForm(newFormsId);
        formRequest.then( (form) => {
            props.setForm(form,newFormsId);
            props.setEditForm(newFormsId);
        })
    };

    if( props.editForm.hasOwnProperty( 'fields' ) ){
        return <FormPrivacySettings
            form={props.editForm}
            onSave={() => {
                    props.unsetEditForm();
                }
            }
        />
    }

    return (
        <div>
            <FormSelectorNoGutenberg
                forms={props.forms}
                onChange={onChange}
            />
        </div>

    )
};

PrivacySettings.propTypes = {
    forms: PropTypes.array.isRequired,
    editForm: PropTypes.object
};

const mapStateToProps = state => {
    return {
        forms : state.formState.forms,
        editForm: state.formState.editForm,
        roy: 'hi'
    }
};

const mapDispatchToProps = {
    setEditForm,
    setForm,
    unsetEditForm
};



export const PrivacySettingsWrapped = connect(
    mapStateToProps,
    mapDispatchToProps
)(PrivacySettings);