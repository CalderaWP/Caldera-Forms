import {shallow,mount} from 'enzyme';
import EnzymeAdapter from '../../../createEnzymeAdapter'
import {fileFieldConfigs} from "./fileFieldConfigs";
import {MockFileFieldRenderer} from "./MockFileFieldRenderer";
import React from 'react';

describe( 'DOM testing file components', () => {

	it( 'Can change value', () => {
		const field = fileFieldConfigs.required_single_allow_png;
		const component = mount(
			<MockFileFieldRenderer
				field={field}

			/>
		);
		component.instance().onChange( 'seven' );
		expect(component.state('value')).toBe('seven')
	});

	it( 'Can setIsInvalid', () => {
		const field = fileFieldConfigs.required_single_allow_png;
		const component = mount(
			<MockFileFieldRenderer
				field={field}

			/>
		);
		component.instance().setIsInvalid( true );
		expect(component.state('isInvalid')).toBe(true);
	});


	it( 'Can set disabled', () => {
		const field = fileFieldConfigs.required_single_allow_png;
		const component = mount(
			<MockFileFieldRenderer
				field={field}
			/>
		);
		component.instance().setIsInvalid( true );
		expect(component.state('isInvalid')).toBe(true);
	});


	it( 'Can set message', () => {
		const field = fileFieldConfigs.required_single_allow_png;
		const component = mount(
			<MockFileFieldRenderer
				field={field}

			/>
		);
		const message = {error: true, message: 'Fail' };
		component.instance().setMessage(message );
		expect(component.state('message')).toBe(message)
	});

});

describe( 'DOM testing file components', () => {


	it( 'If multiple upload option is not enabled, can not add a second file', () => {
		const field = fileFieldConfigs.required_single_allow_png;
		const component = mount(
			<MockFileFieldRenderer
				field={field}

			/>
		);
		expect(component.find( '.btn').length).toBe(1);
		component.instance().setValue(['File blob']);
		expect(component.find( '.btn').length).toBe(2);

	});

});
