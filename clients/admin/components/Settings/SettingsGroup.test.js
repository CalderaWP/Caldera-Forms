import renderer from 'react-test-renderer';
import React from 'react';
import {shallow} from 'enzyme';
import Enzyme from 'enzyme';
import Adapter from 'enzyme-adapter-react-16';
import {SettingsGroup} from "./SettingsGroup";

Enzyme.configure({adapter: new Adapter()});
const handler = () => {
};

describe('Settings component', () => {
	const mockSettings = {
		roy: 'Sivan'
	};

	const configFields = [
		{
			id: 'roy',
			onValueChange: handler,
			value: 3,
			type: 'number',
			path: 'mockStuff.roy'
		}
	];

	it('Renders with minimal props', () => {
		const componet = renderer.create(
			<SettingsGroup
				onSettingsSave={handler}
				settings={mockSettings}
				settingsKey={'mockStuff'}
				configFields={configFields}
			/>
		);
		expect(componet.toJSON()).toMatchSnapshot();
	});
	it('Sets settings in state', () => {
		const componet = shallow(
			<SettingsGroup
				onSettingsSave={handler}
				settings={mockSettings}
				settingsKey={'mockStuff'}
				configFields={configFields}
			/>
		);
		expect(componet.state('mockStuff')).toEqual(mockSettings);
	});

	it('Passes updates to state to save handle', () => {
		const updatedSettings = {
			roy: true
		};

		let updates = {};
		const component = shallow(
			<SettingsGroup
				onSettingsSave={(values) => {
					updates = values;
				}}
				settings={mockSettings}
				settingsKey={'mockStuff'}
				configFields={[
					{
						id: 'roy',
						onValueChange: handler,
						value: 3,
						type: 'number'
					}
				]}

			/>
		);

		component.setState({mockStuff: updatedSettings});
		component.instance().onSettingsSave();
		expect(updates.roy).toEqual(true);
	});

	it('Merges settings on update', () => {
		const updatedSettings = {
			roy: true
		};

		const component = shallow(
			<SettingsGroup
				onSettingsSave={handler}
				settings={mockSettings}
				settingsKey={'mockStuff'}
				configFields={[
					{
						id: 'roy',
						onValueChange: handler,
						value: 3,
						type: 'number'
					}
				]}

			/>
		);

		component.instance().onSettingsChange(updatedSettings);
		expect(component.state('mockStuff')).toEqual(updatedSettings);
	});

});