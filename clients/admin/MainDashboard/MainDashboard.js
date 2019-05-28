import {Panel, PanelBody, PanelRow, TabPanel} from "@wordpress/components";
import {DocSearchApp} from "../../components/DocSearch";
import DashboardPanels from './DashboardPanels';


/**
 *
 */
export default function MainDashboard({isProConnected}) {
    return (
        <DashboardPanels
            showSurveyFirst={true}
            isProConnected={isProConnected}
            apiRoot={'https://calderaforms.com/wp-json'}
        />
    );

}
