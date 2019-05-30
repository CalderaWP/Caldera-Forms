import React, {Component} from 'react'
import {Panel, PanelBody, PanelRow} from "@wordpress/components";
import {DocSearchApp} from '../../../components';
import ProDashboard from "./ProDashboard";
import Translate from "../components/Translate/Translate";
import {CalderaFormsUserSurvey} from "../components/CalderaFormsUserSurvey/CalderaFormsUserSurvey";

export default class DashboardPanels extends Component {

    constructor(props) {
        super(props);
        this.state = {
            translateClicked: false,
            translatePageLoaded: false,
            translatePageData:{},
            surveyCompleted: false,
        };
        this.onOpenTranslate = this.onOpenTranslate.bind(this);
        this.onSurveyCompleted = this.onSurveyCompleted.bind(this);
    }

    onOpenTranslate(){
        if (!this.state.translateClicked) {
            this.setState({translateClicked: true});
            fetch('https://calderaforms.com/wp-json/wp/v2/pages/140638', {
                mode: 'cors',
                redirect: 'follow',
                cache: "default"
            }).then(response => response.json())
                .catch(error => console.error('Error:', error))
                .then(response => {
                    this.setState(
                        {
                            translatePageLoaded: true,
                            translatePageData: response
                        }
                    )
                });
        }
    }

    onSurveyCompleted(){
        this.setState({surveyCompleted:true});
    }

    render() {
        const {apiRoot,isProConnected,showSurveyFirst} = this.props;
        const {translatePageData,translatePageLoaded,surveyCompleted} = this.state;

        return (
            <div className={'caldera-grid'}>
                <Panel header="Welcome To Caldera Forms">
                    <PanelBody
                        title="Documentation"
                        icon="welcome-widgets-menus"
                        initialOpen={false}
                    >
                        <PanelRow>
                            <div className={'caldera-grid'}>
                                <DocSearchApp apiRoot={apiRoot}/>
                            </div>
                        </PanelRow>
                    </PanelBody>
                    { showSurveyFirst  &&
                        <PanelBody
                            title="Caldera Forms User Survey"
                            icon="welcome-widgets-menus"
                            initialOpen={false === surveyCompleted}
                        >
                            <PanelRow>
                                <div className={'caldera-grid'}>
                                    <CalderaFormsUserSurvey
                                        apiRoot={'https://dev-futurecapable.pantheonsite.io/wp-json/caldera-api/v1/messages/mailchimp/v1'}
                                        listId={'f402a6993d'}
                                        onSubmit={this.onSurveyCompleted}
                                    />
                                </div>
                            </PanelRow>
                        </PanelBody>
                    }
                    <PanelBody
                        title="Go Pro!"
                        icon="thumbs-up"
                        initialOpen={showSurveyFirst}
                    >
                        <ProDashboard isProConnected={isProConnected}/>
                    </PanelBody>
                    <PanelBody
                        title="Translate Your Forms"
                        icon="translation"
                        initialOpen={false}
                        onToggle={this.onOpenTranslate}
                    >
                        <PanelRow>
                            <div className={'caldera-grid'}>
                                {translatePageLoaded &&
                                    <Translate post={translatePageData} apiRoot={apiRoot}/>
                                }
                            </div>
                        </PanelRow>
                    </PanelBody>
                </Panel>

            </div>


        );
    }

}

DashboardPanels.defaultProps = {
    apiRoot: 'https://calderaforms.com/wp-json',
    isProConnected: false,
    showSurveyFirst: true,
};
