import {Panel, PanelBody, PanelRow} from "@wordpress/components";
import {DocSearchApp} from '../../../components';
import {RemotePost} from "../../../components";
import ProDashboard from "./ProDashboard";
import {Component} from "@wordpress/element";
import Translate from "../components/Translate/Translate";

export default class DashboardPanels extends Component {

    constructor(props) {
        super(props);
        this.state = {
            translateClicked: false,
            translatePageLoaded: false,
            translatePageData:{},
        };
        this.onOpenTranslate = this.onOpenTranslate.bind(this);
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

    render() {
        const {apiRoot,isProConnected} = this.props;
        const {translatePageData,translatePageLoaded} = this.state;
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
                    <PanelBody
                        title="Go Pro!"
                        icon="thumbs-up"
                        initialOpen={true}
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
}