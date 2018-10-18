import {Component} from 'react';

import {Admin} from '@caldera-labs/components'
import Grid from 'react-css-grid';
import PropTypes from 'prop-types';
import {FormsSection} from "./containers/FormsSection";
import {TopNav} from "./components/TopNav/TopNav";
import {ClassicEntryViewer} from "./components/ClassicEntryViewer";
import {findFormById} from "../state/actions/functions";
import {NewForm} from "./components/NewForm/NewForm";
import {Settings} from "./components/Settings/Settings";

export class CalderaAdmin extends Component {

	constructor(props){
		super(props);
		this.openEntryViewerForForm = this.openEntryViewerForForm.bind(this);
		this.onFormUpdate = this.onFormUpdate.bind(this);
		this.onCloseEntryViewer = this.onCloseEntryViewer.bind(this);
		this.toggleShowSettings = this.toggleShowSettings.bind(this);
		this.toggleShowNewForm = this.toggleShowNewForm.bind(this);
		this.state = {
			isLoading: true,
			showStatus: false,
			entryViewerFormId: '',
			showEntryViewer: false,
			showSettings: false,
			showNewForm: false,
		}
	}


	onCreateForm(newForm){

	}

	onFormUpdate(form){
		console.log(form);
	};

	toggleShowNewForm(){
		this.setState({
			showNewForm: ! this.state.showNewForm
		});
	}

	toggleShowSettings(){
		this.setState({
			showSettings: ! this.state.showSettings
		});
	}

	openEntryViewerForForm(entryViewerFormId){
		this.setState({entryViewerFormId,showEntryViewer:true});
	};

	onCloseEntryViewer(){
		this.setState({showEntryViewer:false});

	}
	placeholderHandler(something){
		console.log('placeholderHandler');
		console.log(something);
	}


	inside(state,props){

	}

	render() {
		const {
			templates,
			forms
		} = this.props;
		const{
			isLoading,
			showStatus,
			showEntryViewer,
			entryViewerFormId,
			showNewForm,
			showSettings
		} = this.state;
		const entryViewerForm = forms.find( form => form.ID === entryViewerFormId );
		const showFormArea = false === showSettings && false === showNewForm;
		return(
			<Admin.PageBody>
				<TopNav
					onClickNewForm={this.toggleShowNewForm}
					onClickSettings={this.toggleShowSettings}
					mainStatus={
						{
							loading: isLoading,
							show: showStatus
						}
					}
				/>
				<Grid>
					{showSettings &&
						<Settings/>
					}
					{showNewForm &&
						<NewForm
							onCreate={this.onCreateForm}
							templates={templates}
						/>
					}
				</Grid>

				{showFormArea &&
					<Grid>
						{!showEntryViewer &&
							<FormsSection
								hasForms={forms.length > 0}
								onFormUpdate={this.onFormUpdate}
								openEntryViewerForForm={this.openEntryViewerForForm}
								forms={forms}
							/>
						}
						{showEntryViewer &&
							<ClassicEntryViewer
								form={entryViewerForm}
								onClose={this.onCloseEntryViewer}
							/>
						}

					</Grid>
				}

			</Admin.PageBody>


		);
	}


}

CalderaAdmin.propTypes = {
	forms: PropTypes.array,
	templates: PropTypes.object

}