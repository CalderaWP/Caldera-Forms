import React from 'react';
import Grid from 'react-css-grid';
import {sortFormsBy} from "../components/FormsList/sortFormsBy";
import {FormListSort, SORT_FORMS_BY_NAME} from "../components/FormsList/FormListSort";
import {FormList } from "../components/FormsList/FormList";
import {Notice} from '@wordpress/components'
import PropTypes from 'prop-types';
import {CalderaAdmin} from "../CalderaAdmin";


const filter = require('lodash.filter');

/**
type Props = {
	...formState,
	...formEntryViewerState,
	...calderaAdminProps
};

type State = {
	showFormList: boolean,
	formOrderBy: string,
	orderedForms: Object,
	formSearchTerm: string
};
*/
export class FormsSection extends React.Component {


	/**
	 * Create the section of screen that shows forms and their entries
	 * @param props
	 */
	constructor(props) {
		super(props);
		this.state = {
			showFormList: true,
			formOrderBy: 'name',
			orderedForms: props.forms,
			formSearchTerm: ''
		};

		this.onChangeFormOrder = this.onChangeFormOrder.bind(this);
		this.onFormSearch = this.onFormSearch.bind(this);
	}

	/**
	 * When form order changes, resort forms
	 * @param formOrderBy
	 */
	onChangeFormOrder(formOrderBy) {
		this.setState({
			formOrderBy,
			orderedForms: sortFormsBy(formOrderBy, this.props.forms)
		});

	}

	/**
	 * Handle form searches
	 *
	 * @param formSearchTerm
	 */
	onFormSearch(formSearchTerm) {
		const {orderedForms,formOrderBy} = this.state;
		if (formSearchTerm) {
			const newOrderedForms = filter(orderedForms, (form) => {
				return form.name.includes(formSearchTerm);
			});

			this.setState({
				formSearchTerm,
				orderedForms:newOrderedForms
			});
		} else {
			this.setState({
				formSearchTerm,
				orderedForms: sortFormsBy(
					formOrderBy,
					this.props.forms
				)
			});
		}

	}


	/**
	 * Render FormSection component
	 * @return {*}
	 */
	render() {
		const {entryPage,hasForms,openEntryViewerForForm,onFormUpdate} = this.props;
		if (!hasForms) {
			return (
				<Notice
					status="error"
					isDismissible
				>
					No Forms Found
				</Notice>
			)
		}
		const {orderedForms, formOrderBy, formSearchTerm} = this.state;

		return (
			<Grid>
				<div>
					<FormListSort
						order={formOrderBy}
						onChangeOrder={this.onChangeFormOrder}
						onFormSearch={this.onFormSearch}
						formSearchTerm={formSearchTerm}
					/>
					<FormList
						hasForms={hasForms}
						forms={orderedForms}
						onFormUpdate={onFormUpdate}
						openEntryViewerForForm={openEntryViewerForForm}
					/>
				</div>

			</Grid>

		)
	}
}

FormsSection.propTypes = {
	hasForms: PropTypes.bool.isRequired,
	forms: PropTypes.array.isRequired,
	onFormUpdate: PropTypes.func.isRequired,
	openEntryViewerForForm: PropTypes.func.isRequired
};