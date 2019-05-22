import {TabPanel} from "@wordpress/components";
import {ProWhatIs} from "../../../components/ProSettings";

const onSelect = () => {
};
import {Addons} from "../components/AddOns/Addons";
import {Fragment} from "react";

export default function ProDashboard({isProConnected,apiRoot}) {
    return (
        <Fragment>
            <ProWhatIs />

            <TabPanel className="cf-pro-dashboard-panels"
                      activeClass="active-tab"
                      onSelect={onSelect}
                      tabs={[
                          {
                              name: 'pro',
                              title: 'Pro: Email Delivery & Anti-Spam',
                              className: 'pro-pro',
                          },
                          {
                              name: 'email',
                              title: 'Email Marketing & CRMs',
                              className: 'pro-email',
                          },
                          {
                              name: 'payment',
                              title: 'Payment Gateways',
                              className: 'pro-payment',
                          },
                          {
                              name: 'tools',
                              title: 'Tools',
                              className: 'tools',
                          },
                      ]}>
                {
                    (tab) => (
                        <Addons
                            isProConnected={isProConnected}
                            apiRoot={apiRoot}
                            show={tab.name}
                        />
                    )
                }
            </TabPanel>
        </Fragment>
    );
}

ProDashboard.defaultProps =  {
    apiRoot: 'https://calderaforms.com/wp-json',
    isProConnected: false,
};