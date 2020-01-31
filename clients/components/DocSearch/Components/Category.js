import React from 'react'

export class Category extends React.Component {
    constructor(props){
        super(props);
        this.state = {
            idAttr: `category-search-${props.category}`
        }
    }

    render() {
        return (
            <div className="checkbox">
                
                    <label
                        htmlFor={this.state.idAttr}
                        className="control-label"
                    >
                        {this.props.label}
                    </label>
                    <input
                        type="checkbox"
                        value="true"
                        checked={this.props.checked}
                        onChange={this.props.onChange}
                        id={this.state.idAttr}
                    />
                
            </div>
        )


    }
}
