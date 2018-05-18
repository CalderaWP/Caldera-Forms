import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux'
import {
    setForm,
    setEditForm,
    unsetEditForm,
    setFormPrivacyForm
} from "../actions";
import {getFormPrivacySettings} from "../selectors/privacy";
import {
    requestForm,
    requestPrivacySettings,
    requestUpdatePrivacySettings
} from "../../state/api";
import {FormPrivacySettings} from "../components/FormPrivacySettings";
import {FormSelectorNoGutenberg} from "../../components/FormSelectorNoGutenberg";

/**
 * Container componet for privacy settings
 *
 * @since 1.7.0
 *
 * @param props
 * @returns {*}
 * @constructor
 */
export const PrivacySettings = (props) => {
    /**
     * When exporter is enabled for a change route changes
     *
     * @since 1.7.0
     *
     * @param {String} newFormsId
     */
    const onToggleEnable = (newFormsId) => {
        const formRequest = requestForm(newFormsId);
        formRequest.then( (form) => {
            props.setForm(form,newFormsId);
            props.setEditForm(newFormsId);
        });
        const privacySettingsRequest = requestPrivacySettings(newFormsId);
        privacySettingsRequest.then( (settings) => {
            props.setFormPrivacyForm(settings)
        });
    };

    /**
     * When a form is saved, route changes, actions, etc.
     *
     * @since 1.7.0
     *
     * @param {Object} newPrivacySettings
     * @param {String} formId
     */
    const onSaveForm = (newPrivacySettings,formId) => {
        props.setFormPrivacyForm(newPrivacySettings);
        const update = requestUpdatePrivacySettings(newPrivacySettings,formId);
        update.then( (response) => {
            props.setFormPrivacyForm(response);
            props.unsetEditForm();
        });


    };

    if( props.editForm.hasOwnProperty( 'fields' ) ){
        if( ! props.formPrivacySettings.loaded ){
            return <p>Loading Settings</p>
        }
        return <FormPrivacySettings
            privacySettings={props.formPrivacySettings.settings}
            form={props.editForm}
            onSave={onSaveForm}
            onStateChange={props.setFormPrivacyForm}
        />
    }

    return (
        <div>
            <FormSelectorNoGutenberg
                forms={props.forms}
                onChange={onToggleEnable}
            />
        </div>

    )
};

PrivacySettings.propTypes = {
    forms: PropTypes.array.isRequired,
    editForm: PropTypes.object,
};

const mapStateToProps = state => {
    let props = {
        forms : state.formState.forms,
        editForm: state.formState.editForm,
        roy: 'hi',
        formPrivacySettings: {
            loaded:false
        }
    };

    if( props.editForm.hasOwnProperty('fields') ){
        const settings =  getFormPrivacySettings(props.editForm.ID, state);
        if (settings) {
            props.formPrivacySettings = {
                ...{loaded: true},
                settings
            }
        }

    }

    return props;
};

const mapDispatchToProps = {
    setEditForm,
    setForm,
    unsetEditForm,
    requestPrivacySettings,
    setFormPrivacyForm,
};

export const PrivacySettingsWrapped = connect(
    mapStateToProps,
    mapDispatchToProps
)(PrivacySettings);