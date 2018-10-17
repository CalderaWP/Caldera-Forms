import renderer from 'react-test-renderer';
import React from 'react';
import { shallow } from 'enzyme';
import Enzyme from 'enzyme';
import Adapter from 'enzyme-adapter-react-16';
import {ProEnterApp} from "./ProEnterApp";

Enzyme.configure({ adapter: new Adapter() });

describe( 'ProEnterApp component', () => {
	it( 'Matches snapshot with minimal props', () => {
		expect(
			renderer.create(
				<ProEnterApp/>
			).toJSON()
		).toMatchSnapshot()
	});

	it( 'Is wrapped in the right class', () => {
		expect(
			shallow(
				<ProEnterApp/>
			).find( '.' + ProEnterApp.classNames.wrapper )
				.length
		).toEqual(1)
	});
});