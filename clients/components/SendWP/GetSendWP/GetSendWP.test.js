import renderer from 'react-test-renderer';
import React from 'react';
import { shallow } from 'enzyme/build';
import Enzyme from 'enzyme/build';
import Adapter from 'enzyme-adapter-react-16/build';
import {GetSendWP} from "./GetSendWP";

Enzyme.configure({ adapter: new Adapter() });

describe( 'GetSendWP component', () => {
	it( 'Matches snapshot with minimal props', () => {
		expect(
			renderer.create(
				<GetSendWP/>
			).toJSON()
		).toMatchSnapshot()
	});

	it( 'Is wrapped in the right class', () => {
		expect(
			shallow(
				<GetSendWP/>
			).find( '.' + GetSendWP.classNames.wrapper )
				.length
		).toEqual(1)
	});
});