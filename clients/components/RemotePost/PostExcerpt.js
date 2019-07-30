import React from "react";

export default function PostExcerpt({post}){
    /**
     * Prepare excerpt to be shown
     *
     * Needed because WordPress REST API embeds html in excerpt
     *
     * @returns {{__html: string}}
     */
    function createExcerpt() {
        return {__html: post.excerpt.rendered};
    }

    return (
        <div
            className="entry-content"
        >
            <div
                dangerouslySetInnerHTML={createExcerpt()}
            />

        </div>
    )
}