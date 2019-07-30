import React from 'react';
import {RemotePost} from "../../RemotePost/";

export class Results extends React.Component {
    render() {
        return (
            <div>
                {
                    this.props.posts.map((post) => {
                        return (
                            <div key={post.id}>
                                <RemotePost
                                    key={post.id}
                                    apiRoot={this.props.apiRoot}
                                    post={post}
                                    lastParams={this.props.lastParams}
                                />
                            </div>
                        );
                    })
                }
            </div>

        );
    }
}