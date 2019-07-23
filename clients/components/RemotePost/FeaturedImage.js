import React from 'react';
import axios from 'axios';
import Image from './Image';
import url from '../functions/url';
import {cacheAdapterEnhancer} from 'axios-extensions';

export class FeaturedImage extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            loaded: true,
            alt_text: '',
            width: 1484,
            height: 592,
            source_url: 'https://calderaforms.com/wp-content/uploads/2017/12/forms_copy.jpg',

            markup: '',
        };

    }

    componentDidMount() {
        axios({
            method: 'get',
            url: `${this.props.apiRoot}/wp/v2/media`,
            params: {
                parent: this.props.post.id
            },
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
        return (
            <div>
                <a
                    href={url(this.props.lastParams, this.props.post.link)}
                >
                    <Image
                        className=""
                        alt={this.state.alt_text}
                        width={this.state.width}
                        height={this.state.height}
                        src={this.state.source_url}
                    />
                </a>

            </div>

        )

    }


}