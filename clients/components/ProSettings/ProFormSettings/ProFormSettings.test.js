import renderer from 'react-test-renderer';
import React from 'react';
import { shallow } from 'enzyme/build';
import Enzyme from 'enzyme/build';
import Adapter from 'enzyme-adapter-react-16/build';
import {ProFormSettings} from "./ProFormSettings";
import {optionFactory} from "../../../../../../Desktop/components/util/optionFactory";

Enzyme.configure({ adapter: new Adapter() });

describe( 'ProFormSettings component', () => {
	it( 'Matches snapshot with minimal props', () => {
		expect(
			renderer.create(
				<ProFormSettings/>
			).toJSON()
		).toMatchSnapshot()
	});


	it( 'Matches snapshot with layouts', () => {
		expect(
			renderer.create(
				<ProFormSettings
					layouts={[
						optionFactory(1,"one"),
						optionFactory(2,'two')

					]}

				/>
			).toJSON()
		).toMatchSnapshot()
	});

	it( 'Is wrapped in the right class', () => {
		expect(
			shallow(
				<ProFormSettings/>
			).find( '.' + ProFormSettings.defaultProps.wrapperClass )
				.length
		).toEqual(1)
	});
});