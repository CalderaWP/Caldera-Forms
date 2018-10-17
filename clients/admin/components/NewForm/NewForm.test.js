import React from 'react';
import {NewForm} from "./NewForm";
import renderer from "react-test-renderer";
import {mount} from 'enzyme';
import Enzyme from 'enzyme';
import Adapter from 'enzyme-adapter-react-16';
Enzyme.configure({adapter: new Adapter()});

describe( 'CreateFormSlot Component', () => {
	it( 'Matches snapshot', () => {
		const component = renderer.create(
			<NewForm
				onCreate={() => {}}

			/>
		);
		expect( component.toJSON() ).toMatchSnapshot();
	});

	it( 'Matches snapshot with templates passed', () => {
		const component = renderer.create(
			<NewForm
				onCreate={() => {}}
				templates={[
					{
						label: 'Roy',
						value: 'roy'
					}
				]}

			/>
		);
		expect( component.toJSON() ).toMatchSnapshot();
	});

	describe( 'Updates its internal state', () => {
		it( 'Sets form name', () => {
			const component = mount(
				<NewForm
					onCreate={() => {}}
				/>
			);
			component.find( '#newFormName' ).children().find('input' ).simulate('change', { target: { value: 'Contact Form' } });
			expect( component.state().newFormName ).toEqual( 'Contact Form');
		});

		it( 'Passes state update', () => {
			let stateReceived = {};
			const component = mount(
				<NewForm
					onCreate={(values) => {
						stateReceived = values;
					}}
					templates={[
						{
							label: 'Roy',
							value: 'roy'
						}
					]}
				/>
			);
			component.find( '#newFormName' ).children().find('input' ).simulate('change', { target: { value: 'Contact Form' } });
			component.find( '#newFormSubmitButton' ).children().find('input' ).simulate('click');
			expect( stateReceived.newFormName ).toEqual( 'Contact Form' );
		});

	});
});
