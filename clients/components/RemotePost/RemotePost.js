import React from 'react';
import {FeaturedImage} from "./FeaturedImage";
import {Grid, Row, Col} from 'react-bootstrap';
import url from '../functions/url';
import PostExcerpt from './PostExcerpt';
import PostTitle from './PostTitle';
export class RemotePost extends React.Component {





    render() {
        const {post,lastParams,apiRoot} = this.props;
        return (
            <Grid>
                <article
                    id={`post-${post.id}`}
                    className={`post-${post.id} row not-box hentry`}
                >
                    <Row>
                        <Col
                            xs={12}
                        >

                        </Col>
                    </Row>

                    <Row>
                        <Col
                            xs={12}
                            sm={3}
                            md={4}
                        >
                            <FeaturedImage
                                apiRoot={apiRoot}
                                post={post}
                                lastParams={lastParams}
                            />
                        </Col>
                        <Col
                            xs={12}
                            sm={12}
                            md={8}
                        >
                            <PostExcerpt post={post}/>
                        </Col>
                        <Col
                            xs={12}
                        >
                            <a
                                href={url(lastParams, post.link)}
                                target="_blank"
                                className="btn btn-green btn-block btn-readmore"
                                rel="bookmark"
                            >
                                Read More
                            </a>
                        </Col>
                    </Row>


                    <footer className="entry-footer">
                    </footer>


                </article>
            </Grid>


        )
    }

}