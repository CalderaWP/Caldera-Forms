import React from 'react';
import {PureComponent} from 'react';
import propTypes from 'prop-types';
import {ShortcodeViewer} from "./ShortcodeViewer";
import classNames from 'classnames';
//import {Button} from '@wordpress/components'

/**
 * Show one form in the FormList
 */
export class Form extends PureComponent {

	/**
	 * Create Form component
	 *
	 * @param props
	 */
	constructor(props) {
		super(props);
		this.state = {
			showShortcode: false,
		};

		this.toggleShortcodeView = this.toggleShortcodeView.bind(this);
		this.getEntriesCount = this.getEntriesCount.bind(this);
		this.openEntryViewerForForm = this.openEntryViewerForForm.bind(this);
	}

	/**
	 * Open or close the shortcode view
	 */
	toggleShortcodeView() {
		this.setState({showShortcode: !this.state.showShortcode});
	}

	/**
	 * Get the entries count
	 *
	 * Returns false if disabled
	 * @return {Number|Boolean}
	 */
	getEntriesCount() {
		if (!this.props.form.hasOwnProperty('entries')|| !this.props.form.entries.hasOwnProperty('count')) {
			return false;
		}
		return parseInt(this.props.form.entries.count,10);
	}

	/**
	 * Dispatch action to open entry viewer for one form
	 */
	openEntryViewerForForm() {
		this.props.openEntryViewerForForm(this.props.form.ID);
	}

	/**
	 * Render Form list item
	 * @return {*}
	 */
	render() {

		const activeForm = this.props.form.hasOwnProperty('form_draft')
			? this.props.form.form_draft
			: false;
		return (

			<tr
				id={`form_row_${this.props.form.ID}`}
				className={classNames({
					alternate: this.props.isAlternate,
					form_entry_row: true,
				})}
			>
				<td
					className={
						classNames(
							{'active-form': activeForm}
						)
					}
				>
					{!this.state.showShortcode &&
					<span className="cf-form-name-preview">{this.props.form.name}</span>
					}
					<span className="cf-form-view-shorcode">
						<ShortcodeViewer
							formId={this.props.form.ID}
							onButtonClick={this.toggleShortcodeView}
							show={this.state.showShortcode}
						/>
					</span>

					<span className="row-actions">
						<span className="edit">
							<a
								href={`${this.props.form.editLink}`}
							>
								Edit
							</a>
							{false !== this.getEntriesCount() &&
								<button
									className={ Form.classNames.entryButton }
									onClick={this.openEntryViewerForForm}
								>
									View Entries
								</button>
							}

						</span>
					</span>
				</td>
				<td
					style={
						{
							width: '4em',
							textAign: 'center'
						}
					}
					className={`entry_count_${this.props.form.ID}`}
				>
					{false === this.getEntriesCount() &&
					<React.Fragment>
						Disabled
					</React.Fragment>
					}
					{false !== this.getEntriesCount() &&
					<React.Fragment>
						{this.getEntriesCount()}
					</React.Fragment>
					}
				</td>
			</tr>

		);
	}

}

/**
 * Prop definitions for form component
 *
 * @type {{form: *, onFormUpdate: *, openEntryViewerForForm: shim}}
 */
Form.propTypes = {
	isAlternate: propTypes.bool,
	form: propTypes.object.isRequired,
	onFormUpdate: propTypes.func.isRequired,
	openEntryViewerForForm: propTypes.func.isRequired
};


Form.classNames = {
	isAlternate: false,
	entryButton: 'view-entry-button'
};


