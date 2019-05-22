import {TabPanel} from "@wordpress/components";
import {ProWhatIs} from "../../../components/ProSettings";

const onSelect = () => {
};
import {Addons} from "../components/AddOns/Addons";
import {Fragment} from "react";

export default function ProDashboard() {
    return (
        <Fragment>
            <ProWhatIs />

            <TabPanel className="cf-pro-dashboard-panels"
                      activeClass="active-tab"
                      onSelect={onSelect}
                      tabs={[
                          {
                              name: 'pro',
                              title: 'Caldera Forms Pro',
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
                            show={tab.name}
                        />
                    )
                }
            </TabPanel>
        </Fragment>
    );
}