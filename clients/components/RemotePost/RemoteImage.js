import React from 'react';
import axios from 'axios';
import Image from './Image';
import url from '../functions/url';
import {cacheAdapterEnhancer} from 'axios-extensions';

export default class RemoteImage extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            loaded: true,
            alt_text: '',
            width: 1484,
            height: 592,
            source_url: 'https://calderaforms.com/wp-content/uploads/2019/03/canva-ebook-Caldera-Forms-1.png',

            markup: '',
        };

    }

    componentDidMount() {
        const stripTrailingSlash = (str) => {
            return str.endsWith('/') ?
                str.slice(0, -1) :
                str;
        };

        const {imageId,apiRoot} = this.props;
        axios({
            method: 'get',
            url: `${stripTrailingSlash(apiRoot)}/wp/v2/media/${imageId}`,
            adapter: cacheAdapterEnhancer(axios.defaults.adapter, true)
        }).then((response) => {
            const newImage = response.data[0];
            if (newImage && newImage.hasOwnProperty('alt_text')) {
                this.setState({
                    alt_text: newImage.alt_text,
                    source_url: newImage.source_url,
                    width: newImage.media_details.width,
                    height: newImage.media_details.height,
                    markup: newImage.hasOwnProperty('description') ? newImage.description.rendered : ''
                });
            }

        })
    }



    render() {
        const {link} = this.props;
        return (
            <div>
                <a
                    href={link}
                    target={'_blank'}
                >
                    <Image
                        className=""
                        alt={this.state.alt_text}
                        width={this.state.width}
                        height={this.state.height}
                        src={this.state.source_url}
                        style={{
                            width: '100%',
                            height: 'auto'
                        }}
                    />
                </a>

            </div>

        )

    }


}