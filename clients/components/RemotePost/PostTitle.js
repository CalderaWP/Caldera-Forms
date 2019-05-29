import url from "../functions/url";
import React from "react";

export default function PostTitle({post}) {
    /**
     * Prepare title to be shown
     *
     * Needed because WordPress REST API might embed html in title. Often there are funny characters.
     *
     * @returns {{__html}}
     */
    function createTitle() {
        return {__html: post.title.rendered};
    }

    return (
        <div
            className="entry-header"
            role="heading"
        >
            <h2
                className="entry-title"
            >
                <a
                    href={url({}, post.link)}
                    rel="bookmark"
                    target={'_blank'}
                >
                    <div
                        dangerouslySetInnerHTML={createTitle()}
                    />
                </a>
            </h2>
        </div>
    )
}