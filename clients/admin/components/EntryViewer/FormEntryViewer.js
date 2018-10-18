import React from "react";
import PropTypes from 'prop-types';
import {EntryViewer} from "./EntryViewer";
import {Entry} from "./Entry";
import {getFormColumns} from "./getFormColumns";
import getFormRows from "./getFormRows";
import Grid from 'react-css-grid'
import {RenderGroup} from '@caldera-labs/components';
import {Button} from '@wordpress/components';

const gridCollapse = 320;
const gridGap = 24;


/**
 * Encapsulates the UI for viewing the saved entries of a form.
 */
export class FormEntryViewer extends React.PureComponent {


	/**
	 *
	 * @param props
	 */
	constructor(props) {
		super(props);
		this.state = {
			currentEntry: 0,
			perPage: 20,
			entryListOnly: true
		};
		this.setCurrentEntry = this.setCurrentEntry.bind(this);
		this.onEntryAction = this.onEntryAction.bind(this);
		this.getEntryFields = this.getEntryFields.bind(this);
		this.entriesGrid = this.entriesGrid.bind(this);
		this.getTotalPages = this.getTotalPages.bind(this);
		this.topControls = this.topControls.bind(this);
		this.closeSingleEntry = this.closeSingleEntry.bind(this);
	}


	/**
	 * Get the total number of pages
	 * @return {number}
	 */
	getTotalPages() {
		return Math.ceil(this.props.form.entries.count / this.state.perPage);
	}

	closeSingleEntry() {
		this.props.onSingleEntryViewerClose();
		this.setCurrentEntry(0);
	}

	/**
	 * Set ID of current entry
	 *
	 * @param {Number} currentEntry
	 */
	setCurrentEntry(currentEntry) {
		this.setState({currentEntry})
	}

	/**
	 * Handle clicks on entry action buttons
	 *
	 * @param {String} eventType Type of event to dispatch
	 * @param {String} entryId Entry ID
	 */
	onEntryAction(eventType, entryId) {
		switch (eventType) {
			case 'view':
				this.props.onSingleEntryViewerOpen(entryId);
				this.setCurrentEntry(entryId);
			break;
			case 'resend':
				this.props.onEntryResend(entryId);
				break;
			case 'delete':
				this.props.onEntryDelete(entryId);
				break;
			default:
				return;
		}
	}

	/**
	 * Create fields for single entry viewer
	 *
	 * @param entry
	 * @return {Array}
	 */
	getEntryFields(entry) {
		const formFields = this.props.form.fields;

		let fields = [];
		if (undefined !== fields || 0 === Object.keys(fields).length) {
			// eslint-disable-next-line
			Object.keys(entry.fields).map(
				// eslint-disable-next-line
				fieldId => {
				let field = entry.fields[fieldId];
				if (formFields.hasOwnProperty(fieldId)) {
					field.label = formFields[fieldId].name;
				} else {
					field.label = field.slug;

				}
				fields.push(field);
			});
		}
		return fields;
	}

	/**
	 * Create entry viewer grid view
	 * @return {*}
	 */
	entriesGrid() {
		return (
			<div

			>
				<div>
					{this.topControls()}
				</div>
				<EntryViewer
					columns={getFormColumns(
						this.props.form,
						this.state.entryListOnly,
						true
					)}
					rows={getFormRows(
						this.props.entries,
						this.onEntryAction
					)}
					totalPages={this.getTotalPages()}
					onPageNav={this.props.onEntryPageNav}
				/>
			</div>
		);
	}

	/**
	 * Display controls for top of entry viewer
	 * @return {*}
	 */
	topControls() {
		const {entryListOnly} = this.state;
		const summaryOnlyFieldConfig = {
			id: 'cf-form-entry-viewer-summary-only',
			label: 'Summary Fields Only?',
			desc: '',
			type: 'fieldset',
			innerType: 'checkbox',
			value: entryListOnly,
			options: [
				{
					value: true,
					label: 'Yes'
				}
			],
			onValueChange: () => {
				this.setState({
					entryListOnly: !entryListOnly
				});
				this.props.onSingleEntryViewerOpen();

			}
		};


		const configFields = [
			summaryOnlyFieldConfig,
		];

		return (
			<Grid
				width={gridCollapse}
				gap={gridGap}
			>
				<Button
					onClick={this.props.onEntryListViewClose}
				>
					Close
				</Button>
				<RenderGroup
					configFields={configFields}
					className={'caldera-forms-entry-viewer-top-controls'}
				/>
			</Grid>

		);

	}

	/**
	 * Render the FormEntryViewer
	 * @return {*}
	 */
	render() {
		const {currentEntry} = this.state;

		if (!currentEntry) {
			return (
				<Grid
					width={gridCollapse}
					gap={gridGap}
					className={FormEntryViewer.classNames.wrapper}
				>
					{this.entriesGrid()}
				</Grid>

			)
		}
		if (!this.props.entries.hasOwnProperty(currentEntry)) {
			return <p>Entry {currentEntry} not found</p>
		}

		const entry = this.props.entries[currentEntry];
		let fields = this.getEntryFields(entry);
		return (
			<Grid
				width={gridCollapse}
				gap={gridGap}
				className={FormEntryViewer.classNames.wrapper}
			>
				<Entry
					fields={fields}
					user={entry.user}
					id={entry.id}
					form={this.props.form}
					onClose={() => {
						this.closeSingleEntry();
					}}
					page={this.props.page}
				/>
				{this.entriesGrid()}
			</Grid>
		)

	}


};

/**
 * Default props for the <FormEntryViewer> component
 *
 * @type {{form: *, getEntries: shim}}
 */
FormEntryViewer.propTypes = {
	form: PropTypes.object.isRequired,
	entries: PropTypes.oneOfType([
			PropTypes.object,
			PropTypes.array,
		]
	),
	onSingleEntryViewerOpen: PropTypes.func,
	onSingleEntryViewerClose: PropTypes.func,
	onEntryListViewClose: PropTypes.func,
	onEntryPageNav: PropTypes.func
};

FormEntryViewer.classNames = {
	wrapper: 'caldera-forms-entry-viewer'
};

