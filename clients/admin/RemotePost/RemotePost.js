import React from 'react';
import {postPropTypes} from "./propTypes";
import EmbedContainer from 'react-oembed-container';
import PropTypes from 'prop-types'

/**
 * The main container component for the RemotePost
 *
 * @param {Object} props
 * @return {*}
 * @constructor
 */
export const RemotePost = (props) => {
	const {post} = props;

	const Content = () => {
		if (props.showExcerpt) {
			return (<div dangerouslySetInnerHTML={{__html: post.excerpt.rendered}}/>);

		}
		return (
			<div dangerouslySetInnerHTML={{__html: post.content.rendered}}/>
		);

	}
	return (
		<EmbedContainer
			markup={post.content.rendered}
			className={props.className}

		>
			<article
				id={`post-${post.id}`}
			>
				<h2>
					{post.title.rendered}
				</h2>
				{Content()}
				<a
					href={props.post.href}
					target="_blank"
					className={props.buttonClassName}

				>
					{props.readMore}
				</a>
			</article>

		</EmbedContainer>
	);
};

RemotePost.propTypes = {
	...postPropTypes,
	className: PropTypes.string,
	showExcerpt: PropTypes.bool,
	readMore: PropTypes.string

};

RemotePost.defaultProps = {
	showExcerpt: true,
	readMore: 'Learn More',
	buttonClassName: 'btn btn-green'

}
