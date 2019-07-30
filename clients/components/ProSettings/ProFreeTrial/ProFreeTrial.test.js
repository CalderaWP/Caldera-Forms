import renderer from 'react-test-renderer';
import React from 'react';
import { shallow } from 'enzyme/build';
import Enzyme from 'enzyme/build';
import Adapter from 'enzyme-adapter-react-16/build';
import {ProFreeTrial} from "./ProFreeTrial";

Enzyme.configure({ adapter: new Adapter() });

describe( 'ProFreeTrial component', () => {
	it( 'Matches snapshot with minimal props', () => {
		expect(
			renderer.create(
				<ProFreeTrial/>
			).toJSON()
		).toMatchSnapshot()
	});

	it( 'Is wrapped in the right class', () => {
		expect(
			shallow(
				<ProFreeTrial/>
			).find( '.' + ProFreeTrial.classNames.wrapper )
				.length
		).toEqual(1)
	});
});