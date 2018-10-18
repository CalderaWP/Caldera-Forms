import renderer from "react-test-renderer";
import React from 'react';
import {ShortcodeViewer} from "./ShortcodeViewer";


describe('ShortcodeViewer component', () => {
	it('Shows inital view', () => {
		const formList = renderer.create(
			<ShortcodeViewer
				formId={'cf1'}
				onButtonClick={() => {
				}}
			/>
		);
		expect(formList.toJSON()).toMatchSnapshot();
	});

	it('Shows shorcode actually', () => {
		const formList = renderer.create(
			<ShortcodeViewer
				formId={'cf1'}
				onButtonClick={() => {
				}}
				show={true}
			/>
		);
		expect(formList.toJSON()).toMatchSnapshot();
	});

});