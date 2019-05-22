import {Component} from '@wordpress/element';
import axios from "axios";
import {cacheAdapterEnhancer} from "axios-extensions";
import {ProEnterApp} from "../../../../components/ProSettings";
import {ProFreeTrial} from "../../../../components/ProSettings";
import url from "../../../../components/functions/url";
export class Addons extends Component {


    constructor(props) {
        super(props);
        this.state = {
            paymentAddonsLoaded: false,
            emailAddonsLoaded: false,
            paymentAddons: {},
            emailAddons: {},
            toolsAddonsLoaded: false,
            toolsAddons: {}
        };
    }

    componentDidMount() {
        const stripTrailingSlash = (str) => {
            return str.endsWith('/') ?
                str.slice(0, -1) :
                str;
        };

        const cacheOptions = { enabledByDefault: true };
        const {
            paymentAddonsLoaded,
            emailAddonsLoaded,
            toolsAddonsLoaded,
            toolsAddons
        } = this.state;
        const {apiRoot} = this.props;
        if (!paymentAddonsLoaded) {
            axios({
                method: 'get',
                url: `${stripTrailingSlash(apiRoot)}/calderawp_api/v2/products/cf-addons?category=payment`,
                adapter: cacheAdapterEnhancer(axios.defaults.adapter, cacheOptions )
            }).then((response) => {
                this.setState({
                    paymentAddons: response.data,
                    paymentAddonsLoaded: true
                })

            });
        }

        if (!emailAddonsLoaded) {
            axios({
                method: 'get',
                url: `${stripTrailingSlash(apiRoot)}/calderawp_api/v2/products/cf-addons?category=email`,
                adapter: cacheAdapterEnhancer(axios.defaults.adapter, cacheOptions )
            }).then((response) => {
                this.setState({
                    emailAddons: response.data,
                    emailAddonsLoaded: true
                })

            });
        }if (!toolsAddonsLoaded) {
            axios({
                method: 'get',
                url: `${stripTrailingSlash(apiRoot)}/calderawp_api/v2/products/cf-addons?category=tools`,
                adapter: cacheAdapterEnhancer(axios.defaults.adapter, cacheOptions )
            }).then((response) => {
                const good = [5070,43154,56332,2465,1940,55330,1950,1934,37221,82236,1223];
                const toolsAddons = [];
                Object.values(response.data).forEach(addon => {
                    if( good.includes(addon.id)){
                        toolsAddons.push(addon);
                    }
                });
                this.setState({
                    toolsAddons,
                    toolsAddonsLoaded: true
                })

            });
        }


    }


    render() {
        const {proConnected} = this.props;
        const {
            paymentAddonsLoaded,
            emailAddonsLoaded,
            paymentAddons,
            emailAddons,
            toolsAddonsLoaded,
            toolsAddons
        } = this.state;

        function Loading() {
            return <div>Loading</div>
        }

        function AddonPanel({addon,category}) {
            const {image_src,tagline,link,name} = addon;
            return (

                <div className="addon-panel">
                    <img src={image_src}
                         style={{
                             width: '100%',
                             'vertical-align': 'top'
                         }}

                    />
                    <h2>{name}</h2>
                    <div
                        style={{
                            margin: '0px',
                            'padding': '6px 7px'
                        }}
                    >
                        {tagline}
                    </div>

                    <div class="panel-footer">

                        <a class="button" href={url({source: 'cf-admin-app',categories:category}, link)} target="_blank" rel="nofollow" style={{
                            width: '100%'
                        }}>
                            Learn More
                        </a>
                    </div>
                </div>

            )
        }

        function AddonsPanel({addons,category}) {
            return (

                <div id="cf-addons">
                    {
                        addons.map(addon => {
                            return (<AddonPanel addon={addon} category={category}/>)
                        })
                    }
                </div>
            );

        }

        const {show} = this.props;
        switch (show) {
            case 'email':
                if (emailAddonsLoaded) {
                    return <AddonsPanel addons={Object.values(emailAddons)} category={'email'}/>
                } else {
                    return <Loading/>
                }
            case 'payment':
                if (paymentAddonsLoaded) {
                    return <AddonsPanel addons={Object.values(paymentAddons)} category={'paymen'}/>
                } else {
                    return <Loading/>
                }
            case 'tools':
                if (toolsAddonsLoaded) {
                    return <AddonsPanel addons={Object.values(toolsAddons)} category={'tools'}/>
                } else {
                    return <Loading/>
                }

            case 'pro':
            default:
                if( proConnected ){
                    return  <ProEnterApp/>
                }
                return  <ProFreeTrial />

        }
    }

}

Addons.defaultProps = {
    apiRoot: 'https://calderaforms.com/wp-json',
    show: 'email',
    proConnected: false
};