import React from 'react';
import {Post} from "./Post";

export class Results extends React.Component {
    render() {
        return (
            <div>
                {
                    this.props.posts.map((post) => {
                        return (
                            <div key={post.id}>
                                <Post
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