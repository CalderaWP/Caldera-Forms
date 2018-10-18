import renderer from 'react-test-renderer';
import React from 'react';
import {shallow} from 'enzyme';
import Enzyme from 'enzyme';
import Adapter from 'enzyme-adapter-react-16';
import {Settings} from "./Settings";
import {GENERAL_SETTINGS} from "./GeneralSettings/generalSettingsType";
import {STYLE_FORM} from "./GeneralSettings/configFields";
import {PRO_CONNECTED, PRO_SETTINGS} from "./ProSettings/proSettingsType";

Enzyme.configure({adapter: new Adapter()});
const handler = () => {
};

describe( 'Settings component', () => {
	it( 'Renders with minimal props', () => {
		const componet = renderer.create(
			<Settings
				onTabSelect={handler}
				onSettingsSave={handler}

			/>
		);
		expect(componet.toJSON()).toMatchSnapshot();
	});



});