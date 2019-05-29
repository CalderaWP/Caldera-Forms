import axios from 'axios';
import React from 'react';
import { FormGroup } from 'react-bootstrap';
import { Catdera } from '../../Catdera/Catdera';
import { Category } from "./Category";
import { cacheAdapterEnhancer } from 'axios-extensions';

export class AddonCategory extends React.Component {
    constructor(props){
        super(props);
        this.state = {
            loaded: false,
            category:{
                name: 'Placeholder Name'
            },
            idAttr: `add-on-search-${this.props.category}`
        }
    }

    componentDidMount(){
        return axios({
            method: 'get',
            url: `${this.props.apiRoot}/wp/v2/categories/${this.props.category}`,
            adapter: cacheAdapterEnhancer(axios.defaults.adapter, true)
        })
            .then( (response) => {
                setTimeout(() => {
                    this.setState({loaded: true});
                }, 250 );
                this.setState({category: response.data});
            });
    }
    render(){
        return(
            <FormGroup>
                {!this.state.loaded &&
                    <Catdera
                        classname="App-catderaspin"
                        width={10}
                    />
                }
                {this.state.loaded &&
                    <Category
                        checked={this.props.checked}
                        onChange={this.props.onChange}
                        category={this.props.category}
                        label={this.state.category.name}

                    />
                }

            </FormGroup>

        )
    }
}