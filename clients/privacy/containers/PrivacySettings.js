import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
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
import {CalderaHeader} from "../../components/CalderaHeader";
import {PageBody} from "../../components/PageBody";
import {StatusIndicator} from "../../components/StatusIndicator";
import {DocLinks} from "../components/DocLinks";
import {
    startSpinner,
    stopSpinner,
    closeStatus,
    updateStatus
} from "../actions/status";

/**
 * Container component for privacy settings
 *
 * @since 1.7.0
 *
 * @param props
 * @returns {*}
 * @constructor
 */
export const PrivacySettings = (props) => {
    /**
     * Stop spinner after a delay
     *
     * @since 1.7.0
     *
     * @param {Integer} delay Optional. Number of milliseconds to delay hiding by.
     */
    function stopSpinner(delay=300) {
        setTimeout(() => {
            props.stopSpinner();
        },delay);
    }

    /**
     * Hide status after a delay
     *
     * @since 1.7.0
     *
     * @param {Integer} delay Optional. Number of milliseconds to delay hiding by.
     */
    function hideStatusIndicator(delay=2500) {
        setTimeout(() => {
            props.closeStatus();
        },delay);
    }

    /**
     * Report an API error using status component
     *
     * @since 1.7.0
     *
     * @param {Object} r
     */
    function reportApiError(r) {
        props.stopSpinner();
		if (r.hasOwnProperty('responseText')) {
			const response = JSON.parse(r.responseText);
			props.updateStatus(response.message, false);
		}else if( r.hasOwnProperty('message' ) ){
			props.updateStatus(r.message, false);
		}else{
			props.updateStatus('Error', false);

		}
       // hideStatusIndicator();
    }

    /**
     * When exporter is enabled for a change route changes
     *
     * @since 1.7.0
     *
     * @param {String} newFormsId
     */
    const onToggleEnable = (newFormsId) => {
        props.startSpinner();
        const notComplete = {
            f:false,
            p:false,
            stopped:true,
        };

        //Find form details
        const formRequest = requestForm(newFormsId);
        formRequest.then( (form) => {
            if( form.hasOwnProperty('responseText') ){
				reportApiError(form);
            }else{
				props.setForm(form,newFormsId);
				props.setEditForm(newFormsId);
            }

            delete notComplete.f;
            if( ! notComplete.hasOwnProperty('p') ){
                stopSpinner();
                notComplete.stopped =true;
            }
        }).catch( (r) => {
            reportApiError(r);
        });

        //Find privacy settings
        const privacySettingsRequest = requestPrivacySettings(newFormsId);
        privacySettingsRequest.then( (settings) => {
			if( settings.hasOwnProperty('responseText') ) {
			    reportApiError(settings);
			}else{
				props.setFormPrivacyForm(settings);
			}
            delete notComplete.p;
            if( ! notComplete.hasOwnProperty('f') ){
                stopSpinner();
                notComplete.stopped =true;
            }
        }).catch( (r) => {
			reportApiError(r);
        });

        //Make sure spinner gets stopped
        setTimeout(() => {
            if( false === notComplete.stopped){
                stopSpinner();
            }
        },2500);
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
        props.startSpinner();
        props.setFormPrivacyForm(newPrivacySettings);
        const update = requestUpdatePrivacySettings(newPrivacySettings,formId);
        update.then( (response) => {
            props.setFormPrivacyForm(response);
            props.unsetEditForm();
            props.stopSpinner();
            props.updateStatus('Settings Saved');
            hideStatusIndicator();
        }).catch( (r) => {
            reportApiError(r);
        });

    };

    const headerText = 'Privacy and Data Settings';
    if( props.editForm.hasOwnProperty( 'fields' ) ){
        if( ! props.formPrivacySettings.loaded ){
            return <p>Loading Settings</p>
        }

        return (
                <PageBody>
                    <CalderaHeader
                        name={headerText}
                    >

                        <li key="status-indicator">
                            <StatusIndicator
                                message={props.status.message}
                                show={props.status.show}
                                success={props.status.success}
                            />
                        </li>

                        {true === props.spin &&
                            <li key="spinner" className={'spinner is-active' }></li>
                        }

                    </CalderaHeader>
                    <FormPrivacySettings
                        privacySettings={props.formPrivacySettings.settings}
                        form={props.editForm}
                        onSave={onSaveForm}
                        onStateChange={props.setFormPrivacyForm}
                    />
                </PageBody>
            )

    }

    return (
        <div>
            <CalderaHeader
                name={headerText}
            >
                <li key="form-selector"
                    style={{
                        marginTop: '6px',
                        paddingLeft: '12px'
                    }}
                >
                    <FormSelectorNoGutenberg
                        forms={props.forms}
                        onChange={onToggleEnable}
                    />
                </li>
            </CalderaHeader>
            <PageBody>
                <p className={'screen-reader-text' }>Choose a form to begin</p>
                <div>
                    <StatusIndicator
                        message={props.status.message}
                        show={props.status.show}
                        success={props.status.success}
                    />
                    <DocLinks >
                        {'Strange errors after loading a form? Make sure pretty permalinks are enabled.'}
                    </DocLinks>
                </div>
                {true === props.spin &&
                    <p className={'spinner is-active' } />
                }
            </PageBody>
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
        },
        status: {
            show: state.statusState.show,
            message: state.statusState.message,
            success: state.statusState.success
        },
        spin: state.statusState.spin
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
    startSpinner,
    stopSpinner,
    closeStatus,
    updateStatus
};

export const PrivacySettingsWrapped = connect(
    mapStateToProps,
    mapDispatchToProps
)(PrivacySettings);