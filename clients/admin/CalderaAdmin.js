import {Component} from 'react';

import {Admin} from '@caldera-labs/components'
import Grid from 'react-css-grid';
import PropTypes from 'prop-types';
import {FormsSection} from "./containers/FormsSection";
import {TopNav} from "./components/TopNav/TopNav";

export class CalderaAdmin extends Component {

	constructor(props){
		super(props);
		this.openEntryViewerForForm = this.openEntryViewerForForm.bind(this);
		this.onFormUpdate = this.onFormUpdate.bind(this);
		this.state = {
			isLoading: true,
			showStatus: false,
		}
	}

	onFormUpdate(form){
		console.log(form);
	};

	openEntryViewerForForm(form){
		console.log(form);
	};

	placeholderHandler(something){
		console.log('placeholderHandler');
		console.log(something);
	}

	render() {
		const {forms} = this.props;
		const{
			isLoading,
			showStatus,
		} = this.state;
		return (
			<Admin.PageBody>
				<TopNav
					onClickNewForm={this.placeholderHandler}
					onClickSettings={this.placeholderHandler}
					mainStatus={
						{
							loading:isLoading,
							show: showStatus
						}
					}
				/>
				<FormsSection
					hasForms={forms.length > 0 }
					onFormUpdate={this.onFormUpdate}
					openEntryViewerForForm={this.openEntryViewerForForm}
					forms={forms}
				/>
			</Admin.PageBody>


		);
	}
}

CalderaAdmin.propTypes = {
	forms: PropTypes.array,

}