import { Panel, PanelBody, PanelRow } from "@wordpress/components";
import { __ } from "@wordpress/i18n";
import { DocSearchApp } from "../../../components";
import { RemotePost } from "../../../components";
import ProDashboard from "./ProDashboard";
import { Component } from "@wordpress/element";
import Translate from "../components/Translate/Translate";

export default class DashboardPanels extends Component {
	constructor(props) {
		super(props);
		this.state = {
			translateClicked: false,
			translatePageLoaded: false,
			translatePageData: {}
		};
		this.onOpenTranslate = this.onOpenTranslate.bind(this);
	}

	onOpenTranslate() {
		if (!this.state.translateClicked) {
			this.setState({ translateClicked: true });
			fetch("https://calderaforms.com/wp-json/wp/v2/pages/140638", {
				mode: "cors",
				redirect: "follow",
				cache: "default"
			})
				.then(response => response.json())
				.catch(error => console.error("Error:", error))
				.then(response => {
					this.setState({
						translatePageLoaded: true,
						translatePageData: response
					});
				});
		}
	}

	render() {
		const { apiRoot, isProConnected } = this.props;
		const { translatePageData, translatePageLoaded } = this.state;
		return (
			<div className={"caldera-grid"}>
				<Panel header={__("Welcome To Caldera Forms", "caldera-forms")}>
					<PanelBody
						title={__("Documentation", "caldera-forms")}
						icon="welcome-widgets-menus"
						initialOpen={false}
					>
						<PanelRow>
							<div className={"caldera-grid"}>
								<DocSearchApp apiRoot={apiRoot} />
							</div>
						</PanelRow>
					</PanelBody>
					<PanelBody
						title={__("Get more!", "caldera-forms")}
						icon="thumbs-up"
						initialOpen={true}
					>
						<ProDashboard isProConnected={isProConnected} />
					</PanelBody>
					<PanelBody
						title={__("Translate Your Forms", "caldera-forms")}
						icon="translation"
						initialOpen={false}
						onToggle={this.onOpenTranslate}
					>
						<PanelRow>
							<div className={"caldera-grid"}>
								{translatePageLoaded && (
									<Translate post={translatePageData} apiRoot={apiRoot} />
								)}
							</div>
						</PanelRow>
					</PanelBody>
				</Panel>
			</div>
		);
	}
}

DashboardPanels.defaultProps = {
	apiRoot: "https://calderaforms.com/wp-json",
	isProConnected: false
};
