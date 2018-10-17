import React from 'react';
//import type {wpPost} from "../../types/types/wpPost";
import Grid from 'react-css-grid';
import {RemotePost} from "./RemotePost";

export const RemotePosts = (props) => {
	const {posts} = props;
	const readMore = props.readMore ? props.readMore : 'Learn More';
	return (
		<Grid>
			{
				posts.map(function (post) {
					return (
						<RemotePost
							post={post}
							key={post.id}
							readMore={readMore}
						/>
					)
				})
			}
		</Grid>
	)
};