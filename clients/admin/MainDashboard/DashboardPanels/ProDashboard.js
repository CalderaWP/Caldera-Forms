import {TabPanel} from "@wordpress/components";

const onSelect = () => {};
export default function ProDashboard(){
    return(
        <TabPanel className="my-tab-panel"
                  activeClass="active-tab"
                  onSelect={ onSelect }
                  tabs={ [
                      {
                          name: 'pro',
                          title: 'Caldera Forms Pro',
                          className: 'pro-pro',
                      },
                      {
                          name: 'email',
                          title: 'Email Marketing Add-ons',
                          className: 'pro-email',
                      },
                      {
                          name: 'payment',
                          title: 'Payment Gateways',
                          className: 'pro-payment',
                      },
                      {
                          name: 'allAddOns',
                          title: 'All Add-ons ',
                          className: 'all-add-ons',
                      },
                  ] }>
            {
                ( tab ) => <p>{ tab.title }</p>
            }
        </TabPanel>
    );
}