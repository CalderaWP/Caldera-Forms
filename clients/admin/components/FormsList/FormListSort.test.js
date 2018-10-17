import renderer from "react-test-renderer";
import {FormListSort,SORT_FORMS_BY_NAME, SORT_FORMS_BY_UPDATE} from "./FormListSort";
import React from 'react';
import {mount} from 'enzyme';
import Enzyme from 'enzyme';
import Adapter from 'enzyme-adapter-react-16';
Enzyme.configure({adapter: new Adapter()});

describe('FormListSort component', () => {
	it('Matches snapshot', () => {
		const formList = renderer.create(
			<FormListSort
				onChangeOrder={() => {} }
				order={SORT_FORMS_BY_NAME}
			/>
		);
		expect(formList.toJSON()).toMatchSnapshot();
	});

	it( 'updates order', () => {
		let recived = '';
		const component = mount(
			<FormListSort
				onChangeOrder={(
					newValue
				) => {
					recived = newValue;
				}
				}
				order={SORT_FORMS_BY_NAME}
			/>
		);

		component.find( '.' + FormListSort.classNames.order ).simulate('change',
			{target: { value: SORT_FORMS_BY_UPDATE}}
		);
		expect(recived).toEqual(SORT_FORMS_BY_UPDATE);
	});




});