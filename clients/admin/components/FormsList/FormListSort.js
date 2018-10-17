import React from 'react';
import PropTypes from 'prop-types'
import {RenderGroup} from '@caldera-labs/components';

export const SORT_FORMS_BY_NAME = 'name';
export const SORT_FORMS_BY_UPDATE = '_last_updated';

/**
 * Form list sort and search controls.
 *
 * @param props
 * @return {*}
 * @constructor
 */
export const FormListSort = (props) => {

	const sortOptions = {
		id: 'cf-form-sort-options',
		label: 'Sort Forms By',
		desc: '',
		type: 'select',
		inputClass: FormListSort.classNames.order,
		value: props.order,
		options: [
			{
				value: SORT_FORMS_BY_NAME,
				label: 'Name'
			},
			{
				value: SORT_FORMS_BY_UPDATE,
				label: 'Last Update'
			}
		],
		onValueChange: (event) => {
			props.onChangeOrder(event.target.value)

		}
	};
	const searchField = {
		id: 'cf-form-search',
		label: 'Search Forms By',
		desc: '',
		type: 'input',
		innerType: 'search',
		inputClass: FormListSort.classNames.search,
		value: props.formSearchTerm,
		onValueChange: (newValue) => {
			props.onFormSearch(newValue);
		}
	};
	return (
		<div>
			<RenderGroup
				configFields={[
					searchField,
					sortOptions
				]}
				className={'caldera-forms-admin-list-sort'}
			/>
		</div>
	);
};


FormListSort.propTypes = {
	order: PropTypes.oneOf([
		SORT_FORMS_BY_NAME,
		SORT_FORMS_BY_UPDATE
	]),
	onChangeOrder: PropTypes.func,
	formSearchTerm: PropTypes.string,
	onFormSearch: PropTypes.func

};

FormListSort.defaultProps = {
	order: SORT_FORMS_BY_NAME
};

FormListSort.classNames = {
	order: 'order-forms-select',
	search: 'search-froms'
};


