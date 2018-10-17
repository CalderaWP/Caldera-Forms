import React from "react";
import "react-table/react-table.css";
import PropTypes from 'prop-types';
import {Button} from "@wordpress/components"
import ReactDataGrid from 'react-data-grid'
import {pickArray} from "../util/pickArray";

/**
 * A viewer for Caldera (FormsSlot) entries
 *
 * Keep this abstracted from the shape of Caldera (FormsSlot) entries.
 * <FormEntryViewer> uses this component, it shapes data to rows/ columns.
 */
export class EntryViewer extends React.PureComponent {

	/**
	 * @param props
	 */
	constructor(props) {
		super(props);
		this.state = {
			loading: true,
			columnIds: [],
		};

		this.onNext = this.onNext.bind(this);
		this.onPrevious = this.onPrevious.bind(this);
		this.rowGetter = this.rowGetter.bind(this);
		this.setColumnIds = this.setColumnIds.bind(this);
		this.rowHasColumn = this.rowHasColumn.bind(this);
		this.componentDidMount = this.componentDidMount.bind(this);
		this.pagination = this.pagination.bind(this);
	}

	/**
	 * @inheritDoc
	 */
	componentDidMount(){
		this.setColumnIds();
	}

	/**
	 * Set the columnIds index of state
	 */
	setColumnIds(){
		const columnIds = pickArray(this.props.columns,'key');
		this.setState({columnIds});
	}

	/**
	 * Check if an index in a row represents a valid column
	 * @param {String}rowId
	 * @return {boolean}
	 */
	rowHasColumn(rowId) {
		return this.state.columnIds.includes(rowId);
	}

	/**
	 * Navigate to previous page
	 */
	onPrevious() {
		let {page} = this.props;
		page--;
		this.props.onPageNav(page - 1 );
	}

	/**
	 * Navigate to next page
	 */
	onNext() {
		let {page,totalPages} = this.props;
		page++;
		if( page < totalPages){
			this.props.onPageNav(page);
		}
	}


	/**
	 * Check if we have a page before this page
	 */
	showPreviousNav() {
		return 1 !== this.props.page;
	}

	/**
	 * Check if we have a page after this page
	 */
	showNextNav(){
		return this.props.totalPages >= this.props.page;
	}

	/**
	 * Get data for one row
	 * @param {Number} i
	 * @return {*}
	 */
	rowGetter(i){
		return this.props.rows[i];
	};

	/**
	 * Render EntryViewer component
	 * @return {*}
	 */
	render() {
		return  (
			<div>
				{this.pagination()}
				<ReactDataGrid
					columns={this.props.columns}
					rowGetter={this.rowGetter}
					rowsCount={this.props.rows.length}
					minHeight={500}
				/>
				{this.pagination()}
			</div>
		);
	}

	pagination() {
		return <div>
			{true === this.showPreviousNav() &&
			<Button
				className={EntryViewer.classNames.nextNav}
				isLarge
				isDefault
				onClick={this.onPrevious}
			>
				Previous Page
			</Button>
			}

			{false === this.showPreviousNav() &&
			<Button
				className={EntryViewer.classNames.nextNav}
				isLarge
				isDefault
				disabled
				onClick={this.onPrevious}
			>
				Previous Page
			</Button>
			}

			<span>
						Page {this.props.page} of {this.props.totalPages}
					</span>
			{true === this.showNextNav() &&
			<Button
				className={EntryViewer.classNames.nextNav}
				isLarge
				isDefault
				onClick={this.onNext}
			>
				Next Page
			</Button>
			}


		</div>;
	}
};

/**
 * Prop type definitions for EntryViewer component
 *
 * @type {{columns: shim, rows: shim, className: shim, data: shim, totalPages: shim, onPageNav: shim, onDelete: shim, onExport: shim, onChangeEntryStatus: shim, dateFormat: shim, defaultPageSize: shim, prePrepareData: shim}}
 */
EntryViewer.propTypes = {
	columns: PropTypes.array.isRequired,
	rows: PropTypes.array.isRequired,
	className: PropTypes.string,
	page: PropTypes.number,
	totalPages: PropTypes.number,
	onPageNav: PropTypes.func.isRequired,
	onDelete: PropTypes.func,
	onExport: PropTypes.func,
	onChangeEntryStatus: PropTypes.func,
};

/**
 * Default properties for the EntryViewer component
 * @type {{dateFormat: string, defaultPageSize: number, prePrepareData: EntryViewer.defaultProps.prePrepareData}}
 */
EntryViewer.defaultProps = {
	page: 1,
	totalPages: 1
};

EntryViewer.classNames = {
	prevNav: 'entry-nav-prev',
	nextNav: 'entry-nav-next'
};