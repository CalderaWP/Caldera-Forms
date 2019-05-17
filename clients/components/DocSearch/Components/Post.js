import React from 'react';
import {FeaturedImage} from "./FeaturedImage";
import {Grid, Row, Col} from 'react-bootstrap';
import url from '../functions/url';

export class Post extends React.Component {

    /**
     * Prepare excerpt to be shown
     *
     * Needed because WordPress REST API embeds html in excerpt
     *
     * @returns {{__html: string}}
     */
    createExcerpt() {
        return {__html: this.props.post.excerpt.rendered};
    }

    /**
     * Prepare title to be shown
     *
     * Needed because WordPress REST API might embed html in title. Often there are funny characters.
     *
     * @returns {{__html}}
     */
    createTitle() {
        return {__html: this.props.post.title.rendered};
    }

    render() {
        return (
            <Grid>
                <article
                    id={`post-${this.props.post.id}`}
                    className={`post-${this.props.post.id} row not-box hentry`}
                >
                    <Row>
                        <Col
                            xs={12}
                        >
                            <div
                                className="entry-header"
                                role="heading"
                            >
                                <h2
                                    className="entry-title"
                                >
                                    <a
                                        href={url(this.props.lastParams, this.props.post.link)}
                                        rel="bookmark"
                                    >
                                        <div
                                            dangerouslySetInnerHTML={this.createTitle()}
                                        />
                                    </a>
                                </h2>
                            </div>
                        </Col>
                    </Row>

                    <Row>
                        <Col
                            xs={12}
                            sm={3}
                            md={4}
                        >
                            <FeaturedImage
                                apiRoot={this.props.apiRoot}
                                post={this.props.post}
                                lastParams={this.props.lastParams}
                            />
                        </Col>
                        <Col
                            xs={12}
                            sm={12}
                            md={8}
                        >
                            <div
                                className="entry-content"
                            >
                                <div
                                    dangerouslySetInnerHTML={this.createExcerpt()}
                                />

                            </div>
                        </Col>
                        <Col
                            xs={12}
                        >
                            <a
                                href={url(this.props.lastParams, this.props.post.link)}
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