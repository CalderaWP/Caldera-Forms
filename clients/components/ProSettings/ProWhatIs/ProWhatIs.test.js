import renderer from 'react-test-renderer';
import React from 'react';
import { shallow } from 'enzyme/build';
import Enzyme from 'enzyme/build';
import Adapter from 'enzyme-adapter-react-16/build';
import {ProWhatIs} from "./ProWhatIs";

Enzyme.configure({ adapter: new Adapter() });

describe( 'ProWhatIs component', () => {
	it( 'Matches snapshot with minimal props', () => {
		expect(
			renderer.create(
				<ProWhatIs/>
			).toJSON()
		).toMatchSnapshot()
	});

	it( 'Is wrapped in the right class', () => {
		expect(
			shallow(
				<ProWhatIs/>
			).find( '.' + ProWhatIs.classNames.wrapper )
				.length
		).toEqual(1)
	});
});