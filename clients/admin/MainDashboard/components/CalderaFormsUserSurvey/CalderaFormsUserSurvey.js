import {Component, Fragment} from "@wordpress/element";
import {CalderaMailChimpSurveyForm} from '@calderajs/forms';

export class CalderaFormsUserSurvey extends Component {


    constructor(props){
        super(props);
        this.state = {
          token: '',
        };
    }

    componentDidMount(){
        const {apiRoot} = this.props;
        fetch(`${apiRoot}/token`, {
            method: 'POST'
        })
            .then(r => r.json())
            .then(r => {
                this.setState({token:r.token});
            })
            .catch(e => console.log(e));
    }
    render() {
        const {listId, apiRoot, onSubmit, Loading} = this.props;
        const {token} = this.state;
        if (!token) {
            if (Loading) {
                return <Loading/>
            }
            return <Fragment/>
        }


        return (
            <CalderaMailChimpSurveyForm
                token={token}
                apiRoot={apiRoot}
                listId={listId}
                onSubmit={onSubmit}
            />
        )
    }


}
