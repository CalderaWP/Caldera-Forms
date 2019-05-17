import {Fragment} from "react";
import {FeaturedImage, PostExcerpt, PostTitle} from "./index";
import {Card} from 'react-bootstrap';

export default function PostCard({post, lastParams = {}, apiRoot}) {
    return  (
        <Card style={{ width: '18rem' }}>
            <FeaturedImage post={post} apiRoot={apiRoot} lastParams={lastParams}/>
            <Card.Body>
                <Card.Title><PostTitle post={post}/></Card.Title>
                <Card.Text>
                    <PostExcerpt post={post}/>
                </Card.Text>
                <Button variant="primary">Go somewhere</Button>
            </Card.Body>
        </Card>
    )
    return <Fragment>
        <FeaturedImage post={post} apiRoot={apiRoot} lastParams={lastParams}/>
        <PostTitle post={post}/>
        <PostExcerpt post={post}/>
    </Fragment>;
}
