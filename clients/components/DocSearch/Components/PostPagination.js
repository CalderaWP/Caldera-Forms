import React from 'react';
import { Pagination } from 'react-bootstrap';

export class PostPagination extends React.Component {

    constructor(props){
        super(props);
        this.disablePrev = this.disablePrev.bind(this);
        this.disableNext = this.disableNext.bind(this);
        this.showPagination = this.showPagination.bind(this);
    }

    disablePrev(){
        return this.props.page === 1;
    }
    disableNext(){
        return this.props.page >= this.props.pages;
    }
    showPagination(){
        return 1 === this.props.pages;
    }

    render() {
        return (
            <div>

                    <Pagination.Item
                        disabled={this.disablePrev()}
                        previous
                        href="#"
                        onClick={this.props.prevHandler}
                    >
                        Previous Page
                    </Pagination.Item>
                    <Pagination.Item
                        disabled={this.disableNext()}
                        next
                        href="#"
                        onClick={this.props.nextHandler}
                    >
                        Next Page
                    </Pagination.Item>


                <ul className="list-inline text-center">
                    <li className="sr-only">Page: {this.props.page}</li>
                    <li className="sr-only">Total: {this.props.pages}</li>
                </ul>

            </div>
        )
    }
}